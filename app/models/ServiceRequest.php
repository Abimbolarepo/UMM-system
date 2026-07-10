<?php

require_once __DIR__ . "/../config/database.php";

class ServiceRequest
{
    private $db;

    private const STATUS_PENDING     = 'Pending';
    private const STATUS_ASSIGNED    = 'Assigned';
    private const STATUS_IN_PROGRESS = 'In Progress';
    private const STATUS_COMPLETED   = 'Completed';
    private const STATUS_CANCELLED   = 'Cancelled';

    private const VALID_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ASSIGNED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED
    ];

    /*
    | Defines which status a request is allowed to move to next.
    | Completed and Cancelled are terminal - nothing can leave those states.
    */
    private const STATUS_TRANSITIONS = [
        self::STATUS_PENDING     => [self::STATUS_ASSIGNED, self::STATUS_CANCELLED],
        self::STATUS_ASSIGNED    => [self::STATUS_IN_PROGRESS, self::STATUS_CANCELLED],
        self::STATUS_IN_PROGRESS => [self::STATUS_COMPLETED, self::STATUS_CANCELLED],
        self::STATUS_COMPLETED   => [],
        self::STATUS_CANCELLED   => [self::STATUS_ASSIGNED]
    ];

    private const ROLE_MAINTENANCE_OFFICER = 2;

    /*
    |--------------------------------------------------------------------------
    | Check Whether A Status Transition Is Allowed
    |--------------------------------------------------------------------------
    */
    public function canTransition(string $from, string $to): bool
    {
        return in_array(
            $to,
            self::STATUS_TRANSITIONS[$from] ?? [],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    | Accepts an optional PDO instance so tests can inject a mock connection.
    | Falls back to the shared Database singleton when none is provided, so
    | every existing `new ServiceRequest()` call site keeps working unchanged.
    */
    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getConnection();
    }

    /*
    |--------------------------------------------------------------------------
    | Log Database Errors
    |--------------------------------------------------------------------------
    | Accepts the calling method's name (pass __METHOD__ at the call site)
    | so log entries stay traceable to where the failure actually occurred.
    */
    private function logError(PDOException $e, string $method): void
    {
        error_log(sprintf("[%s] %s", $method, $e->getMessage()));
    }

    /*
    |--------------------------------------------------------------------------
    | Fetch A Single Row Safely
    |--------------------------------------------------------------------------
    | Normalizes PDO's fetch() "false means no row" into null, since null
    | reads more clearly than false at every call site.
    */
    private function fetchOne(PDOStatement $stmt): ?array
    {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result !== false ? $result : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Shared Base Query - Request + Requester + Category + Assigned Officer
    |--------------------------------------------------------------------------
    | Used by getAllRequests(), searchRequests(), getRequestsByStatus(),
    | filterRequests(), and getRequestById() to avoid repeating the same
    | joins in five different places.
    */
    private function baseRequestQuery(): string
    {
        return "
            SELECT

                sr.*,

                CONCAT(u.firstname,' ',u.lastname) AS fullname,

                u.email,

                u.phone,

                u.department,

                c.category_name,

                CONCAT(o.firstname,' ',o.lastname) AS assigned_officer

            FROM service_requests sr

            INNER JOIN users u
                ON sr.user_id = u.user_id

            INNER JOIN categories c
                ON sr.category_id = c.category_id

            LEFT JOIN users o
                ON sr.assigned_to = o.user_id
        ";
    }

    /*
    |--------------------------------------------------------------------------
    | Shared Statistics Query
    |--------------------------------------------------------------------------
    | Powers getStudentStatistics(), getOfficerStatistics(), and
    | getReportStatistics() so the status breakdown SQL only exists once.
    | Pass an optional WHERE clause (with leading " WHERE ...") and its
    | bound parameters to scope the counts to a student or officer.
    */
    private function getStatistics(string $whereClause = "", array $params = []): array
    {
        $sql = "SELECT

                    COUNT(*) AS total_requests,

                    SUM(CASE WHEN status = '" . self::STATUS_PENDING . "' THEN 1 ELSE 0 END) AS pending,

                    SUM(CASE WHEN status = '" . self::STATUS_ASSIGNED . "' THEN 1 ELSE 0 END) AS assigned,

                    SUM(CASE WHEN status = '" . self::STATUS_IN_PROGRESS . "' THEN 1 ELSE 0 END) AS in_progress,

                    SUM(CASE WHEN status = '" . self::STATUS_COMPLETED . "' THEN 1 ELSE 0 END) AS completed,

                    SUM(CASE WHEN status = '" . self::STATUS_CANCELLED . "' THEN 1 ELSE 0 END) AS cancelled

                FROM service_requests" . $whereClause;

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->execute($params);

            $stats = $this->fetchOne($stmt) ?? [];
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            $stats = [];
        }

        return [

            'total_requests' => (int)($stats['total_requests'] ?? 0),
            'pending'        => (int)($stats['pending'] ?? 0),
            'assigned'       => (int)($stats['assigned'] ?? 0),
            'in_progress'    => (int)($stats['in_progress'] ?? 0),
            'completed'      => (int)($stats['completed'] ?? 0),
            'cancelled'      => (int)($stats['cancelled'] ?? 0)

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Centralized Status Change
    |--------------------------------------------------------------------------
    | Every status transition (assign, start work, complete, generic admin
    | update) flows through here. It:
    |   1. Validates the requested status against STATUS_TRANSITIONS using
    |      the request's CURRENT status, so illegal jumps (e.g. Completed
    |      back to Pending) are rejected in one place.
    |   2. Optionally sets assigned_to (used by assignOfficer()).
    |   3. Optionally restricts the update to a specific officer's own
    |      assignment (used by startWork() / completeRequest()).
    |   4. Optionally records completion notes.
    |   5. Writes an audit trail row to request_logs and wraps the status
    |      update + log insert in a transaction, since that is now genuinely
    |      two related writes that must succeed or fail together.
    |
    | NOTE: this requires a request_logs table. Run this migration first:
    |
    |   CREATE TABLE request_logs (
    |       log_id      INT AUTO_INCREMENT PRIMARY KEY,
    |       request_id  INT NOT NULL,
    |       action      VARCHAR(100) NOT NULL,
    |       from_status VARCHAR(50) NULL,
    |       to_status   VARCHAR(50) NOT NULL,
    |       performed_by INT NULL,
    |       notes       TEXT NULL,
    |       created_at  DATETIME NOT NULL,
    |       FOREIGN KEY (request_id) REFERENCES service_requests(request_id),
    |       FOREIGN KEY (performed_by) REFERENCES users(user_id)
    |   );
    */
    private function changeStatus(
        int $requestId,
        string $newStatus,
        ?int $assignTo = null,
        ?int $ownedByOfficer = null,
        ?string $notes = null,
        ?int $performedBy = null
    ): bool {
        if (!in_array($newStatus, self::VALID_STATUSES, true)) {
            return false;
        }

        $current = $this->getRequestById($requestId);

        if ($current === null) {
            return false;
        }

        if (!$this->canTransition($current['status'], $newStatus)) {
            return false;
        }

        $setClauses = ["status = ?", "updated_at = NOW()"];
        $params = [$newStatus];

        if ($assignTo !== null) {
            $setClauses[] = "assigned_to = ?";
            $params[] = $assignTo;
        }

        if ($notes !== null) {
            $setClauses[] = "completion_notes = ?";
            $params[] = $notes;
        }

        $sql = "UPDATE service_requests SET " . implode(', ', $setClauses) . "
                WHERE request_id = ?
                AND status = ?";

        $params[] = $requestId;
        $params[] = $current['status'];

        if ($ownedByOfficer !== null) {
            $sql .= " AND assigned_to = ?";
            $params[] = $ownedByOfficer;
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            $updated = $stmt->rowCount() > 0;

            if ($updated) {
                $action = $this->describeTransition($current['status'], $newStatus);

                $this->logStatusChange($requestId, $action, $current['status'], $newStatus, $performedBy, $notes);
            }

            $this->db->commit();

            return $updated;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            $this->logError($e, __METHOD__);
            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Human-Readable Label For A Status Transition
    |--------------------------------------------------------------------------
    | Used only for the audit trail's "action" column, so reports and a
    | future timeline view can show "Assigned Officer" instead of just
    | "Pending -> Assigned".
    */
    private function describeTransition(?string $from, string $to): string
    {
        return match (true) {
            $from === null                                       => 'Created',
            $to === self::STATUS_ASSIGNED && $from === self::STATUS_CANCELLED => 'Reassigned Officer',
            $to === self::STATUS_ASSIGNED                         => 'Assigned Officer',
            $to === self::STATUS_IN_PROGRESS                      => 'Started Work',
            $to === self::STATUS_COMPLETED                        => 'Completed Request',
            $to === self::STATUS_CANCELLED                        => 'Cancelled Request',
            default                                               => 'Status Updated'
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Record A Status Change In The Audit Trail
    |--------------------------------------------------------------------------
    */
    private function logStatusChange(
        int $requestId,
        string $action,
        ?string $fromStatus,
        string $toStatus,
        ?int $performedBy,
        ?string $notes
    ): void {
        $sql = "INSERT INTO request_logs
                (request_id, action, from_status, to_status, performed_by, notes, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $requestId,
            $action,
            $fromStatus,
            $toStatus,
            $performedBy,
            $notes
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Status Timeline For A Request
    |--------------------------------------------------------------------------
    | Powers a "View Timeline" screen: Created -> Assigned -> In Progress ->
    | Completed, with who did it and when.
    */
    public function getRequestLogs(int $requestId): array
    {
        $sql = "SELECT

                    rl.action,

                    rl.from_status,

                    rl.to_status,

                    rl.notes,

                    rl.created_at,

                    CONCAT(u.firstname,' ',u.lastname) AS performed_by_name

                FROM request_logs rl

                LEFT JOIN users u
                    ON rl.performed_by = u.user_id

                WHERE rl.request_id = ?

                ORDER BY rl.created_at ASC";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->execute([$requestId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Data Before Creating A Request
    |--------------------------------------------------------------------------
    */
    private function validateCreateData(array $data): bool
    {
        $required = [
            'ticket_number',
            'user_id',
            'category_id',
            'title',
            'description',
            'location',
            'building',
            'room_number',
            'priority'
        ];

        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return false;
            }
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Ticket Number
    |--------------------------------------------------------------------------
    */
    public function generateTicket(): string
    {
        return sprintf(
            "UMMS-%s-%s",
            date("YmdHis"),
            strtoupper(bin2hex(random_bytes(3)))
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Maintenance Request
    |--------------------------------------------------------------------------
    */
    public function create(array $data): bool
    {
        if (!$this->validateCreateData($data)) {
            return false;
        }

        $sql = "INSERT INTO service_requests
        (
            ticket_number,
            user_id,
            category_id,
            title,
            description,
            location,
            building,
            room_number,
            priority,
            image,
            status
        )
        VALUES
        (
            :ticket_number,
            :user_id,
            :category_id,
            :title,
            :description,
            :location,
            :building,
            :room_number,
            :priority,
            :image,
            '" . self::STATUS_PENDING . "'
        )";

        // Ensure 'image' key always exists to avoid missing-parameter errors
        // when no file was uploaded with the request.
        if (!array_key_exists('image', $data)) {
            $data['image'] = null;
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare($sql);

            $stmt->execute($data);

            $created = $stmt->rowCount() > 0;

            if ($created) {
                $requestId = (int) $this->db->lastInsertId();

                $this->logStatusChange(
                    $requestId,
                    'Created',
                    null,
                    self::STATUS_PENDING,
                    (int) $data['user_id'],
                    'Request submitted'
                );
            }

            $this->db->commit();

            return $created;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            $this->logError($e, __METHOD__);
            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Student - View Own Requests
    |--------------------------------------------------------------------------
    */
    public function getStudentRequests(int $userId): array
    {
        $sql = "SELECT

                    sr.*,

                    c.category_name

                FROM service_requests sr

                INNER JOIN categories c
                    ON sr.category_id = c.category_id

                WHERE sr.user_id = ?

                ORDER BY sr.created_at DESC";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->execute([$userId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get Single Request
    |--------------------------------------------------------------------------
    */
    public function getRequestById(int $requestId): ?array
    {
        $sql = $this->baseRequestQuery() . " WHERE sr.request_id = ?";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->execute([$requestId]);

            return $this->fetchOne($stmt);
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Student Dashboard Statistics
    |--------------------------------------------------------------------------
    */
    public function getStudentStatistics(int $userId): array
    {
        return $this->getStatistics(" WHERE user_id = ?", [$userId]);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRATOR MODULE
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Method 1 - Get All Requests
    |--------------------------------------------------------------------------
    | Pagination is opt-in: leave $limit null to fetch every row exactly like
    | before. Pass both arguments once your admin views are ready to page
    | through results, e.g. getAllRequests(20, $page * 20).
    */
    public function getAllRequests(?int $limit = null, int $offset = 0): array
    {
        $sql = $this->baseRequestQuery() . " ORDER BY sr.created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        try {
            $stmt = $this->db->prepare($sql);

            if ($limit !== null) {
                $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
                $stmt->bindValue(':offset', max(0, $offset), PDO::PARAM_INT);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Method 2 - Search Requests
    |--------------------------------------------------------------------------
    */
    public function searchRequests(string $keyword): array
    {
        $sql = $this->baseRequestQuery() . "
                WHERE

                    sr.ticket_number LIKE ?

                    OR sr.title LIKE ?

                    OR sr.status LIKE ?

                    OR sr.priority LIKE ?

                    OR sr.building LIKE ?

                    OR sr.location LIKE ?

                    OR sr.room_number LIKE ?

                    OR CONCAT(u.firstname,' ',u.lastname) LIKE ?

                    OR u.department LIKE ?

                    OR c.category_name LIKE ?

                    OR CONCAT(o.firstname,' ',o.lastname) LIKE ?

                ORDER BY sr.created_at DESC";

        $search = "%{$keyword}%";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->execute(array_fill(0, 11, $search));

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Method 3 - Filter Requests By Status
    |--------------------------------------------------------------------------
    */
    public function getRequestsByStatus(string $status): array
    {
        $sql = $this->baseRequestQuery() . "
                WHERE sr.status = ?

                ORDER BY sr.created_at DESC";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->execute([$status]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Method 4 - Update Request Status
    |--------------------------------------------------------------------------
    */
    public function updateStatus(int $requestId, string $status, ?int $performedBy = null): bool
    {
        return $this->changeStatus($requestId, $status, null, null, null, $performedBy);
    }

    /*
    |--------------------------------------------------------------------------
    | Method 5 - Get Maintenance Officers
    |--------------------------------------------------------------------------
    */
    public function getMaintenanceOfficers(): array
    {
        $sql = "SELECT

                    user_id,

                    firstname,

                    lastname,

                    CONCAT(firstname,' ',lastname) AS fullname,

                    email,

                    phone,

                    department

                FROM users

                WHERE role_id = " . self::ROLE_MAINTENANCE_OFFICER . "

                AND status = 'Active'

                ORDER BY firstname ASC, lastname ASC";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Verify Officer Exists And Is Active
    |--------------------------------------------------------------------------
    */
    public function officerExists(int $officerId): bool
    {
        try {
            $stmt = $this->db->prepare("
                SELECT user_id
                FROM users
                WHERE user_id = ?
                AND role_id = " . self::ROLE_MAINTENANCE_OFFICER . "
                AND status = 'Active'
            ");

            $stmt->execute([$officerId]);

            return $this->fetchOne($stmt) !== null;
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Method 6 - Get Complete Request Details
    |--------------------------------------------------------------------------
    */
    public function getRequestDetails(int $requestId): ?array
    {
        return $this->getRequestById($requestId);
    }

    /*
    |--------------------------------------------------------------------------
    | Method 7 - Filter Requests
    |--------------------------------------------------------------------------
    */
    public function filterRequests(?string $status = null, ?string $priority = null): array
    {
        $sql = $this->baseRequestQuery() . " WHERE 1=1";

        $params = [];

        if (!empty($status)) {
            $sql .= " AND sr.status = ?";
            $params[] = $status;
        }

        if (!empty($priority)) {
            $sql .= " AND sr.priority = ?";
            $params[] = $priority;
        }

        $sql .= " ORDER BY sr.created_at DESC";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Reports - Overall Statistics
    |--------------------------------------------------------------------------
    */

    public function getReportStatistics(): array
    {
        return $this->getStatistics();
    }

    /*
    |--------------------------------------------------------------------------
    | Reports - Requests By Category
    |--------------------------------------------------------------------------
    */

    public function getRequestsByCategory(): array
    {
        $sql = "SELECT

                    c.category_name,

                    COUNT(sr.request_id) AS total

                FROM categories c

                LEFT JOIN service_requests sr
                    ON sr.category_id = c.category_id

                GROUP BY c.category_id

                ORDER BY total DESC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Reports - Recent Activities
    |--------------------------------------------------------------------------
    */

    public function getRecentActivities(int $limit = 10): array
    {
        $limit = max(1, $limit);

        $sql = "SELECT

                    sr.ticket_number,
                    sr.title,
                    sr.status,
                    sr.priority,
                    sr.created_at,

                    CONCAT(u.firstname,' ',u.lastname) AS fullname,

                    c.category_name,

                    CONCAT(o.firstname,' ',o.lastname) AS assigned_officer

                FROM service_requests sr

                INNER JOIN users u
                    ON sr.user_id = u.user_id

                INNER JOIN categories c
                    ON sr.category_id = c.category_id

                LEFT JOIN users o
                    ON sr.assigned_to = o.user_id

                ORDER BY sr.created_at DESC

                LIMIT :limit";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Count All Requests
    |--------------------------------------------------------------------------
    */

    public function countAllRequests(): int
    {
        try {
            $stmt = $this->db->query("
                SELECT COUNT(*) AS total
                FROM service_requests
            ");

            return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            return 0;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Count By Status
    |--------------------------------------------------------------------------
    */

    public function countByStatus(string $status): int
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) AS total
                FROM service_requests
                WHERE status = ?
            ");

            $stmt->execute([$status]);

            return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            return 0;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Assign Maintenance Officer
    |--------------------------------------------------------------------------
    */

    public function assignOfficer(int $requestId, int $officerId, ?int $performedBy = null): bool
    {
        if (!$this->officerExists($officerId)) {
            return false;
        }

        return $this->changeStatus($requestId, self::STATUS_ASSIGNED, $officerId, null, null, $performedBy ?? $officerId);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Assigned Officer
    |--------------------------------------------------------------------------
    */

    public function getAssignedOfficer(int $requestId): ?array
    {
        $sql = "

            SELECT

                u.user_id,

                CONCAT(u.firstname,' ',u.lastname) AS fullname

            FROM service_requests sr

            INNER JOIN users u

                ON sr.assigned_to = u.user_id

            WHERE sr.request_id = ?

        ";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->execute([$requestId]);

            return $this->fetchOne($stmt);
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Maintenance Officer Dashboard Statistics
    |--------------------------------------------------------------------------
    */

    public function getOfficerStatistics(int $officerId): array
    {
        return $this->getStatistics(" WHERE assigned_to = ?", [$officerId]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Requests Assigned To Maintenance Officer
    |--------------------------------------------------------------------------
    */

    public function getOfficerRequests(int $officerId): array
    {
        $sql = "SELECT

                    sr.*,

                    CONCAT(u.firstname,' ',u.lastname) AS student_name,

                    u.email,

                    u.phone,

                    u.department,

                    c.category_name

                FROM service_requests sr

                INNER JOIN users u
                    ON sr.user_id = u.user_id

                INNER JOIN categories c
                    ON sr.category_id = c.category_id

                WHERE sr.assigned_to = ?

                ORDER BY sr.created_at DESC";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->execute([$officerId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logError($e, __METHOD__);
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Start Work
    |--------------------------------------------------------------------------
    */

    public function startWork(int $requestId, int $officerId): bool
    {
        return $this->changeStatus($requestId, self::STATUS_IN_PROGRESS, null, $officerId, null, $officerId);
    }

    /*
    |--------------------------------------------------------------------------
    | Complete Request
    |--------------------------------------------------------------------------
    */

    public function completeRequest(int $requestId, string $remarks, int $officerId): bool
    {
        return $this->changeStatus($requestId, self::STATUS_COMPLETED, null, $officerId, $remarks, $officerId);
    }
}
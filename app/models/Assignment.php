<?php

require_once __DIR__ . "/../config/database.php";

class Assignment
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /*
    |--------------------------------------------------------------------------
    | Assign Request
    |--------------------------------------------------------------------------
    */

    public function assignOfficer($requestId, $officerId, $adminId)
    {
        $sql = "INSERT INTO assignments
                (
                    request_id,
                    officer_id,
                    assigned_by
                )
                VALUES
                (
                    ?,
                    ?,
                    ?
                )";

        $stmt = $this->db->prepare($sql);

        if ($stmt->execute([
            $requestId,
            $officerId,
            $adminId
        ]))
        {
            $update = $this->db->prepare("
                UPDATE service_requests
                SET
                    status = 'Assigned',
                    assigned_to = ?,
                    assigned_by = ?,
                    assigned_at = NOW()
                WHERE request_id = ?
            ");

            $update->execute([
                $officerId,
                $adminId,
                $requestId
            ]);

            return true;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Check if Request is Already Assigned
    |--------------------------------------------------------------------------
    */

    public function alreadyAssigned($requestId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM assignments
            WHERE request_id = ?
        ");

        $stmt->execute([$requestId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Assignment Details
    |--------------------------------------------------------------------------
    */

    public function getAssignment($requestId)
    {
        $stmt = $this->db->prepare("
            SELECT

                a.*,

                CONCAT(u.firstname,' ',u.lastname) AS officer_name,

                u.email,

                u.phone,

                u.department

            FROM assignments a

            INNER JOIN users u
                ON a.officer_id = u.user_id

            WHERE a.request_id = ?
        ");

        $stmt->execute([$requestId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | Maintenance Officer Dashboard Statistics
    |--------------------------------------------------------------------------
    */

    public function getOfficerStatistics($officerId)
    {
        $sql = "SELECT

                    COUNT(*) AS total_jobs,

                    SUM(CASE WHEN sr.status = 'Assigned' THEN 1 ELSE 0 END) AS assigned,

                    SUM(CASE WHEN sr.status = 'In Progress' THEN 1 ELSE 0 END) AS in_progress,

                    SUM(CASE WHEN sr.status = 'Completed' THEN 1 ELSE 0 END) AS completed

                FROM assignments a

                INNER JOIN service_requests sr
                    ON a.request_id = sr.request_id

                WHERE a.officer_id = ?";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([$officerId]);

        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        return [

            'total_jobs'  => (int)($stats['total_jobs'] ?? 0),

            'assigned'    => (int)($stats['assigned'] ?? 0),

            'in_progress' => (int)($stats['in_progress'] ?? 0),

            'completed'   => (int)($stats['completed'] ?? 0)

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Assignments for a Maintenance Officer
    |--------------------------------------------------------------------------
    */

    public function getOfficerAssignments($officerId)
    {
        $sql = "SELECT

                    a.assignment_id,

                    a.status AS assignment_status,

                    a.assigned_at,

                    sr.request_id,

                    sr.ticket_number,

                    sr.title,

                    sr.description,

                    sr.location,

                    sr.building,

                    sr.room_number,

                    sr.priority,

                    sr.status AS request_status,

                    sr.image,

                    sr.created_at,

                    c.category_name,

                    CONCAT(u.firstname,' ',u.lastname) AS student_name,

                    u.email,

                    u.phone

                FROM assignments a

                INNER JOIN service_requests sr
                    ON a.request_id = sr.request_id

                INNER JOIN users u
                    ON sr.user_id = u.user_id

                INNER JOIN categories c
                    ON sr.category_id = c.category_id

                WHERE a.officer_id = ?

                ORDER BY a.assigned_at DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([$officerId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Single Job Assigned to Officer
    |--------------------------------------------------------------------------
    */

    public function getOfficerJob($requestId, $officerId)
    {
        $sql = "SELECT

                    a.assignment_id,

                    a.status AS assignment_status,

                    a.assigned_at AS assignment_assigned_at,

                    a.remarks,

                    sr.request_id,

                    sr.ticket_number,

                    sr.title,

                    sr.description,

                    sr.location,

                    sr.building,

                    sr.room_number,

                    sr.priority,

                    sr.status AS request_status,

                    sr.image,

                    sr.created_at,

                    sr.assigned_at,

                    sr.completed_at,

                    c.category_name,

                    CONCAT(u.firstname,' ',u.lastname) AS student_name,

                    u.email,

                    u.phone

                FROM assignments a

                INNER JOIN service_requests sr
                    ON a.request_id = sr.request_id

                INNER JOIN users u
                    ON sr.user_id = u.user_id

                INNER JOIN categories c
                    ON sr.category_id = c.category_id

                WHERE

                    a.request_id = ?

                    AND a.officer_id = ?";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $requestId,
            $officerId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | Complete Maintenance Job
    |--------------------------------------------------------------------------
    */

    public function completeJob($requestId, $officerId, $remarks)
    {
        try {

            $this->db->beginTransaction();

            /*
            |------------------------------------------------------------------
            | Verify Assignment
            |------------------------------------------------------------------
            */

            $check = $this->db->prepare("
                SELECT assignment_id
                FROM assignments
                WHERE request_id = ?
                AND officer_id = ?
            ");

            $check->execute([
                $requestId,
                $officerId
            ]);

            if (!$check->fetch(PDO::FETCH_ASSOC)) {

               if ($this->db->inTransaction()) {
                   $this->db->rollBack();
                }

                return false;
            }

            /*
            |------------------------------------------------------------------
            | Update Assignment
            |------------------------------------------------------------------
            */

            $assignment = $this->db->prepare("
                UPDATE assignments
                SET
                    status = 'Completed',
                    remarks = ?
                WHERE
                    request_id = ?
                AND officer_id = ?
            ");

            if (!$assignment->execute([
                $remarks,
                $requestId,
                $officerId
            ])) {
                throw new Exception("Failed to update assignment.");
            }

            /*
            |------------------------------------------------------------------
            | Update Service Request
            |------------------------------------------------------------------
            */

            $request = $this->db->prepare("
                UPDATE service_requests
                SET
                    status = 'Completed',
                    completed_at = NOW()
                WHERE
                    request_id = ?
            ");

            if (!$request->execute([
                $requestId
            ])) {
                throw new Exception("Failed to update service request.");
            }

            $this->db->commit();

            return true;

        } catch (Exception $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get Completed Jobs for Logged-in Officer
    |--------------------------------------------------------------------------
    */

    public function getOfficerCompletedJobs($officerId)
    {
        $sql = "SELECT

                    sr.request_id,
                    sr.ticket_number,
                    sr.title,
                    sr.priority,
                    sr.completed_at,

                    c.category_name,

                    CONCAT(u.firstname,' ',u.lastname) AS student_name,

                    a.remarks

                FROM assignments a

                INNER JOIN service_requests sr
                    ON a.request_id = sr.request_id

                INNER JOIN categories c
                    ON sr.category_id = c.category_id

                INNER JOIN users u
                    ON sr.user_id = u.user_id

                WHERE

                    a.officer_id = ?
                    AND sr.status = 'Completed'

                ORDER BY sr.completed_at DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([$officerId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | Administrator - View Completed Maintenance Jobs
    |--------------------------------------------------------------------------
    */

    public function getCompletedJobs()
    {
        $sql = "SELECT

                    sr.ticket_number,

                    sr.title,

                    sr.location,

                    sr.building,

                    sr.completed_at,

                    a.remarks,

                    CONCAT(student.firstname,' ',student.lastname) AS student_name,

                    CONCAT(officer.firstname,' ',officer.lastname) AS officer_name,

                    c.category_name

                FROM assignments a

                INNER JOIN service_requests sr
                    ON a.request_id = sr.request_id

                INNER JOIN users student
                    ON sr.user_id = student.user_id

                INNER JOIN users officer
                    ON a.officer_id = officer.user_id

                INNER JOIN categories c
                    ON sr.category_id = c.category_id

                WHERE sr.status = 'Completed'

                ORDER BY sr.completed_at DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
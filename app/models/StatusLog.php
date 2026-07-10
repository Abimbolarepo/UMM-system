<?php

require_once "../app/core/Model.php";

class StatusLog extends Model
{
    protected $table = "status_logs";
    protected $primaryKey = "log_id";

    /**
     * Record a status change
     */
    public function create($requestId, $updatedBy, $oldStatus, $newStatus, $comment = null)
    {
        $sql = "INSERT INTO status_logs
                (
                    request_id,
                    updated_by,
                    old_status,
                    new_status,
                    comment
                )
                VALUES
                (
                    :request_id,
                    :updated_by,
                    :old_status,
                    :new_status,
                    :comment
                )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':request_id' => $requestId,
            ':updated_by' => $updatedBy,
            ':old_status' => $oldStatus,
            ':new_status' => $newStatus,
            ':comment' => $comment
        ]);
    }

    /**
     * View request history
     */
    public function getHistory($requestId)
    {
        $sql = "SELECT
                    sl.*,
                    u.firstname,
                    u.lastname
                FROM status_logs sl
                JOIN users u
                    ON sl.updated_by = u.user_id
                WHERE sl.request_id = :request_id
                ORDER BY sl.created_at DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':request_id' => $requestId
        ]);

        return $stmt->fetchAll();
    }
}
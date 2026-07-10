<?php

require_once "../app/core/Model.php";

class AuditLog extends Model
{
    protected $table = "audit_logs";
    protected $primaryKey = "audit_id";

    /**
     * Record an activity
     */
    public function log($userId, $activity)
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

        $stmt = $this->db->prepare("
            INSERT INTO audit_logs
            (
                user_id,
                activity,
                ip_address
            )
            VALUES
            (
                :user_id,
                :activity,
                :ip_address
            )
        ");

        return $stmt->execute([
            ':user_id' => $userId,
            ':activity' => $activity,
            ':ip_address' => $ipAddress
        ]);
    }

    /**
     * View audit logs
     */
    public function getAllLogs()
    {
        $stmt = $this->db->query("
            SELECT
                a.*,
                u.firstname,
                u.lastname
            FROM audit_logs a
            LEFT JOIN users u
                ON a.user_id = u.user_id
            ORDER BY a.created_at DESC
        ");

        return $stmt->fetchAll();
    }
}
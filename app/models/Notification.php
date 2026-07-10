<?php

require_once "../app/core/Model.php";

class Notification extends Model
{
    protected $table = "notifications";
    protected $primaryKey = "notification_id";

    /**
     * Create notification
     */
    public function create($userId, $title, $message)
    {
        $stmt = $this->db->prepare("
            INSERT INTO notifications
            (
                user_id,
                title,
                message
            )
            VALUES
            (
                :user_id,
                :title,
                :message
            )
        ");

        return $stmt->execute([
            ':user_id' => $userId,
            ':title' => $title,
            ':message' => $message
        ]);
    }

    /**
     * Get notifications for a user
     */
    public function getUserNotifications($userId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM notifications
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ");

        $stmt->execute([
            ':user_id' => $userId
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        $stmt = $this->db->prepare("
            UPDATE notifications
            SET is_read = TRUE
            WHERE notification_id = :notification_id
        ");

        return $stmt->execute([
            ':notification_id' => $notificationId
        ]);
    }
}
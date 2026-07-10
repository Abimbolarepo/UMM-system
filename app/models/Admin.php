<?php

require_once __DIR__ . "/../config/database.php";

class Admin
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Statistics
    |--------------------------------------------------------------------------
    */

    public function getDashboardStatistics()
    {
        return [

            'total_users' => $this->db->query(
                "SELECT COUNT(*) FROM users"
            )->fetchColumn(),

            'total_requests' => $this->db->query(
                "SELECT COUNT(*) FROM service_requests"
            )->fetchColumn(),

            'pending' => $this->db->query(
                "SELECT COUNT(*) FROM service_requests
                 WHERE status='Pending'"
            )->fetchColumn(),

            'assigned' => $this->db->query(
                "SELECT COUNT(*) FROM service_requests
                 WHERE status='Assigned'"
            )->fetchColumn(),

            'in_progress' => $this->db->query(
                "SELECT COUNT(*) FROM service_requests
                 WHERE status='In Progress'"
            )->fetchColumn(),

            'completed' => $this->db->query(
                "SELECT COUNT(*) FROM service_requests
                 WHERE status='Completed'"
            )->fetchColumn()

        ];
    }
}
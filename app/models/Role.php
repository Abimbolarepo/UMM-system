<?php

require_once "../app/core/Model.php";

class Role extends Model
{
    /**
     * Get all roles
     */
    public function all()
    {
        $stmt = $this->db->query("
            SELECT *
            FROM roles
            ORDER BY role_name
        ");

        return $stmt->fetchAll();
    }

    /**
     * Find role by ID
     */
    public function find($id)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM roles
            WHERE role_id=:id
        ");

        $stmt->execute([
            ':id'=>$id
        ]);

        return $stmt->fetch();
    }
}
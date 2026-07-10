<?php

require_once __DIR__ . '/../core/Database.php';

class User
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /*
    |--------------------------------------------------------------------------
    | Check if Email Exists
    |--------------------------------------------------------------------------
    */

    public function emailExists($email)
    {
        $sql = "SELECT user_id
                FROM users
                WHERE email = ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | Check if Email Exists For Another User
    |--------------------------------------------------------------------------
    */

    public function emailExistsForAnotherUser($email, $userId)
    {
        $sql = "SELECT user_id
                FROM users
                WHERE email = ?
                AND user_id != ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email, $userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | Register New User
    |--------------------------------------------------------------------------
    */

    public function create($data)
    {
        $sql = "INSERT INTO users
        (
            firstname,
            lastname,
            email,
            phone,
            department,
            password,
            role_id
        )

        VALUES
        (
            :firstname,
            :lastname,
            :email,
            :phone,
            :department,
            :password,
            :role_id
        )";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([

            ':firstname'  => $data['firstname'],
            ':lastname'   => $data['lastname'],
            ':email'      => $data['email'],
            ':phone'      => $data['phone'],
            ':department' => $data['department'],
            ':password'   => password_hash($data['password'], PASSWORD_DEFAULT),
            ':role_id'    => $data['role_id']

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Find User By Email
    |--------------------------------------------------------------------------
    */

    public function findByEmail($email)
    {
        $sql = "SELECT
                    users.*,
                    roles.role_name

                FROM users

                INNER JOIN roles
                ON users.role_id = roles.role_id

                WHERE email = ?

                LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | Find User By ID
    |--------------------------------------------------------------------------
    */

    public function findById($id)
    {
        $sql = "SELECT *
                FROM users
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Users
    |--------------------------------------------------------------------------
    */

    public function getAll()
    {
        $sql = "SELECT
                    users.*,
                    roles.role_name

                FROM users

                INNER JOIN roles
                ON users.role_id = roles.role_id

                ORDER BY firstname ASC";

        $stmt = $this->conn->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | Update User
    |--------------------------------------------------------------------------
    */

    public function update($id, $data)
    {
        $sql = "UPDATE users

                SET

                firstname = :firstname,
                lastname = :lastname,
                phone = :phone,
                department = :department,
                role_id = :role_id,
                status = :status

                WHERE user_id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([

            ':firstname'  => $data['firstname'],
            ':lastname'   => $data['lastname'],
            ':phone'      => $data['phone'],
            ':department' => $data['department'],
            ':role_id'    => $data['role_id'],
            ':status'     => $data['status'],
            ':id'         => $id

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete User
    |--------------------------------------------------------------------------
    */

    public function delete($id)
    {
        $sql = "DELETE FROM users
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([$id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Administrator - Get All Users
    |--------------------------------------------------------------------------
    */

    public function getAllUsers()
    {
        $sql = "SELECT

                    u.user_id,
                    u.firstname,
                    u.lastname,
                    u.email,
                    u.phone,
                    u.department,
                    r.role_name,
                    u.role_id,
                    u.status,
                    u.created_at

                FROM users u

                INNER JOIN roles r
                    ON u.role_id = r.role_id

                ORDER BY u.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | Get User By ID
    |--------------------------------------------------------------------------
    */

    public function getUserById($userId)
    {
        $sql = "SELECT

                    u.*,
                    r.role_name

                FROM users u

                INNER JOIN roles r
                    ON u.role_id = r.role_id

                WHERE u.user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | Activate / Deactivate User
    |--------------------------------------------------------------------------
    */

    public function updateStatus($userId, $status)
    {
        $stmt = $this->conn->prepare("
            UPDATE users
            SET status = ?
            WHERE user_id = ?
        ");

        return $stmt->execute([
            $status,
            $userId
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update User
    |--------------------------------------------------------------------------
    */

    public function updateUser(
        $userId,
        $firstname,
        $lastname,
        $email,
        $phone,
        $department,
        $roleId,
        $status
    )
    {
        $sql = "UPDATE users
                SET
                    firstname = ?,
                    lastname = ?,
                    email = ?,
                    phone = ?,
                    department = ?,
                    role_id = ?,
                    status = ?,
                    updated_at = NOW()
                WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            $firstname,
            $lastname,
            $email,
            $phone,
            $department,
            $roleId,
            $status,
            $userId
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create User
    |--------------------------------------------------------------------------
    */

    public function createUser(
        $firstname,
        $lastname,
        $email,
        $phone,
        $department,
        $roleId,
        $password
    )
    {
        $stmt = $this->conn->prepare("
            INSERT INTO users(

                firstname,
                lastname,
                email,
                phone,
                department,
                role_id,
                password

            )

            VALUES(?,?,?,?,?,?,?)
        ");

        return $stmt->execute([

            $firstname,
            $lastname,
            $email,
            $phone,
            $department,
            $roleId,
            password_hash($password, PASSWORD_DEFAULT)

        ]);
    }

}
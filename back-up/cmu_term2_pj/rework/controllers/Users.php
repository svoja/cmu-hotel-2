<?php
include_once '../config/db.php';

class Users {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Login User (Check by `email` or `name`)
    public function loginUser($usernameOrEmail, $password) {
        $sql = "SELECT * FROM users WHERE email = :usernameOrEmail OR name = :usernameOrEmail";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":usernameOrEmail", $usernameOrEmail, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }    

    // Register User (Prevents duplicate emails)
    public function createUser($name, $email, $password, $phone, $role = 'user') {
        // Check if email already exists
        if ($this->getUserByEmail($email)) {
            return "Email is already registered.";
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password, phone, role) 
                VALUES (:name, :email, :password, :phone, :role)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":password", $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(":phone", $phone, PDO::PARAM_STR);
        $stmt->bindParam(":role", $role, PDO::PARAM_STR);

        return $stmt->execute() ? true : false;
    }

    // Get All Users
    public function getUsers() {
        $sql = "SELECT id, name, email, phone, role FROM users"; // Avoid fetching passwords
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update User (Now includes Role)
    public function updateUser($id, $name, $email, $phone, $role) {
        $sql = "UPDATE users 
                SET name = :name, email = :email, phone = :phone, role = :role
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":phone", $phone, PDO::PARAM_STR);
        $stmt->bindParam(":role", $role, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Delete User
    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // Get User By Email (For Registration Check)
    public function getUserByEmail($email) {
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn(); // Instead of fetch(PDO::FETCH_ASSOC)
    }        
}
?>
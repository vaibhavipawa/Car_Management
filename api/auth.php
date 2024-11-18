<?php
require_once '../config/database.php';

class Auth {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function register($username, $email, $password) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashed_password);
            
            if($stmt->execute()) {
                return [
                    "status" => "success",
                    "message" => "User registered successfully",
                    "user_id" => $this->conn->lastInsertId()
                ];
            }
            
            return ["status" => "error", "message" => "Unable to register user"];
        } catch(PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    
    public function login($email, $password) {
        try {
            $query = "SELECT id, username, password FROM users WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if(password_verify($password, $row['password'])) {
                    session_start();
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    
                    return [
                        "status" => "success",
                        "message" => "Login successful",
                        "user" => [
                            "id" => $row['id'],
                            "username" => $row['username']
                        ]
                    ];
                }
            }
            
            return ["status" => "error", "message" => "Invalid credentials"];
        } catch(PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    
    public function logout() {
        session_start();
        session_destroy();
        return ["status" => "success", "message" => "Logged out successfully"];
    }
}
?>

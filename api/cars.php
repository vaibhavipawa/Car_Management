<?php
require_once '../config/database.php';

class Cars {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function create($user_id, $title, $description, $car_type, $company, $dealer, $images, $tags) {
        try {
            $this->conn->beginTransaction();
            
            // Insert car details
            $query = "INSERT INTO cars (user_id, title, description, car_type, company, dealer) 
                     VALUES (:user_id, :title, :description, :car_type, :company, :dealer)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":car_type", $car_type);
            $stmt->bindParam(":company", $company);
            $stmt->bindParam(":dealer", $dealer);
            
            $stmt->execute();
            $car_id = $this->conn->lastInsertId();
            
            // Insert images
            if (!empty($images)) {
                $query = "INSERT INTO car_images (car_id, image_path) VALUES (:car_id, :image_path)";
                $stmt = $this->conn->prepare($query);
                
                foreach($images as $image_path) {
                    $stmt->bindParam(":car_id", $car_id);
                    $stmt->bindParam(":image_path", $image_path);
                    $stmt->execute();
                }
            }
            
            // Insert tags
            if (!empty($tags)) {
                $query = "INSERT INTO car_tags (car_id, tag_name) VALUES (:car_id, :tag_name)";
                $stmt = $this->conn->prepare($query);
                
                foreach($tags as $tag) {
                    $stmt->bindParam(":car_id", $car_id);
                    $stmt->bindParam(":tag_name", $tag);
                    $stmt->execute();
                }
            }
            
            $this->conn->commit();
            return ["status" => "success", "message" => "Car created successfully", "car_id" => $car_id];
            
        } catch(PDOException $e) {
            $this->conn->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    
    public function list($user_id, $search = "") {
        try {
            $query = "SELECT c.*, GROUP_CONCAT(DISTINCT ci.image_path) as images, 
                     GROUP_CONCAT(DISTINCT ct.tag_name) as tags
                     FROM cars c
                     LEFT JOIN car_images ci ON c.id = ci.car_id
                     LEFT JOIN car_tags ct ON c.id = ct.car_id
                     WHERE c.user_id = :user_id";
            
            if(!empty($search)) {
                $query .= " AND (c.title LIKE :search OR c.description LIKE :search 
                           OR c.car_type LIKE :search OR c.company LIKE :search 
                           OR c.dealer LIKE :search OR ct.tag_name LIKE :search)";
            }
            
            $query .= " GROUP BY c.id ORDER BY c.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            
            if(!empty($search)) {
                $search = "%{$search}%";
                $stmt->bindParam(":search", $search);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    
    public function get($car_id, $user_id) {
        try {
            $query = "SELECT c.*, GROUP_CONCAT(DISTINCT ci.image_path) as images, 
                     GROUP_CONCAT(DISTINCT ct.tag_name) as tags
                     FROM cars c
                     LEFT JOIN car_images ci ON c.id = ci.car_id
                     LEFT JOIN car_tags ct ON c.id = ct.car_id
                     WHERE c.id = :car_id AND c.user_id = :user_id
                     GROUP BY c.id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":car_id", $car_id);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    
    public function update($car_id, $user_id, $title, $description, $car_type, $company, $dealer, $images, $tags) {
        try {
            $this->conn->beginTransaction();
            
            // Update car details
            $query = "UPDATE cars SET title = :title, description = :description, 
                     car_type = :car_type, company = :company, dealer = :dealer 
                     WHERE id = :car_id AND user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":car_type", $car_type);
            $stmt->bindParam(":company", $company);
            $stmt->bindParam(":dealer", $dealer);
            $stmt->bindParam(":car_id", $car_id);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();
            
            // Update images if provided
            if (!empty($images)) {
                // Delete existing images
                $query = "DELETE FROM car_images WHERE car_id = :car_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":car_id", $car_id);
                $stmt->execute();
                
                // Insert new images
                $query = "INSERT INTO car_images (car_id, image_path) VALUES (:car_id, :image_path)";
                $stmt = $this->conn->prepare($query);
                
                foreach($images as $image_path) {
                    $stmt->bindParam(":car_id", $car_id);
                    $stmt->bindParam(":image_path", $image_path);
                    $stmt->execute();
                }
            }
            
            // Update tags if provided
            if (!empty($tags)) {
                // Delete existing tags
                $query = "DELETE FROM car_tags WHERE car_id = :car_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":car_id", $car_id);
                $stmt->execute();
                
                // Insert new tags
                $query = "INSERT INTO car_tags (car_id, tag_name) VALUES (:car_id, :tag_name)";
                $stmt = $this->conn->prepare($query);
                
                foreach($tags as $tag) {
                    $stmt->bindParam(":car_id", $car_id);
                    $stmt->bindParam(":tag_name", $tag);
                    $stmt->execute();
                }
            }
            
            $this->conn->commit();
            return ["status" => "success", "message" => "Car updated successfully"];
            
        } catch(PDOException $e) {
            $this->conn->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    
    public function delete($car_id, $user_id) {
        try {
            $query = "DELETE FROM cars WHERE id = :car_id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":car_id", $car_id);
            $stmt->bindParam(":user_id", $user_id);
            
            if($stmt->execute()) {
                return ["status" => "success", "message" => "Car deleted successfully"];
            }
            
            return ["status" => "error", "message" => "Unable to delete car"];
            
        } catch(PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
?>

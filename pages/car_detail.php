<?php
require_once '../includes/auth_middleware.php';
require_once '../api/cars.php';

requireAuth();

if (!isset($_GET['id'])) {
    header('Location: car_list.php');
    exit();
}

$cars = new Cars();
$car = $cars->get($_GET['id'], getCurrentUserId());

if (!$car) {
    header('Location: car_list.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($car['title']); ?> - Car Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1>Car Details</h1>
            <div>
                <a href="car_list.php" class="btn">Back to List</a>
                <a href="car_create.php?id=<?php echo $car['id']; ?>" class="btn btn-primary">Edit</a>
                <button class="btn btn-danger delete-car" data-id="<?php echo $car['id']; ?>">Delete</button>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="car-detail">
            <div class="car-images">
                <?php
                $images = explode(',', $car['images']);
                foreach ($images as $image):
                ?>
                    <img src="../assets/uploads/<?php echo htmlspecialchars($image); ?>" 
                         alt="<?php echo htmlspecialchars($car['title']); ?>">
                <?php endforeach; ?>
            </div>
            
            <div class="car-info">
                <h2><?php echo htmlspecialchars($car['title']); ?></h2>
                
                <div class="info-group">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($car['description'])); ?></p>
                </div>
                
                <div class="info-group">
                    <h3>Details</h3>
                    <ul>
                        <li><strong>Car Type:</strong> <?php echo htmlspecialchars($car['car_type']); ?></li>
                        <li><strong>Company:</strong> <?php echo htmlspecialchars($car['company']); ?></li>
                        <li><strong>Dealer:</strong> <?php echo htmlspecialchars($car['dealer']); ?></li>
                    </ul>
                </div>
                
                <div class="info-group">
                    <h3>Tags</h3>
                    <div class="car-tags">
                        <?php
                        $tags = explode(',', $car['tags']);
                        foreach ($tags as $tag):
                        ?>
                            <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="info-group">
                    <p class="text-muted">Added on: <?php echo date('F j, Y', strtotime($car['created_at'])); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>

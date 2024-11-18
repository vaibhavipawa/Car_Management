<?php
require_once '../includes/auth_middleware.php';
require_once '../api/cars.php';

requireAuth();

$cars = new Cars();
$search = $_GET['search'] ?? '';
$car_list = $cars->list(getCurrentUserId(), $search);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cars - Car Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1>My Cars</h1>
            <div>
                <a href="car_create.php" class="btn btn-primary">Add New Car</a>
                <a href="../api/auth.php?action=logout" class="btn">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="search-container">
            <form id="search-form">
                <input type="text" id="search" name="search" 
                       placeholder="Search cars..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn">Search</button>
            </form>
        </div>

        <div class="car-grid">
            <?php if (empty($car_list)): ?>
                <p>No cars found.</p>
            <?php else: ?>
                <?php foreach ($car_list as $car): ?>
                    <div class="car-card">
                        <?php
                        $images = explode(',', $car['images']);
                        $first_image = $images[0] ?? 'default-car.jpg';
                        ?>
                        <img src="../assets/uploads/<?php echo htmlspecialchars($first_image); ?>" 
                             alt="<?php echo htmlspecialchars($car['title']); ?>">
                        
                        <div class="car-info">
                            <h3><?php echo htmlspecialchars($car['title']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($car['description'], 0, 100)) . '...'; ?></p>
                            
                            <div class="car-tags">
                                <?php
                                $tags = explode(',', $car['tags']);
                                foreach ($tags as $tag):
                                ?>
                                    <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="car-actions">
                                <a href="car_detail.php?id=<?php echo $car['id']; ?>" 
                                   class="btn btn-secondary">View Details</a>
                                <a href="car_create.php?id=<?php echo $car['id']; ?>" 
                                   class="btn btn-primary">Edit</a>
                                <button class="btn btn-danger delete-car" 
                                        data-id="<?php echo $car['id']; ?>">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>

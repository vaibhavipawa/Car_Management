<?php
require_once '../includes/auth_middleware.php';
require_once '../api/cars.php';

requireAuth();

$cars = new Cars();
$car = null;
$error = '';

if (isset($_GET['id'])) {
    $car = $cars->get($_GET['id'], getCurrentUserId());
    if (!$car) {
        header('Location: car_list.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $images = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $filename = uniqid() . '_' . $_FILES['images']['name'][$key];
            $upload_path = '../assets/uploads/' . $filename;
            
            if (move_uploaded_file($tmp_name, $upload_path)) {
                $images[] = $filename;
            }
        }
    }
    
    $tags = json_decode($_POST['tags']) ?? [];
    
    if (isset($_GET['id'])) {
        $result = $cars->update(
            $_GET['id'],
            getCurrentUserId(),
            $_POST['title'],
            $_POST['description'],
            $_POST['car_type'],
            $_POST['company'],
            $_POST['dealer'],
            $images,
            $tags
        );
    } else {
        $result = $cars->create(
            getCurrentUserId(),
            $_POST['title'],
            $_POST['description'],
            $_POST['car_type'],
            $_POST['company'],
            $_POST['dealer'],
            $images,
            $tags
        );
    }
    
    if ($result['status'] === 'success') {
        header('Location: car_list.php');
        exit();
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $car ? 'Edit Car' : 'Add New Car'; ?> - Car Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1><?php echo $car ? 'Edit Car' : 'Add New Car'; ?></h1>
            <a href="car_list.php" class="btn">Back to List</a>
        </div>
    </nav>

    <div class="container">
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="car-form">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required 
                       value="<?php echo $car ? htmlspecialchars($car['title']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required rows="4"><?php 
                    echo $car ? htmlspecialchars($car['description']) : ''; 
                ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="car_type">Car Type</label>
                <input type="text" id="car_type" name="car_type" required 
                       value="<?php echo $car ? htmlspecialchars($car['car_type']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="company">Company</label>
                <input type="text" id="company" name="company" required 
                       value="<?php echo $car ? htmlspecialchars($car['company']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="dealer">Dealer</label>
                <input type="text" id="dealer" name="dealer" required 
                       value="<?php echo $car ? htmlspecialchars($car['dealer']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="car-images">Images (Max 10)</label>
                <input type="file" id="car-images" name="images[]" multiple accept="image/*"
                       <?php echo $car ? '' : 'required'; ?>>
                <div id="image-preview" class="image-preview">
                    <?php
                    if ($car && !empty($car['images'])) {
                        $images = explode(',', $car['images']);
                        foreach ($images as $image):
                    ?>
                        <img src="../assets/uploads/<?php echo htmlspecialchars($image); ?>" 
                             class="preview-image" alt="Car image">
                    <?php
                        endforeach;
                    }
                    ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="tag-input">Tags</label>
                <input type="text" id="tag-input" placeholder="Type tag and press Enter">
                <div id="tag-container" class="tag-container">
                    <?php
                    if ($car && !empty($car['tags'])) {
                        $tags = explode(',', $car['tags']);
                        foreach ($tags as $tag):
                    ?>
                        <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                    <?php
                        endforeach;
                    }
                    ?>
                </div>
                <input type="hidden" id="tags" name="tags" value="<?php 
                    echo $car ? htmlspecialchars($car['tags']) : '[]'; 
                ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">
                <?php echo $car ? 'Update Car' : 'Add Car'; ?>
            </button>
        </form>
    </div>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>
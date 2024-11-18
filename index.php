<?php
require_once 'includes/auth_middleware.php';

if (isAuthenticated()) {
    header('Location: pages/car_list.php');
} else {
    header('Location: pages/login.php');
}
exit();
?>

<?php
include 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $review_id = (int)$_POST['review_id'];
    
    if ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->execute([$review_id]);
            
            $_SESSION['message'] = 'Review deleted successfully';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Error deleting review: ' . $e->getMessage();
        }
        
        header('Location: admin.php?tab=reviews');
        exit;
    }
}

header('Location: admin.php?tab=reviews');
exit;
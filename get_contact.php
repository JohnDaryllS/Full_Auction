<?php
include 'db_connect.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

if (isset($_GET['id'])) {
    $contact_id = (int)$_GET['id'];
    
    $stmt = $pdo->prepare("SELECT c.*, u.fullname as user_name 
                          FROM admin_contacts c
                          LEFT JOIN users u ON c.user_id = u.id
                          WHERE c.id = ?");
    $stmt->execute([$contact_id]);
    $contact = $stmt->fetch();
    
    if ($contact) {
        header('Content-Type: application/json');
        echo json_encode($contact);
        exit;
    }
}

header('HTTP/1.1 404 Not Found');
exit;
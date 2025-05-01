<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle user submission
    if (isset($_POST['name']) && isset($_POST['email'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $subject = trim($_POST['subject']);
        $message = trim($_POST['message']);
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $created_at = date('Y-m-d H:i:s');

        // Validate inputs
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            $_SESSION['error'] = 'Please fill all fields';
            header('Location: index.php#contact-admin');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Invalid email format';
            header('Location: index.php#contact-admin');
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO admin_contacts 
                                 (user_id, name, email, subject, message, created_at, status) 
                                 VALUES (?, ?, ?, ?, ?, ?, 'unread')");
            $stmt->execute([$user_id, $name, $email, $subject, $message, $created_at]);

            $_SESSION['message'] = 'Your message has been sent to administrators!';
            header('Location: index.php#contact-admin');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Error sending your message. Please try again.';
            header('Location: index.php#contact-admin');
            exit;
        }
    }
    
    // Handle admin actions
    if (isset($_SESSION['admin_logged_in']) && isset($_POST['action'])) {
        $action = $_POST['action'];
        $contact_id = (int)$_POST['contact_id'];
        
        try {
            switch ($action) {
                case 'mark_read':
                    $stmt = $pdo->prepare("UPDATE admin_contacts SET status = 'read' WHERE id = ?");
                    $stmt->execute([$contact_id]);
                    $_SESSION['message'] = 'Message marked as read';
                    break;
                    
                case 'delete':
                    $stmt = $pdo->prepare("DELETE FROM admin_contacts WHERE id = ?");
                    $stmt->execute([$contact_id]);
                    $_SESSION['message'] = 'Message deleted';
                    break;
            }
            
            header('Location: admin.php?tab=contacts');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Database error: ' . $e->getMessage();
            header('Location: admin.php?tab=contacts');
            exit;
        }
    }
}

header('Location: index.php');
exit;
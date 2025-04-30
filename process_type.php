<?php
include 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    
    try {
        if ($action === 'add') {
            // Validate inputs
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            
            if (empty($name)) {
                $_SESSION['error'] = 'Type name is required';
                header('Location: admin.php?tab=types');
                exit;
            }

            // Handle file upload
            $imageName = 'default-type.jpg'; // Default image
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $targetDir = "images/type-images/";
                
                // Create directory if it doesn't exist
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                
                // Validate image
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if ($check === false) {
                    throw new Exception('File is not an image');
                }
                
                // Check file size (max 2MB)
                if ($_FILES["image"]["size"] > 2000000) {
                    throw new Exception('File is too large (max 2MB)');
                }
                
                // Allow certain file formats
                $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
                if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                    throw new Exception('Only JPG, JPEG, PNG & GIF files are allowed');
                }
                
                // Generate unique filename
                $imageName = uniqid() . '.' . $imageFileType;
                $targetPath = $targetDir . $imageName;
                
                if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
                    throw new Exception('Error uploading image');
                }
            }
            
            // Insert into database
            $stmt = $pdo->prepare("INSERT INTO auction_types (name, description, image) VALUES (?, ?, ?)");
            $stmt->execute([$name, $description, $imageName]);
            
            $_SESSION['message'] = 'Auction type added successfully';
        }
        elseif ($action === 'edit') {
            $type_id = (int)$_POST['type_id'];
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            
            if (empty($name)) {
                $_SESSION['error'] = 'Type name is required';
                header('Location: admin.php?tab=types');
                exit;
            }
            
            // Get current image
            $stmt = $pdo->prepare("SELECT image FROM auction_types WHERE id = ?");
            $stmt->execute([$type_id]);
            $currentType = $stmt->fetch();
            $currentImage = $currentType['image'];
            
            $imageName = $currentImage; // Default to current image
            
            // Handle file upload if new image was provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $targetDir = "images/type-images/";
                
                // Validate image
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if ($check === false) {
                    throw new Exception('File is not an image');
                }
                
                // Check file size (max 2MB)
                if ($_FILES["image"]["size"] > 2000000) {
                    throw new Exception('File is too large (max 2MB)');
                }
                
                // Allow certain file formats
                $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
                if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                    throw new Exception('Only JPG, JPEG, PNG & GIF files are allowed');
                }
                
                // Generate unique filename
                $imageName = uniqid() . '.' . $imageFileType;
                $targetPath = $targetDir . $imageName;
                
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
                    // Delete old image if it's not the default
                    if ($currentImage !== 'default-type.jpg' && file_exists($targetDir . $currentImage)) {
                        unlink($targetDir . $currentImage);
                    }
                } else {
                    throw new Exception('Error uploading image');
                }
            }
            
            // Update database
            $stmt = $pdo->prepare("UPDATE auction_types SET name = ?, description = ?, image = ? WHERE id = ?");
            $stmt->execute([$name, $description, $imageName, $type_id]);
            
            $_SESSION['message'] = 'Auction type updated successfully';
        }
        elseif ($action === 'delete') {
            $type_id = (int)$_POST['type_id'];
            
            // Get the image to delete it
            $stmt = $pdo->prepare("SELECT image FROM auction_types WHERE id = ?");
            $stmt->execute([$type_id]);
            $type = $stmt->fetch();
            
            // First update all items of this type to NULL
            $stmt = $pdo->prepare("UPDATE items SET type_id = NULL WHERE type_id = ?");
            $stmt->execute([$type_id]);
            
            // Then delete the type
            $stmt = $pdo->prepare("DELETE FROM auction_types WHERE id = ?");
            $stmt->execute([$type_id]);
            
            // Delete the image if it's not the default
            if ($type['image'] !== 'default-type.jpg') {
                $imagePath = "images/type-images/" . $type['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $_SESSION['message'] = 'Auction type deleted successfully';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    header('Location: admin.php?tab=types');
    exit;
}

// Handle GET requests for editing
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_type'])) {
    $type_id = (int)$_GET['edit_type'];
    $stmt = $pdo->prepare("SELECT * FROM auction_types WHERE id = ?");
    $stmt->execute([$type_id]);
    $type = $stmt->fetch();
    
    if ($type) {
        header('Content-Type: application/json');
        echo json_encode([
            'id' => $type['id'],
            'name' => $type['name'],
            'description' => $type['description'],
            'image' => $type['image']
        ]);
        exit;
    }
}

header('Location: admin.php?tab=types');
exit;
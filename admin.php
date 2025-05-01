<?php 
include 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Display messages
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Get current datetime for the form
$currentDateTime = date('Y-m-d\TH:i');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TagHammer Auctions</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="images/crop.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Additional Admin Styles */
        .admin-container {
            padding: 2rem 0;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .admin-table th, .admin-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }
        
        .admin-table th {
            background-color: #f5f5f5;
            font-weight: 600;
            color: #333;
        }
        
        .admin-table tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        /* Status Badges */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            display: inline-block;
        }
        
        .status-upcoming {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-ended {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .time-remaining {
            font-size: 0.75rem;
            color: #666;
            margin-top: 4px;
        }
        
        /* Badge Styles */
        .limited-badge {
            background-color: #fff3e0;
            color: #e65100;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            display: inline-block;
        }
        
        .unlimited-badge {
            background-color: #e3f2fd;
            color: #1565c0;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            display: inline-block;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: nowrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }
        
        .btn-outline {
            border: 1px solid #ddd;
            color: #333;
            background-color: white;
        }
        
        .btn-outline:hover {
            background-color: #f5f5f5;
            border-color: #ccc;
        }
        
        .btn-error {
            background-color: #f44336;
            color: white;
            border: 1px solid #d32f2f;
        }
        
        .btn-error:hover {
            background-color: #d32f2f;
        }
        
        .btn-small {
            padding: 5px 10px;
            font-size: 0.75rem;
        }
        
        /* Item Thumbnail */
        .item-thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #eee;
        }
        
        .auction-ended {
            opacity: 0.8;
            background-color: #f8f9fa;
        }
        
        .auction-ended td {
            color: #6c757d;
        }
        
        /* Form Styles */
        .form-card {
            background-color: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        /* Search Form Styles */
        .search-form {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .search-form .form-group {
            display: flex;
            gap: 0.5rem;
            width: 100%;
        }

        .search-form select {
            flex: 1;
            min-width: 200px;
        }

        .admin-actions {
            display: flex;
            gap: 0.5rem;
        }

        /* New styles for type cards */
        .types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .type-card {
            background-color: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .type-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .type-card h3 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .type-card p {
            color: #666;
            font-size: 0.9rem;
        }

        /* New styles for type management */
        .type-image-preview {
            max-width: 200px;
            max-height: 150px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: none;
        }
        
        .selected-type-image {
            max-width: 100px;
            max-height: 75px;
            margin-left: 10px;
            vertical-align: middle;
            border-radius: 4px;
        }
        
        .type-with-image {
            display: flex;
            align-items: center;
        }
        
        /* Style for the type dropdown options */
        .type-option {
            display: flex;
            align-items: center;
            padding: 5px;
        }
        
        .type-option img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
            border-radius: 3px;
            object-fit: cover;
        }

        .winner-actions {
            display: flex;
            gap: 5px;
        }

        .winner-actions .btn {
            padding: 5px 8px;
            font-size: 0.8rem;
        }

        .unread-message {
            background-color: #f0f7ff;
        }

        .message-info {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .message-content {
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 8px;
            width: 80%;
            max-width: 700px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .modal .close {
            float: right;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
        }

        /* Analytics Tab Styles */
        .analytics-filters {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .analytics-filters .form-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .analytics-filters label {
            font-weight: 500;
        }
        
        .analytics-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .summary-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .summary-card h3 {
            color: #666;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .summary-value {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .analytics-table {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .no-data {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .admin-table {
                display: block;
                overflow-x: auto;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }
            
            .btn {
                width: 100%;
                padding: 8px;
            }

            .analytics-filters .form-group {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .summary-card {
                padding: 1rem;
            }
            
            .summary-value {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar admin-nav">
        <div class="navbar-left">
            <div class="logo">
                <img src="images/faviconsss.png" alt="Coffee-Auction" style="width:50px;">
                <span class="logo-text"> TagHammer Auctions</span>
            </div>
        </div>
        <div class="navbar-center">
            <a href="admin.php?tab=users" class="nav-link <?= ($_GET['tab'] ?? 'users') === 'users' ? 'active' : '' ?>">Admin Dashboard</a>
        </div>
        <div class="navbar-right">
            <a href="logout.php" class="btn btn-outline">Logout</a>
        </div>
    </nav>

    <main class="container admin-container">
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <h1>Admin Dashboard</h1>
        
        <div class="admin-tabs">
            <button class="tab-btn <?= ($_GET['tab'] ?? 'users') === 'users' ? 'active' : '' ?>" data-tab="users">User Management</button>
            <button class="tab-btn <?= ($_GET['tab'] ?? '') === 'items' ? 'active' : '' ?>" data-tab="items">Auction Items</button>
            <button class="tab-btn <?= ($_GET['tab'] ?? '') === 'types' ? 'active' : '' ?>" data-tab="types">Auction Types</button>
            <button class="tab-btn <?= ($_GET['tab'] ?? '') === 'reviews' ? 'active' : '' ?>" data-tab="reviews">Reviews Management</button>
            <button class="tab-btn <?= ($_GET['tab'] ?? '') === 'winners' ? 'active' : '' ?>" data-tab="winners">Auction Winners</button>
            <button class="tab-btn <?= ($_GET['tab'] ?? '') === 'contacts' ? 'active' : '' ?>" data-tab="contacts">User Messages</button>
            <button class="tab-btn <?= ($_GET['tab'] ?? '') === 'analytics' ? 'active' : '' ?>" data-tab="analytics">Analytics</button>
        </div>
        
        <section class="tab-content <?= ($_GET['tab'] ?? 'users') === 'users' ? 'active' : '' ?>" id="users-tab">
            <!-- Existing user management content -->
            <h2>User Management</h2>
            
            <div class="admin-actions">
                <button id="search-users-btn" class="btn btn-outline">
                    <i class="fas fa-search"></i> Search Users
                </button>
                <div id="search-users-form" style="display: <?= isset($_GET['search']) ? 'block' : 'none' ?>; margin-top: 1rem;">
                    <form method="get" action="admin.php" class="search-form">
                        <input type="hidden" name="tab" value="users">
                        <div class="form-group">
                            <input type="text" name="search" placeholder="Search by name, email or phone" 
                                   class="form-control" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <?php if (!empty($_GET['search'])): ?>
                                <a href="admin.php?tab=users" class="btn btn-outline">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php
                // Get search parameters
                $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                $status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

                // Base query
                $query = "SELECT * FROM users WHERE role = 'user'";
                $params = [];

                // Add conditions
                if (!empty($search)) {
                    $query .= " AND (fullname LIKE ? OR email LIKE ? OR phone LIKE ?)";
                    array_push($params, "%$search%", "%$search%", "%$search%");
                }

                // Add status filter
                $valid_statuses = ['pending', 'approved', 'suspended'];
                if (!empty($status_filter) && in_array($status_filter, $valid_statuses)) {
                    $query .= " AND status = ?";
                    $params[] = $status_filter;
                }

                // Add sorting
                $query .= " ORDER BY status, created_at DESC";

                try {
                    $stmt = $pdo->prepare($query);
                    $stmt->execute($params);
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    die("Database error: " . $e->getMessage());
                }
            ?>
            
            <?php if (count($users) > 0): ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['fullname']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['phone']) ?></td>
                                    <td>
                                        <span class="status-badge <?= $user['status'] ?>">
                                            <?= ucfirst($user['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                    <td class="actions-cell">
                                        <div class="dropdown">
                                            <button class="btn btn-outline btn-small dropdown-toggle">Actions</button>
                                            <div class="dropdown-content">
                                                <?php if ($user['status'] == 'pending'): ?>
                                                    <form action="process_user.php" method="post">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" name="action" value="approve" class="dropdown-link">Approve User</button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <button class="dropdown-link" onclick="openPasswordModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['fullname']) ?>')">Reset Password</button>
                                                
                                                <?php if ($user['status'] == 'approved'): ?>
                                                    <form action="process_user.php" method="post">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" name="action" value="suspend" class="dropdown-link">Suspend User</button>
                                                    </form>
                                                <?php else: ?>
                                                    <form action="process_user.php" method="post">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" name="action" value="activate" class="dropdown-link">Activate User</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No users found.</p>
            <?php endif; ?>
        </section>
        
        <section class="tab-content <?= ($_GET['tab'] ?? '') === 'items' ? 'active' : '' ?>" id="items-tab">
            <div class="admin-section-header">
                <h2>Auctions Items</h2>
                <div class="admin-actions">
                    <button id="add-item-btn" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Item
                    </button>
                    <button id="search-items-btn" class="btn btn-outline">
                        <i class="fas fa-search"></i> Search Items
                    </button>
                </div>
            </div>
            
            <!-- Search form for items by type -->
            <div id="search-items-form" style="display: <?= isset($_GET['search_type']) ? 'block' : 'none' ?>; margin-top: 1rem;">
                <form method="get" action="admin.php" class="search-form">
                    <input type="hidden" name="tab" value="items">
                    <div class="form-group">
                        <select name="search_type" class="form-control">
                            <option value="">-- All Types --</option>
                            <?php
                            $types = $pdo->query("SELECT * FROM auction_types ORDER BY name ASC");
                            while ($type = $types->fetch()):
                                $selected = isset($_GET['search_type']) && $_GET['search_type'] == $type['id'] ? 'selected' : '';
                            ?>
                                <option value="<?= $type['id'] ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($type['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <?php if (!empty($_GET['search_type'])): ?>
                            <a href="admin.php?tab=items" class="btn btn-outline">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Add/Edit Form Container -->
            <div id="item-form-container" style="display: none;">
                <div class="form-card">
                    <h3 id="form-title">Add New Auction</h3>
                    <form id="item-form" action="process_item.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" id="form-action" value="add">
                        <input type="hidden" name="item_id" id="form-item-id" value="">
                        
                        <div class="form-group">
                            <label for="item-type">Auction Type</label>
                            <select id="item-type" name="type_id" class="form-control" required>
                                <option value="">-- Select Type --</option>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM auction_types ORDER BY name ASC");
                                while ($type = $stmt->fetch()):
                                ?>
                                    <option value="<?= $type['id'] ?>" data-image="<?= htmlspecialchars($type['image']) ?>">
                                        <?= htmlspecialchars($type['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <div id="selected-type-image-container" style="margin-top: 10px; display: none;">
                                <p>Type Image Preview:</p>
                                <img id="selected-type-image" src="" class="type-image-preview">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="item-name">Coffee Name</label>
                            <input type="text" id="item-name" name="name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="item-description">Description</label>
                            <textarea id="item-description" name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="item-price">Starting Price (₱)</label>
                            <input type="number" id="item-price" name="starting_price" class="form-control" min="0.01" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="bid-start-date">Bid Start Date/Time</label>
                            <input type="datetime-local" id="bid-start-date" name="bid_start_date" class="form-control" required>
                            <small>Set the date and time when bidding will open</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="bid-end-date">Bid End Date/Time</label>
                            <input type="datetime-local" id="bid-end-date" name="bid_end_date" class="form-control" required>
                            <small>Set the date and time when bidding will close</small>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="is-limited" name="is_limited" value="1">
                                This is a limited edition item (special badge, no quantity restrictions)
                            </label>
                        </div>
                        
                        <div class="form-group" id="image-upload-group">
                            <label for="item-image">Image</label>
                            <input type="file" id="item-image" name="image" class="form-control" accept="image/*">
                            <small>Recommended size: 600x400 pixels</small>
                            <div id="current-image-container" style="display: none; margin-top: 10px;">
                                <p>Current Image:</p>
                                <img id="current-image-preview" src="" style="max-width: 200px; max-height: 150px;">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" id="cancel-item-form" class="btn btn-outline">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Item</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Get type filter if set
                        $type_filter = isset($_GET['search_type']) ? (int)$_GET['search_type'] : 0;

                        // Base query
                        $query = "SELECT i.*, t.name as type_name, t.image as type_image FROM items i LEFT JOIN auction_types t ON i.type_id = t.id ";

                        // Add type filter if selected
                        if ($type_filter > 0) {
                            $query .= "WHERE i.type_id = $type_filter ";
                        }

                        $query .= "ORDER BY i.bid_start_date ASC";

                        $stmt = $pdo->query($query);
                        while ($item = $stmt->fetch()): 
                            $now = new DateTime();
                            $start_date = new DateTime($item['bid_start_date']);
                            $end_date = new DateTime($item['bid_end_date']);
                            
                            if ($end_date <= $now) {
                                $status = 'ended';
                                $status_class = 'status-ended';
                            } elseif ($start_date > $now) {
                                $status = 'upcoming';
                                $status_class = 'status-upcoming';
                            } else {
                                $status = 'active';
                                $status_class = 'status-active';
                            }
                        ?>
                            <tr class="<?= $status === 'ended' ? 'auction-ended' : '' ?>">
                                <td><?= $item['id'] ?></td>
                                <td>
                                    <?php if ($item['type_name']): ?>
                                        <div class="type-with-image">
                                            <?= htmlspecialchars($item['type_name']) ?>
                                            <?php if ($item['type_image']): ?>
                                                <img src="images/type-images/<?= htmlspecialchars($item['type_image']) ?>" class="selected-type-image">
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        --
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td>₱<?= number_format($item['starting_price'], 2) ?></td>
                                <td>
                                    <?php if ($item['is_limited']): ?>
                                        <span class="limited-badge">Limited Edition</span>
                                    <?php else: ?>
                                        <span class="unlimited-badge">Unlimited</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge <?= $status_class ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $start_date->format('M j, Y H:i') ?>
                                    <?php if ($status === 'upcoming'): ?>
                                        <div class="time-remaining">
                                            (<?= $now->diff($start_date)->format('%a days %h hours %i minutes until start') ?>)
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $end_date->format('M j, Y H:i') ?>
                                    <?php if ($status === 'active'): ?>
                                        <div class="time-remaining">
                                            (<?= $now->diff($end_date)->format('%a days %h hours %i minutes left') ?>)
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <img src="images/<?= htmlspecialchars($item['image']) ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>" 
                                         class="item-thumbnail">
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="product_view.php?id=<?= $item['id'] ?>&admin=1" 
                                           class="btn btn-outline btn-small" 
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                            <span class="action-text">View</span>
                                        </a>
                                        <button onclick="setupEditItem(<?= $item['id'] ?>)" 
                                                class="btn btn-outline btn-small" 
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                            <span class="action-text">Edit</span>
                                        </button>
                                        <form action="process_item.php" method="post" class="inline-form">
                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                            <button type="submit" 
                                                    name="action" 
                                                    value="delete" 
                                                    class="btn btn-error btn-small" 
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this item?')">
                                                <i class="fas fa-trash-alt"></i>
                                                <span class="action-text">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
        
        <section class="tab-content <?= ($_GET['tab'] ?? '') === 'types' ? 'active' : '' ?>" id="types-tab">
            <div class="admin-section-header">
                <h2>Auction Types</h2>
                <button id="add-type-btn" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Type
                </button>
            </div>
            
            <div id="type-form-container" style="display: none;">
                <div class="form-card">
                    <h3 id="type-form-title">Add New Auction Type</h3>
                    <form id="type-form" action="process_type.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" id="type-form-action" value="add">
                        <input type="hidden" name="type_id" id="form-type-id" value="">
                        
                        <div class="form-group">
                            <label for="type-name">Type Name</label>
                            <input type="text" id="type-name" name="name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="type-description">Description</label>
                            <textarea id="type-description" name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-group" id="type-image-upload-group">
                            <label for="type-image">Type Image</label>
                            <input type="file" id="type-image" name="image" class="form-control" accept="image/*">
                            <small>Recommended size: 400x300 pixels</small>
                            <div id="current-type-image-container" style="display: none; margin-top: 10px;">
                                <p>Current Image:</p>
                                <img id="current-type-image-preview" src="" class="type-image-preview">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" id="cancel-type-form" class="btn btn-outline">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Type</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM auction_types ORDER BY name ASC");
                        while ($type = $stmt->fetch()): 
                        ?>
                            <tr>
                                <td><?= $type['id'] ?></td>
                                <td><?= htmlspecialchars($type['name']) ?></td>
                                <td><?= htmlspecialchars($type['description']) ?></td>
                                <td>
                                    <?php if ($type['image']): ?>
                                        <img src="images/type-images/<?= htmlspecialchars($type['image']) ?>" class="item-thumbnail">
                                    <?php else: ?>
                                        No image
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M j, Y', strtotime($type['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button onclick="setupEditType(<?= $type['id'] ?>)" 
                                                class="btn btn-outline btn-small" 
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                            <span class="action-text">Edit</span>
                                        </button>
                                        <form action="process_type.php" method="post" class="inline-form">
                                            <input type="hidden" name="type_id" value="<?= $type['id'] ?>">
                                            <button type="submit" 
                                                    name="action" 
                                                    value="delete" 
                                                    class="btn btn-error btn-small" 
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this auction type? Items in this type will not be deleted.')">
                                                <i class="fas fa-trash-alt"></i>
                                                <span class="action-text">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-content <?= ($_GET['tab'] ?? '') === 'reviews' ? 'active' : '' ?>" id="reviews-tab">
            <h2>Reviews Management</h2>
            
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Category</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Anonymous</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $reviews = $pdo->query("
                            SELECT r.*, 
                                u.fullname as user_name,
                                t.name as category_name
                            FROM reviews r
                            JOIN users u ON r.user_id = u.id
                            JOIN auction_types t ON r.category_id = t.id
                            ORDER BY r.created_at DESC
                        ");
                        
                        while ($review = $reviews->fetch()): 
                        ?>
                            <tr>
                                <td><?= $review['id'] ?></td>
                                <td><?= htmlspecialchars($review['user_name']) ?></td>
                                <td><?= htmlspecialchars($review['category_name']) ?></td>
                                <td>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star<?= $i > $review['rating'] ? '-empty' : '' ?>"></i>
                                    <?php endfor; ?>
                                </td>
                                <td><?= htmlspecialchars(substr($review['description'], 0, 50)) ?><?= strlen($review['description']) > 50 ? '...' : '' ?></td>
                                <td><?= $review['is_anonymous'] ? 'Yes' : 'No' ?></td>
                                <td><?= date('M j, Y', strtotime($review['created_at'])) ?></td>
                                <td>
                                    <form action="process_review.php" method="post" class="inline-form">
                                        <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                        <button type="submit" name="action" value="delete" class="btn btn-error btn-small"
                                                onclick="return confirm('Are you sure you want to delete this review?')">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-content <?= ($_GET['tab'] ?? '') === 'winners' ? 'active' : '' ?>" id="winners-tab">
            <h2>Auction Winners</h2>
            
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Auction ID</th>
                            <th>Item Name</th>
                            <th>Winner</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Winning Bid</th>
                            <th>Bid Time</th>
                            <th>Auction Ended</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $winners = $pdo->query("
                            SELECT 
                                i.id as item_id,
                                i.name as item_name,
                                u.id as user_id,
                                u.fullname as winner_name,
                                u.email,
                                u.phone,
                                MAX(b.bid_amount) as winning_bid,
                                MAX(b.timestamp) as bid_time,
                                i.bid_end_date
                            FROM items i
                            JOIN bids b ON i.id = b.item_id
                            JOIN users u ON b.user_id = u.id
                            WHERE i.bid_end_date < NOW()
                            GROUP BY i.id
                            ORDER BY i.bid_end_date DESC
                        ");
                        
                        while ($winner = $winners->fetch()): 
                        ?>
                            <tr>
                                <td><?= $winner['item_id'] ?></td>
                                <td><?= htmlspecialchars($winner['item_name']) ?></td>
                                <td><?= htmlspecialchars($winner['winner_name']) ?></td>
                                <td><?= htmlspecialchars($winner['email']) ?></td>
                                <td><?= htmlspecialchars($winner['phone']) ?></td>
                                <td>₱<?= number_format($winner['winning_bid'], 2) ?></td>
                                <td><?= date('M j, Y H:i', strtotime($winner['bid_time'])) ?></td>
                                <td><?= date('M j, Y H:i', strtotime($winner['bid_end_date'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="product_view.php?id=<?= $winner['item_id'] ?>&admin=1" 
                                        class="btn btn-outline btn-small" 
                                        title="View Auction">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="mailto:<?= htmlspecialchars($winner['email']) ?>" 
                                        class="btn btn-outline btn-small" 
                                        title="Email Winner">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-content <?= ($_GET['tab'] ?? '') === 'contacts' ? 'active' : '' ?>" id="contacts-tab">
            <h2>User Messages</h2>
            
            <div class="admin-actions">
                <form method="get" action="admin.php" class="search-form">
                    <input type="hidden" name="tab" value="contacts">
                    <div class="form-group">
                        <select name="status" onchange="this.form.submit()">
                            <option value="">All Messages</option>
                            <option value="unread" <?= ($_GET['status'] ?? '') === 'unread' ? 'selected' : '' ?>>Unread</option>
                            <option value="read" <?= ($_GET['status'] ?? '') === 'read' ? 'selected' : '' ?>>Read</option>
                        </select>
                    </div>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $status_filter = $_GET['status'] ?? '';
                        $query = "SELECT c.*, u.fullname as user_name 
                                FROM admin_contacts c
                                LEFT JOIN users u ON c.user_id = u.id";
                        
                        if ($status_filter) {
                            $query .= " WHERE c.status = ?";
                        }
                        
                        $query .= " ORDER BY c.created_at DESC";
                        
                        $stmt = $pdo->prepare($query);
                        $stmt->execute($status_filter ? [$status_filter] : []);
                        
                        while ($contact = $stmt->fetch()): 
                        ?>
                            <tr class="<?= $contact['status'] === 'unread' ? 'unread-message' : '' ?>">
                                <td><?= $contact['id'] ?></td>
                                <td><?= $contact['user_name'] ? htmlspecialchars($contact['user_name']) : htmlspecialchars($contact['name']) ?></td>
                                <td><?= htmlspecialchars($contact['email']) ?></td>
                                <td><?= htmlspecialchars($contact['subject']) ?></td>
                                <td><?= htmlspecialchars(substr($contact['message'], 0, 50)) ?><?= strlen($contact['message']) > 50 ? '...' : '' ?></td>
                                <td><?= date('M j, Y H:i', strtotime($contact['created_at'])) ?></td>
                                <td>
                                    <span class="status-badge <?= $contact['status'] ?>">
                                        <?= ucfirst($contact['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button onclick="openMessageModal(<?= $contact['id'] ?>)" 
                                                class="btn btn-outline btn-small">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <form action="process_contact.php" method="post" class="inline-form">
                                            <input type="hidden" name="contact_id" value="<?= $contact['id'] ?>">
                                            <button type="submit" name="action" value="delete" 
                                                    class="btn btn-error btn-small"
                                                    onclick="return confirm('Delete this message?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-content <?= ($_GET['tab'] ?? '') === 'analytics' ? 'active' : '' ?>" id="analytics-tab">
            <h2>Auctions Analytics</h2>
            
            <div class="analytics-filters">
                <form method="get" action="admin.php">
                    <input type="hidden" name="tab" value="analytics">
                    <div class="form-group">
                        <label for="date-filter">Filter by Date:</label>
                        <input type="date" id="date-filter" name="date" value="<?= $_GET['date'] ?? date('Y-m-d') ?>">
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </div>
                </form>
            </div>
            
            <?php
            // Get selected date or use today
            $selected_date = $_GET['date'] ?? date('Y-m-d');
            
            // Query to get today's winning auctions
            $winning_auctions = $pdo->prepare("
                SELECT i.id, i.name, i.starting_price, MAX(b.bid_amount) as winning_bid, 
                    u.fullname as winner_name, u.email as winner_email
                FROM items i
                JOIN bids b ON i.id = b.item_id
                JOIN users u ON b.user_id = u.id
                WHERE DATE(i.bid_end_date) = ?
                GROUP BY i.id
                ORDER BY winning_bid DESC
            ");
            $winning_auctions->execute([$selected_date]);
            $auctions = $winning_auctions->fetchAll();
            
            // Calculate totals
            $total_auctions = count($auctions);
            $total_revenue = array_sum(array_column($auctions, 'winning_bid'));
            ?>
            
            <div class="analytics-summary">
                <div class="summary-card">
                    <h3>Total Winning Auctions</h3>
                    <div class="summary-value"><?= $total_auctions ?></div>
                </div>
                <div class="summary-card">
                    <h3>Total Revenue</h3>
                    <div class="summary-value">₱<?= number_format($total_revenue, 2) ?></div>
                </div>
                <div class="summary-card">
                    <h3>Average Winning Bid</h3>
                    <div class="summary-value">₱<?= $total_auctions > 0 ? number_format($total_revenue/$total_auctions, 2) : '0.00' ?></div>
                </div>
            </div>
            
            <div class="analytics-table">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Auction ID</th>
                            <th>Item Name</th>
                            <th>Starting Price</th>
                            <th>Winning Bid</th>
                            <th>Winner</th>
                            <th>Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($auctions as $auction): ?>
                            <tr>
                                <td><?= $auction['id'] ?></td>
                                <td><?= htmlspecialchars($auction['name']) ?></td>
                                <td>₱<?= number_format($auction['starting_price'], 2) ?></td>
                                <td>₱<?= number_format($auction['winning_bid'], 2) ?></td>
                                <td>
                                    <?= htmlspecialchars($auction['winner_name']) ?><br>
                                    <small><?= htmlspecialchars($auction['winner_email']) ?></small>
                                </td>
                                <td>₱<?= number_format($auction['winning_bid'] - $auction['starting_price'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($auctions)): ?>
                            <tr>
                                <td colspan="6" class="no-data">No winning auctions found for this date</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Add this modal at the bottom of admin.php -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3 id="messageSubject"></h3>
            <div class="message-info">
                <p><strong>From:</strong> <span id="messageSender"></span></p>
                <p><strong>Email:</strong> <span id="messageEmail"></span></p>
                <p><strong>Date:</strong> <span id="messageDate"></span></p>
            </div>
            <div class="message-content" id="messageContent"></div>
            <div class="form-actions">
                <form action="process_contact.php" method="post" class="inline-form">
                    <input type="hidden" name="contact_id" id="modalContactId">
                    <button type="submit" name="action" value="mark_read" class="btn btn-primary">
                        Mark as Read
                    </button>
                </form>
                <button class="btn btn-outline" onclick="document.getElementById('messageModal').style.display='none'">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Password Reset Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Reset Password for <span id="userName"></span></h3>
            <form action="process_user.php" method="post" id="passwordForm">
                <input type="hidden" name="user_id" id="modalUserId">
                <input type="hidden" name="action" value="reset_password">
                
                <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <input type="password" id="newPassword" name="new_password" class="form-control" required>
                    <small>Minimum 8 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirm_password" class="form-control" required>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('passwordModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/messageModal.js"></script>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'users';
    
    // Activate the requested tab
    document.querySelector(`.tab-btn[data-tab="${activeTab}"]`).click();
    
    // Show search form if coming from search
    if (activeTab === 'users' && urlParams.has('search')) {
        document.getElementById('search-users-form').style.display = 'block';
    }

    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active class from all buttons and tabs
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked button and corresponding tab
            btn.classList.add('active');
            const tabId = btn.getAttribute('data-tab') + '-tab';
            document.getElementById(tabId).classList.add('active');
            
            // Update URL without reload
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('tab', btn.getAttribute('data-tab'));
            window.history.pushState({}, '', newUrl);
        });
    });

    // User search toggle
    document.getElementById('search-users-btn').addEventListener('click', function() {
        const form = document.getElementById('search-users-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    });

    // Item search toggle
    document.getElementById('search-items-btn').addEventListener('click', function() {
        const form = document.getElementById('search-items-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    });

    // Add item button
    document.getElementById('add-item-btn').addEventListener('click', function() {
        const formContainer = document.getElementById('item-form-container');
        const formTitle = document.getElementById('form-title');
        const form = document.getElementById('item-form');
        
        formContainer.style.display = 'block';
        formTitle.textContent = 'Add New Coffee Auction';
        form.reset();
        document.getElementById('form-action').value = 'add';
        document.getElementById('form-item-id').value = '';
        document.getElementById('current-image-container').style.display = 'none';
        document.getElementById('selected-type-image-container').style.display = 'none';
        document.getElementById('image-upload-group').querySelector('label').textContent = 'Image';
        
        // Set minimum datetime (now + 1 hour)
        const now = new Date();
        now.setHours(now.getHours() + 1);
        const minDateTime = now.toISOString().slice(0, 16);
        document.getElementById('bid-start-date').min = minDateTime;
        document.getElementById('bid-end-date').min = minDateTime;
        
        formContainer.scrollIntoView({ behavior: 'smooth' });
    });

    // Cancel item form
    document.getElementById('cancel-item-form').addEventListener('click', function() {
        document.getElementById('item-form-container').style.display = 'none';
    });

    // Add type button
    document.getElementById('add-type-btn').addEventListener('click', function() {
        const formContainer = document.getElementById('type-form-container');
        const formTitle = document.getElementById('type-form-title');
        const form = document.getElementById('type-form');
        
        formContainer.style.display = 'block';
        formTitle.textContent = 'Add New Auction Type';
        form.reset();
        document.getElementById('type-form-action').value = 'add';
        document.getElementById('form-type-id').value = '';
        document.getElementById('current-type-image-container').style.display = 'none';
        
        formContainer.scrollIntoView({ behavior: 'smooth' });
    });

    // Cancel type form
    document.getElementById('cancel-type-form').addEventListener('click', function() {
        document.getElementById('type-form-container').style.display = 'none';
    });

    // Limited quantity toggle
    document.getElementById('is-limited').addEventListener('change', function() {
        // This is now just for the badge - no quantity field needed
        // We keep this handler in case we need it for other UI updates
        console.log('Limited item checked:', this.checked);
    });

    // Type image preview in dropdown
    document.getElementById('item-type').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const typeImage = selectedOption.getAttribute('data-image');
        const typeImagePreview = document.getElementById('selected-type-image');
        const typeImageContainer = document.getElementById('selected-type-image-container');
        
        if (typeImage) {
            typeImagePreview.src = 'images/type-images/' + typeImage;
            typeImageContainer.style.display = 'block';
        } else {
            typeImageContainer.style.display = 'none';
        }
    });

    // Image preview for item image upload
    document.getElementById('item-image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.getElementById('current-image-preview');
                preview.src = event.target.result;
                document.getElementById('current-image-container').style.display = 'block';
                document.getElementById('image-upload-group').querySelector('label').textContent = 'Change Image';
            };
            reader.readAsDataURL(file);
        }
    });

    // Image preview for type image upload
    document.getElementById('type-image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.getElementById('current-type-image-preview');
                preview.src = event.target.result;
                document.getElementById('current-type-image-container').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Password modal
    window.openPasswordModal = function(userId, userName) {
        document.getElementById('modalUserId').value = userId;
        document.getElementById('userName').textContent = userName;
        document.getElementById('passwordModal').style.display = 'block';
    };

    // Close modal when clicking X
    document.querySelector('.close').addEventListener('click', function() {
        document.getElementById('passwordModal').style.display = 'none';
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === document.getElementById('passwordModal')) {
            document.getElementById('passwordModal').style.display = 'none';
        }
    });
});

// Edit item function
function setupEditItem(itemId) {
    fetch(`process_item.php?edit_item=${itemId}`)
        .then(response => response.json())
        .then(item => {
            // Show the form
            document.getElementById('item-form-container').style.display = 'block';
            
            // Update form title and action
            document.getElementById('form-title').textContent = 'Edit Coffee Auction';
            document.getElementById('form-action').value = 'edit';
            document.getElementById('form-item-id').value = item.id;
            
            // Fill form fields
            document.getElementById('item-name').value = item.name;
            document.getElementById('item-description').value = item.description;
            document.getElementById('item-price').value = item.starting_price;
            document.getElementById('item-type').value = item.type_id || '';
            
            // Show type image preview if available
            if (item.type_image) {
                const typeImagePreview = document.getElementById('selected-type-image');
                typeImagePreview.src = 'images/type-images/' + item.type_image;
                document.getElementById('selected-type-image-container').style.display = 'block';
            }
            
            // Format dates for datetime-local inputs
            const startDate = new Date(item.bid_start_date);
            const formattedStartDate = startDate.toISOString().slice(0, 16);
            document.getElementById('bid-start-date').value = formattedStartDate;
            
            const endDate = new Date(item.bid_end_date);
            const formattedEndDate = endDate.toISOString().slice(0, 16);
            document.getElementById('bid-end-date').value = formattedEndDate;
            
            // Handle limited checkbox (no quantity needed)
            document.getElementById('is-limited').checked = item.is_limited == 1;
            
            // Handle image (show current image preview)
            const currentImagePreview = document.getElementById('current-image-preview');
            currentImagePreview.src = `images/${item.image}`;
            document.getElementById('current-image-container').style.display = 'block';
            document.getElementById('image-upload-group').querySelector('label').textContent = 'Change Image';
            
            // Scroll to form
            document.getElementById('item-form-container').scrollIntoView({ behavior: 'smooth' });
        })
        .catch(error => {
            console.error('Error fetching item data:', error);
            alert('Error loading item data. Please try again.');
        });
}

// Edit type function
function setupEditType(typeId) {
    fetch(`process_type.php?edit_type=${typeId}`)
        .then(response => response.json())
        .then(type => {
            // Show the form
            document.getElementById('type-form-container').style.display = 'block';
            
            // Update form title and action
            document.getElementById('type-form-title').textContent = 'Edit Auction Type';
            document.getElementById('type-form-action').value = 'edit';
            document.getElementById('form-type-id').value = type.id;
            
            // Fill form fields
            document.getElementById('type-name').value = type.name;
            document.getElementById('type-description').value = type.description;
            
            // Handle image (show current image preview)
            const currentImageContainer = document.getElementById('current-type-image-container');
            const currentImagePreview = document.getElementById('current-type-image-preview');
            
            if (type.image) {
                currentImagePreview.src = `images/type-images/${type.image}`;
                currentImageContainer.style.display = 'block';
            } else {
                currentImageContainer.style.display = 'none';
            }
            
            // Scroll to form
            document.getElementById('type-form-container').scrollIntoView({ behavior: 'smooth' });
        })
        .catch(error => {
            console.error('Error fetching type data:', error);
            alert('Error loading type data. Please try again.');
        });
}
</script>
</body>
</html>
<?php 
include 'db_connect.php';

// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);
$is_home = ($current_page == 'index.php' || $current_page == 'user_account.php');

// Pagination settings
$reviews_per_page = 8;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $reviews_per_page;

// Get selected category filter
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Process form submission (only for logged-in users)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $category_id = (int)$_POST['category_id'];
    $rating = (int)$_POST['rating'];
    $description = trim($_POST['description']);
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $review_id = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;
    
    // Validate inputs
    if ($category_id <= 0 || $rating < 1 || $rating > 5 || empty($description)) {
        $_SESSION['error'] = 'Please fill all fields correctly';
        header('Location: reviews.php');
        exit;
    }
    
    try {
        // Check if this is an edit or new review
        if ($review_id > 0) {
            // Update existing review
            $stmt = $pdo->prepare("UPDATE reviews SET 
                                 rating = ?, 
                                 description = ?, 
                                 is_anonymous = ?,
                                 created_at = NOW()
                                 WHERE id = ? AND user_id = ?");
            $stmt->execute([$rating, $description, $is_anonymous, $review_id, $_SESSION['user_id']]);
            
            $_SESSION['message'] = 'Review updated successfully!';
        } else {
            // Check if user already reviewed this category
            $stmt = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND category_id = ?");
            $stmt->execute([$_SESSION['user_id'], $category_id]);
            
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'You have already reviewed this category';
                header('Location: reviews.php');
                exit;
            }
            
            // Insert new review
            $stmt = $pdo->prepare("INSERT INTO reviews 
                                 (user_id, category_id, rating, description, is_anonymous) 
                                 VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $category_id, $rating, $description, $is_anonymous]);
            
            $_SESSION['message'] = 'Review submitted successfully!';
        }
        
        header('Location: reviews.php?page=1');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header('Location: reviews.php');
        exit;
    }
}

// Check if editing a review (only for logged-in users)
$editing_review = null;
if (isset($_GET['edit']) && isset($_SESSION['user_id'])) {
    $review_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM reviews WHERE id = ? AND user_id = ?");
    $stmt->execute([$review_id, $_SESSION['user_id']]);
    $editing_review = $stmt->fetch();
}

// Get all categories for the dropdown
$categories = $pdo->query("SELECT * FROM auction_types ORDER BY name ASC")->fetchAll();

// Build query for reviews with filters
$query = "
    SELECT r.*, 
           u.fullname as user_name,
           t.name as category_name,
           t.image as category_image
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN auction_types t ON r.category_id = t.id
";

$count_query = "SELECT COUNT(*) FROM reviews r";

// Add category filter if selected
if ($category_filter > 0) {
    $query .= " WHERE r.category_id = $category_filter";
    $count_query .= " WHERE r.category_id = $category_filter";
}

// Complete queries
$query .= " ORDER BY r.created_at DESC LIMIT $offset, $reviews_per_page";

// Get total reviews count for pagination
$total_reviews = $pdo->query($count_query)->fetchColumn();
$total_pages = ceil($total_reviews / $reviews_per_page);

// Get paginated reviews with user and category info
$reviews = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews - TagHammer Auctions</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="images/crop.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .reviews-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .review-form {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 3rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .rating-stars {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .rating-stars input {
            display: none;
        }
        
        .rating-stars label {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }
        
        .rating-stars input:checked ~ label,
        .rating-stars label:hover,
        .rating-stars label:hover ~ label {
            color: #ffc107;
        }
        
        .rating-stars input:checked + label {
            color: #ffc107;
        }
        
        textarea {
            width: 100%;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 150px;
            font-family: inherit;
        }
        
        .reviews-list {
            margin-top: 2rem;
        }
        
        .review-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .reviewer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .reviewer-name {
            font-weight: 600;
        }
        
        .review-category {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.25rem;
            font-size: 0.9rem;
            color: #666;
        }
        
        .category-badge {
            background: #f0f0f0;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        .review-date {
            color: #999;
            font-size: 0.9rem;
        }
        
        .review-rating {
            color: #ffc107;
            margin: 0.5rem 0;
        }
        
        .review-content {
            line-height: 1.6;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .pagination a {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .pagination a:hover {
            background: #f5f5f5;
        }
        
        .pagination .active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .pagination .disabled {
            color: #999;
            cursor: not-allowed;
        }
        
        .anonymous-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .anonymous-checkbox input {
            width: auto;
        }
        
        .no-reviews {
            text-align: center;
            padding: 2rem;
            color: #666;
            font-size: 1.1rem;
        }
        
        .category-filter {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            align-items: center;
        }
        
        .category-filter label {
            font-weight: 500;
        }
        
        .category-filter select {
            padding: 0.5rem;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .category-filter .btn {
            padding: 0.5rem 1rem;
        }
    </style>
</head>
<body>

<nav class="navbar">
        <div class="navbar-left">
           <div class="logo">
                <img src="images/faviconsss.png" alt="Coffee-Auction" style="width:50px;">
                <span class="logo-text"> TagHammer Auctions</span>
            </div>
        </div>
        <div class="navbar-center">
            <a href="index.php" class="nav-link <?= $is_home ? 'active' : '' ?>">Home</a>
            <a href="auction.php" class="nav-link <?= $current_page == 'auction.php' ? 'active' : '' ?>">Auction</a>
            <a href="reviews.php" class="nav-link <?= $current_page == 'reviews.php' ? 'active' : '' ?>">Reviews</a>
            <a href="about.php" class="nav-link <?= $current_page == 'about.php' ? 'active' : '' ?>">About</a>
            <a href="faqs.php" class="nav-link <?= $current_page == 'faqs.php' ? 'active' : '' ?>">FAQ</a>
        </div>
        <div class="navbar-right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="notification-container">
                    <span class="user-greeting">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    <?php
                    // Get unread notification count
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = FALSE");
                    $stmt->execute([$_SESSION['user_id']]);
                    $unreadCount = $stmt->fetchColumn();
                    ?>
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span class="notification-badge"><?= $unreadCount ?></span>
                        <?php endif; ?>
                        <div class="notification-dropdown">
                            <div class="notification-header">
                                <h4>Notifications</h4>
                                <a href="mark_all_read.php" class="mark-all-read">Mark all as read</a>
                            </div>
                            <div class="notification-list">
                                <?php
                                $notifications = getUserNotifications($_SESSION['user_id']);
                                
                                if (empty($notifications)) {
                                    echo '<div class="notification-item empty">No notifications</div>';
                                } else {
                                    foreach ($notifications as $notification) {
                                        $class = $notification['is_read'] ? 'read' : 'unread';
                                        echo '<div class="notification-item '.$class.'">';
                                        echo htmlspecialchars($notification['message']);
                                        
                                        // Add exact time along with relative time
                                        $createdAt = new DateTime($notification['created_at']);
                                        $createdAt->setTimezone(new DateTimeZone('Asia/Manila'));
                                        echo '<div class="notification-time" title="'.$createdAt->format('M j, Y h:i A').'">';
                                        echo time_elapsed_string($notification['created_at']);
                                        echo '</div>';
                                        
                                        if (!$notification['is_read']) {
                                            echo '<a href="mark_read.php?id='.$notification['id'].'" class="mark-read">Mark read</a>';
                                        }
                                        echo '</div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <a href="logout.php" class="btn btn-outline">Logout</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="register.php" class="btn btn-primary">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="reviews-container">
        <?php if (isset($_SESSION['user_id'])): ?>
            <h1><?= $editing_review ? 'Edit Your Review' : 'Leave a Review' ?></h1>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            
            <div class="review-form">
                <form action="reviews.php" method="post">
                    <?php if ($editing_review): ?>
                        <input type="hidden" name="review_id" value="<?= $editing_review['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="category">Select Category</label>
                        <select id="category" name="category_id" class="form-control" required <?= $editing_review ? 'disabled' : '' ?>>
                            <option value="">-- Select a category --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                    <?= ($editing_review && $editing_review['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($editing_review): ?>
                            <small>Note: You cannot change the category for an existing review</small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Your Rating</label>
                        <div class="rating-stars">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" 
                                    <?= ($editing_review && $editing_review['rating'] == $i) ? 'checked' : '' ?> required>
                                <label for="star<?= $i ?>"><i class="fas fa-star"></i></label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="anonymous-checkbox">
                        <input type="checkbox" id="is_anonymous" name="is_anonymous" value="1"
                            <?= ($editing_review && $editing_review['is_anonymous']) ? 'checked' : '' ?>>
                        <label for="is_anonymous">Post this review anonymously</label>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Your Review</label>
                        <textarea id="description" name="description" required><?= 
                            $editing_review ? htmlspecialchars($editing_review['description']) : '' 
                        ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <?= $editing_review ? 'Update Review' : 'Submit Review' ?>
                    </button>
                    
                    <?php if ($editing_review): ?>
                        <a href="reviews.php" class="btn btn-outline">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        <?php endif; ?>
        
        <h2>Customer Reviews</h2>
        
        <!-- Category Filter -->
        <div class="category-filter">
            <form method="get" action="reviews.php">
                <label for="category-filter">Filter by Category:</label>
                <select id="category-filter" name="category" onchange="this.form.submit()">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $category_filter == $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="page" value="1">
                <?php if ($category_filter > 0): ?>
                    <a href="reviews.php" class="btn btn-outline">Clear Filter</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="reviews-list">
            <?php if (empty($reviews)): ?>
                <div class="no-reviews">
                    No reviews found for this category.
                </div>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="reviewer-info">
                                <div class="reviewer-avatar">
                                    <?= $review['is_anonymous'] ? 'A' : substr($review['user_name'], 0, 1) ?>
                                </div>
                                <div>
                                    <div class="reviewer-name">
                                        <?= $review['is_anonymous'] ? 'Anonymous' : htmlspecialchars($review['user_name']) ?>
                                    </div>
                                    <div class="review-category">
                                        <span>Reviewed</span>
                                        <span class="category-badge"><?= htmlspecialchars($review['category_name']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="review-actions">
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['user_id']): ?>
                                    <a href="reviews.php?edit=<?= $review['id'] ?>" class="btn btn-small btn-outline">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                <?php endif; ?>
                                <span class="review-date">
                                    <?= date('M j, Y', strtotime($review['created_at'])) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="review-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?= $i > $review['rating'] ? '-empty' : '' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        
                        <div class="review-content">
                            <?= nl2br(htmlspecialchars($review['description'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">&laquo; Previous</a>
                <?php else: ?>
                    <span class="disabled">&laquo; Previous</span>
                <?php endif; ?>
                
                <?php 
                // Show page numbers
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                if ($start_page > 1) {
                    echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '">1</a>';
                    if ($start_page > 2) {
                        echo '<span>...</span>';
                    }
                }
                
                for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" <?= $i == $page ? 'class="active"' : '' ?>>
                        <?= $i ?>
                    </a>
                <?php endfor; 
                
                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo '<span>...</span>';
                    }
                    echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $total_pages])) . '">' . $total_pages . '</a>';
                }
                ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next &raquo;</a>
                <?php else: ?>
                    <span class="disabled">Next &raquo;</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="modern-footer">
        <div class="footer-container">
            <!-- Top Section - Main Content -->
            <div class="footer-top">
                <!-- Brand Info -->
                <div class="footer-brand">
                    <div class="footer-logo">
                        <img src="images/faviconsss.png" alt="Coffee Auction Logo" style="width:50px;">
                        <span class="logo-text">TagHammer Auctions</span>
                    </div>
                    <p class="footer-tagline">From Rare Collectibles to Everyday Deals, Our All-in-One Auction Platform Lets You Explore, Compete, and Win No Matter Where You Are.</p>
                    <div class="newsletter">
                        <h4>Stay Updated</h4>
                        <form class="newsletter-form">
                            <input type="email" placeholder="Your email address" required>
                            <button type="submit" class="btn-subscribe">Subscribe</button>
                        </form>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-links">
                    <div class="links-column">
                        <h4>Navigation</h4>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li><a href="auction.php">Auctions</a></li>
                            <li><a href="about.php">About Us</a></li>
                            <li><a href="reviews.php">Reviews</a></li>
                            <li><a href="faqs.php">FAQ</a></li>
                        </ul>
                    </div>
                    <div class="links-column">
                        <h4>Legal</h4>
                        <ul>
                            <li>Terms of Service</li>
                            <li>Privacy Policy</li>
                            <li>Refund Policy</li>
                            <li>Bidding Rules</li>
                        </ul>
                    </div>
                    <div class="links-column">
                        <h4>Contact</h4>
                        <ul class="contact-info">
                            <li><i class="fas fa-envelope"></i> admin@coffeeauction.com</li>
                            <li><i class="fas fa-phone"></i> (082)224-1002 | (082) 333-6712</li>
                            <li><i class="fas fa-map-marker-alt"></i> 2F, Molave Street corner Calamansi Street Juna Subdivision, Matina, Davao City</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Bottom Section - Copyright and Social -->
            <div class="footer-bottom">
                <div class="copyright">
                    © 2025 TagHammer Auctions. All rights reserved.
                </div>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
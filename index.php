<?php include 'db_connect.php'; ?>

<?php 
// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);
$is_home = ($current_page == 'index.php' || $current_page == 'user_account.php');

// Get the item with the highest current bid
$highestBidStmt = $pdo->query("SELECT i.*, MAX(b.bid_amount) as max_bid 
                              FROM items i 
                              LEFT JOIN bids b ON i.id = b.item_id 
                              WHERE i.bid_end_date > NOW()
                              GROUP BY i.id 
                              ORDER BY max_bid DESC 
                              LIMIT 1");
$highestBidItem = $highestBidStmt->fetch();
$highestBid = $highestBidItem ? ($highestBidItem['max_bid'] ?: $highestBidItem['starting_price']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TagHammer Auctions</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="images/faviconsss.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Add this to your existing CSS */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        /* For mobile devices */
        @media (max-width: 768px) {
            .navbar.scrolled {
                padding: 10px 0;
            }

            .navbar.scrolled .navbar-center,
            .navbar.scrolled .navbar-right {
                display: none;
            }

            .navbar.scrolled .navbar-left {
                width: 100%;
                justify-content: center;
            }
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/432.png');
            background-size: cover;
            background-position: center;
            position: relative;
            height: 80vh;
            overflow: hidden;
            color: white;
            display: flex;
            align-items: center;
            text-align: center;
            margin-top: 80px;
        }

        .hero-content {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .hero p {
            font-size: 1.5rem;
            margin-bottom: 2.5rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .highest-bid-banner {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem auto;
            max-width: 500px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .bid-info {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .bid-info .label {
            font-size: 1.1rem;
            font-weight: 500;
            color: brown;
        }

        .bid-info .amount {
            font-size: 2rem;
            font-weight: 700;
            color: #f8c537;
        }

        .item-info {
            font-style: italic;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .auction-types {
            margin: 4rem 0;
        }

        .types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .type-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: block;
            color: inherit;
        }

        .type-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            text-decoration: none;
        }

        .type-image {
            height: 200px;
            overflow: hidden;
        }

        .type-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .type-card:hover .type-image img {
            transform: scale(1.05);
        }

        .type-info {
            padding: 1.5rem;
        }

        .type-info h3 {
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .type-info p {
            color: #666;
            font-size: 0.9rem;
        }

        .rating {
            color: #ffc107;
            margin: 0.5rem 0;
            font-size: 0.9rem;
        }

        .rating i {
            margin-right: 2px;
        }

        .rating span {
            color: #666;
            margin-left: 5px;
            font-size: 0.8rem;
        }

        .contact-admin-section {
            background-color: #f9f9f9;
            padding: 3rem 0;
            margin-top: 3rem;
            border-top: 1px solid #eee;
        }

        .contact-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .contact-form {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .contact-form .form-group {
            margin-bottom: 1.5rem;
        }

        .contact-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .contact-form input:focus,
        .contact-form textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
        }

        .contact-form textarea {
            resize: vertical;
            min-height: 150px;
        }

        .contact-form button {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
        }

        /* Legal Modal Styles */
        .legal-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .legal-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .legal-modal {
            background-color: white;
            border-radius: 10px;
            width: 90%;
            max-width: 800px;
            max-height: 80vh;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
            transform: translateY(-50px);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .legal-modal-overlay.active .legal-modal {
            transform: translateY(0);
        }
        
        .legal-modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .legal-modal-header h2 {
            margin: 0;
            color: var(--primary-color);
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #777;
            padding: 5px;
            transition: color 0.3s;
        }
        
        .close-modal:hover {
            color: #333;
        }
        
        .legal-modal-content {
            padding: 20px;
        }
        
        .legal-tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .legal-tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .legal-tab.active {
            border-bottom-color: var(--primary-color);
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .legal-tab-content {
            display: none;
        }
        
        .legal-tab-content.active {
            display: block;
        }
        
        .legal-modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            position: sticky;
            bottom: 0;
            background: white;
        }
        
        .policy-content {
            max-height: 50vh;
            overflow-y: auto;
            padding-right: 10px;
            margin-bottom: 20px;
        }
        
        .policy-content h3 {
            margin-top: 25px;
            color: var(--primary-color);
        }
        
        .policy-content h4 {
            margin-top: 20px;
        }
        
        .btn-accept {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-deny {
            background-color: #f5f5f5;
            color: #333;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1.2rem;
            }
            
            .bid-info {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .bid-info .amount {
                font-size: 1.8rem;
            }
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

    <header class="hero">
        <div class="hero-content">
            <h1>Where Bidders Compete. You Win.</h1>
            <p>From Rare Collectibles to Everyday Deals, Our All-in-One Auction Platform Lets You Explore, Compete, and Win No Matter Where You Are.</p>
            <a href="auction.php" class="btn btn-primary btn-large">View Auctions</a>
        </div>
    </header>

    <main class="container">
        <section class="auction-types">
            <div class="section-header">
                <h2>Browse Auction Categories</h2>
            </div>
            <div class="types-grid">
                <?php
                $types = $pdo->query("
                    SELECT t.*, 
                           AVG(r.rating) as avg_rating,
                           COUNT(r.id) as review_count
                    FROM auction_types t
                    LEFT JOIN reviews r ON t.id = r.category_id
                    GROUP BY t.id
                    ORDER BY name ASC
                ");
                while ($type = $types->fetch()):
                ?>
                    <a href="auction.php?type=<?= $type['id'] ?>" class="type-card">
                        <div class="type-image">
                            <img src="images/type-images/<?= htmlspecialchars($type['image']) ?>" alt="<?= htmlspecialchars($type['name']) ?>">
                        </div>
                        <div class="type-info">
                            <h3><?= htmlspecialchars($type['name']) ?></h3>
                            <p><?= htmlspecialchars($type['description']) ?></p>
                            <div class="rating">
                                <?php if ($type['review_count'] > 0): ?>
                                    <?php 
                                    $avg_rating = round($type['avg_rating']);
                                    for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star<?= $i > $avg_rating ? '-half-alt' : '' ?>"></i>
                                    <?php endfor; ?>
                                    <span>(<?= number_format($type['avg_rating'], 1) ?>/5 from <?= $type['review_count'] ?> reviews)</span>
                                <?php else: ?>
                                    <span>No reviews yet</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        </section>
    </main>

    <section class="contact-admin-section">
    <div class="contact-container">
        <h2>Need Help? Contact Our Administrators</h2>
        <div class="contact-form">
            <form id="contactAdminForm" action="process_contact.php" method="post">
                <div class="form-group">
                    <label for="contactName">Your Name</label>
                    <input type="text" id="contactName" name="name" required 
                           value="<?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="contactEmail">Your Email</label>
                    <input type="email" id="contactEmail" name="email" required
                           value="<?= isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="contactSubject">Subject</label>
                    <input type="text" id="contactSubject" name="subject" required>
                </div>
                <div class="form-group">
                    <label for="contactMessage">Message</label>
                    <textarea id="contactMessage" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
    </div>
</section>

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

<div class="legal-modal-overlay" id="legalModal">
        <div class="legal-modal">
            <div class="legal-modal-header">
                <h2>Terms and Policies</h2>
                <button class="close-modal" id="closeModalBtn">&times;</button>
            </div>
            
            <div class="legal-tabs">
                <div class="legal-tab active" data-tab="terms">Terms of Service</div>
                <div class="legal-tab" data-tab="privacy">Privacy Policy</div>
                <div class="legal-tab" data-tab="refund">Refund Policy</div>
                <div class="legal-tab" data-tab="bidding">Bidding Rules</div>
            </div>
            
            <div class="legal-modal-content">
                <!-- Terms of Service -->
                <div class="legal-tab-content active" id="terms-content">
                    <div class="policy-content">
                        <h3>Terms of Service</h3>
                        <p>Last Updated: January 1, 2025</p>
                        
                        <h4>1. Acceptance of Terms</h4>
                        <p>By accessing or using TagHammer Auctions ("the Platform"), you agree to be bound by these Terms of Service and all applicable laws and regulations.</p>
                        
                        <h4>2. User Accounts</h4>
                        <p>You must be at least 18 years old to create an account. You are responsible for maintaining the confidentiality of your account credentials.</p>
                        
                        <h4>3. Prohibited Conduct</h4>
                        <p>Users may not:
                            <ul>
                                <li>Engage in fraudulent activities</li>
                                <li>List prohibited items</li>
                                <li>Circumvent auction fees</li>
                                <li>Interfere with other users' transactions</li>
                            </ul>
                        </p>
                    </div>
                </div>
                
                <!-- Privacy Policy -->
                <div class="legal-tab-content" id="privacy-content">
                    <div class="policy-content">
                        <h3>Privacy Policy</h3>
                        <p>Last Updated: January 1, 2025</p>
                        
                        <h4>1. Information We Collect</h4>
                        <p>We collect personal information including:
                            <ul>
                                <li>Contact details (name, email, phone)</li>
                                <li>Payment information</li>
                                <li>Browsing and bidding activity</li>
                            </ul>
                        </p>
                    </div>
                </div>
                
                <!-- Refund Policy -->
                <div class="legal-tab-content" id="refund-content">
                    <div class="policy-content">
                        <h3>Refund Policy</h3>
                        <p>Last Updated: January 1, 2025</p>
                        
                        <h4>1. Buyer Protection</h4>
                        <p>We offer refunds in cases where:
                            <ul>
                                <li>Item is significantly not as described</li>
                                <li>Item is damaged during shipping</li>
                            </ul>
                        </p>
                    </div>
                </div>
                
                <!-- Bidding Rules -->
                <div class="legal-tab-content" id="bidding-content">
                    <div class="policy-content">
                        <h3>Bidding Rules</h3>
                        <p>Last Updated: January 1, 2025</p>
                        
                        <h4>1. Binding Bids</h4>
                        <p>All bids are binding contracts. By placing a bid, you agree to purchase the item if you win.</p>
                    </div>
                </div>
            </div>
            
            <div class="legal-modal-footer">
                <button class="btn btn-deny" id="denyBtn">Deny</button>
                <button class="btn btn-accept" id="acceptBtn">I Accept</button>
            </div>
        </div>
    </div>

    <button id="scrollToTopBtn" class="scroll-to-top" aria-label="Scroll to top">
        <i class="fas fa-arrow-up"></i>
    </button>

<script src="js/scrollup.js"></script>
<script src="js/terms.js"></script>
<script src="js/scroll.js"></script>
</body>
</html>
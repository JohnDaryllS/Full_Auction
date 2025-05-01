<?php include 'db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs - TagHammer Auctions</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="images/faviconsss.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* FAQ Page Styles */
        .faq-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
        }
        
        .faq-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .faq-header h1 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .faq-header p {
            color: #666;
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .faq-search {
            max-width: 600px;
            margin: 30px auto 50px;
            position: relative;
        }
        
        .faq-search input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #ddd;
            border-radius: 50px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .faq-search input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.2);
        }
        
        .faq-search i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        .faq-categories {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 40px;
        }
        
        .faq-category {
            padding: 10px 20px;
            background: #f5f5f5;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .faq-category:hover, .faq-category.active {
            background: var(--primary-color);
            color: white;
        }
        
        .faq-accordion {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .faq-item {
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }
        
        .faq-question {
            padding: 20px 30px 20px 0;
            position: relative;
            cursor: pointer;
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
        }
        
        .faq-question::after {
            content: '+';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
            color: var(--primary-color);
            transition: all 0.3s;
        }
        
        .faq-item.active .faq-question::after {
            content: '-';
        }
        
        .faq-answer {
            padding: 0 20px 0 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            color: #666;
            line-height: 1.6;
        }
        
        .faq-item.active .faq-answer {
            max-height: 500px;
            padding-bottom: 20px;
        }
        
        .faq-contact {
            text-align: center;
            margin-top: 60px;
            padding: 40px;
            background: #f9f9f9;
            border-radius: 10px;
        }
        
        .faq-contact h2 {
            margin-bottom: 20px;
            color: var(--primary-color);
        }
        
        .faq-contact p {
            margin-bottom: 30px;
            color: #666;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            color: #666;
            display: none;
        }
        
        /* Highlight for search terms */
        .highlight {
            background-color: #fff3cd;
            padding: 0 2px;
            border-radius: 3px;
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .faq-header h1 {
                font-size: 2rem;
            }
            
            .faq-question {
                font-size: 1rem;
                padding-right: 25px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-left">
            <div class="logo">
                <img src="images/faviconsss.png" alt="TagHammer Auctions" style="width:50px;">
                <span class="logo-text">TagHammer Auctions</span>
            </div>
        </div>
        <div class="navbar-center">
            <a href="index.php" class="nav-link">Home</a>
            <a href="auction.php" class="nav-link">Auctions</a>
            <a href="reviews.php" class="nav-link">Reviews</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="faq.php" class="nav-link active">FAQ</a>
        </div>
        <div class="navbar-right">
            <?php if (isset($_SESSION['user_id'])): ?>
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
            <?php else: ?>
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="register.php" class="btn btn-primary">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- FAQ Content -->
    <main class="faq-container">
        <div class="faq-header">
            <h1>Frequently Asked Questions</h1>
            <p>Find answers to common questions about bidding, selling, and everything in between on TagHammer Auctions.</p>
        </div>
        
        <div class="faq-search">
            <input type="text" id="faqSearch" placeholder="Search FAQs..." autocomplete="off">
            <i class="fas fa-search"></i>
        </div>
        
        <div class="faq-categories">
            <div class="faq-category active" data-category="all">All Categories</div>
            <div class="faq-category" data-category="bidding">Bidding</div>
            <div class="faq-category" data-category="selling">Selling</div>
            <div class="faq-category" data-category="payments">Payments</div>
            <div class="faq-category" data-category="shipping">Shipping</div>
            <div class="faq-category" data-category="account">Account</div>
        </div>
        
        <div class="no-results">
            <h3>No results found</h3>
            <p>Try different search terms or browse our categories</p>
        </div>
        
        <div class="faq-accordion" id="faqAccordion">
            <!-- General Questions -->
            <div class="faq-item active" data-categories="general">
                <div class="faq-question">What is TagHammer Auctions?</div>
                <div class="faq-answer">
                    TagHammer Auctions is a premium online auction platform specializing in exclusive items across multiple categories including collectibles, electronics, jewelry, and more. We connect buyers with unique items and sellers with passionate collectors.
                </div>
            </div>
            
            <!-- Bidding Questions -->
            <div class="faq-item" data-categories="bidding">
                <div class="faq-question">How does the bidding process work?</div>
                <div class="faq-answer">
                    <ol>
                        <li>Create a free account</li>
                        <li>Browse or search for items you're interested in</li>
                        <li>Place your bid by entering the amount you're willing to pay</li>
                        <li>If you're outbid, you'll receive a notification</li>
                        <li>If you win, you'll complete payment and arrange shipping</li>
                    </ol>
                    <p>All auctions have clear end times, and the highest bid at that time wins.</p>
                </div>
            </div>
            
            <div class="faq-item" data-categories="bidding">
                <div class="faq-question">What is proxy bidding?</div>
                <div class="faq-answer">
                    Proxy bidding is our automatic bidding system. You enter the maximum amount you're willing to pay, and our system will bid incrementally on your behalf, only bidding as much as needed to maintain your position as highest bidder (up to your maximum). This helps you win at the lowest possible price without constantly monitoring the auction.
                </div>
            </div>
            
            <div class="faq-item" data-categories="bidding">
                <div class="faq-question">Can I retract a bid?</div>
                <div class="faq-answer">
                    Bids are generally binding. However, you may retract a bid under exceptional circumstances (such as entering the wrong amount) by contacting our support team immediately. Unauthorized bid retractions may result in account restrictions.
                </div>
            </div>
            
            <!-- Selling Questions -->
            <div class="faq-item" data-categories="selling">
                <div class="faq-question">How do I sell items on TagHammer Auctions?</div>
                <div class="faq-answer">
                    <ol>
                        <li>Create a seller account (requires verification)</li>
                        <li>Click "Sell" and create your listing with clear photos and description</li>
                        <li>Set your starting price, duration, and other details</li>
                        <li>Submit for approval (we review all listings)</li>
                        <li>Once approved, your auction goes live</li>
                        <li>When the auction ends, arrange shipping with the buyer</li>
                    </ol>
                </div>
            </div>
            
            <div class="faq-item" data-categories="selling">
                <div class="faq-question">What are the seller fees?</div>
                <div class="faq-answer">
                    Our standard seller fee is 10% of the final sale price, with a minimum fee of $5 per item. Premium seller accounts (monthly subscription) receive reduced fees. There are no listing fees - you only pay when your item sells.
                </div>
            </div>
            
            <!-- Payment Questions -->
            <div class="faq-item" data-categories="payments">
                <div class="faq-question">What payment methods do you accept?</div>
                <div class="faq-answer">
                    We accept all major credit cards (Visa, Mastercard, American Express), PayPal, and bank transfers. All payments are processed securely through our encrypted payment system. We never store your full payment details on our servers.
                </div>
            </div>
            
            <div class="faq-item" data-categories="payments">
                <div class="faq-question">When do I need to pay for a won auction?</div>
                <div class="faq-answer">
                    Payment is due immediately upon winning an auction. You'll receive an email notification with payment instructions. Items will be shipped only after payment is confirmed. Unpaid items may be relisted after 48 hours, and repeated non-payment may result in account suspension.
                </div>
            </div>
            
            <!-- Shipping Questions -->
            <div class="faq-item" data-categories="shipping">
                <div class="faq-question">How does shipping work?</div>
                <div class="faq-answer">
                    Shipping methods and costs vary by seller. Each listing will specify:
                    <ul>
                        <li>Shipping options available</li>
                        <li>Estimated shipping costs</li>
                        <li>Processing time before shipment</li>
                        <li>Whether international shipping is available</li>
                    </ul>
                    Most sellers ship within 1-3 business days after payment confirmation. You'll receive tracking information once your item ships.
                </div>
            </div>
            
            <div class="faq-item" data-categories="shipping">
                <div class="faq-question">What if my item arrives damaged or isn't as described?</div>
                <div class="faq-answer">
                    We offer buyer protection for qualified transactions. If your item isn't as described or arrives damaged:
                    <ol>
                        <li>Contact the seller immediately (within 3 days of receipt)</li>
                        <li>If unresolved, open a case with our support team</li>
                        <li>Provide photos/video evidence of the issue</li>
                        <li>We'll mediate and may issue a refund if warranted</li>
                    </ol>
                </div>
            </div>
            
            <!-- Account Questions -->
            <div class="faq-item" data-categories="account">
                <div class="faq-question">How do I reset my password?</div>
                <div class="faq-answer">
                    Click "Forgot Password" on the login page and enter your registered email address. You'll receive a secure link to create a new password. If you don't see the email, check your spam folder or contact support.
                </div>
            </div>
            
            <div class="faq-item" data-categories="account">
                <div class="faq-question">Why was my account suspended?</div>
                <div class="faq-answer">
                    Accounts may be suspended for violations of our Terms of Service, including but not limited to:
                    <ul>
                        <li>Non-payment for won auctions</li>
                        <li>Listing prohibited items</li>
                        <li>Fraudulent activity</li>
                        <li>Abusive behavior</li>
                        <li>Attempts to circumvent our fees</li>
                    </ul>
                    If you believe this was in error, please contact our support team.
                </div>
            </div>
        </div>
        
        <div class="faq-contact">
            <h2>Still have questions?</h2>
            <p>Our support team is available 24/7 to help with any questions not covered in our FAQs.</p>
            <a href="contact.php" class="btn btn-primary">Contact Support</a>
        </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const faqItems = document.querySelectorAll('.faq-item');
            const searchInput = document.getElementById('faqSearch');
            const noResults = document.querySelector('.no-results');
            const categories = document.querySelectorAll('.faq-category');
            const faqAccordion = document.getElementById('faqAccordion');
            
            // Initialize all FAQs as active (visible)
            let activeCategory = 'all';
            let activeSearchTerm = '';
            
            // Accordion functionality
            function setupAccordion() {
                faqItems.forEach(item => {
                    const question = item.querySelector('.faq-question');
                    
                    question.addEventListener('click', () => {
                        // Close all other items if they're not part of the current search/category
                        faqItems.forEach(otherItem => {
                            if (otherItem !== item && otherItem.classList.contains('active')) {
                                otherItem.classList.remove('active');
                                otherItem.querySelector('.faq-answer').style.maxHeight = '0';
                            }
                        });
                        
                        // Toggle current item
                        item.classList.toggle('active');
                        const answer = item.querySelector('.faq-answer');
                        
                        if (item.classList.contains('active')) {
                            answer.style.maxHeight = answer.scrollHeight + 'px';
                        } else {
                            answer.style.maxHeight = '0';
                        }
                    });
                });
            }
            
            // Search functionality
            function performSearch(searchTerm) {
                activeSearchTerm = searchTerm.toLowerCase();
                let hasResults = false;
                
                faqItems.forEach(item => {
                    const question = item.querySelector('.faq-question').textContent.toLowerCase();
                    const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
                    const itemCategories = item.dataset.categories.split(' ');
                    
                    // Check if item matches search term and current category filter
                    const matchesSearch = activeSearchTerm === '' || 
                                        question.includes(activeSearchTerm) || 
                                        answer.includes(activeSearchTerm);
                                        
                    const matchesCategory = activeCategory === 'all' || 
                                         itemCategories.includes(activeCategory);
                    
                    if (matchesSearch && matchesCategory) {
                        item.style.display = '';
                        hasResults = true;
                        
                        // Highlight search terms
                        if (activeSearchTerm !== '') {
                            const regex = new RegExp(activeSearchTerm, 'gi');
                            const questionText = item.querySelector('.faq-question').textContent;
                            const answerText = item.querySelector('.faq-answer').textContent;
                            
                            item.querySelector('.faq-question').innerHTML = 
                                questionText.replace(regex, match => `<span class="highlight">${match}</span>`);
                                
                            item.querySelector('.faq-answer').innerHTML = 
                                answerText.replace(regex, match => `<span class="highlight">${match}</span>`);
                        }
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Show/hide no results message
                noResults.style.display = hasResults ? 'none' : 'block';
                faqAccordion.style.display = hasResults ? 'block' : 'none';
            }
            
            // Category filtering
            categories.forEach(cat => {
                cat.addEventListener('click', () => {
                    categories.forEach(c => c.classList.remove('active'));
                    cat.classList.add('active');
                    activeCategory = cat.dataset.category;
                    performSearch(activeSearchTerm);
                });
            });
            
            // Search input handler with debounce
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch(e.target.value);
                }, 300);
            });
            
            // Initialize
            setupAccordion();
            performSearch(''); // Show all FAQs initially
            
            // Open FAQ if URL has hash (e.g., #faq-1)
            if (window.location.hash) {
                const targetItem = document.querySelector(window.location.hash);
                if (targetItem) {
                    targetItem.classList.add('active');
                    targetItem.querySelector('.faq-answer').style.maxHeight = 
                        targetItem.querySelector('.faq-answer').scrollHeight + 'px';
                    targetItem.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    </script>
</body>
</html>
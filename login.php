<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TagHammer Auctions</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="images/crop.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            <a href="index.php" class="nav-link">Home</a>
            <a href="auction.php" class="nav-link">Auction</a>
            <a href="reviews.php" class="nav-link <?= $current_page == 'reviews.php' ? 'active' : '' ?>">Reviews</a>
            <a href="about.php" class="nav-link">About</a>
        </div>
        <div class="navbar-right">
            <a href="login.php" class="btn btn-outline">Login</a>
            <a href="register.php" class="btn btn-primary">Register</a>
        </div>
    </nav>

    <main class="container auth-container">
        <div class="auth-card">
            <h2>Login to Coffee Auction</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    if ($_GET['error'] == 'invalid') echo "Invalid email or password";
                    if ($_GET['error'] == 'pending') echo "Your account is pending admin verification.";
                    ?>
                </div>
            <?php endif; ?>
            
            <form action="process_login.php" method="post">
                <div class="form-group">
                    <label for="login-type">Login As:</label>
                    <select id="login-type" name="login_type" class="form-control">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            
            <div class="auth-footer">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
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
</body>
</html>
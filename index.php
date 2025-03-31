<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$page_title = "Home";
include 'includes/header.php';
?>

<div class="hero">
    <div class="hero-content">
        <h1>Swap Books with Your Neighbors</h1>
        <p>Discover new books and share your favorites with people near you.</p>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="hero-buttons">
                <a href="auth/register.php" class="btn">Join Now</a>
                <a href="auth/login.php" class="btn secondary">Login</a>
            </div>
        <?php else: ?>
            <div class="hero-buttons">
                <a href="pages/dashboard.php" class="btn">Go to Dashboard</a>
                <a href="pages/browse.php" class="btn secondary">Browse Books</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="features">
    <div class="container">
        <h2>Why Use BookSwap?</h2>
        
        <div class="features-grid">
            <div class="feature">
                <i class="fas fa-book-open"></i>
                <h3>Discover New Books</h3>
                <p>Find books you'll love from people in your community.</p>
            </div>
            
            <div class="feature">
                <i class="fas fa-exchange-alt"></i>
                <h3>Easy Exchanges</h3>
                <p>Swap books with nearby users without any cost.</p>
            </div>
            
            <div class="feature">
                <i class="fas fa-users"></i>
                <h3>Build Community</h3>
                <p>Connect with other book lovers in your area.</p>
            </div>
            
            <div class="feature">
                <i class="fas fa-leaf"></i>
                <h3>Eco-Friendly</h3>
                <p>Reduce waste by reusing and sharing books.</p>
            </div>
        </div>
    </div>
</div>

<?php if (!isset($_SESSION['user_id'])): ?>
    <div class="cta">
        <div class="container">
            <h2>Ready to Start Swapping?</h2>
            <p>Join BookSwap today and start exchanging books with your neighbors.</p>
            <a href="auth/register.php" class="btn">Sign Up Now</a>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
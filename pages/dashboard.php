<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);
$nearby_books = getNearbyBooks($user_id);
$user_books = getBooksByUser($user_id);

$page_title = "Dashboard";
include '../includes/header.php';
?>

<div class="dashboard">
    <div class="welcome">
        <h2>Welcome, <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>!</h2>
        <p>Find and exchange books with people near you.</p>
    </div>
    
    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3>Nearby Books</h3>
            <?php if (!empty($nearby_books)): ?>
                <div class="book-list">
                    <?php foreach (array_slice($nearby_books, 0, 3) as $book): ?>
                        <div class="book-item">
                            <div class="book-image">
                                <img src="<?php echo !empty($book['image']) ? htmlspecialchars($book['image']) : '../assets/images/book-placeholder.png'; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                            </div>
                            <div class="book-details">
                                <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                                <p>by <?php echo htmlspecialchars($book['author']); ?></p>
                                <p><?php echo number_format($book['distance'], 2); ?> miles away</p>
                                <a href="../pages/browse.php?book_id=<?php echo $book['id']; ?>" class="btn small">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="../pages/browse.php" class="btn">Browse All Nearby Books</a>
            <?php else: ?>
                <p>No nearby books found. Try adjusting your location in your profile.</p>
            <?php endif; ?>
        </div>
        
        <div class="dashboard-card">
            <h3>Your Books</h3>
            <?php if (!empty($user_books)): ?>
                <div class="book-list">
                    <?php foreach (array_slice($user_books, 0, 3) as $book): ?>
                        <div class="book-item">
                            <div class="book-image">
                                <img src="<?php echo !empty($book['image']) ? htmlspecialchars($book['image']) : '../assets/images/book-placeholder.png'; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                            </div>
                            <div class="book-details">
                                <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                                <p>by <?php echo htmlspecialchars($book['author']); ?></p>
                                <p>Status: <?php echo $book['available'] ? 'Available' : 'Unavailable'; ?></p>
                                <a href="../pages/add_book.php?edit=<?php echo $book['id']; ?>" class="btn small">Edit</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="../pages/add_book.php" class="btn">Add New Book</a>
            <?php else: ?>
                <p>You haven't added any books yet.</p>
                <a href="../pages/add_book.php" class="btn">Add Your First Book</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
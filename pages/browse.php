<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle book request
if (isset($_GET['request']) && is_numeric($_GET['request'])) {
    $book_id = (int)$_GET['request'];
    $book = getBookById($book_id);
    
    if ($book && $book['available'] && $book['user_id'] != $user_id) {
        $success = createBookExchange($book_id, $user_id, $book['user_id']);
        if ($success) {
            updateBookAvailability($book_id, 0);
            header("Location: messages.php");
            exit();
        }
    }
}

// Get filter values
$genre_filter = isset($_GET['genre']) ? $_GET['genre'] : null;
$condition_filter = isset($_GET['condition']) ? $_GET['condition'] : null;

// Get books based on filters
if ($genre_filter || $condition_filter) {
    $nearby_books = getFilteredBooks($user_id, $genre_filter, $condition_filter);
} else {
    $nearby_books = getNearbyBooks($user_id);
}

$page_title = "Browse Books";
include '../includes/header.php';
?>

<div class="browse-books">
    <h2>Browse Nearby Books</h2>
    
    <div class="filters">
        <form method="get" action="browse.php">
            <div class="form-group">
                <label for="genre">Filter by Genre:</label>
                <select id="genre" name="genre">
                    <option value="">All Genres</option>
                    <option value="Fiction" <?php echo isset($_GET['genre']) && $_GET['genre'] === 'Fiction' ? 'selected' : ''; ?>>Fiction</option>
                    <option value="Non-Fiction" <?php echo isset($_GET['genre']) && $_GET['genre'] === 'Non-Fiction' ? 'selected' : ''; ?>>Non-Fiction</option>
                    <option value="Science Fiction" <?php echo isset($_GET['genre']) && $_GET['genre'] === 'Science Fiction' ? 'selected' : ''; ?>>Science Fiction</option>
                    <option value="Fantasy" <?php echo isset($_GET['genre']) && $_GET['genre'] === 'Fantasy' ? 'selected' : ''; ?>>Fantasy</option>
                    <option value="Mystery" <?php echo isset($_GET['genre']) && $_GET['genre'] === 'Mystery' ? 'selected' : ''; ?>>Mystery</option>
                    <option value="Romance" <?php echo isset($_GET['genre']) && $_GET['genre'] === 'Romance' ? 'selected' : ''; ?>>Romance</option>
                    <option value="Biography" <?php echo isset($_GET['genre']) && $_GET['genre'] === 'Biography' ? 'selected' : ''; ?>>Biography</option>
                    <option value="History" <?php echo isset($_GET['genre']) && $_GET['genre'] === 'History' ? 'selected' : ''; ?>>History</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="condition">Filter by Condition:</label>
                <select id="condition" name="condition">
                    <option value="">All Conditions</option>
                    <option value="New" <?php echo isset($_GET['condition']) && $_GET['condition'] === 'New' ? 'selected' : ''; ?>>New</option>
                    <option value="Like New" <?php echo isset($_GET['condition']) && $_GET['condition'] === 'Like New' ? 'selected' : ''; ?>>Like New</option>
                    <option value="Very Good" <?php echo isset($_GET['condition']) && $_GET['condition'] === 'Very Good' ? 'selected' : ''; ?>>Very Good</option>
                    <option value="Good" <?php echo isset($_GET['condition']) && $_GET['condition'] === 'Good' ? 'selected' : ''; ?>>Good</option>
                    <option value="Fair" <?php echo isset($_GET['condition']) && $_GET['condition'] === 'Fair' ? 'selected' : ''; ?>>Fair</option>
                    <option value="Poor" <?php echo isset($_GET['condition']) && $_GET['condition'] === 'Poor' ? 'selected' : ''; ?>>Poor</option>
                </select>
            </div>
            
            <button type="submit" class="btn small">Apply Filters</button>
            <a href="browse.php" class="btn small secondary">Reset</a>
        </form>
    </div>
    
    <?php if (!empty($nearby_books)): ?>
        <div class="book-grid">
            <?php foreach ($nearby_books as $book): ?>
                <div class="book-card">
                    <div class="book-image">
                        <img src="<?php echo !empty($book['image']) ? htmlspecialchars($book['image']) : '../assets/images/book-placeholder.png'; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                    </div>
                    <div class="book-details">
                        <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p class="author">by <?php echo htmlspecialchars($book['author']); ?></p>
                        <?php if (isset($book['distance'])): ?>
                            <p class="distance"><?php echo number_format($book['distance'], 2); ?> miles away</p>
                        <?php endif; ?>
                        <p class="owner">Owner: <?php echo htmlspecialchars($book['full_name'] ?? $book['username']); ?></p>
                        
                        <?php if (!empty($book['genre'])): ?>
                            <p class="genre">Genre: <?php echo htmlspecialchars($book['genre']); ?></p>
                        <?php endif; ?>
                        
                        <p class="condition">Condition: <?php echo htmlspecialchars($book['condition']); ?></p>
                        
                        <?php if (!empty($book['description'])): ?>
                            <div class="description">
                                <p><?php echo htmlspecialchars($book['description']); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="actions">
                            <a href="browse.php?request=<?php echo $book['id']; ?>" class="btn">Request Exchange</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-results">
            <p>No books found matching your criteria. Try adjusting your filters.</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
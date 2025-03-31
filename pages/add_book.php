<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $isbn = trim($_POST['isbn']);
    $genre = trim($_POST['genre']);
    $book_condition = $_POST['condition'];
    $description = trim($_POST['description']);
    
    if (empty($title)) {
        $errors[] = "Title is required";
    }
    
    if (empty($author)) {
        $errors[] = "Author is required";
    }
    
    if (empty($book_condition)) {
        $errors[] = "Condition is required";
    }
    
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('book_') . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                $image_path = 'uploads/' . $file_name;
            } else {
                $errors[] = "Failed to upload image";
            }
        } else {
            $errors[] = "Only JPG, PNG, and GIF images are allowed";
        }
    }
    
    $edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
    if ($edit_id) {
        $book = getBookById($edit_id);
        if ($book && $book['user_id'] == $user_id) {
            $image_path = $image_path ?: $book['image'];
        } else {
            $errors[] = "Invalid book or unauthorized access";
        }
    }
    
    if (empty($errors)) {
        $db = new Database();
        $conn = $db->getConnection();
        
        if ($edit_id) {
            $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, isbn = ?, genre = ?, `condition` = ?, description = ?, image = ? WHERE id = ? AND user_id = ?");
            $success = $stmt->execute([$title, $author, $isbn, $genre, $book_condition, $description, $image_path, $edit_id, $user_id]);
        } else {
            $stmt = $conn->prepare("INSERT INTO books (user_id, title, author, isbn, genre, `condition`, description, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $success = $stmt->execute([$user_id, $title, $author, $isbn, $genre, $book_condition, $description, $image_path]);
        }
        
        if ($success) {
            header("Location: dashboard.php");
            exit();
        } else {
            $errors[] = "Failed to save book. Please try again.";
        }
    }
}

$book = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $book = getBookById($edit_id);
    
    if (!$book || $book['user_id'] != $user_id) {
        header("Location: dashboard.php");
        exit();
    }
}

$page_title = $book ? "Edit Book" : "Add New Book";
include '../includes/header.php';
?>

<div class="form-container">
    <h2><?php echo $book ? "Edit Book" : "Add New Book"; ?></h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form action="add_book.php<?php echo $book ? '?edit=' . $book['id'] : ''; ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title*</label>
            <input type="text" id="title" name="title" value="<?php echo $book ? htmlspecialchars($book['title']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="author">Author*</label>
            <input type="text" id="author" name="author" value="<?php echo $book ? htmlspecialchars($book['author']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn" value="<?php echo $book ? htmlspecialchars($book['isbn']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre" value="<?php echo $book ? htmlspecialchars($book['genre']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="condition">Condition*</label>
            <select id="condition" name="condition" required>
                <option value="">Select Condition</option>
                <option value="New" <?php echo $book && $book['condition'] === 'New' ? 'selected' : ''; ?>>New</option>
                <option value="Like New" <?php echo $book && $book['condition'] === 'Like New' ? 'selected' : ''; ?>>Like New</option>
                <option value="Very Good" <?php echo $book && $book['condition'] === 'Very Good' ? 'selected' : ''; ?>>Very Good</option>
                <option value="Good" <?php echo $book && $book['condition'] === 'Good' ? 'selected' : ''; ?>>Good</option>
                <option value="Fair" <?php echo $book && $book['condition'] === 'Fair' ? 'selected' : ''; ?>>Fair</option>
                <option value="Poor" <?php echo $book && $book['condition'] === 'Poor' ? 'selected' : ''; ?>>Poor</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description"><?php echo $book ? htmlspecialchars($book['description']) : ''; ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="image">Book Image</label>
            <input type="file" id="image" name="image" accept="image/*">
            <?php if ($book && $book['image']): ?>
                <div class="current-image">
                    <p>Current Image:</p>
                    <img src="../<?php echo htmlspecialchars($book['image']); ?>" alt="Current book image" style="max-width: 200px;">
                </div>
            <?php endif; ?>
        </div>
        
        <button type="submit" class="btn"><?php echo $book ? 'Update Book' : 'Add Book'; ?></button>
        <a href="dashboard.php" class="btn secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
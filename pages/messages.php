<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$db = new Database();
$conn = $db->getConnection();

// Get all exchanges involving the current user
$stmt = $conn->prepare("
    SELECT e.*, b.title AS book_title, b.image AS book_image,
           owner.username AS owner_username, owner.full_name AS owner_name,
           requester.username AS requester_username, requester.full_name AS requester_name
    FROM exchanges e
    JOIN books b ON e.book_id = b.id
    JOIN users owner ON e.owner_id = owner.id
    JOIN users requester ON e.requester_id = requester.id
    WHERE e.owner_id = ? OR e.requester_id = ?
    ORDER BY e.request_date DESC
");
$stmt->execute([$user_id, $user_id]);
$exchanges = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exchange_id']) && isset($_POST['message'])) {
    $exchange_id = (int)$_POST['exchange_id'];
    $message = trim($_POST['message']);
    
    if (!empty($message)) {
        sendMessage($exchange_id, $user_id, $message);
    }
    
    // Redirect to prevent form resubmission
    header("Location: messages.php?exchange=" . $exchange_id);
    exit();
}

// Get active exchange (from query parameter)
$active_exchange = null;
$active_messages = [];
if (isset($_GET['exchange']) && is_numeric($_GET['exchange'])) {
    $exchange_id = (int)$_GET['exchange'];
    
    // Verify the user is part of this exchange
    foreach ($exchanges as $exchange) {
        if ($exchange['id'] == $exchange_id) {
            $active_exchange = $exchange;
            break;
        }
    }
    
    if ($active_exchange) {
        $active_messages = getMessages($active_exchange['id']);
    }
}

// Handle exchange status update
if (isset($_GET['action']) && isset($_GET['exchange_id'])) {
    $exchange_id = (int)$_GET['exchange_id'];
    $action = $_GET['action'];
    
    // Verify the user is the owner of the book in this exchange
    $exchange = getExchangeById($exchange_id);
    if ($exchange && $exchange['owner_id'] == $user_id) {
        switch ($action) {
            case 'accept':
                updateExchangeStatus($exchange_id, 'Accepted');
                break;
            case 'reject':
                updateExchangeStatus($exchange_id, 'Rejected');
                updateBookAvailability($exchange['book_id'], 1); // Make book available again
                break;
            case 'complete':
                updateExchangeStatus($exchange_id, 'Completed');
                break;
        }
        
        // Redirect to prevent duplicate actions
        header("Location: messages.php?exchange=" . $exchange_id);
        exit();
    }
}

$page_title = "Messages";
include '../includes/header.php';
?>

<div class="messages-container">
    <h2>Your Book Exchanges</h2>
    
    <div class="messages-layout">
        <div class="exchanges-list">
            <h3>Your Exchanges</h3>
            
            <?php if (empty($exchanges)): ?>
                <p>You have no active book exchanges.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($exchanges as $exchange): ?>
                        <li class="<?php echo ($active_exchange && $active_exchange['id'] == $exchange['id']) ? 'active' : ''; ?>">
                            <a href="messages.php?exchange=<?php echo $exchange['id']; ?>">
                                <div class="exchange-book">
                                    <img src="<?php echo !empty($exchange['book_image']) ? htmlspecialchars($exchange['book_image']) : '../assets/images/book-placeholder.png'; ?>" alt="<?php echo htmlspecialchars($exchange['book_title']); ?>">
                                </div>
                                <div class="exchange-info">
                                    <h4><?php echo htmlspecialchars($exchange['book_title']); ?></h4>
                                    <p>
                                        <?php if ($exchange['owner_id'] == $user_id): ?>
                                            Request from <?php echo htmlspecialchars($exchange['requester_name'] ?? $exchange['requester_username']); ?>
                                        <?php else: ?>
                                            Your request to <?php echo htmlspecialchars($exchange['owner_name'] ?? $exchange['owner_username']); ?>
                                        <?php endif; ?>
                                    </p>
                                    <p class="status <?php echo strtolower($exchange['status']); ?>"><?php echo $exchange['status']; ?></p>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
        <div class="messages-content">
            <?php if ($active_exchange): ?>
                <div class="exchange-header">
                    <h3><?php echo htmlspecialchars($active_exchange['book_title']); ?></h3>
                    <p class="status <?php echo strtolower($active_exchange['status']); ?>">Status: <?php echo $active_exchange['status']; ?></p>
                    
                    <?php if ($active_exchange['owner_id'] == $user_id): ?>
                        <p>Request from: <?php echo htmlspecialchars($active_exchange['requester_name'] ?? $active_exchange['requester_username']); ?></p>
                    <?php else: ?>
                        <p>Your request to: <?php echo htmlspecialchars($active_exchange['owner_name'] ?? $active_exchange['owner_username']); ?></p>
                    <?php endif; ?>
                    
                    <?php if ($active_exchange['owner_id'] == $user_id && $active_exchange['status'] == 'Pending'): ?>
                        <div class="exchange-actions">
                            <a href="messages.php?action=accept&exchange_id=<?php echo $active_exchange['id']; ?>" class="btn small">Accept Request</a>
                            <a href="messages.php?action=reject&exchange_id=<?php echo $active_exchange['id']; ?>" class="btn small secondary">Reject Request</a>
                        </div>
                    <?php elseif ($active_exchange['status'] == 'Accepted'): ?>
                        <div class="exchange-actions">
                            <?php if ($active_exchange['owner_id'] == $user_id): ?>
                                <a href="messages.php?action=complete&exchange_id=<?php echo $active_exchange['id']; ?>" class="btn small">Mark as Completed</a>
                            <?php else: ?>
                                <p>The owner has accepted your request. Please arrange the exchange.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="messages-list">
                    <?php if (empty($active_messages)): ?>
                        <p>No messages yet. Start the conversation!</p>
                    <?php else: ?>
                        <?php foreach ($active_messages as $message): ?>
                            <div class="message <?php echo $message['sender_id'] == $user_id ? 'sent' : 'received'; ?>">
                                <div class="message-content">
                                    <p><?php echo htmlspecialchars($message['message']); ?></p>
                                    <span class="message-time"><?php echo date('M j, Y g:i a', strtotime($message['sent_at'])); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="message-form">
                    <form action="messages.php" method="post">
                        <input type="hidden" name="exchange_id" value="<?php echo $active_exchange['id']; ?>">
                        <div class="form-group">
                            <textarea name="message" placeholder="Type your message..." required></textarea>
                        </div>
                        <button type="submit" class="btn">Send Message</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="no-exchange-selected">
                    <p>Select an exchange from the list to view messages.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
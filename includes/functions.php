<?php
require_once 'db.php';

function getUserById($id) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getBooksByUser($user_id) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM books WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getNearbyBooks($user_id, $distance = MAX_DISTANCE) {
    $user = getUserById($user_id);
    if (!$user || !isset($user['latitude']) || !isset($user['longitude'])) {
        return [];
    }
    
    $db = new Database();
    $conn = $db->getConnection();
    
    $query = "
        SELECT b.*, u.username, u.full_name, 
               (3959 * ACOS(COS(RADIANS(?)) * COS(RADIANS(u.latitude)) * 
                COS(RADIANS(u.longitude) - RADIANS(?)) + 
                SIN(RADIANS(?)) * SIN(RADIANS(u.latitude)))) AS distance 
        FROM books b
        JOIN users u ON b.user_id = u.id
        WHERE b.available = 1 AND b.user_id != ?
        HAVING distance < ?
        ORDER BY distance
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([
        $user['latitude'],
        $user['longitude'],
        $user['latitude'],
        $user_id,
        $distance
    ]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createBookExchange($book_id, $requester_id, $owner_id) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("INSERT INTO exchanges (book_id, requester_id, owner_id) VALUES (?, ?, ?)");
    return $stmt->execute([$book_id, $requester_id, $owner_id]);
}

function sendMessage($exchange_id, $sender_id, $message) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("INSERT INTO messages (exchange_id, sender_id, message) VALUES (?, ?, ?)");
    return $stmt->execute([$exchange_id, $sender_id, $message]);
}

function getMessages($exchange_id) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM messages WHERE exchange_id = ? ORDER BY sent_at ASC");
    $stmt->execute([$exchange_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getExchangeById($exchange_id) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM exchanges WHERE id = ?");
    $stmt->execute([$exchange_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateExchangeStatus($exchange_id, $status) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("UPDATE exchanges SET status = ?, response_date = NOW() WHERE id = ?");
    return $stmt->execute([$status, $exchange_id]);
}

function getBookById($book_id) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateBookAvailability($book_id, $available) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("UPDATE books SET available = ? WHERE id = ?");
    return $stmt->execute([$available, $book_id]);
}

function getFilteredBooks($user_id, $genre = null, $condition = null) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $query = "SELECT b.*, u.username, u.full_name 
              FROM books b
              JOIN users u ON b.user_id = u.id
              WHERE b.available = 1 AND b.user_id != ?";
    
    $params = [$user_id];
    
    if ($genre) {
        $query .= " AND genre = ?";
        $params[] = $genre;
    }
    
    if ($condition) {
        $query .= " AND `condition` = ?";
        $params[] = $condition;
    }
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
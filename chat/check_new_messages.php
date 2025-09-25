<?php
require_once "config/db.php";

header('Content-Type: application/json');

// Assume a function to check for new messages exists
function hasNewMessages($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as message_count FROM messages WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    error_log("User $userId has " . $result['message_count'] . " unread messages."); // Debugging output
    return $result['message_count'] > 0;
}

// Get the user ID from session or request
$userId = $_SESSION['client_id'] ?? null;

if ($userId) {
    $newMessages = hasNewMessages($userId);
    echo json_encode(['newMessages' => $newMessages]);
} else {
    echo json_encode(['newMessages' => false]);
}
?>

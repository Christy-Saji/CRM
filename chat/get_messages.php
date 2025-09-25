<?php
// Prevent output before headers
ob_start();
header('Content-Type: application/json');
require_once "config/db.php";

if (!isset($_GET['complaint_id'])) {
    ob_end_clean();
    echo json_encode(['error' => 'Complaint ID is required']);
    exit();
}

$complaint_id = intval($_GET['complaint_id']);

try {
    $stmt = $conn->prepare("SELECT * FROM messages WHERE complaint_id = ? ORDER BY sent_at ASC");
    $stmt->execute([$complaint_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ob_end_clean();
    echo json_encode(['messages' => $messages]);
} catch (PDOException $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

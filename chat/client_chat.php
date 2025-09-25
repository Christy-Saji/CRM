


<?php
ob_start();
session_start();
require_once "config/db.php";

// Check if client is logged in
if (!isset($_SESSION['client_id'])) {
    ob_end_clean();
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

// Get complaint_id from URL
if (!isset($_GET['complaint_id'])) {
    ob_end_clean();
    echo json_encode(['error' => 'Complaint ID not provided']);
    exit();
}

$complaint_id = intval($_GET['complaint_id']);
$client_id = $_SESSION['client_id'];

// Fetch complaint info with product status
$stmt = $conn->prepare("SELECT c.*, e.name as employee_name, p.is_active as product_active
                       FROM complaints c 
                       LEFT JOIN assignments a ON c.complaint_id = a.complaint_id 
                       LEFT JOIN employees e ON a.employee_id = e.employee_id
                       LEFT JOIN products p ON c.product_id = p.product_id
                       WHERE c.complaint_id = ? AND c.client_id = ?");
$stmt->execute([$complaint_id, $client_id]);
$complaint = $stmt->fetch();

// Unauthorized access
if (!$complaint) {
    echo json_encode(['error' => 'Unauthorized access to this complaint']);
    exit();
}

// Check if product is deactivated
$is_deactivated = !$complaint['product_active'];

// Handle new message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    // Check if product is deactivated
    $stmt = $conn->prepare("SELECT is_active FROM products WHERE product_id = (SELECT product_id FROM complaints WHERE complaint_id = ?)");
    $stmt->execute([$complaint_id]);
    $product = $stmt->fetch();
    
    if (!$product['is_active']) {
        echo json_encode(['error' => 'This product has been deactivated. You cannot send new messages.']);
        exit();
    }

    $message = trim($_POST['message']);
    if (!empty($message)) {
        $insert = $conn->prepare("INSERT INTO messages (complaint_id, sender, message, sent_at) 
                                VALUES (?, 'client', ?, NOW())");
        $insert->execute([$complaint_id, $message]);
        http_response_code(204); // No Content
        exit();
    } else {
        echo json_encode(['error' => 'Message cannot be empty']);
    }
    exit();
}

// Fetch messages for this complaint
$msg_stmt = $conn->prepare("SELECT * FROM messages WHERE complaint_id = ? ORDER BY sent_at ASC");
$msg_stmt->execute([$complaint_id]);
$messages = $msg_stmt->fetchAll(PDO::FETCH_ASSOC);

// If this is an AJAX/API request, return JSON and exit
if (isset($_GET['api']) && $_GET['api'] === '1') {
    ob_end_clean();
    echo json_encode(['messages' => $messages]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Chat - Complaint #<?php echo htmlspecialchars($complaint_id); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #e9eff1;
            margin: 0;
            padding: 20px;
        }
         body {
            background: url('assets/backgorund.png') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-light);
        }
        .chat-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .chat-header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .chat-messages {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 20px;
            max-width: 70%;
            clear: both;
            position: relative;
            padding: 10px 15px;
            border-radius: 10px;
        }
        .message.client-message {
            float: right;
            background-color: #007bff;
            color: white;
        }
        .message.employee-message {
            float: left;
            background-color: #e9ecef;
            color: #333;
        }
        .message p {
            margin-bottom: 5px;
            font-size: 0.9em;
        }
        .message span {
            display: block;
            margin-top: 5px;
            font-size: 0.8em;
            opacity: 0.7;
        }
        .chat-messages::after {
            content: '';
            display: table;
            clear: both;
        }
        form {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        textarea {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            padding: 12px 24px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-button:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="chat-container">
    <a href="client/status.php" class="back-button">← Back to Status</a>
    
    <div class="chat-header">
        <h2>Chat for Complaint #<?php echo htmlspecialchars($complaint_id); ?></h2>
    </div>

    <div class="chat-messages">
        <?php foreach ($messages as $msg): ?>
            <div class="message <?php echo $msg['sender'] === 'client' ? 'client-message' : 'employee-message'; ?>">
                <p><?php echo htmlspecialchars($msg['message']); ?></p>
                <span><?php echo htmlspecialchars($msg['sent_at']); ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($is_deactivated): ?>
        <div style="margin-top: 20px; padding: 15px; background: #f8d7da; border-radius: 5px; color: #721c24;">
            This product has been deactivated. You can view previous messages but cannot send new ones.
        </div>
    <?php else: ?>
        <form method="post">
            <textarea name="message" placeholder="Type your message..."></textarea>
            <button type="submit">Send</button>
        </form>
    <?php endif; ?>
</div>

<script>
    const complaintId = <?php echo json_encode($complaint_id); ?>;
    const clientId = <?php echo json_encode($_SESSION['client_id']); ?>;

    // Helper function to capitalize first letter
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Function to fetch new messages
    function fetchMessages() {
        fetch(`get_messages.php?complaint_id=${complaintId}`)
            .then(response => response.json())
            .then(data => {
                if (data.messages) {
                    const messagesContainer = document.querySelector('.chat-messages');
                    messagesContainer.innerHTML = '';
                    data.messages.forEach(msg => {
                        const messageDiv = document.createElement('div');
                        messageDiv.classList.add('message', msg.sender === 'client' ? 'client-message' : 'employee-message');
                        messageDiv.innerHTML = `<p>${capitalizeFirstLetter(msg.message)}</p><span>${msg.sent_at}</span>`;
                        messagesContainer.appendChild(messageDiv);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error fetching messages: ' + error.message);
            });
    }

    // Start polling for new messages every 2 seconds
    setInterval(fetchMessages, 2000);

    // Fetch messages on page load
    fetchMessages();
</script>

</body>
</html>
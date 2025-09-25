<?php
session_start();
require '../config/db.php';

// Ensure employee is logged in
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$employee_id = $_SESSION['employee_id'];

// Fetch complaints assigned to this employee from the assignments table (including deadline)
$sql = "SELECT c.complaint_id, c.description, c.priority, cl.name AS client_name, a.deadline, a.assigned_at, c.product_id
        FROM assignments a
        JOIN complaints c ON a.complaint_id = c.complaint_id
        JOIN clients cl ON c.client_id = cl.client_id
        JOIN products p ON c.product_id = p.product_id
        WHERE a.employee_id = :employee_id AND c.status != 'Resolved'
        ORDER BY a.assigned_at DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute(['employee_id' => $employee_id]);
    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Function to check if a product is active
function isProductActive($product_id) {
    global $conn;
    if (!$product_id) {
        return false;
    }
    $stmt = $conn->prepare("SELECT is_active FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    return $product ? $product['is_active'] : false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --background-color: #f8f9fa;
            --card-background: #ffffff;
            --text-color: #2c3e50;
            --border-radius: 12px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: var(--text-color);
            padding: 20px;
        }
        

        .container {
            max-width: 1200px;
            margin: 40px auto;
            background-color: var(--card-background);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            animation: fadeIn 0.5s ease;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        h2 {
            color: var(--primary-color);
            font-weight: 600;
        }

        .logout-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--danger-color);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: var(--border-radius);
            background-color: #f8d7da;
            transition: all 0.3s ease;
        }

        .logout-link:hover {
            background-color: #f1b0b7;
            transform: translateY(-2px);
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: white;
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 0.5px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: var(--background-color);
        }

        .priority-high {
            color: var(--danger-color);
            font-weight: 500;
        }

        .priority-medium {
            color: #ffc107;
            font-weight: 500;
        }

        .priority-low {
            color: var(--success-color);
            font-weight: 500;
        }

        .resolve-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--success-color);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: var(--border-radius);
            background-color: #d4edda;
            transition: all 0.3s ease;
        }

        .resolve-link:hover {
            background-color: #c3e6cb;
            transform: translateY(-2px);
        }

        .no-complaints {
            text-align: center;
            padding: 30px;
            color: var(--secondary-color);
            font-size: 1.1em;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 20px;
            }

            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            th, td {
                padding: 12px 8px;
                font-size: 0.9em;
            }

            .table-container {
                margin: 0 -20px;
                border-radius: 0;
            }
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .chat-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: var(--border-radius);
            background-color: #e8eeff;
            transition: all 0.3s ease;
        }

        .chat-link:hover {
            background-color: #d1dbff;
            transform: translateY(-2px);
        }
    </style>
    <style>
        .popup-message {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            z-index: 1000;
        }
        .popup-message.active {
            display: block;
        }
        .popup-message-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .popup-message-overlay.active {
            display: block;
        }
        .popup-message button {
            margin-top: 15px;
            padding: 8px 16px;
            border: none;
            border-radius: var(--border-radius);
            background: var(--danger-color);
            color: white;
            cursor: pointer;
        }
        .popup-message button:hover {
            opacity: 0.9;
        }
    </style>
    <script>
        function showDeactivatedMessage() {
            const popup = document.getElementById('deactivatedPopup');
            const overlay = document.getElementById('popupOverlay');
            popup.classList.add('active');
            overlay.classList.add('active');
        }

        function closePopup() {
            const popup = document.getElementById('deactivatedPopup');
            const overlay = document.getElementById('popupOverlay');
            popup.classList.remove('active');
            overlay.classList.remove('active');
        }
    </script>
</head>
<div id="popupOverlay" class="popup-message-overlay"></div>
<div id="deactivatedPopup" class="popup-message">
    <p>This product has been deactivated. Please contact the system administrator for assistance.</p>
    <button onclick="closePopup()">OK</button>
</div>
<body>
<div class="container">
    <div class="header">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['employee_name']); ?>!</h2>
        <a href="logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <h3>Your Assigned Complaints</h3>

    <?php if (empty($complaints)): ?>
        <div class="no-complaints">
            <i class="fas fa-check-circle"></i> You have no assigned complaints.
        </div>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Description</th>
                        <th>Priority</th>
                        <th>Deadline</th>
                        <th>Assigned At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $complaint): ?>
                        <tr>
                            <td><?= htmlspecialchars($complaint['complaint_id']) ?></td>
                            <td><?= htmlspecialchars($complaint['client_name']) ?></td>
                            <td><?= htmlspecialchars($complaint['description']) ?></td>
                            <td class="priority-<?= strtolower($complaint['priority']) ?>">
                                <?= htmlspecialchars($complaint['priority']) ?>
                            </td>
                            <td><?= htmlspecialchars($complaint['deadline']) ?></td>
                            <td><?= htmlspecialchars($complaint['assigned_at']) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="../employee_chat.php?complaint_id=<?= htmlspecialchars($complaint['complaint_id']) ?>" 
                                       class="chat-link">
                                        <i class="fas fa-comments"></i> Chat
                                    </a>
                                    <?php if (isset($complaint['product_id']) && isProductActive($complaint['product_id'])): ?>
                                        <a href="resolve.php?id=<?= htmlspecialchars($complaint['complaint_id']) ?>" 
                                           class="resolve-link">
                                            <i class="fas fa-check"></i> Resolve
                                        </a>
                                    <?php else: ?>
                                        <button class="resolve-link" onclick="showDeactivatedMessage()" style="cursor: not-allowed;">
                                            Resolve
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
    // Function to check for new messages
    function checkForNewMessages() {
        fetch('../check_new_messages.php')
            .then(response => response.json())
            .then(data => {
                if (data.newMessages) {
                    alert('You have new messages!');
                }
            })
            .catch(error => console.error('Error checking for new messages:', error));
    }

    // Check for new messages every 30 seconds
    setInterval(checkForNewMessages, 30000);
</script>
</body>
</html>

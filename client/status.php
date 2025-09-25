<?php
session_start();
if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db.php";

$client_id = $_SESSION['client_id'];

$stmt = $conn->prepare("
    SELECT 
        c.complaint_id,
        c.description,
        c.status,
        a.employee_id
    FROM 
        complaints c
    LEFT JOIN 
        assignments a ON c.complaint_id = a.complaint_id
    WHERE 
        c.client_id = ?
    ORDER BY c.complaint_id DESC
");
$stmt->execute([$client_id]);
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Complaints</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --background-color: #f1f4fb;
            --card-background: #ffffff;
            --text-color: #2c3e50;
            --border-radius: 12px;
            --box-shadow: 0 8px 16px rgba(0, 0, 0, 0.06);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
            min-height: 100vh;
            color: var(--text-color);
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 30px;
            background-color: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            animation: fadeIn 0.7s ease;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        h2 {
            color: var(--primary-color);
            font-weight: 600;
        }

        .back {
            display: inline-flex;
            align-items: center;
            background-color: var(--secondary-color);
            color: white;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .back i {
            margin-right: 8px;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 25px;
            border-radius: var(--border-radius);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            background-color: white;
            animation: slideIn 0.6s ease;
        }

        th, td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-size: 0.85em;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background-color: #f8f9fa;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.85em;
            font-weight: 500;
            animation: fadeBadge 0.8s ease;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-in-progress {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-resolved {
            background-color: #d4edda;
            color: #155724;
        }

        .chat-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: var(--primary-color);
            color: white;
            padding: 8px 16px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-size: 0.9em;
            font-weight: 500;
            transition: all 0.3s ease;
            animation: pulse 2s infinite;
        }

        .chat-button:hover {
            background-color: #3451d1;
            transform: translateY(-2px);
        }

        .no-complaints {
            text-align: center;
            padding: 50px;
            background-color: var(--background-color);
            border-radius: var(--border-radius);
        }

        .no-complaints i {
            font-size: 3em;
            color: var(--secondary-color);
            margin-bottom: 15px;
        }

        .no-complaints p {
            font-size: 1.1em;
            font-weight: 500;
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

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeBadge {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(74, 107, 255, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(74, 107, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(74, 107, 255, 0); }
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 20px;
            }

            th, td {
                padding: 12px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>My Complaints Status</h2>
        <a href="dashboard.php" class="back">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <?php if (count($complaints) > 0): ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Complaint ID</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Assigned Employee</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($complaints as $complaint): ?>
                <tr>
                    <td><?= htmlspecialchars($complaint['complaint_id']) ?></td>
                    <td><?= htmlspecialchars($complaint['description']) ?></td>
                    <td>
                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $complaint['status'] ?? 'pending')) ?>">
                            <?= htmlspecialchars($complaint['status'] ?? 'Pending') ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($complaint['employee_id'] ?? 'Not Assigned') ?></td>
                    <td>
                        <?php if ($complaint['employee_id']): ?>
                            <a href="../client_chat.php?complaint_id=<?= htmlspecialchars($complaint['complaint_id']) ?>" 
                               class="chat-button">
                                <i class="fas fa-comments"></i> Chat
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="no-complaints">
            <i class="fas fa-clipboard-list"></i>
            <p>No complaints found.</p>
        </div>
    <?php endif; ?>
</div>

<script>
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

    setInterval(checkForNewMessages, 30000);
</script>

</body>
</html>

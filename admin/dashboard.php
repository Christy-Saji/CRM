<?php
session_start();
require_once "../config/db.php";

// Handle sort option from dropdown
$sortOption = $_GET['sort'] ?? 'all';

$sql = "SELECT c.*, cl.name AS client_name, p.name AS product_name, 
               EXISTS(SELECT 1 FROM assignments WHERE complaint_id = c.complaint_id) as is_assigned
        FROM complaints c 
        JOIN clients cl ON c.client_id = cl.client_id
        LEFT JOIN products p ON c.product_id = p.product_id";

switch ($sortOption) {
    case 'resolved':
        $sql .= " WHERE c.status = 'Resolved'";
        break;
    case 'unresolved':
        $sql .= " WHERE c.status != 'Resolved'";
        break;
    case 'priority_high':
        $sql .= " ORDER BY c.priority DESC";
        break;
    case 'priority_low':
        $sql .= " ORDER BY c.priority ASC";
        break;
    default:
        $sql .= " ORDER BY c.created_at DESC";
}

$stmt = $conn->query($sql);
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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

        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            gap: 8px;
        }

        .back-link:hover {
            color: var(--primary-color);
            transform: translateX(-5px);
        }

        .filter {
            margin-bottom: 30px;
            background-color: var(--background-color);
            padding: 20px;
            border-radius: var(--border-radius);
        }

        .filter form {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        label {
            font-weight: 500;
            color: var(--text-color);
        }

        select {
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            background-color: white;
            font-size: 14px;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.1);
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

        .btn-assign {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: all 0.3s ease;
            gap: 5px;
            cursor: pointer;
        }

        .btn-assign.active {
            background-color: var(--success-color);
            color: white;
        }

        .btn-assign.active:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .btn-assign.resolved {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-assign.assigned {
            background-color: var(--secondary-color);
            color: white;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 500;
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

        .priority-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 500;
        }

        .priority-high {
            background-color: #f8d7da;
            color: #721c24;
        }

        .priority-medium {
            background-color: #fff3cd;
            color: #856404;
        }

        .priority-low {
            background-color: #d4edda;
            color: #155724;
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

            .filter form {
                flex-direction: column;
                align-items: stretch;
            }

            th, td {
                padding: 12px 8px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Welcome, Admin</h2>
        <a href="management.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
        <a href="report.php" class="back-link">
            <i class="fas fa-chart-bar"></i> View Reports
        </a>
    </div>

    <div class="filter">
        <form method="GET" action="dashboard.php">
            <label for="sort">Sort Complaints:</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="all" <?= $sortOption == 'all' ? 'selected' : '' ?>>All Complaints</option>
                <option value="resolved" <?= $sortOption == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                <option value="unresolved" <?= $sortOption == 'unresolved' ? 'selected' : '' ?>>Unresolved</option>
                <option value="priority_high" <?= $sortOption == 'priority_high' ? 'selected' : '' ?>>Priority High → Low</option>
                <option value="priority_low" <?= $sortOption == 'priority_low' ? 'selected' : '' ?>>Priority Low → High</option>
            </select>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Product</th>
                    <th>Description</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($complaints as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['complaint_id']) ?></td>
                    <td><?= htmlspecialchars($row['client_name']) ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td>
                        <span class="priority-badge priority-<?= strtolower($row['priority']) ?>">
                            <?= htmlspecialchars($row['priority']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $row['status'])) ?>">
                            <?= htmlspecialchars($row['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($row['status'] !== 'Resolved' && !$row['is_assigned']): ?>
                            <a href="assign.php?complaint_id=<?= $row['complaint_id'] ?>&priority=<?= $row['priority'] ?>" class="btn-assign active">
                                <i class="fas fa-user-plus"></i> Assign
                            </a>
                        <?php else: ?>
                            <button class="btn-assign <?php echo $row['status'] === 'Resolved' ? 'resolved' : 'assigned'; ?>" style="cursor: not-allowed;" disabled>
                                <i class="fas fa-user-plus"></i> 
                                <?php if ($row['status'] === 'Resolved'): ?>
                                    Resolved
                                <?php else: ?>
                                    Assigned
                                <?php endif; ?>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

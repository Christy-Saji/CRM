<?php
session_start();
require_once "../config/db.php";

// Sanitize and validate filter inputs
$dateFrom = filter_input(INPUT_GET, 'date_from', FILTER_DEFAULT);
$dateTo = filter_input(INPUT_GET, 'date_to', FILTER_DEFAULT);
$statusFilter = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$priorityFilter = filter_input(INPUT_GET, 'priority', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$productFilter = filter_input(INPUT_GET, 'product', FILTER_SANITIZE_NUMBER_INT);
$clientFilter = filter_input(INPUT_GET, 'client', FILTER_SANITIZE_NUMBER_INT);

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

if ($dateFrom && !validateDate($dateFrom)) $dateFrom = '';
if ($dateTo && !validateDate($dateTo)) $dateTo = '';

try {
    $products = $conn->query("SELECT product_id, name FROM products ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    $clients = $conn->query("SELECT client_id, name FROM clients ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    $statuses = $conn->query("SELECT DISTINCT status FROM complaints ORDER BY status")->fetchAll(PDO::FETCH_ASSOC);
    $employees = $conn->query("SELECT employee_id, name FROM employees ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

    $filterConditions = [];
    $params = [];

    if ($dateFrom) {
        $filterConditions[] = "c.created_at >= :date_from";
        $params[':date_from'] = $dateFrom;
    }
    if ($dateTo) {
        $filterConditions[] = "c.created_at <= :date_to";
        $params[':date_to'] = $dateTo;
    }
    if ($statusFilter) {
        $filterConditions[] = "c.status = :status";
        $params[':status'] = $statusFilter;
    }
    if ($priorityFilter) {
        $filterConditions[] = "c.priority = :priority";
        $params[':priority'] = $priorityFilter;
    }
    if ($productFilter) {
        $filterConditions[] = "c.product_id = :product_id";
        $params[':product_id'] = $productFilter;
    }
    if ($clientFilter) {
        $filterConditions[] = "c.client_id = :client_id";
        $params[':client_id'] = $clientFilter;
    }
    // Employee filter via assignments table
    $employeeFilter = filter_input(INPUT_GET, 'employee', FILTER_SANITIZE_NUMBER_INT);
    if ($employeeFilter) {
        $filterConditions[] = "EXISTS (SELECT 1 FROM assignments a WHERE a.complaint_id = c.complaint_id AND a.employee_id = :employee_id)";
        $params[':employee_id'] = $employeeFilter;
    }

    $filterSql = !empty($filterConditions) ? 'WHERE ' . implode(' AND ', $filterConditions) : '';

    $queries = [
        'productReports' => "
            SELECT p.name AS product_name, COUNT(c.complaint_id) AS complaint_count,
                   SUM(CASE WHEN c.status = 'Resolved' THEN 1 ELSE 0 END) AS resolved_count,
                   GROUP_CONCAT(c.description SEPARATOR '; ') AS descriptions
            FROM complaints c
            JOIN products p ON c.product_id = p.product_id
            $filterSql
            GROUP BY p.product_id",

        'clientReports' => "
            SELECT cl.name AS client_name, COUNT(c.complaint_id) AS complaint_count,
                   GROUP_CONCAT(DISTINCT c.status SEPARATOR ', ') AS status_distribution,
                   GROUP_CONCAT(c.description SEPARATOR '; ') AS descriptions
            FROM complaints c
            JOIN clients cl ON c.client_id = cl.client_id
            $filterSql
            GROUP BY cl.client_id",

        'recentComplaints' => "
            SELECT c.complaint_id, cl.name AS client_name, p.name AS product_name, 
                   c.description, c.status, c.priority
            FROM complaints c
            JOIN clients cl ON c.client_id = cl.client_id
            JOIN products p ON c.product_id = p.product_id
            $filterSql
            ORDER BY c.complaint_id DESC
            LIMIT 10",

        'statusDistributionData' => "
            SELECT c.status, COUNT(c.complaint_id) AS count
            FROM complaints c
            $filterSql
            GROUP BY c.status",

        'complaintTrendsData' => "
            SELECT p.name AS product_name, COUNT(c.complaint_id) AS complaint_count
            FROM complaints c
            JOIN products p ON c.product_id = p.product_id
            $filterSql
            GROUP BY p.product_id"
    ];

    foreach ($queries as $key => $sql) {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $$key = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complaint Reports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
            color: #333;
        }
        h1, h2 {
            color: #2c3e50;
        }
        a.back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #2980b9;
            text-decoration: none;
            font-weight: 600;
        }
        a.back-link:hover {
            text-decoration: underline;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            display: grid;
            gap: 15px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            margin-bottom: 40px;
        }
        form label {
            font-weight: 600;
        }
        form input, form select {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        form button {
            grid-column: span 2;
            padding: 10px;
            background-color: #2980b9;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        form button:hover {
            background-color: #1f6396;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 40px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #2980b9;
            color: white;
            position: sticky;
            top: 0;
            z-index: 2;
        }
        tr:hover {
            background-color: #f1f9ff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

    <a href="dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    <h1>Complaint Management Reports</h1>

    <h2>Filter Reports</h2>
    <form method="GET">
        <div>
            <label for="date_from">From:</label>
            <input type="date" id="date_from" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>">
        </div>
        <div>
            <label for="date_to">To:</label>
            <input type="date" id="date_to" name="date_to" value="<?= htmlspecialchars($dateTo) ?>">
        </div>
        <div>
            <label for="client">Client:</label>
            <select name="client" id="client">
                <option value="">-- All --</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?= $client['client_id'] ?>" <?= ($clientFilter == $client['client_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($client['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="product">Product:</label>
            <select name="product" id="product">
                <option value="">-- All --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['product_id'] ?>" <?= ($productFilter == $product['product_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($product['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="employee">Employee:</label>
            <select name="employee" id="employee">
                <option value="">-- All --</option>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?= $employee['employee_id'] ?>" <?= (isset($_GET['employee']) && $_GET['employee'] == $employee['employee_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($employee['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="">-- All --</option>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= htmlspecialchars($status['status']) ?>" <?= ($statusFilter == $status['status']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($status['status']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit">Apply Filter</button>
    </form>

    <h2>Product Reports</h2>
    <table>
        <tr><th>Product</th><th>Total Complaints</th><th>Resolved</th><th>Descriptions</th></tr>
        <?php foreach ($productReports as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td><?= $row['complaint_count'] ?></td>
                <td><?= $row['resolved_count'] ?></td>
                <td><?= htmlspecialchars($row['descriptions']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Client Reports</h2>
    <table>
        <tr><th>Client</th><th>Total Complaints</th><th>Status Distribution</th><th>Descriptions</th></tr>
        <?php foreach ($clientReports as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['client_name']) ?></td>
                <td><?= $row['complaint_count'] ?></td>
                <td><?= htmlspecialchars($row['status_distribution']) ?></td>
                <td><?= htmlspecialchars($row['descriptions']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Recent Complaints</h2>
    <table>
        <tr><th>ID</th><th>Client</th><th>Product</th><th>Description</th><th>Status</th><th>Priority</th></tr>
        <?php foreach ($recentComplaints as $row): ?>
            <tr>
                <td><?= $row['complaint_id'] ?></td>
                <td><?= htmlspecialchars($row['client_name']) ?></td>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['priority']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Status Distribution</h2>
    <table>
        <tr><th>Status</th><th>Count</th></tr>
        <?php foreach ($statusDistributionData as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= $row['count'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Complaint Trends by Product</h2>
    <table>
        <tr><th>Product</th><th>Total Complaints</th></tr>
        <?php foreach ($complaintTrendsData as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td><?= $row['complaint_count'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>

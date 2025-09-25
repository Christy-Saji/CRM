<?php
session_start();
require '../config/db.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Load product_id from GET request
if (!isset($_GET['complaint_id']) || empty($_GET['complaint_id'])) {
    echo "<div class='error'>Error: Complaint ID not provided.</div>";
    exit();
}

$complaint_id = $_GET['complaint_id'];

// Fetch complaint product_id
$complaint_stmt = $conn->prepare("SELECT product_id FROM complaints WHERE complaint_id = :complaint_id");
$complaint_stmt->execute(['complaint_id' => $complaint_id]);
$complaint = $complaint_stmt->fetch(PDO::FETCH_ASSOC);

if (!$complaint) {
    echo "<div class='error'>Complaint not found.</div>";
    exit();
}

$product_id = $complaint['product_id'];

// Load employees related to the complaint's product (filtered by product_id in employees table)
$employees_stmt = $conn->prepare("SELECT employee_id, name FROM employees WHERE product_id = :product_id");
$employees_stmt->execute(['product_id' => $product_id]);
$employees = $employees_stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize variables
$priority = 'Medium'; // Default priority

// Handle POST (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $priority = $_POST['priority'];
    $assigned_time = date('Y-m-d H:i:s');

    // Set deadline based on priority
    switch ($priority) {
        case 'High':
            $deadline = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($assigned_time)));
            break;
        case 'Medium':
            $deadline = date('Y-m-d H:i:s', strtotime('+3 days', strtotime($assigned_time)));
            break;
        case 'Low':
            $deadline = date('Y-m-d H:i:s', strtotime('+5 days', strtotime($assigned_time)));
            break;
        default:
            $deadline = null;
    }

    // Insert into assignments table
    $insertStmt = $conn->prepare("INSERT INTO assignments (complaint_id, employee_id, assigned_at, deadline) 
                                  VALUES (:complaint_id, :employee_id, :assigned_at, :deadline)");
    $insertStmt->execute([
        'complaint_id' => $complaint_id,
        'employee_id' => $employee_id,
        'assigned_at' => $assigned_time,
        'deadline' => $deadline
    ]);

    // Update complaint status
    $updateStmt = $conn->prepare("UPDATE complaints 
                                  SET status = 'Assigned', priority = :priority 
                                  WHERE complaint_id = :complaint_id");
    $updateStmt->execute([
        'priority' => $priority,
        'complaint_id' => $complaint_id
    ]);

    $_SESSION['msg'] = "Complaint assigned successfully!";
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Complaint</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background-color: var(--card-background);
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 90%;
            max-width: 500px;
            animation: fadeIn 0.5s ease;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        h2 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .complaint-id {
            color: var(--secondary-color);
            font-size: 0.9em;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }

        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            font-size: 14px;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: white;
        }

        select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.1);
        }

        button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #3a5bef;
            transform: translateY(-2px);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            background-color: var(--background-color);
        }

        .back-link:hover {
            color: var(--primary-color);
            transform: translateX(-5px);
        }

        .error {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            background-color: #f8d7da;
            color: #721c24;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error i {
            font-size: 1.2em;
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

        @media (max-width: 480px) {
            .container {
                padding: 30px;
            }

            h2 {
                font-size: 1.5em;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Assign Complaint</h2>
        <div class="complaint-id">Complaint #<?= htmlspecialchars($complaint_id) ?></div>
    </div>

    <form method="POST" action="">
        <input type="hidden" name="complaint_id" value="<?= htmlspecialchars($complaint_id) ?>">

        <div class="form-group">
            <label for="employee_id">Select Employee:</label>
            <select name="employee_id" id="employee_id" required>
                <?php foreach ($employees as $emp): ?>
                    <option value="<?= $emp['employee_id'] ?>"><?= htmlspecialchars($emp['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="priority">Select Priority:</label>
            <select name="priority" id="priority" required>
                <option value="High" <?= $priority === 'High' ? 'selected' : '' ?>>High</option>
                <option value="Medium" <?= $priority === 'Medium' ? 'selected' : '' ?>>Medium</option>
                <option value="Low" <?= $priority === 'Low' ? 'selected' : '' ?>>Low</option>
            </select>
        </div>

        <button type="submit">
            <i class="fas fa-user-plus"></i> Assign Complaint
        </button>
    </form>

    <a href="dashboard.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>
</body>
</html>

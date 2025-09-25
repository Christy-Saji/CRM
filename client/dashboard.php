<?php
session_start();
if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db.php";

$client_id = $_SESSION['client_id'];
$message = "";

$product_stmt = $conn->prepare("SELECT product_id, name FROM products WHERE is_active = TRUE");
$product_stmt->execute();
$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['submit_complaint'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $product_id = intval($_POST['product_id']);
    $description = trim($_POST['description']);
    $priority = 'Medium';

    $check_product = $conn->prepare("SELECT is_active FROM products WHERE product_id = ?");
    $check_product->execute([$product_id]);
    $product = $check_product->fetch(PDO::FETCH_ASSOC);

    if (!$product || !$product['is_active']) {
        $message = "<div class='error'>Cannot submit complaint: The selected product is no longer available.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO complaints (client_id, product_id, description, priority) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$client_id, $product_id, $description, $priority])) {
            $productName = array_column($products, 'name', 'product_id')[$product_id] ?? 'selected product';
            $message = "<div class='success fade-in'>Complaint for <strong>$productName</strong> submitted successfully!</div>";
        } else {
            $message = "<div class='error'>Error submitting complaint.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6bff;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --card-bg: #ffffff;
            --fade-in: fadeIn 0.6s ease-out forwards;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #e0eafc, #cfdef3);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #333;
        }

        .container {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 40px;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            animation: fadeInScale 0.8s ease forwards;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h2 {
            font-weight: 600;
            color: var(--primary-color);
        }

        .logout {
            background: var(--danger-color);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .logout:hover {
            background: #b52a37;
        }

        form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
        }

        select, textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1.5px solid #ccc;
            font-size: 14px;
            transition: border 0.3s;
        }

        select:focus, textarea:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        button {
            background: var(--primary-color);
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #3a5bef;
        }

        .status-link {
            display: inline-block;
            text-align: center;
            margin-top: 20px;
            padding: 12px;
            background: var(--success-color);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .status-link:hover {
            background: #1e7e34;
        }

        .success, .error {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['client_name']) ?>!</h2>
        <a class="logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <?= $message ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="form-group">
            <label for="product_id">Select Product:</label>
            <select name="product_id" id="product_id" required>
                <option value="">-- Select Product --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['product_id'] ?>" <?= isset($product_id) && $product_id == $product['product_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($product['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Complaint Description:</label>
            <textarea name="description" id="description" required placeholder="Describe your issue in detail..."><?= htmlspecialchars($description ?? '') ?></textarea>
        </div>

        <button type="submit" name="submit_complaint"><i class="fas fa-paper-plane"></i> Submit Complaint</button>
    </form>

    <a href="status.php" class="status-link"><i class="fas fa-tasks"></i> View My Complaints</a>
</div>

</body>
</html>
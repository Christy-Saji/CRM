<?php
session_start();
require_once "../config/db.php";
require_once __DIR__ . "/update_client_status.php";

// Handle add product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, is_active) VALUES (?, ?, ?, TRUE)");
    $stmt->execute([$name, $desc, $price]);
    
    if ($stmt) {
        $_SESSION['message'] = "Product added successfully";
    } else {
        $_SESSION['error'] = "Failed to add product";
    }
    header("Location: product.php");
    exit();
}

// First, check if the product_id column exists in clients table
$columnExists = false;
try {
    $checkStmt = $conn->query("SHOW COLUMNS FROM clients LIKE 'product_id'");
    $columnExists = $checkStmt->rowCount() > 0;
} catch (PDOException $e) {
    // Column doesn't exist yet, we'll handle it
}

// Check if filter is set, default to showing active products
$showActive = !isset($_GET['status']) || $_GET['status'] !== 'inactive';

// Fetch all products with client count if column exists
$query = "SELECT p.*, COUNT(c.client_id) as client_count 
          FROM products p 
          LEFT JOIN clients c ON p.product_id = c.product_id ";

// Add status filter if needed
if ($showActive) {
    $query .= " WHERE p.is_active = TRUE ";
} else {
    $query .= " WHERE p.is_active = FALSE ";
}

$query .= " GROUP BY p.product_id 
             ORDER BY p.created_at DESC";

$stmt = $conn->query($query);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get counts for filter tabs
$counts = [
    'active' => $conn->query("SELECT COUNT(*) FROM products WHERE is_active = TRUE")->fetchColumn(),
    'inactive' => $conn->query("SELECT COUNT(*) FROM products WHERE is_active = FALSE")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
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
            max-width: 1000px;
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
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: 500;
        }
        
        .status-badge.active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-badge.inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .form-container {
            background-color: var(--background-color);
            padding: 25px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
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

        input[type="text"],
        textarea,
        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: white;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.1);
        }

        button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: var(--primary-color);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #3a5bef;
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

        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .filter-tab {
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            color: var(--secondary-color);
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-tab:hover, .filter-tab.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .badge {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 0.8em;
        }
        
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9em;
        }
        
        .deactivate-btn {
            background-color: #ffc107;
            color: #212529;
        }
        
        .deactivate-btn:hover {
            background-color: #e0a800;
            transform: translateY(-2px);
        }
        
        .reactivate-btn {
            background-color: #28a745;
            color: white;
        }
        
        .reactivate-btn:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        
        .delete-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background-color: var(--danger-color);
            color: white;
            padding: 8px 15px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #c82333;
            transform: translateY(-2px);
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
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Manage Products</h2>
        <a href="management.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
    </div>
    
    <!-- Status Filter Tabs -->
    <div class="filter-tabs" style="margin-bottom: 20px;">
        <a href="?status=active" class="filter-tab <?= $showActive ? 'active' : '' ?>">
            Active Products <span class="badge"><?= $counts['active'] ?></span>
        </a>
        <a href="?status=inactive" class="filter-tab <?= !$showActive ? 'active' : '' ?>">
            Inactive Products <span class="badge"><?= $counts['inactive'] ?></span>
        </a>
    </div>

    <div class="form-container">
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" id="name" name="name" required placeholder="Enter product name">
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="3" required placeholder="Enter product description"></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" required placeholder="Enter product price">
            </div>

            <button type="submit" name="add_product">
                <i class="fas fa-plus"></i> Add Product
            </button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Clients</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['product_id'] ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?php echo htmlspecialchars($product['description']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <?php if ($product['is_active']): ?>
                                <span class="status-badge active">Active</span>
                            <?php else: ?>
                                <span class="status-badge inactive">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $product['client_count']; ?> clients</td>
                        <td class="actions">
                            <div class="actions-container" style="display: flex; gap: 8px;">
                                <?php if ($product['is_active']): ?>
                                    <form method="post" action="update_client_status.php" style="margin: 0;">
                                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                        <button type="submit" name="deactivate_product" class="action-btn deactivate-btn" onclick="return confirm('This will deactivate the product and mark all associated clients as inactive. Continue?')">
                                            <i class="fas fa-ban"></i> Deactivate
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" action="update_client_status.php" style="margin: 0;">
                                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                        <button type="submit" name="reactivate_product" class="action-btn reactivate-btn" onclick="return confirm('This will reactivate the product. Continue?')">
                                            <i class="fas fa-check"></i> Reactivate
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <a href="?delete=<?= $product['product_id'] ?>" class="action-btn delete-btn" onclick="return confirm('This will delete the product and mark all associated clients as inactive. Continue?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

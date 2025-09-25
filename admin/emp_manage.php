<?php
session_start();
require_once "../config/db.php";

// Add employee
if (isset($_POST['add_employee'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $product_id = $_POST['product_id'];
    $password = $_POST['password']; // Plain string password

    $stmt = $conn->prepare("INSERT INTO employees (name, email, product_id, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $product_id, $password]);
}

// Delete employee
if (isset($_GET['delete'])) {
    $employee_id = $_GET['delete'];

    $conn->prepare("DELETE FROM assignments WHERE employee_id = ?")->execute([$employee_id]);
    $conn->prepare("DELETE FROM employees WHERE employee_id = ?")->execute([$employee_id]);

    header("Location: emp_manage.php");
    exit();
}

// Fetch all employees
$employees = $conn->query("SELECT e.*, p.name AS product_name 
                           FROM employees e 
                           LEFT JOIN products p ON e.product_id = p.product_id")->fetchAll(PDO::FETCH_ASSOC);

// Fetch products for dropdown
$products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employees</title>
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

        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: white;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.1);
        }

        button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: var(--success-color);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #218838;
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
        <h2>Employee Management</h2>
        <a href="management.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
    </div>

    <div class="form-container">
        <form method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required placeholder="Enter employee name">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required placeholder="Enter employee email">
            </div>

            <div class="form-group">
                <label for="product_id">Product:</label>
                <select id="product_id" name="product_id" required>
                    <option value="">--Select Product--</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['product_id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="text" id="password" name="password" required placeholder="Enter employee password">
            </div>

            <button type="submit" name="add_employee">
                <i class="fas fa-user-plus"></i> Add Employee
            </button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Product</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><?= $emp['employee_id'] ?></td>
                        <td><?= htmlspecialchars($emp['name']) ?></td>
                        <td><?= htmlspecialchars($emp['email']) ?></td>
                        <td><?= htmlspecialchars($emp['product_name']) ?></td>
                        <td>
                            <a class="delete-btn" href="?delete=<?= $emp['employee_id'] ?>" onclick="return confirm('Are you sure you want to delete this employee?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

<?php
session_start();

// Optional: Add authentication check
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: login.php");
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Dashboard</title>
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
            text-align: center;
            width: 90%;
            max-width: 500px;
            animation: fadeIn 0.5s ease;
        }

        h1 {
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 2em;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        .btn-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background-color: var(--primary-color);
            color: white;
            padding: 15px 25px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-link:hover {
            background-color: #3a5bef;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(74, 107, 255, 0.2);
        }

        .btn-link i {
            font-size: 1.2em;
        }

        .logout-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            background-color: var(--background-color);
        }

        .logout-link:hover {
            color: var(--danger-color);
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

        @media (max-width: 480px) {
            .container {
                padding: 30px;
            }

            h1 {
                font-size: 1.8em;
            }

            .btn-link {
                padding: 12px 20px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Management Dashboard</h1>

        <div class="nav-links">
            <a href="product.php" class="btn-link">
                <i class="fas fa-box"></i> Manage Products
            </a>
            <a href="dashboard.php" class="btn-link">
                <i class="fas fa-users"></i> Manage Clients
            </a>
            <a href="emp_manage.php" class="btn-link">
                <i class="fas fa-user-tie"></i> Manage Employees
            </a>
        </div>

        <a href="logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</body>
</html>

<?php
session_start();
require_once "../config/db.php";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hardcoded admin for now (or you can create an admin table)
    if ($email === 'admin@crm.com' && $password === 'admin123') {
        $_SESSION['admin'] = $email;
        header("Location: management.php");
        exit();
    } else {
        $error = "Invalid admin credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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
            --dark-bg: #1a1a1a;
            --dark-text: #ffffff;
            --dark-card: #2d2d2d;
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
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .container {
            max-width: 400px;
            width: 90%;
            background-color: var(--card-background);
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
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

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }

        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: white;
        }

        input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.1);
        }

        button {
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        button:hover {
            background-color: #3a5bef;
            transform: translateY(-2px);
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

        .links {
            margin-top: 25px;
            text-align: center;
        }

        .links p {
            margin-bottom: 10px;
            color: var(--text-color);
        }

        .links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .links a:hover {
            color: #3a5bef;
            transform: translateY(-2px);
        }

        .employee-login-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: var(--success-color);
            color: white;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .employee-login-btn:hover {
            background-color: #218838;
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

            .employee-login-btn {
                padding: 8px 16px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>

<a href="../employee/emp_login.php" class="employee-login-btn">
    <i class="fas fa-user-tie"></i> Employee Login
</a>

<div class="container">
    <div class="header">
        <h2>Admin Login</h2>
    </div>

    <?php if (isset($error)): ?>
        <div class="error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Enter admin email">
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required placeholder="Enter password">
        </div>

        <button type="submit" name="login">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </form>

    <div class="links">
        <p><a href="../index.php">
            <i class="fas fa-home"></i> Back to Home
        </a></p>
    </div>
</div>

</body>
</html>

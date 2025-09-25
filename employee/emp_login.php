<?php
session_start();
require_once "../config/db.php";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Verify employee credentials (plain text password)
    $stmt = $conn->prepare("SELECT * FROM employees WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($employee) {
        $_SESSION['employee_id'] = $employee['employee_id'];
        $_SESSION['employee_name'] = $employee['name'];
        header("Location: emp_dash.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login</title>
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
            max-width: 400px;
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
            text-align: center;
            margin-top: 20px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: var(--primary-color);
            transform: translateX(-5px);
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
        <h2>Employee Login</h2>
    </div>

    <?php if (isset($error)): ?>
        <div class="error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required placeholder="Enter your email">
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required placeholder="Enter your password">
        </div>

        <button type="submit" name="login">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </form>

    <div class="links">
        <a href="../admin/login.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </div>
</div>
</body>
</html>

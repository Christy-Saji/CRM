<?php
session_start();
require_once "../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($client && password_verify($password, $client['password'])) {
        $_SESSION['client_id'] = $client['client_id'];
        $_SESSION['client_name'] = $client['name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
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
            --border-radius: 16px;
            --box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 420px;
            width: 100%;
            background-color: var(--card-background);
            padding: 45px 35px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            animation: fadeIn 0.6s ease-in-out;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        h2 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 26px;
            margin-bottom: 5px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: var(--text-color);
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #dee2e6;
            border-radius: var(--border-radius);
            font-size: 14px;
            background-color: white;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.2);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        button:hover {
            background-color: #3756e2;
            transform: translateY(-2px);
        }

        .error {
            padding: 14px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            background-color: #f8d7da;
            color: #721c24;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #f5c6cb;
        }

        .error i {
            font-size: 1.1em;
        }

        .links {
            margin-top: 25px;
            text-align: center;
        }

        .links p {
            margin-bottom: 12px;
            color: var(--text-color);
            font-size: 14px;
        }

        .links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .links a:hover {
            color: #3756e2;
            transform: translateY(-1px);
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
                padding: 35px 25px;
            }

            h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Client Login</h2>
    </div>

    <?php if (isset($error)): ?>
        <div class="error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required placeholder="Enter your email">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Enter your password">
        </div>

        <button type="submit">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </form>

    <div class="links">
        <p>Not registered? 
            <a href="register.php"><i class="fas fa-user-plus"></i> Register here</a>
        </p>
        <p>
            <a href="../index.php"><i class="fas fa-home"></i> Back to Home</a>
        </p>
    </div>
</div>

</body>
</html>

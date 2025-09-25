<?php
require_once "../config/db.php";
$message = "";

// Fetch active products for the registration form
$products = [];
try {
    $stmt = $conn->query("SELECT * FROM products WHERE is_active = TRUE");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "<div class='error'>Error loading products. Please try again later.</div>";
}

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $product_id = !empty($_POST['product_id']) ? intval($_POST['product_id']) : null;

    $stmt = $conn->prepare("SELECT * FROM clients WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $message = "<div class='error'>Email already registered!</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO clients (name, email, password, product_id, status) VALUES (?, ?, ?, ?, 'active')");
        if ($stmt->execute([$name, $email, $password, $product_id])) {
            $message = "<div class='success'>Registration successful! <a href='login.php'>Login now</a></div>";
        } else {
            $message = "<div class='error'>Error registering. Please try again.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Registration</title>
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
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 500px;
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

        .success, .error {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .success i, .error i {
            font-size: 1.2em;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
            padding: 12px;
            background-color: var(--background-color);
            border-radius: var(--border-radius);
        }

        .btn-back:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
        }

        .btn-back i {
            margin-right: 8px;
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
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Register as a Client</h2>
    </div>

    <?php if ($message): ?>
        <div class="<?php echo strpos($message, 'success') !== false ? 'success' : 'error'; ?>">
            <i class="fas <?php echo strpos($message, 'success') !== false ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required placeholder="Enter your full name">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Enter your email address">
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <button type="submit" name="register">
            <i class="fas fa-user-plus"></i> Register
        </button>
    </form>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match. Please try again.');
            }
        });
    </script>

    <a href="../index.php" class="btn-back">
        <i class="fas fa-arrow-left"></i> Back to Home
    </a>
</div>

</body>
</html>

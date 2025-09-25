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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                        url('../images/bac.png') no-repeat center center fixed;
            background-size: cover;
            color: white;
            padding: 20px;
        }

        .container {
            background: rgba(210, 132, 132, 0.9);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin: 100px auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-weight: 500;
            display: block;
            margin-bottom: 10px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .msg {
            font-weight: bold;
            text-align: center;
            margin-top: 15px;
            color: green;
        }

        .error {
            font-weight: bold;
            text-align: center;
            margin-top: 15px;
            color: red;
        }

        .links {
            text-align: center;
            margin-top: 20px;
        }

        a {
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        footer {
            text-align: center;
            margin-top: 50px;
            color: #888;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Employee Login</h2>

    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit" name="login">Login</button>
    </form>

    <div class="links">
        <p><a href="management.php">To Dashboard</a></p>
    </div>
</div>

<footer>
    <p>&copy; 2025 CRM System. All rights reserved.</p>
</footer>

</body>
</html>

<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "crm_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>NextGen CRM Platform</title>
<style>
    /* Reset */
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }

    body {
        height: 100vh;
        overflow: hidden;
        background: linear-gradient(135deg, #0d47a1, #1976d2);
        position: relative;
        color: white;
    }

    /* Animated SVG Background */
    .bg-pattern {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        z-index: 0;
    }

    /* Top right login buttons */
    .login-buttons {
        position: absolute;
        top: 20px;
        right: 30px;
        display: flex;
        gap: 15px;
        z-index: 2;
    }
    .login-buttons a {
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 8px;
        background: rgba(255,255,255,0.15);
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
        transition: 0.3s ease;
        font-weight: bold;
    }
    .login-buttons a:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
    }

    /* Center heading */
    .center-content {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        z-index: 2;
    }
    .center-content h1 {
        font-size: 3rem;
        font-weight: bold;
        margin-bottom: 15px;
        text-shadow: 0px 2px 10px rgba(0,0,0,0.3);
    }
    .center-content p {
        font-size: 1.2rem;
        opacity: 0.85;
    }

</style>
</head>
<body>

<!-- Animated SVG Background -->
<svg class="bg-pattern" preserveAspectRatio="none" viewBox="0 0 800 400">
    <path fill="rgba(255,255,255,0.05)">
        <animate attributeName="d" dur="10s" repeatCount="indefinite"
            values="
            M0,100 C150,200 350,0 500,100 C650,200 850,0 1000,100 L1000,00 L0,0 Z;
            M0,50 C150,150 350,50 500,150 C650,250 850,50 1000,150 L1000,00 L0,0 Z;
            M0,100 C150,200 350,0 500,100 C650,200 850,0 1000,100 L1000,00 L0,0 Z
            " />
    </path>
</svg>

<!-- Login buttons -->
<div class="login-buttons">
    <a href="admin/login.php">Admin Login</a>
    <a href="client/login.php">Client Login</a>
</div>

<!-- Center heading -->
<div class="center-content">
    <h1>CRM PLATFORM</h1>
    <p>Smart • Scalable • Secure</p>
</div>

</body>
</html>
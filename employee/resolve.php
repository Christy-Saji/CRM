<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: emp_login.php");
    exit();
}

require_once "../config/db.php";

$complaint_id = $_GET['id'];

// First, check if the complaint is inactive or associated with an inactive product
$check_sql = "SELECT c.is_inactive, p.is_active as product_active 
             FROM complaints c 
             LEFT JOIN products p ON c.product_id = p.product_id 
             WHERE c.complaint_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->execute([$complaint_id]);
$complaint = $check_stmt->fetch(PDO::FETCH_ASSOC);

if ($complaint && ($complaint['is_inactive'] || !$complaint['product_active'])) {
    $message = "Cannot resolve: This complaint is inactive.";
    $message_type = "error";
} else {
    // Mark complaint as resolved
    $stmt = $conn->prepare("UPDATE complaints SET status = 'Resolved' WHERE complaint_id = ?");
    if ($stmt->execute([$complaint_id])) {
        // Update the product status to active if it was previously deactivated
        $update_product_sql = "UPDATE products SET is_active = TRUE WHERE product_id = (SELECT product_id FROM complaints WHERE complaint_id = ?)";
        $update_product_stmt = $conn->prepare($update_product_sql);
        $update_product_stmt->execute([$complaint_id]);
        
        $message = "Complaint resolved successfully! The associated product has been reactivated.";
        $message_type = "success";
    } else {
        $message = "Failed to resolve complaint.";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resolve Complaint</title>
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
            max-width: 500px;
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        h2 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
        }

        .message {
            padding: 20px;
            margin-bottom: 30px;
            border-radius: var(--border-radius);
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            padding: 12px 24px;
            border-radius: var(--border-radius);
            background-color: var(--background-color);
            transition: all 0.3s ease;
        }

        .back-link:hover {
            background-color: #e9ecef;
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
    <h2>Resolve Complaint</h2>

    <div class="message <?php echo $message_type; ?>">
        <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
        <?php echo $message; ?>
    </div>

    <a href="emp_dash.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>
</body>
</html>

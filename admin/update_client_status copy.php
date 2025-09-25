<?php
session_start();
require_once "../config/db.php";

/**
 * Check if a column exists in a table
 */
function columnExists($conn, $table, $column) {
    try {
        $stmt = $conn->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
        $stmt->execute([$column]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Update client status based on product status
 * @param PDO $conn Database connection
 * @param int $product_id The ID of the product
 * @param bool $is_active Whether the product is being activated or deactivated
 */
function updateClientStatusForProduct($conn, $product_id, $is_active) {
    // Only update if the columns exist
    if (columnExists($conn, 'clients', 'status') && columnExists($conn, 'clients', 'product_id')) {
        $status = $is_active ? 'active' : 'inactive';
        $stmt = $conn->prepare("UPDATE clients SET status = ? WHERE product_id = ?");
        return $stmt->execute([$status, $product_id]);
    }
    return true;
}

// Handle product deactivation
if (isset($_POST['deactivate_product'])) {
    $product_id = $_POST['product_id'];
    
    try {
        // Check if is_active column exists in products table
        if (columnExists($conn, 'products', 'is_active')) {
            $stmt = $conn->prepare("UPDATE products SET is_active = FALSE WHERE product_id = ?");
        } else {
            // Fallback if is_active column doesn't exist
            $stmt = $conn->prepare("UPDATE products SET 1=1 WHERE product_id = ?");
        }
        
        if ($stmt->execute([$product_id])) {
            // Update all clients with this product to inactive
            updateClientStatusForProduct($conn, $product_id, false);
            $_SESSION['message'] = "Product deactivated and associated clients marked as inactive";
        } else {
            $_SESSION['error'] = "Failed to deactivate product";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
    
    header("Location: product.php");
    exit();
}

// Handle product reactivation
if (isset($_POST['reactivate_product'])) {
    $product_id = $_POST['product_id'];
    
    try {
        // Check if is_active column exists in products table
        if (columnExists($conn, 'products', 'is_active')) {
            $stmt = $conn->prepare("UPDATE products SET is_active = TRUE WHERE product_id = ?");
        } else {
            // Fallback if is_active column doesn't exist
            $stmt = $conn->prepare("UPDATE products SET 1=1 WHERE product_id = ?");
        }
        
        if ($stmt->execute([$product_id])) {
            // Update all clients with this product to active
            updateClientStatusForProduct($conn, $product_id, true);
            $_SESSION['message'] = "Product reactivated and associated clients marked as active";
        } else {
            $_SESSION['error'] = "Failed to reactivate product";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
    
    header("Location: product.php?status=inactive");
    exit();
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    
    try {
        // Only try to update clients if the columns exist
        if (columnExists($conn, 'clients', 'product_id')) {
            if (columnExists($conn, 'clients', 'status')) {
                $stmt = $conn->prepare("UPDATE clients SET status = 'inactive', product_id = NULL WHERE product_id = ?");
            } else {
                $stmt = $conn->prepare("UPDATE clients SET product_id = NULL WHERE product_id = ?");
            }
            $stmt->execute([$product_id]);
        }
        
        // Then delete the product
        $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        if ($stmt->execute([$product_id])) {
            $_SESSION['message'] = "Product deleted and associated clients updated";
        } else {
            $_SESSION['error'] = "Failed to delete product";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
    
    header("Location: product.php");
    exit();
}

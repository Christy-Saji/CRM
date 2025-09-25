<?php
require_once "config/db.php";

try {
    // Start transaction
    $conn->beginTransaction();

    // Check if columns already exist
    $check = $conn->query("SHOW COLUMNS FROM complaints LIKE 'is_inactive'");
    if ($check->rowCount() == 0) {
        // Read and execute migration file
        $migrationFile = __DIR__ . '/migrations/20240505_add_inactive_to_complaints.sql';
        $sql = file_get_contents($migrationFile);
        
        if ($sql === false) {
            throw new Exception("Failed to read migration file");
        }
        
        $conn->exec($sql);
        echo "Migration completed successfully!\n";
    } else {
        echo "Migration already applied.\n";
    }
    
    // Commit transaction
    $conn->commit();
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "Migration failed: " . $e->getMessage() . "\n";
}

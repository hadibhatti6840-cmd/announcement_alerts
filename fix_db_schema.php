<?php
require_once 'config/db.php';

try {
    // Add missing columns to announcements table
    $pdo->exec("ALTER TABLE announcements ADD COLUMN attachment_path VARCHAR(255) NULL");
    echo "Added attachment_path successfully.\n";
}
catch (Exception $e) {
    echo "attachment_path might already exist: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE announcements ADD COLUMN attachment_name VARCHAR(255) NULL");
    echo "Added attachment_name successfully.\n";
}
catch (Exception $e) {
    echo "attachment_name might already exist: " . $e->getMessage() . "\n";
}
?>

<?php
require_once 'config/db.php';
try {
    $pdo->exec("ALTER TABLE announcements ADD COLUMN attachment_path VARCHAR(255) NULL, ADD COLUMN attachment_name VARCHAR(255) NULL");
    echo "Migration successful.";
}
catch (Exception $e) {
    echo "Migration failed or already applied: " . $e->getMessage();
}
unlink(__FILE__);
?>

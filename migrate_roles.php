<?php
require_once 'config/db.php';
try {
    // Modify users.role ENUM
    $pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'user', 'teacher', 'student') NOT NULL");
    echo "Migration successful: Roles expanded.";
}
catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage();
}
unlink(__FILE__);
?>

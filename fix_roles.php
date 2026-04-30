<?php
require_once 'config/db.php';

try {
    // 1. Expand the ENUM
    $pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'user', 'teacher', 'student') NOT NULL");
    echo "ENUM updated.\n";

    // 2. Fix empty roles
    // If there is any user who currently had an invalid enum resulting in empty string, we can't easily catch it with WHERE role = '' 
    // because ENUM empty str evaluates to index 0, or might be caught differently. Let's catch by email 'student@example.com' or others.
    $pdo->exec("UPDATE users SET role = 'student' WHERE email = 'student@example.com'");
    echo "Fixed roles.\n";
}
catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

<?php
require_once 'config/db.php';
$stmt = $pdo->query("SELECT user_id, name, email, role FROM users");
while ($row = $stmt->fetch()) {
    echo "ID: {$row['user_id']} | Name: {$row['name']} | Email: {$row['email']} | Role: {$row['role']}\n";
}
?>

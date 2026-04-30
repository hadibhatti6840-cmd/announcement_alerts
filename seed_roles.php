<?php
require_once 'config/db.php';

try {
    // Departments
    $dept = $pdo->query("SELECT department_id FROM departments LIMIT 1")->fetchColumn();
    if (!$dept) {
        $pdo->exec("INSERT INTO departments (department_name) VALUES ('Computer Science')");
        $dept = $pdo->lastInsertId();
    }

    $users = [
        ['name' => 'Default Teacher', 'email' => 'teacher@example.com', 'password' => 'teacher123', 'role' => 'teacher', 'dept' => $dept],
        ['name' => 'Default Student', 'email' => 'student@example.com', 'password' => 'student123', 'role' => 'student', 'dept' => $dept]
    ];

    foreach ($users as $u) {
        // Check if exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$u['email']]);
        if ($stmt->fetchColumn() == 0) {
            $hashed = password_hash($u['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, department_id, status) VALUES (?, ?, ?, ?, ?, 'active')");
            $stmt->execute([$u['name'], $u['email'], $hashed, $u['role'], $u['dept']]);
            echo "Created {$u['role']} account: {$u['email']}\n";
        }
        else {
            echo "Account {$u['email']} already exists.\n";
        }
    }
}
catch (Exception $e) {
    echo "Error seeding: " . $e->getMessage();
}
unlink(__FILE__);
?>

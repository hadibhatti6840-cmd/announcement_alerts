<?php
require_once 'config/db.php';

try {
    $depts = ['DBA', 'CS', 'IT', 'SE', 'ENGLISH'];
    foreach ($depts as $name) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM departments WHERE department_name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO departments (department_name) VALUES (?)");
            $stmt->execute([$name]);
            echo "Added department: $name\n";
        }
        else {
            echo "Department $name already exists.\n";
        }
    }
}
catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
unlink(__FILE__);
?>

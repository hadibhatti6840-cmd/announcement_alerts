<?php
require_once 'config/db.php';
$sql = file_get_contents('procedures.sql');
try {
    $pdo->exec($sql);
    echo "Procedures created successfully.";
}
catch (Exception $e) {
    echo "Error creating procedures: " . $e->getMessage();
}
unlink(__FILE__);
?>

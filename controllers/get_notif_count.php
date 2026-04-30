<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['count' => 0]);
    exit();
}

$userId = $_SESSION['user_id'];
$deptId = $_SESSION['department_id'];

$stmt = $pdo->prepare("CALL sp_GetUserSummary(?, ?)");
$stmt->execute([$userId, $deptId]);
$stats = $stmt->fetch();

echo json_encode(['count' => (int)$stats['unread_count'], 'active' => (int)$stats['active_announcements']]);
?>

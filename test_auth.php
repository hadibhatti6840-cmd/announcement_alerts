<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

echo "<h1>System Health Check</h1>";

// 1. File Check
$files = [
    'config/db.php',
    'includes/functions.php',
    'manage_users.php',
    'manage_departments.php',
    'manage_announcements.php',
    'manage_alerts.php',
    'notifications.php',
    'reports.php',
    'views/shared/header.php',
    'views/shared/sidebar.php',
    'views/shared/footer.php',
    'uploads/.htaccess'
];

echo "<h3>1. Files Status:</h3><ul>";
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<li><span style='color:green'>[OK]</span> $file</li>";
    }
    else {
        echo "<li><span style='color:red'>[MISSING]</span> $file</li>";
    }
}
echo "</ul>";

// 2. Database Check
echo "<h3>2. Database Status:</h3><ul>";
try {
    $tables = ['users', 'departments', 'announcements', 'alerts', 'notifications'];
    foreach ($tables as $table) {
        $pdo->query("SELECT 1 FROM $table LIMIT 1");
        echo "<li><span style='color:green'>[OK]</span> Table '$table' exists.</li>";
    }
}
catch (Exception $e) {
    echo "<li><span style='color:red'>[ERROR]</span> Database error: " . $e->getMessage() . "</li>";
}
echo "</ul>";

// 3. Stored Procedures Check
echo "<h3>3. Procedures Status:</h3><ul>";
try {
    $procs = ['sp_MarkAllNotificationsRead', 'sp_CleanupExpiredData', 'sp_GetUserSummary'];
    foreach ($procs as $proc) {
        $stmt = $pdo->query("SHOW PROCEDURE STATUS WHERE Name = '$proc'");
        if ($stmt->fetch()) {
            echo "<li><span style='color:green'>[OK]</span> Procedure '$proc' exists.</li>";
        }
        else {
            echo "<li><span style='color:red'>[MISSING]</span> Procedure '$proc'.</li>";
        }
    }
}
catch (Exception $e) {
    echo "<li><span style='color:red'>[ERROR]</span> Procedure check failed: " . $e->getMessage() . "</li>";
}
echo "</ul>";

echo "<hr><p>If anything is [MISSING] or [ERROR], please let me know and I will fix it immediately.</p>";
?>

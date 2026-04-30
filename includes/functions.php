<?php
session_start();

/**
 * Check if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Check if user has a specific role
 */
function hasRole($role)
{
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Redirect to login if not authenticated
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

/**
 * Redirect based on role if already logged in
 */
function redirectDashboard()
{
    if (isLoggedIn()) {
        switch ($_SESSION['role']) {
            case 'admin':
                header("Location: admin_dashboard.php");
                break;
            case 'manager':
                header("Location: manager_dashboard.php");
                break;
            case 'teacher':
                header("Location: teacher_dashboard.php");
                break;
            case 'student':
                header("Location: student_dashboard.php");
                break;
            default:
                header("Location: user_dashboard.php");
                break;
        }
        exit();
    }
}

/**
 * Sanitize input
 */
function sanitize($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

/**
 * Format Priority Badge
 */
function getPriorityBadge($priority)
{
    switch ($priority) {
        case 'high':
            return '<span class="badge badge-danger">High</span>';
        case 'medium':
            return '<span class="badge badge-warning">Medium</span>';
        default:
            return '<span class="badge badge-info">Low</span>';
    }
}

/**
 * Format Alert Type Badge
 */
function getAlertTypeBadge($type)
{
    return $type === 'emergency'
        ? '<span class="badge badge-danger">EMERGENCY</span>'
        : '<span class="badge badge-primary">INFO</span>';
}

/**
 * Trigger notifications for target audience
 */
function createNotifications($pdo, $announcementId, $alertId, $targetType, $targetId)
{
    $userIds = [];

    if ($targetType === 'all') {
        $stmt = $pdo->query("SELECT user_id FROM users WHERE status = 'active'");
        $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    elseif ($targetType === 'department') {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE department_id = ? AND status = 'active'");
        $stmt->execute([$targetId]);
        $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    elseif ($targetType === 'specific') {
        $userIds = [$targetId];
    }

    if (!empty($userIds)) {
        $sql = "INSERT INTO notifications (user_id, announcement_id, alert_id, status) VALUES ";
        $placeholders = [];
        $values = [];
        foreach ($userIds as $userId) {
            $placeholders[] = "(?, ?, ?, 'unread')";
            $values[] = $userId;
            $values[] = $announcementId;
            $values[] = $alertId;
        }
        $sql .= implode(', ', $placeholders);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
    }
}
?>

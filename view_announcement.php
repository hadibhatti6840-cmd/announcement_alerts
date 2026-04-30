<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];
$userId = $_SESSION['user_id'];
$deptId = $_SESSION['department_id'];

// If notification ID passed, mark it as read
if (isset($_GET['nid'])) {
    $nid = (int)$_GET['nid'];
    $stmt = $pdo->prepare("UPDATE notifications SET status = 'read' WHERE notification_id = ? AND user_id = ?");
    $stmt->execute([$nid, $userId]);
}

// Fetch announcement
$stmt = $pdo->prepare("
    SELECT a.*, u.name as author, d.department_name 
    FROM announcements a 
    JOIN users u ON a.created_by = u.user_id 
    LEFT JOIN departments d ON u.department_id = d.department_id
    WHERE a.announcement_id = ?
");
$stmt->execute([$id]);
$ann = $stmt->fetch();

if (!$ann) {
    die("Announcement not found.");
}

// Security Check: Is user allowed to see this?
$allowed = false;
if ($ann['target_type'] == 'all')
    $allowed = true;
if ($ann['target_type'] == 'department' && $ann['target_id'] == $deptId)
    $allowed = true;
if ($ann['target_type'] == 'specific' && $ann['target_id'] == $userId)
    $allowed = true;
if (hasRole('admin') || $ann['created_by'] == $userId)
    $allowed = true;

if (!$allowed) {
    die("Access denied.");
}

$pageTitle = "View Announcement";
$activeMenu = "notifications";

include 'views/shared/header.php';
include 'views/shared/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Announcement Details</h1>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo htmlspecialchars($ann['title']); ?></h3>
                        <div class="card-tools">
                            <?php echo getPriorityBadge($ann['priority']); ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between text-muted mb-3 border-bottom pb-2">
                            <span><i class="fas fa-user"></i> By: <?php echo htmlspecialchars($ann['author']); ?> (<?php echo htmlspecialchars($ann['department_name'] ?? 'N/A'); ?>)</span>
                            <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y H:i', strtotime($ann['created_at'])); ?></span>
                        </div>
                        
                        <div class="announcement-content py-3">
                            <?php echo nl2br(htmlspecialchars($ann['description'])); ?>
                        </div>

                        <?php if ($ann['attachment_path']): ?>
                            <div class="attachment-section mt-4 p-3 bg-light border rounded">
                                <h6><i class="fas fa-paperclip"></i> Attachment:</h6>
                                <a href="uploads/<?php echo $ann['attachment_path']; ?>" target="_blank" class="btn btn-sm btn-info shadow-sm">
                                    <i class="fas fa-download"></i> <?php echo htmlspecialchars($ann['attachment_name']); ?>
                                </a>
                            </div>
                        <?php
endif; ?>

                        <?php if ($ann['expiry_date']): ?>
                            <div class="mt-4 text-sm text-danger">
                                <i class="fas fa-clock"></i> Expires on: <?php echo date('M d, Y', strtotime($ann['expiry_date'])); ?>
                            </div>
                        <?php
endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="notifications.php" class="btn btn-default">Back to Notifications</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </section>
</div>

<?php include 'views/shared/footer.php'; ?>

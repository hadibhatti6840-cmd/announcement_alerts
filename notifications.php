<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();

$pageTitle = "My Notifications";
$activeMenu = "notifications";
$userId = $_SESSION['user_id'];

// Mark specific as read
if (isset($_GET['read'])) {
    $nid = (int)$_GET['read'];
    $stmt = $pdo->prepare("UPDATE notifications SET status = 'read' WHERE notification_id = ? AND user_id = ?");
    $stmt->execute([$nid, $userId]);
}

// Mark all as read
if (isset($_GET['read_all'])) {
    $stmt = $pdo->prepare("UPDATE notifications SET status = 'read' WHERE user_id = ?");
    $stmt->execute([$userId]);
}

// Fetch notifications with details
$stmt = $pdo->prepare("
    SELECT n.*, a.title as ann_title, al.message as alert_msg, al.alert_type
    FROM notifications n
    LEFT JOIN announcements a ON n.announcement_id = a.announcement_id
    LEFT JOIN alerts al ON n.alert_id = al.alert_id
    WHERE n.user_id = ?
    ORDER BY n.sent_at DESC
");
$stmt->execute([$userId]);
$notifications = $stmt->fetchAll();

include 'views/shared/header.php';
include 'views/shared/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Notifications</h1>
        </div>
        <div class="col-sm-6 text-right">
          <a href="notifications.php?read_all=1" class="btn btn-sm btn-outline-secondary">Mark all as read</a>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <?php if (empty($notifications)): ?>
                    <div class="alert alert-info">No notifications found.</div>
                <?php
else: ?>
                    <div class="timeline">
                        <?php
    $lastDate = "";
    foreach ($notifications as $n):
        $currentDate = date('Y-m-d', strtotime($n['sent_at']));
        if ($currentDate != $lastDate):
            $lastDate = $currentDate;
?>
                            <div class="time-label">
                                <span class="bg-blue"><?php echo date('d M. Y', strtotime($n['sent_at'])); ?></span>
                            </div>
                        <?php
        endif; ?>

                        <div>
                            <?php if ($n['announcement_id']): ?>
                                <i class="fas fa-bullhorn <?php echo $n['status'] == 'unread' ? 'bg-warning' : 'bg-gray'; ?>"></i>
                                <div class="timeline-item shadow-sm">
                                    <span class="time"><i class="fas fa-clock"></i> <?php echo date('H:i', strtotime($n['sent_at'])); ?></span>
                                    <h3 class="timeline-header">
                                        <a href="view_announcement.php?id=<?php echo $n['announcement_id']; ?>&nid=<?php echo $n['notification_id']; ?>">
                                            <?php echo htmlspecialchars($n['ann_title']); ?>
                                        </a> (Announcement)
                                    </h3>
                                    <?php if ($n['status'] == 'unread'): ?>
                                        <div class="timeline-footer">
                                            <a href="notifications.php?read=<?php echo $n['notification_id']; ?>" class="btn btn-xs btn-primary">Mark as read</a>
                                        </div>
                                    <?php
            endif; ?>
                                </div>
                            <?php
        else: ?>
                                <i class="fas fa-exclamation-triangle <?php echo $n['alert_type'] == 'emergency' ? 'bg-danger' : 'bg-info'; ?>"></i>
                                <div class="timeline-item shadow-sm">
                                    <span class="time"><i class="fas fa-clock"></i> <?php echo date('H:i', strtotime($n['sent_at'])); ?></span>
                                    <h3 class="timeline-header no-border">
                                        <strong><?php echo $n['alert_type'] == 'emergency' ? '[EMERGENCY]' : '[INFO]'; ?></strong> 
                                        <?php echo htmlspecialchars($n['alert_msg']); ?>
                                    </h3>
                                    <?php if ($n['status'] == 'unread'): ?>
                                        <div class="timeline-footer">
                                            <a href="notifications.php?read=<?php echo $n['notification_id']; ?>" class="btn btn-xs btn-primary">Mark as read</a>
                                        </div>
                                    <?php
            endif; ?>
                                </div>
                            <?php
        endif; ?>
                        </div>
                        <?php
    endforeach; ?>
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                <?php
endif; ?>
            </div>
        </div>
    </div>
  </section>
</div>

<?php include 'views/shared/footer.php'; ?>

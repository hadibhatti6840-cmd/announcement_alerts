<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();

$pageTitle = "User Dashboard";
$activeMenu = "dashboard";

$userId = $_SESSION['user_id'];
$deptId = $_SESSION['department_id'];

// Get recent announcements for this user
$stmt = $pdo->prepare("
    SELECT * FROM announcements 
    WHERE (target_type = 'all') 
       OR (target_type = 'department' AND target_id = ?) 
       OR (target_type = 'specific' AND target_id = ?)
    ORDER BY created_at DESC LIMIT 5
");
$stmt->execute([$deptId, $userId]);
$announcements = $stmt->fetchAll();

include 'views/shared/header.php';
include 'views/shared/sidebar.php';
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">My Dashboard</h1>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary card-outline shadow">
                    <div class="card-header">
                        <h3 class="card-title">Recent Announcements</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="products-list product-list-in-card pl-2 pr-2">
                            <?php if (empty($announcements)): ?>
                                <li class="item text-center p-4">No recent announcements found.</li>
                            <?php
else: ?>
                                <?php foreach ($announcements as $row): ?>
                                <li class="item">
                                    <div class="product-info ml-2">
                                        <a href="view_announcement.php?id=<?php echo $row['announcement_id']; ?>" class="product-title">
                                            <?php echo htmlspecialchars($row['title']); ?>
                                            <?php echo getPriorityBadge($row['priority']); ?>
                                        </a>
                                        <span class="product-description">
                                            <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                        </span>
                                    </div>
                                </li>
                                <?php
    endforeach; ?>
                            <?php
endif; ?>
                        </ul>
                    </div>
                    <div class="card-footer text-center">
                        <a href="notifications.php" class="uppercase">View All Notifications</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box mb-3 bg-warning shadow">
                    <span class="info-box-icon"><i class="fas fa-bell"></i></span>
                    <div class="info-box-content text-dark">
                        <span class="info-box-text">Unread Notifications</span>
                        <span class="info-box-number">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </section>
</div>

<?php include 'views/shared/footer.php'; ?>

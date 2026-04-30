<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();

if (!hasRole('admin')) {
  redirectDashboard();
}

$pageTitle = "Admin Dashboard";
$activeMenu = "dashboard";

// Get stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalDepts = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();
$totalAnnouncements = $pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn();
$totalAlerts = $pdo->query("SELECT COUNT(*) FROM alerts")->fetchColumn();

include 'views/shared/header.php';
include 'views/shared/sidebar.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Admin Dashboard</h1>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info shadow">
            <div class="inner">
              <h3><?php echo $totalUsers; ?></h3>
              <p>Total Users</p>
            </div>
            <div class="icon">
              <i class="fas fa-users"></i>
            </div>
            <a href="manage_users.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success shadow">
            <div class="inner">
              <h3><?php echo $totalDepts; ?></h3>
              <p>Departments</p>
            </div>
            <div class="icon">
              <i class="fas fa-building"></i>
            </div>
            <a href="manage_departments.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning shadow">
            <div class="inner">
              <h3><?php echo $totalAnnouncements; ?></h3>
              <p>Announcements</p>
            </div>
            <div class="icon">
              <i class="fas fa-bullhorn"></i>
            </div>
            <a href="manage_announcements.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger shadow">
            <div class="inner">
              <h3><?php echo $totalAlerts; ?></h3>
              <p>Instant Alerts</p>
            </div>
            <div class="icon">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
            <a href="manage_alerts.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-8">
            <div class="card card-outline card-primary shadow">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list mr-1"></i> Recent System Activity</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Item</th>
                                <th>User</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
// Fetch last 10 activities (Announcements & Alerts)
$activities = $pdo->query("
                                (SELECT 'Announcement' as type, title as item, u.name as user, a.created_at FROM announcements a JOIN users u ON a.created_by = u.user_id)
                                UNION
                                (SELECT 'Alert' as type, SUBSTRING(message, 1, 30) as item, u.name as user, al.created_at FROM alerts al JOIN users u ON al.created_by = u.user_id)
                                ORDER BY created_at DESC LIMIT 10
                            ")->fetchAll();
foreach ($activities as $act):
?>
                            <tr>
                                <td><span class="badge badge-<?php echo $act['type'] == 'Alert' ? 'danger' : 'success'; ?>"><?php echo $act['type']; ?></span></td>
                                <td><?php echo htmlspecialchars($act['item']); ?>...</td>
                                <td><?php echo htmlspecialchars($act['user']); ?></td>
                                <td><small><?php echo date('M d, H:i', strtotime($act['created_at'])); ?></small></td>
                            </tr>
                            <?php
endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-outline card-info shadow">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <a href="manage_announcements.php" class="btn btn-primary btn-block mb-2">New Announcement</a>
                    <a href="manage_alerts.php" class="btn btn-danger btn-block mb-2">Trigger Quick Alert</a>
                    <a href="reports.php" class="btn btn-info btn-block">View System Analytics</a>
                </div>
            </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'views/shared/footer.php'; ?>

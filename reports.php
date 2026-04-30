<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();

if (!hasRole('admin') && !hasRole('manager') && !hasRole('teacher')) {
  redirectDashboard();
}

$pageTitle = "System Analytics & Reports";
$activeMenu = "reports";

// Stats
$totalAnn = $pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn();
$totalAlerts = $pdo->query("SELECT COUNT(*) FROM alerts")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$readNotif = $pdo->query("SELECT COUNT(*) FROM notifications WHERE status = 'read'")->fetchColumn();
$unreadNotif = $pdo->query("SELECT COUNT(*) FROM notifications WHERE status = 'unread'")->fetchColumn();

// Announcements by priority
$priorityStats = $pdo->query("SELECT priority, COUNT(*) as count FROM announcements GROUP BY priority")->fetchAll();
$priorities = [];
$priorityCounts = [];
foreach ($priorityStats as $s) {
  $priorities[] = ucfirst($s['priority']);
  $priorityCounts[] = $s['count'];
}

// Activity by date (last 7 days)
$activityStats = $pdo->query("
    SELECT DATE(created_at) as date, COUNT(*) as count 
    FROM announcements 
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
    GROUP BY DATE(created_at)
")->fetchAll();
$dates = [];
$activityCounts = [];
foreach ($activityStats as $s) {
  $dates[] = date('M d', strtotime($s['date']));
  $activityCounts[] = $s['count'];
}

include 'views/shared/header.php';
include 'views/shared/sidebar.php';
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">System Reports</h1>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <!-- Info boxes -->
      <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box shadow">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-bullhorn"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Announcements</span>
              <span class="info-box-number"><?php echo $totalAnn; ?></span>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3 shadow">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Alerts</span>
              <span class="info-box-number"><?php echo $totalAlerts; ?></span>
            </div>
          </div>
        </div>
        <div class="clearfix hidden-md-up"></div>
        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3 shadow">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Users</span>
              <span class="info-box-number"><?php echo $totalUsers; ?></span>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3 shadow">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-bell"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Engagement (Read)</span>
              <span class="info-box-number"><?php echo $totalAnn > 0 ? round(($readNotif / ($readNotif + $unreadNotif)) * 100) : 0; ?>%</span>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
            <div class="card card-indigo shadow">
              <div class="card-header border-0">
                <h3 class="card-title">Announcements by Priority</h3>
              </div>
              <div class="card-body">
                <canvas id="priorityChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-primary shadow">
              <div class="card-header border-0">
                <h3 class="card-title">Daily Activity (Last 7 Days)</h3>
              </div>
              <div class="card-body">
                <canvas id="activityChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
            </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'views/shared/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(function () {
    // Priority Chart
    new Chart($('#priorityChart').get(0).getContext('2d'), {
      type: 'pie',
      data: {
        labels: <?php echo json_encode($priorities); ?>,
        datasets: [{
          data: <?php echo json_encode($priorityCounts); ?>,
          backgroundColor : ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
        }]
      },
      options: { maintainAspectRatio: false, responsive: true }
    });

    // Activity Chart
    new Chart($('#activityChart').get(0).getContext('2d'), {
      type: 'line',
      data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [{
          label: 'Announcements',
          data: <?php echo json_encode($activityCounts); ?>,
          borderColor: '#007bff',
          fill: true,
          backgroundColor: 'rgba(0,123,255,0.1)'
        }]
      },
      options: { maintainAspectRatio: false, responsive: true }
    });
});
</script>

<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();

if (!hasRole('manager')) {
  redirectDashboard();
}

$pageTitle = "Manager Dashboard";
$activeMenu = "dashboard";

// Stats for manager's department
$deptId = $_SESSION['department_id'];
$totalAnnouncements = $pdo->prepare("SELECT COUNT(*) FROM announcements WHERE (target_type='department' AND target_id=?) OR target_type='all'");
$totalAnnouncements->execute([$deptId]);
$totalAnnouncements = $totalAnnouncements->fetchColumn();

include 'views/shared/header.php';
include 'views/shared/sidebar.php';
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Manager Dashboard</h1>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-4 col-12">
          <div class="small-box bg-info shadow">
            <div class="inner">
              <h3><?php echo $totalAnnouncements; ?></h3>
              <p>Dept. Announcements</p>
            </div>
            <div class="icon">
              <i class="fas fa-bullhorn"></i>
            </div>
            <a href="manage_announcements.php" class="small-box-footer">View List <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <div class="card card-outline card-primary shadow">
        <div class="card-header">
            <h3 class="card-title">Manager Portal</h3>
        </div>
        <div class="card-body">
            You can create and manage announcements for your department here.
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'views/shared/footer.php'; ?>

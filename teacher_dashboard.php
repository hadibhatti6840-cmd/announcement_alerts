<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();

if (!hasRole('teacher') && !hasRole('admin')) {
    redirectDashboard();
}

$pageTitle = "Teacher Dashboard";
$activeMenu = "dashboard";

// Stats for teacher's department
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
          <h1 class="m-0">Teacher Portal</h1>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info shadow">
            <div class="inner">
              <h3><?php echo $totalAnnouncements; ?></h3>
              <p>Dept. Announcements</p>
            </div>
            <div class="icon">
              <i class="fas fa-bullhorn"></i>
            </div>
            <a href="manage_announcements.php" class="small-box-footer">Go to List <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <div class="card card-outline card-primary shadow">
        <div class="card-header">
            <h3 class="card-title">Welcome, Professor <?php echo $_SESSION['name']; ?></h3>
        </div>
        <div class="card-body">
            You can manage announcements for your students and department here. Always check the priority levels before sending alerts.
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'views/shared/footer.php'; ?>

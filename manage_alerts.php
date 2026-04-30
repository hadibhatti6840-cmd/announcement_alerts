<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();

if (!hasRole('admin') && !hasRole('manager') && !hasRole('teacher')) {
    header("Location: index.php");
    exit();
}

$pageTitle = "Manage Instant Alerts";
$activeMenu = "alerts";
$success = "";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM alerts WHERE alert_id = ?");
    if ($stmt->execute([$id])) {
        $success = "Alert deleted successfully.";
    }
}

// Handle Create
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = sanitize($_POST['message']);
    $alertType = $_POST['alert_type'];
    $targetType = $_POST['target_type'];
    $targetId = null;

    if ($targetType === 'department') {
        $targetId = $_POST['department_id'];
    }

    $stmt = $pdo->prepare("INSERT INTO alerts (message, created_by, alert_type) VALUES (?, ?, ?)");
    $stmt->execute([$message, $_SESSION['user_id'], $alertType]);
    $newId = $pdo->lastInsertId();

    // Trigger Notifications
    createNotifications($pdo, null, $newId, $targetType, $targetId);

    $success = "Alert sent and notifications triggered.";
}

// Fetch list
if (hasRole('admin')) {
    $alerts = $pdo->query("SELECT a.*, u.name as author FROM alerts a JOIN users u ON a.created_by = u.user_id ORDER BY a.created_at DESC")->fetchAll();
}
else {
    $alerts = $pdo->prepare("SELECT a.*, u.name as author FROM alerts a JOIN users u ON a.created_by = u.user_id WHERE a.created_by = ? ORDER BY a.created_at DESC");
    $alerts->execute([$_SESSION['user_id']]);
    $alerts = $alerts->fetchAll();
}

$departments = $pdo->query("SELECT * FROM departments ORDER BY department_name ASC")->fetchAll();

include 'views/shared/header.php';
include 'views/shared/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Instant Alerts</h1>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <?php if ($success): ?>
        <div class="alert alert-success mt-2"><?php echo $success; ?></div>
      <?php
endif; ?>

      <div class="row">
        <div class="col-md-5">
            <div class="card card-danger shadow">
                <div class="card-header">
                    <h3 class="card-title">Send New Alert</h3>
                </div>
                <form action="manage_alerts.php" method="post">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Alert Message</label>
                            <textarea name="message" class="form-control" rows="3" placeholder="Enter emergency or info message..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Alert Type</label>
                            <select name="alert_type" class="form-control">
                                <option value="info">Informational</option>
                                <option value="emergency">Emergency</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Target Audience</label>
                            <select name="target_type" id="alertTargetType" class="form-control" required>
                                <option value="all">Broadcast to All</option>
                                <option value="department">Select Department</option>
                            </select>
                        </div>
                        <div id="alertDeptDiv" class="form-group d-none">
                            <label>Department</label>
                            <select name="department_id" class="form-control">
                                <?php foreach ($departments as $d): ?>
                                    <option value="<?php echo $d['department_id']; ?>"><?php echo htmlspecialchars($d['department_name']); ?></option>
                                <?php
endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-danger btn-block">SEND NOW</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="card-title">Recent Alerts sent</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Message</th>
                                <th>Type</th>
                                <th>Sent By</th>
                                <th>Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alerts as $row): ?>
                            <tr>
                                <td title="<?php echo htmlspecialchars($row['message']); ?>">
                                    <?php echo substr(htmlspecialchars($row['message']), 0, 40) . '...'; ?>
                                </td>
                                <td><?php echo getAlertTypeBadge($row['alert_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['author']); ?></td>
                                <td><small><?php echo date('M d, H:i', strtotime($row['created_at'])); ?></small></td>
                                <td>
                                    <a href="manage_alerts.php?delete=<?php echo $row['alert_id']; ?>" class="btn btn-xs btn-outline-danger" onclick="return confirm('Delete this alert log?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php
endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'views/shared/footer.php'; ?>

<script>
$(document).ready(function() {
    $('#alertTargetType').change(function() {
        if ($(this).val() === 'department') {
            $('#alertDeptDiv').removeClass('d-none');
        } else {
            $('#alertDeptDiv').addClass('d-none');
        }
    });
});
</script>

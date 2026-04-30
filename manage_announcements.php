<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();

if (!hasRole('admin') && !hasRole('manager') && !hasRole('teacher') && !hasRole('student')) {
  header("Location: index.php");
  exit();
}

$pageTitle = "Manage Announcements";
$activeMenu = "announcements";
$success = "";
$error = "";

// Handle Delete
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $stmt = $pdo->prepare("DELETE FROM announcements WHERE announcement_id = ?");
  if ($stmt->execute([$id])) {
    $success = "Announcement deleted successfully.";
  }
}

// Handle Create/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = sanitize($_POST['title']);
  $description = $_POST['description']; // Allow HTML if needed, sanitize output instead
  $priority = $_POST['priority'];
  $expiryDate = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
  $targetType = $_POST['target_type'];
  $targetId = null;
  $attachmentPath = null;
  $attachmentName = null;

  if ($targetType === 'department') {
    $targetId = $_POST['department_id'];
  }
  elseif ($targetType === 'specific') {
    $targetId = $_POST['specific_user_id'];
  }

  // Handle File Upload
  if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['attachment']['tmp_name'];
    $fileName = $_FILES['attachment']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
    $uploadFileDir = './uploads/';
    $dest_path = $uploadFileDir . $newFileName;

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'zip'];
    if (in_array($fileExtension, $allowedExtensions)) {
      if (move_uploaded_file($fileTmpPath, $dest_path)) {
        $attachmentPath = $newFileName;
        $attachmentName = $fileName;
      }
    }
  }

  $id = isset($_POST['announcement_id']) ? (int)$_POST['announcement_id'] : 0;

  if ($id > 0) {
    $sql = "UPDATE announcements SET title = ?, description = ?, priority = ?, expiry_date = ?, target_type = ?, target_id = ? ";
    $params = [$title, $description, $priority, $expiryDate, $targetType, $targetId];

    if ($attachmentPath) {
      $sql .= ", attachment_path = ?, attachment_name = ? ";
      $params[] = $attachmentPath;
      $params[] = $attachmentName;
    }

    $sql .= " WHERE announcement_id = ?";
    $params[] = $id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $success = "Announcement updated successfully.";
  }
  else {
    $stmt = $pdo->prepare("INSERT INTO announcements (title, description, created_by, expiry_date, priority, target_type, target_id, attachment_path, attachment_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $_SESSION['user_id'], $expiryDate, $priority, $targetType, $targetId, $attachmentPath, $attachmentName]);
    $newId = $pdo->lastInsertId();

    // Trigger Notifications
    createNotifications($pdo, $newId, null, $targetType, $targetId);

    $success = "Announcement created and notifications sent.";
  }
}

// Fetch list
if (hasRole('admin')) {
  $announcements = $pdo->query("SELECT a.*, u.name as author FROM announcements a JOIN users u ON a.created_by = u.user_id ORDER BY a.created_at DESC")->fetchAll();
}
else if (hasRole('manager') || hasRole('teacher')) {
  // Managers and teachers only see their own
  $announcements = $pdo->prepare("SELECT a.*, u.name as author FROM announcements a JOIN users u ON a.created_by = u.user_id WHERE a.created_by = ? ORDER BY a.created_at DESC");
  $announcements->execute([$_SESSION['user_id']]);
  $announcements = $announcements->fetchAll();
}
else {
  // Students see announcements targeted to them
  $announcements = $pdo->prepare("
      SELECT a.*, u.name as author FROM announcements a 
      JOIN users u ON a.created_by = u.user_id 
      WHERE target_type = 'all' 
         OR (target_type = 'department' AND target_id = ?) 
         OR (target_type = 'specific' AND target_id = ?)
      ORDER BY a.created_at DESC
  ");
  $announcements->execute([$_SESSION['department_id'], $_SESSION['user_id']]);
  $announcements = $announcements->fetchAll();
}

$departments = $pdo->query("SELECT * FROM departments ORDER BY department_name ASC")->fetchAll();
$usersList = $pdo->query("SELECT user_id, name FROM users WHERE status = 'active' ORDER BY name ASC")->fetchAll();

include 'views/shared/header.php';
include 'views/shared/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Manage Announcements</h1>
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
        <div class="col-md-12">
          <div class="card shadow">
            <div class="card-header">
              <h3 class="card-title">Announcements</h3>
              <div class="card-tools">
                <?php if (!hasRole('student')): ?>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#announcementModal">
                  Create New Announcement
                </button>
                <?php
endif; ?>
              </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Target</th>
                            <th>Priority</th>
                            <th>Created By</th>
                            <th>Expires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($announcements as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td>
                                <?php
  echo ucfirst($row['target_type']);
  if ($row['target_type'] != 'all')
    echo " (ID: " . $row['target_id'] . ")";
?>
                            </td>
                            <td><?php echo getPriorityBadge($row['priority']); ?></td>
                            <td><?php echo htmlspecialchars($row['author']); ?></td>
                            <td><?php echo $row['expiry_date'] ?: 'Never'; ?></td>
                            <td>
                                <?php if (!hasRole('student')): ?>
                                  <button class="btn btn-xs btn-info edit-ann" data-data='<?php echo json_encode($row); ?>'><i class="fas fa-edit"></i></button>
                                  <a href="manage_announcements.php?delete=<?php echo $row['announcement_id']; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                                <?php
  else: ?>
                                  <a href="view_announcement.php?id=<?php echo $row['announcement_id']; ?>" class="btn btn-xs btn-primary"><i class="fas fa-eye"></i></a>
                                <?php
  endif; ?>
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

<!-- Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Create Announcement</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="manage_announcements.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="announcement_id" id="annID">
        <div class="modal-body">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" id="annTitle" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="annDesc" class="form-control" rows="4" required></textarea>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Priority</label>
                        <select name="priority" id="annPriority" class="form-control">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="date" name="expiry_date" id="annExpiry" class="form-control">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Attachment (Optional)</label>
                <div class="custom-file">
                    <input type="file" name="attachment" class="custom-file-input" id="annFile">
                    <label class="custom-file-label" for="annFile">Choose file</label>
                </div>
                <small class="text-muted">Allowed: JPG, PNG, PDF, DOC, ZIP</small>
            </div>
            <div class="form-group">
                <label>Target Audience</label>
                <select name="target_type" id="targetType" class="form-control" required>
                    <option value="all">All Users</option>
                    <option value="department">By Department</option>
                    <option value="specific">Specific User</option>
                </select>
            </div>
            <div id="targetIdDept" class="form-group d-none">
                <label>Select Department</label>
                <select name="department_id" id="deptSelect" class="form-control">
                    <?php foreach ($departments as $d): ?>
                        <option value="<?php echo $d['department_id']; ?>"><?php echo htmlspecialchars($d['department_name']); ?></option>
                    <?php
endforeach; ?>
                </select>
            </div>
            <div id="targetIdUser" class="form-group d-none">
                <label>Select User</label>
                <select name="specific_user_id" id="userSelect" class="form-control">
                    <?php foreach ($usersList as $u): ?>
                        <option value="<?php echo $u['user_id']; ?>"><?php echo htmlspecialchars($u['name']); ?></option>
                    <?php
endforeach; ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Announcement</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'views/shared/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<script>
$(document).ready(function() {
    bsCustomFileInput.init();
    
    $('#targetType').change(function() {
        var val = $(this).val();
        $('#targetIdDept').addClass('d-none');
        $('#targetIdUser').addClass('d-none');
        if (val === 'department') $('#targetIdDept').removeClass('d-none');
        if (val === 'specific') $('#targetIdUser').removeClass('d-none');
    });

    $('.edit-ann').click(function() {
        var data = $(this).data('data');
        $('#annID').val(data.announcement_id);
        $('#annTitle').val(data.title);
        $('#annDesc').val(data.description);
        $('#annPriority').val(data.priority);
        $('#annExpiry').val(data.expiry_date);
        $('#targetType').val(data.target_type).trigger('change');
        
        if (data.target_type === 'department') $('#deptSelect').val(data.target_id);
        if (data.target_type === 'specific') $('#userSelect').val(data.target_id);

        $('#modalTitle').text('Edit Announcement');
        $('#announcementModal').modal('show');
    });

    $('#announcementModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $('#annID').val('');
        $('#targetIdDept, #targetIdUser').addClass('d-none');
        $('#modalTitle').text('Create Announcement');
    });
});
</script>

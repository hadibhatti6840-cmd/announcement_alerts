<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();

if (!hasRole('admin')) {
  header("Location: index.php");
  exit();
}

$pageTitle = "Manage Users";
$activeMenu = "users";
$success = "";
$error = "";

// Handle Delete
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
  if ($stmt->execute([$id])) {
    $success = "User deleted successfully.";
  }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = sanitize($_POST['name']);
  $email = sanitize($_POST['email']);
  $role = $_POST['role'];
  $deptId = $_POST['department_id'] ? (int)$_POST['department_id'] : null;
  $status = $_POST['status'];
  $id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

  if ($id > 0) {
    $sql = "UPDATE users SET name = ?, email = ?, role = ?, department_id = ?, status = ? WHERE user_id = ?";
    $params = [$name, $email, $role, $deptId, $status, $id];

    if (!empty($_POST['password'])) {
      $sql = "UPDATE users SET name = ?, email = ?, role = ?, department_id = ?, status = ?, password = ? WHERE user_id = ?";
      $params = [$name, $email, $role, $deptId, $status, password_hash($_POST['password'], PASSWORD_DEFAULT), $id];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $success = "User updated successfully.";
  }
  else {
    $password = password_hash($_POST['password'] ?: '123456', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, department_id, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password, $role, $deptId, $status]);
    $success = "User added successfully.";
  }
}

$users = $pdo->query("SELECT u.*, d.department_name FROM users u LEFT JOIN departments d ON u.department_id = d.department_id ORDER BY u.name ASC")->fetchAll();
$departments = $pdo->query("SELECT * FROM departments ORDER BY department_name ASC")->fetchAll();

include 'views/shared/header.php';
include 'views/shared/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Manage Users</h1>
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
              <h3 class="card-title">User List</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#userModal">
                  Add New User
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($users as $user): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><span class="badge badge-info"><?php echo strtoupper($user['role']); ?></span></td>
                    <td><?php echo htmlspecialchars($user['department_name'] ?? 'N/A'); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $user['status'] == 'active' ? 'success' : 'secondary'; ?>">
                        <?php echo ucfirst($user['status']); ?>
                        </span>
                    </td>
                    <td>
                      <button class="btn btn-xs btn-info edit-user" data-data='<?php echo json_encode($user); ?>'><i class="fas fa-edit"></i></button>
                      <a href="manage_users.php?delete=<?php echo $user['user_id']; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
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
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Add User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="manage_users.php" method="post">
        <input type="hidden" name="user_id" id="userId">
        <div class="modal-body">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" id="userName" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" id="userEmail" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Password (Leave blank to keep current, default: 123456)</label>
            <input type="password" name="password" class="form-control">
          </div>
          <div class="form-group">
            <label>Role</label>
            <select name="role" id="userRole" class="form-control" required>
              <option value="student">Student</option>
              <option value="teacher">Teacher</option>
              <option value="manager">Manager</option>
              <option value="admin">Administrator</option>
            </select>
          </div>
          <div class="form-group">
            <label>Department</label>
            <select name="department_id" id="userDept" class="form-control">
              <option value="">None</option>
              <?php foreach ($departments as $dept): ?>
                <option value="<?php echo $dept['department_id']; ?>"><?php echo htmlspecialchars($dept['department_name']); ?></option>
              <?php
endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="status" id="userStatus" class="form-control">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'views/shared/footer.php'; ?>

<script>
$(document).ready(function() {
    $('.edit-user').click(function() {
        var data = $(this).data('data');
        $('#userId').val(data.user_id);
        $('#userName').val(data.name);
        $('#userEmail').val(data.email);
        $('#userRole').val(data.role);
        $('#userDept').val(data.department_id);
        $('#userStatus').val(data.status);
        $('#modalTitle').text('Edit User');
        $('#userModal').modal('show');
    });

    $('#userModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $('#userId').val('');
        $('#modalTitle').text('Add User');
    });
});
</script>

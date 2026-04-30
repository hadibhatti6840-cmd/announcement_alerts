<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
requireLogin();

if (!hasRole('admin')) {
    header("Location: index.php");
    exit();
}

$pageTitle = "Manage Departments";
$activeMenu = "departments";
$success = "";
$error = "";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM departments WHERE department_id = ?");
    if ($stmt->execute([$id])) {
        $success = "Department deleted successfully.";
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['department_name']);
    $id = isset($_POST['department_id']) ? (int)$_POST['department_id'] : 0;

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE departments SET department_name = ? WHERE department_id = ?");
        $stmt->execute([$name, $id]);
        $success = "Department updated successfully.";
    }
    else {
        $stmt = $pdo->prepare("INSERT INTO departments (department_name) VALUES (?)");
        $stmt->execute([$name]);
        $success = "Department added successfully.";
    }
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
          <h1>Manage Departments</h1>
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
        <div class="col-md-4">
          <div class="card card-primary shadow">
            <div class="card-header">
              <h3 class="card-title"><?php echo isset($_GET['edit']) ? 'Edit' : 'Add New'; ?> Department</h3>
            </div>
            <form action="manage_departments.php" method="post">
              <div class="card-body">
                <?php
$editDept = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM departments WHERE department_id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editDept = $stmt->fetch();
}
?>
                <input type="hidden" name="department_id" value="<?php echo $editDept ? $editDept['department_id'] : ''; ?>">
                <div class="form-group">
                  <label for="deptName">Department Name</label>
                  <input type="text" name="department_name" class="form-control" id="deptName" placeholder="e.g. Computer Science" value="<?php echo $editDept ? $editDept['department_name'] : ''; ?>" required>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary"><?php echo $editDept ? 'Update' : 'Save'; ?></button>
                <?php if ($editDept): ?>
                    <a href="manage_departments.php" class="btn btn-default float-right">Cancel</a>
                <?php
endif; ?>
              </div>
            </form>
          </div>
        </div>

        <div class="col-md-8">
          <div class="card shadow">
            <div class="card-header">
              <h3 class="card-title">Department List</h3>
            </div>
            <div class="card-body p-0">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th style="width: 10px">#</th>
                    <th>Name</th>
                    <th>Created</th>
                    <th style="width: 120px">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($departments as $index => $dept): ?>
                  <tr>
                    <td><?php echo $index + 1; ?>.</td>
                    <td><?php echo htmlspecialchars($dept['department_name']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($dept['created_at'])); ?></td>
                    <td>
                      <a href="manage_departments.php?edit=<?php echo $dept['department_id']; ?>" class="btn btn-xs btn-info"><i class="fas fa-edit"></i></a>
                      <a href="manage_departments.php?delete=<?php echo $dept['department_id']; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
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

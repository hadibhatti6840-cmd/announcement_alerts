<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = sanitize($_POST['name']);
  $email = sanitize($_POST['email']);
  $password = $_POST['password'];
  $confirm = $_POST['confirm_password'];
  $deptId = (int)$_POST['department_id'];
  $role = $_POST['role'] === 'teacher' ? 'teacher' : 'student';

  // Validation
  if ($password !== $confirm) {
    $error = "Passwords do not match.";
  }
  else {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
      $error = "Email already registered.";
    }
    else {
      $hashed = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, department_id, status) VALUES (?, ?, ?, ?, ?, 'active')");
      if ($stmt->execute([$name, $email, $hashed, $role, $deptId])) {
        $success = "Registration successful! You can now <a href='login.php'>login</a>.";
      }
      else {
        $error = "Registration failed. Please try again.";
      }
    }
  }
}

$departments = $pdo->query("SELECT * FROM departments ORDER BY department_name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ERP Registration</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        height: 100vh;
    }
    .register-box { width: 400px; margin: 5% auto; }
  </style>
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="card card-outline card-primary shadow-lg">
    <div class="card-header text-center">
      <a href="index.php" class="h1"><b>ERP</b>System</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Register a new membership</p>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php
endif; ?>
      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
      <?php
endif; ?>

      <form action="register.php" method="post" autocomplete="off">
        <div class="input-group mb-3">
          <input type="text" name="name" class="form-control" placeholder="Full name" autocomplete="off" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-user"></span></div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" autocomplete="off" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" autocomplete="new-password" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="confirm_password" class="form-control" placeholder="Retype password" autocomplete="new-password" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
        </div>
        <div class="form-group">
            <label>Select Your Department</label>
            <select name="department_id" class="form-control" required>
                <option value="">-- Choose --</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?php echo $d['department_id']; ?>"><?php echo htmlspecialchars($d['department_name']); ?></option>
                <?php
endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>I am a:</label>
            <div class="d-flex">
                <div class="custom-control custom-radio mr-3">
                  <input class="custom-control-input" type="radio" id="role1" name="role" value="student" checked>
                  <label for="role1" class="custom-control-label">Student</label>
                </div>
                <div class="custom-control custom-radio">
                  <input class="custom-control-input" type="radio" id="role2" name="role" value="teacher">
                  <label for="role2" class="custom-control-label">Teacher</label>
                </div>
            </div>
        </div>
        <div class="row mt-4">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Register</button>
          </div>
        </div>
      </form>

      <a href="login.php" class="text-center mt-3 d-block">I already have a membership</a>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>

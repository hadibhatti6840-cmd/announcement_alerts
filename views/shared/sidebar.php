  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
      <span class="brand-text font-weight-light pl-3"><b>ERP</b>System</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['name']); ?>&background=random" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $_SESSION['name']; ?></a>
          <small class="text-info"><?php echo ucfirst($_SESSION['role']); ?></small>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          <li class="nav-item">
            <a href="<?php echo !empty($_SESSION['role']) ? $_SESSION['role'] . '_dashboard.php' : 'user_dashboard.php'; ?>" class="nav-link <?php echo($activeMenu == 'dashboard') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <?php if ($_SESSION['role'] === 'admin'): ?>
          <li class="nav-header">ADMINISTRATION</li>
          <li class="nav-item">
            <a href="manage_users.php" class="nav-link <?php echo($activeMenu == 'users') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-users"></i>
              <p>User Management</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="manage_departments.php" class="nav-link <?php echo($activeMenu == 'departments') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-building"></i>
              <p>Departments</p>
            </a>
          </li>
          <?php
endif; ?>

          <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'manager' || $_SESSION['role'] === 'teacher' || $_SESSION['role'] === 'student'): ?>
          <li class="nav-header">COMMUNICATION</li>
          <li class="nav-item">
            <a href="manage_announcements.php" class="nav-link <?php echo($activeMenu == 'announcements') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-bullhorn"></i>
              <p>Announcements</p>
            </a>
          </li>
          <?php
endif; ?>

          <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'manager' || $_SESSION['role'] === 'teacher'): ?>
          <li class="nav-item">
            <a href="manage_alerts.php" class="nav-link <?php echo($activeMenu == 'alerts') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-exclamation-triangle"></i>
              <p>Instant Alerts</p>
            </a>
          </li>
          <?php
endif; ?>

          <li class="nav-header">REPORTS</li>
          <li class="nav-item">
            <a href="notifications.php" class="nav-link <?php echo($activeMenu == 'notifications') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-bell"></i>
              <p>Notifications</p>
            </a>
          </li>
          <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'manager' || $_SESSION['role'] === 'teacher'): ?>
          <li class="nav-item">
            <a href="reports.php" class="nav-link <?php echo($activeMenu == 'reports') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-chart-pie"></i>
              <p>System Reports</p>
            </a>
          </li>
          <?php
endif; ?>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

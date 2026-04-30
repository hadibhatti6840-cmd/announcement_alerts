  <footer class="main-footer p-0" style="background-color: #04253e; border-top: none;">
    <div class="container-fluid" style="padding: 45px 60px;">
      <div class="row">
        <!-- Column 1 -->
        <div class="col-md-3">
          <h5 style="color: #9baec4; font-size: 17px; font-weight: 500; margin-bottom: 20px;">Help Desk Info</h5>
          <ul class="list-unstyled" style="font-size: 14px; line-height: 2.2;">
            <li><a href="mailto:support@campus.edu.pk" style="color: #3e8de2; text-decoration: none;">support@campus.edu.pk</a></li>
            <li style="margin-top: 10px;"><span style="color: #3e8de2;">Monday - Friday | 8 AM - 4 PM PST</span></li>
            <li><span style="color: #3e8de2;">(excluding major holidays)</span></li>
            <li style="margin-top: 10px;"><a href="index.php" style="color: #3e8de2; text-decoration: none;">System Start Page</a></li>
          </ul>
        </div>
        
        <!-- Column 2 -->
        <div class="col-md-3">
          <h5 style="color: #9baec4; font-size: 17px; font-weight: 500; margin-bottom: 20px;">Dashboards</h5>
          <ul class="list-unstyled" style="font-size: 14px; line-height: 2.2;">
            <li><a href="admin_dashboard.php" style="color: #3e8de2; text-decoration: none;">Admin Dashboard</a></li>
            <li><a href="teacher_dashboard.php" style="color: #3e8de2; text-decoration: none;">Teacher Dashboard</a></li>
            <li><a href="student_dashboard.php" style="color: #3e8de2; text-decoration: none;">Student Dashboard</a></li>
            <li><a href="manager_dashboard.php" style="color: #3e8de2; text-decoration: none;">Manager Dashboard</a></li>
            <li><a href="user_dashboard.php" style="color: #3e8de2; text-decoration: none;">General User Dashboard</a></li>
          </ul>
        </div>

        <!-- Column 3 -->
        <div class="col-md-3">
          <h5 style="color: #9baec4; font-size: 17px; font-weight: 500; margin-bottom: 20px;">System Management</h5>
          <ul class="list-unstyled" style="font-size: 14px; line-height: 2.2;">
            <li><a href="manage_announcements.php" style="color: #3e8de2; text-decoration: none;">Manage Announcements</a></li>
            <li><a href="manage_alerts.php" style="color: #3e8de2; text-decoration: none;">Manage Live Alerts</a></li>
            <li><a href="manage_users.php" style="color: #3e8de2; text-decoration: none;">Manage Users</a></li>
            <li><a href="manage_departments.php" style="color: #3e8de2; text-decoration: none;">Manage Departments</a></li>
          </ul>
        </div>

        <!-- Column 4 -->
        <div class="col-md-3">
          <h5 style="color: #9baec4; font-size: 17px; font-weight: 500; margin-bottom: 20px;">Account & Reports</h5>
          <ul class="list-unstyled" style="font-size: 14px; line-height: 2.2;">
            <li><a href="notifications.php" style="color: #3e8de2; text-decoration: none;">View Notifications</a></li>
            <li><a href="reports.php" style="color: #3e8de2; text-decoration: none;">System Reports</a></li>
            <li><a href="login.php" style="color: #3e8de2; text-decoration: none;">Login</a> / <a href="register.php" style="color: #3e8de2; text-decoration: none;">Register</a></li>
            <li><a href="logout.php" style="color: #3e8de2; text-decoration: none;">Secure Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
    // Fetch notification count
    function updateNotifCount() {
        $.get('controllers/get_notif_count.php', function(data) {
            $('#notif-count').text(data.count);
            if (data.count > 0) {
                $('#notif-count').addClass('badge-warning').removeClass('badge-gray');
            } else {
                $('#notif-count').removeClass('badge-warning').addClass('badge-gray');
            }
        }, 'json');
    }
    // Update every 10 seconds
    updateNotifCount();
    setInterval(updateNotifCount, 10000);
});
</script>
</body>
</html>

CREATE DATABASE IF NOT EXISTS announcement_alerts_db;
USE announcement_alerts_db;

-- 1. Departments Table
CREATE TABLE IF NOT EXISTS departments (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Users Table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'user', 'teacher', 'student') NOT NULL,
    department_id INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE SET NULL
);

-- 3. Announcements Table
CREATE TABLE IF NOT EXISTS announcements (
    announcement_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiry_date DATE,
    priority ENUM('low', 'medium', 'high') NOT NULL,
    target_type ENUM('all', 'department', 'specific') NOT NULL,
    target_id INT, -- Stores department_id or specific user_id if needed
    attachment_path VARCHAR(255) NULL,
    attachment_name VARCHAR(255) NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 4. Alerts Table (Instant/Emergency)
CREATE TABLE IF NOT EXISTS alerts (
    alert_id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    alert_type ENUM('emergency', 'info') NOT NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 5. Notifications Table (Tracking)
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    announcement_id INT NULL,
    alert_id INT NULL,
    status ENUM('read', 'unread') DEFAULT 'unread',
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (announcement_id) REFERENCES announcements(announcement_id) ON DELETE CASCADE,
    FOREIGN KEY (alert_id) REFERENCES alerts(alert_id) ON DELETE CASCADE
);

-- PROCEDURES -- 
DELIMITER //

-- 1. Procedure to Mark All Notifications as Read for a User
CREATE PROCEDURE IF NOT EXISTS sp_MarkAllNotificationsRead(IN p_user_id INT)
BEGIN
    UPDATE notifications SET status = 'read' WHERE user_id = p_user_id;
END //

-- 2. Procedure to Cleanup Expired Announcements and their Notifications
CREATE PROCEDURE IF NOT EXISTS sp_CleanupExpiredData()
BEGIN
    -- Delete notifications associated with expired announcements
    DELETE FROM notifications 
    WHERE announcement_id IN (SELECT announcement_id FROM announcements WHERE expiry_date < CURDATE() AND expiry_date IS NOT NULL);
    
    -- Delete early alerts (e.g. older than 30 days) if needed
    DELETE FROM notifications WHERE alert_id IN (SELECT alert_id FROM alerts WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY));
    DELETE FROM alerts WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
    
    -- Delete the announcements
    DELETE FROM announcements WHERE expiry_date < CURDATE() AND expiry_date IS NOT NULL;
END //

-- 3. Procedure to Get User Dashboard Summary
CREATE PROCEDURE IF NOT EXISTS sp_GetUserSummary(IN p_user_id INT, IN p_dept_id INT)
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM notifications WHERE user_id = p_user_id AND status = 'unread') as unread_count,
        (SELECT COUNT(*) FROM announcements WHERE (target_type = 'all' OR (target_type='department' AND target_id=p_dept_id) OR (target_type='specific' AND target_id=p_user_id)) AND (expiry_date >= CURDATE() OR expiry_date IS NULL)) as active_announcements;
END //

DELIMITER ;

-- DEFAULT DATA SEEDING --

INSERT INTO departments (department_name) VALUES ('Computer Science'), ('Business'), ('Engineering')
ON DUPLICATE KEY UPDATE department_name=department_name;

-- Hash generated using password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (name, email, password, role, department_id, status) VALUES 
('System Admin', 'admin@example.com', '$2y$10$HQ0AdS5CrTEz1KeRmqVm9eetXmrBF2jwMlKpdmOYikU0vVq2CO7E.', 'admin', NULL, 'active'),
('System Manager', 'manager@example.com', '$2y$10$HQ0AdS5CrTEz1KeRmqVm9eetXmrBF2jwMlKpdmOYikU0vVq2CO7E.', 'manager', NULL, 'active'),
('John Teacher', 'teacher@example.com', '$2y$10$HQ0AdS5CrTEz1KeRmqVm9eetXmrBF2jwMlKpdmOYikU0vVq2CO7E.', 'teacher', 1, 'active'),
('Jane Student', 'student@example.com', '$2y$10$HQ0AdS5CrTEz1KeRmqVm9eetXmrBF2jwMlKpdmOYikU0vVq2CO7E.', 'student', 1, 'active')
ON DUPLICATE KEY UPDATE email=email;

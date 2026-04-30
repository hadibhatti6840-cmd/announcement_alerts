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

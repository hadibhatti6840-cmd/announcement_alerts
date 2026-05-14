# Announcement & Alerts Management System

An advanced, role-based University Enterprise Resource Planning (ERP) application focused on campus-wide announcements and emergency alerts. This system provides a streamlined communication channel for administrators, managers, teachers, and students, allowing for targeted notifications, file attachments, and instant emergency broadcasting within a sleek, modern interface.

## Features
- **Role-Based Access Control (RBAC):** Distinct dashboards and security permissions for `admin`, `manager`, `teacher`, and `student` roles.
- **Targeted Announcements:** Broadcast messages globally to the entire campus, specific departments, or individual users.
- **Instant Emergency Alerts:** Push critical and emergency notifications that immediately alert users across the system.
- **Attachments & Expiring Content:** Upload supplementary files for announcements and set automatic expiration dates to keep the feed relevant.
- **Modern User Interface:** A responsive and dynamic aesthetic optimized for desktop, tablet, and mobile viewing.

## Tech Stack
- **Frontend:** HTML5, CSS3 (Custom Design System with modern aesthetics), JavaScript
- **Backend:** PHP (Procedural & PDO for robust Database connections)
- **Database:** MySQL (Structured with relations, constraints, and automated stored procedures)

## Folder and File Structure
```text
announcement_alerts_db/
├── assets/                  # CSS stylesheets, JavaScript files, Images and Icons
├── config/                  # Database connectivity and core system configuration
├── controllers/             # PHP backend logic for handling form submissions and requests
├── includes/                # Reusable template parts (header, footer, sidebar navigation)
├── models/                  # Database interaction classes/functions
├── uploads/                 # Directory for storing announcement file attachments
├── views/                   # Specialized UI components and frontend pages
├── index.php                # Main landing/redirect page
├── login.php                # User authentication page
├── register.php             # User registration
├── logout.php               # Ends the session safely
├── database.sql             # Unified Database schema, stored procedures, and seed data
├── *.php                    # System pages (Dashboards for each role, Manage Alerts, etc.)
└── README.md                # Project documentation
```

## Admin Default Credentials
To access the system immediately after database setup, use the following default administrator credentials:

- **Email:** `admin@example.com`
- **Password:** `admin123`

*(Note: Additional test accounts for `manager`, `teacher`, and `student` roles are also available in the database seed using the same `admin123` password).*

## Future Enhancements
We plan to expand the system with the following features in future updates:
1. **SMS & Email Push Notifications:** Integration with external APIs (like Twilio and SendGrid) to send critical alerts directly to users' phones and inboxes.
2. **Single Sign-On (SSO):** Allowing staff and students to log in seamlessly using their existing Google Workspace or Microsoft Azure AD university accounts.
3. **Analytics Dashboard:** A comprehensive insights panel for administrators to track announcement read receipts, engagement rates, and active alert statistics.

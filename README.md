# Announcement & Alerts Management System

An advanced, role-based University Enterprise Resource Planning (ERP) application focused on campus-wide announcements and emergency alerts. Built with a sleek, responsive design, it simplifies communication among administrators, managers, teachers, and students.

## 📸 Screenshots

| Dashboard (Admin) | Announcement Management |
|:---:|:---:|
| ![Admin Dashboard](assets/screenshots/dashboard.jpg) | ![Manage Announcements](assets/screenshots/manage_announcements.jpg) |

*(Note: Add screenshot files to `assets/screenshots/` to display them here)*

## 🚀 How to Use / Installation

1. **Clone or Download** the project to your local server directory, e.g., `c:\xampp\htdocs\announcement_alerts_db`.
2. **Start your XAMPP server** (Apache and MySQL).
3. **Database Setup**: 
   - Open phpMyAdmin or your MySQL client.
   - Run the provided `database.sql` script. It will automatically create the database `announcement_alerts_db`, setup the tables, create the stored procedures, and insert default departments and users.
4. **Access the App**: Navigate to `http://localhost/announcement_alerts_db/login.php` in your browser.

## 🔑 Default Credentials

All default accounts use the password: **`admin123`**

- **Admin**: `admin@example.com`
- **Manager**: `manager@example.com`
- **Teacher**: `teacher@example.com`
- **Student**: `student@example.com`

## ✨ Features

- **Role-Based Access Control (RBAC)**: Distinct dashboards and permissions for `admin`, `manager`, `teacher`, and `student` roles.
- **Targeted Announcements**: Send alerts globally (to all), to specific departments, or individual users.
- **Instant Emergency Alerts**: Quickly broadcast critical emergency messages that immediately notify users.
- **File Attachments**: Upload supplementary documents for announcements.
- **Auto-Expiry**: Announcements automatically expire and are cleared based on a scheduled date.
- **Custom Modern Aesthetic**: Sleek user interface designed with dynamic colors, animations, and typography.
- **Responsive Layout**: Optimized for desktop, tablet, and mobile viewing.

## 🛠 Tech Stack

- **Frontend**: HTML5, Vanilla CSS3 (Custom Design System), JavaScript.
- **Backend**: PHP (Procedural & PDO for DB connection)
- **Database**: MySQL (includes stored procedures for maintenance)

## 🔮 Future Integrations

- **Email & SMS Gateway**: Real-time push notifications via SendGrid/Twilio.
- **Single Sign-On (SSO)**: Google Workspace and Microsoft Azure AD integration.
- **Mobile Push Notifications**: Firebase Cloud Messaging (FCM) integration for a native mobile app.
- **Learning Management System (LMS) Bridge**: Sync courses and announcements with platforms like Moodle or Canvas.
- **Multi-language Support**: i18n support to broadcast messages in multiple languages.

## 📂 File / Folder Project Structure

```text
announcement_alerts_db/
├── assets/                  # CSS stylesheets, JavaScript files, and Images/Icons
├── config/                  # Database connectivity and system configuration
├── controllers/             # PHP backend logic for handling form submissions
├── includes/                # Reusable template parts (header, footer, sidebar)
├── models/                  # Database interaction classes/functions
├── uploads/                 # Directory for announcement file attachments
├── views/                   # Specialized UI components
├── index.php                # Main landing/redirect page
├── login.php                # User authentication page
├── register.php             # User registration
├── logout.php               # Ends the session safely
├── database.sql             # Unified Database schema, procedures, and seed data
├── *.php                    # Additional pages (Dashboards, Manage Alerts, etc.)
└── README.md                # Project documentation (this file)
```

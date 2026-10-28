# StudentHub Portal (ITUE203 WDF, CSPIT-CE)

## Setup (WAMP or XAMPP)
1. Copy the folder into www (WAMP) or htdocs (XAMPP).
2. Start Apache and MySQL.
3. phpMyAdmin > Import > sql/studenthub.sql
4. Open http://localhost/<your-folder>/

## Demo logins
- Admin: admin@studenthub.local / Admin@123
- Student: student@studenthub.local / Student@123

## Pages
Public: index, about, events, explorer, faq, contact, feedback, login, register, timetable, notices.
Student: dashboard, profile, attendance, assignments, results.
Admin: admin/index.php, admin/students.php, admin/events.php, admin/ajax-students.php.
API: api/students.php (JSON).
Test cases: docs/test-report.md

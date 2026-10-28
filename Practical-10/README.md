# Practical 10 - Secure Login/Logout with Sessions and Role-Based Access
StudentHub (ITUE203 WDF, 25CE052)

## Run
1. Copy this folder into `www` (WAMP) or `htdocs` (XAMPP), e.g. `www/Practical-10`.
2. Start Apache and MySQL, then import `sql/studenthub.sql` once in phpMyAdmin (creates database `studenthub`).
3. Open `http://localhost/Practical-10/`

## Demo accounts
- Admin: admin@studenthub.local / Admin@123
- Student: student@studenthub.local / Student@123

To test timeout, change `SESSION_TIMEOUT` in `includes/auth.php` to 60 and stay idle for a minute.

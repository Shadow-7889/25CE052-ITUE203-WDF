<?php
require_once __DIR__ . '/includes/auth.php';
if (is_logged_in()) { redirect(is_admin() ? HOME_ADMIN : HOME_STUDENT); }
redirect('login.php');

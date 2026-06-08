<?php
require_once 'config/db.php';
logAudit("LOGOUT", "users", getCurrentUserId());
session_destroy();
redirect('login.php');
?>
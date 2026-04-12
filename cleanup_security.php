<?php
// Cleanup Security Records
session_start();

// Include required files
require_once 'config/database.php';
require_once 'helper/security_helper.php';
require_once 'helper/auth_helper.php';

// Require admin login
require_login();
require_admin();

// Perform cleanup
cleanup_security_records();

// Log cleanup event
log_security_event('security_cleanup', 'low', get_real_ip(), $_SESSION['id_user'], 'Manual cleanup of old security records');

// Redirect back to security dashboard
header('Location: security_dashboard.php?success=cleanup_completed');
exit();

?>

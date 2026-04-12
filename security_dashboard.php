<?php
// Security Dashboard - Monitor brute force attempts and security events
session_start();

// Include required files
require_once 'config/database.php';
require_once 'helper/security_helper.php';
require_once 'helper/auth_helper.php';

// Require login
require_login();

// Only admin can access security dashboard
if (!is_admin()) {
    header('Location: main.php?module=dashboard&error=access_denied');
    exit();
}

// Get security statistics
$stats = get_security_stats();

// Get recent security events
$events_query = "SELECT * FROM security_events ORDER BY event_time DESC LIMIT 50";
$events_result = mysqli_query($mysqli, $events_query);

// Get recent login attempts
$attempts_query = "SELECT * FROM login_attempts ORDER BY attempt_time DESC LIMIT 20";
$attempts_result = mysqli_query($mysqli, $attempts_query);

// Get blocked IPs
$blocked_query = "SELECT * FROM blocked_ips WHERE (blocked_until IS NULL OR blocked_until > NOW()) ORDER BY blocked_time DESC LIMIT 20";
$blocked_result = mysqli_query($mysqli, $blocked_query);

// Get locked accounts
$locked_query = "SELECT la.*, u.nama_lengkap FROM locked_accounts la 
                JOIN user u ON la.user_id = u.id_user 
                WHERE (la.locked_until IS NULL OR la.locked_until > NOW()) 
                ORDER BY la.locked_time DESC LIMIT 20";
$locked_result = mysqli_query($mysqli, $locked_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Dashboard - Aplikasi Data Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar_menu.php'; ?>
    
    <div class="content">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0">🛡️ Security Dashboard</h1>
                <button class="btn btn-sm btn-outline-danger" onclick="cleanupSecurityRecords()">
                    <i class="fas fa-trash me-1"></i>Cleanup Old Records
                </button>
            </div>

            <!-- Security Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo $stats['recent_failed_logins']; ?></h4>
                                    <p class="card-text">Failed Logins (1h)</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo $stats['blocked_ips']; ?></h4>
                                    <p class="card-text">Blocked IPs</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-ban fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo $stats['locked_accounts']; ?></h4>
                                    <p class="card-text">Locked Accounts</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-lock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo $stats['security_events_24h']; ?></h4>
                                    <p class="card-text">Security Events (24h)</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-shield-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Events -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">📋 Recent Security Events</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Event</th>
                                            <th>Severity</th>
                                            <th>IP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($event = mysqli_fetch_assoc($events_result)): ?>
                                        <tr>
                                            <td><?php echo date('H:i', strtotime($event['event_time'])); ?></td>
                                            <td><?php echo htmlspecialchars($event['event_type']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $event['severity'] === 'high' ? 'danger' : ($event['severity'] === 'medium' ? 'warning' : 'info'); ?>">
                                                    <?php echo $event['severity']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($event['ip_address']); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">🚫 Blocked IPs</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>IP Address</th>
                                            <th>Reason</th>
                                            <th>Blocked Until</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($blocked = mysqli_fetch_assoc($blocked_result)): ?>
                                        <tr>
                                            <td><code><?php echo htmlspecialchars($blocked['ip_address']); ?></code></td>
                                            <td><?php echo htmlspecialchars($blocked['reason']); ?></td>
                                            <td>
                                                <?php 
                                                if ($blocked['is_permanent']) {
                                                    echo '<span class="badge bg-danger">Permanent</span>';
                                                } else {
                                                    echo date('d M H:i', strtotime($blocked['blocked_until']));
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Locked Accounts -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">🔒 Locked Accounts</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Full Name</th>
                                            <th>Lock Reason</th>
                                            <th>Locked Time</th>
                                            <th>Locked Until</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($locked = mysqli_fetch_assoc($locked_result)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($locked['username']); ?></td>
                                            <td><?php echo htmlspecialchars($locked['nama_lengkap']); ?></td>
                                            <td><?php echo htmlspecialchars($locked['lock_reason']); ?></td>
                                            <td><?php echo date('d M H:i', strtotime($locked['locked_time'])); ?></td>
                                            <td>
                                                <?php 
                                                if ($locked['is_permanent']) {
                                                    echo '<span class="badge bg-danger">Permanent</span>';
                                                } else {
                                                    echo date('d M H:i', strtotime($locked['locked_until']));
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function cleanupSecurityRecords() {
        if (confirm('Apakah Anda yakin ingin membersihkan record keamanan lama?')) {
            window.location.href = 'cleanup_security.php';
        }
    }

    // Auto-refresh dashboard every 30 seconds
    setInterval(function() {
        window.location.reload();
    }, 30000);
    </script>
</body>
</html>

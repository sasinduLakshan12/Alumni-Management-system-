<?php 
// Start session and include database connection
session_start();
include '../config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? 'Administrator';

// Get statistics
$total_alumni = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='alumni' AND status='approved'")->fetch_assoc()['count'];
$pending = $conn->query("SELECT COUNT(*) as count FROM users WHERE status='pending'")->fetch_assoc()['count'];
$total_events = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$total_announcements = $conn->query("SELECT COUNT(*) as count FROM announcements")->fetch_assoc()['count'];

// Get recent activity (last 24 hours)
$recent_activity_sql = "SELECT 'user' as type, CONCAT('New registration: ', name) as description, created_at as timestamp 
                        FROM users 
                        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                        UNION
                        SELECT 'event' as type, CONCAT('Event created: ', title) as description, created_at as timestamp 
                        FROM events 
                        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                        UNION
                        SELECT 'announcement' as type, CONCAT('Announcement: ', title) as description, posted_at as timestamp 
                        FROM announcements 
                        WHERE posted_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                        ORDER BY timestamp DESC 
                        LIMIT 10";
$recent_activity = $conn->query($recent_activity_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - University of Vavuniya</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="logo-placeholder">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="nav-title">
                    <span class="university-name">University of Vavuniya</span>
                    <span class="system-name">Admin Panel</span>
                </div>
            </div>
            <div class="nav-links">
                <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="approvals.php"><i class="fas fa-user-check"></i> Approvals <span class="nav-badge"><?php echo $pending; ?></span></a>
                <a href="manage_users.php"><i class="fas fa-users"></i> Alumni</a>
                <a href="manage_events.php"><i class="fas fa-calendar-alt"></i> Events</a>
                <a href="manage_announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Admin Navigation -->
    <div class="admin-nav">
        <a href="dashboard.php" class="admin-nav-link active">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="approvals.php" class="admin-nav-link">
            <i class="fas fa-user-check"></i> Approvals
            <span class="nav-badge"><?php echo $pending; ?></span>
        </a>
        <a href="manage_users.php" class="admin-nav-link">
            <i class="fas fa-users"></i> Manage Alumni
        </a>
        <a href="manage_events.php" class="admin-nav-link">
            <i class="fas fa-calendar-alt"></i> Manage Events
        </a>
        <a href="manage_announcements.php" class="admin-nav-link">
            <i class="fas fa-bullhorn"></i> Announcements
        </a>
    </div>

    <div class="admin-container">
        <!-- Admin Header -->
        <header class="admin-header">
            <h2>Welcome back, <?php echo htmlspecialchars($admin_name); ?>!</h2>
            <p class="admin-subtitle">Admin Dashboard - University of Vavuniya Alumni System</p>
        </header>

        <!-- Statistics Cards -->
        <div class="admin-stats">
            <div class="stat-card approved">
                <div class="stat-card-header">
                    <h3><i class="fas fa-user-graduate stat-icon"></i> Total Alumni</h3>
                    <span class="trend trend-up"><i class="fas fa-arrow-up"></i> 12%</span>
                </div>
                <div class="stat-number total-alumni"><?php echo $total_alumni; ?></div>
                <p class="stat-trend">Approved alumni members</p>
                <div class="stat-actions">
                    <a href="manage_users.php" class="admin-btn">
                        <i class="fas fa-eye"></i> View All
                    </a>
                </div>
            </div>

            <div class="stat-card pending">
                <div class="stat-card-header">
                    <h3><i class="fas fa-clock stat-icon"></i> Pending Approvals</h3>
                    <span class="trend trend-down"><i class="fas fa-exclamation"></i> Attention</span>
                </div>
                <div class="stat-number pending"><?php echo $pending; ?></div>
                <p class="stat-trend">Awaiting admin approval</p>
                <div class="stat-actions">
                    <a href="approvals.php" class="admin-btn">
                        <i class="fas fa-check-circle"></i> Review Now
                    </a>
                </div>
            </div>

            <div class="stat-card events">
                <div class="stat-card-header">
                    <h3><i class="fas fa-calendar-check stat-icon"></i> Total Events</h3>
                    <span class="trend trend-up"><i class="fas fa-arrow-up"></i> 5%</span>
                </div>
                <div class="stat-number total-events"><?php echo $total_events; ?></div>
                <p class="stat-trend">Upcoming and past events</p>
                <div class="stat-actions">
                    <a href="manage_events.php" class="admin-btn">
                        <i class="fas fa-calendar-plus"></i> Add Event
                    </a>
                </div>
            </div>

            <div class="stat-card announcements">
                <div class="stat-card-header">
                    <h3><i class="fas fa-bullhorn stat-icon"></i> Announcements</h3>
                    <span class="trend trend-up"><i class="fas fa-arrow-up"></i> 8%</span>
                </div>
                <div class="stat-number announcements"><?php echo $total_announcements; ?></div>
                <p class="stat-trend">Active announcements</p>
                <div class="stat-actions">
                    <a href="manage_announcements.php" class="admin-btn">
                        <i class="fas fa-plus"></i> New Announcement
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="admin-content">
            <!-- Quick Actions -->
            <section class="quick-actions">
                <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                <div class="action-grid">
                    <a href="approvals.php" class="action-card">
                        <i class="fas fa-user-check action-icon"></i>
                        <h4>Review Approvals</h4>
                        <p>Approve or reject new alumni registrations</p>
                    </a>
                    
                    <a href="add_event.php" class="action-card">
                        <i class="fas fa-calendar-plus action-icon"></i>
                        <h4>Create Event</h4>
                        <p>Schedule new alumni events</p>
                    </a>
                    
                    <a href="add_announcement.php" class="action-card">
                        <i class="fas fa-bullhorn action-icon"></i>
                        <h4>Post Announcement</h4>
                        <p>Create important announcements</p>
                    </a>
                </div>
            </section>

            <!-- Recent Activity -->
            <section class="recent-activity">
                <h3><i class="fas fa-history"></i> Recent Activity</h3>
                <div class="activity-list">
                    <?php if($recent_activity && $recent_activity->num_rows > 0): ?>
                        <?php while($activity = $recent_activity->fetch_assoc()): ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-<?php echo $activity['type'] === 'user' ? 'user' : ($activity['type'] === 'event' ? 'calendar' : 'bullhorn'); ?>"></i>
                                </div>
                                <div class="activity-details">
                                    <h4><?php echo htmlspecialchars($activity['description']); ?></h4>
                                    <small class="activity-time"><?php echo date('d M Y, h:i A', strtotime($activity['timestamp'])); ?></small>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="activity-details">
                                <h4>No recent activity</h4>
                                <p>Activity will appear here as it happens</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div style="margin-top: 20px; text-align: center;">
                    <a href="activity_log.php" class="admin-btn secondary">
                        <i class="fas fa-list-ul"></i> View Full Activity Log
                    </a>
                </div>
            </section>
        </div>

        <!-- Charts Section -->
        <div class="admin-charts">
            <div class="chart-card">
                <h3><i class="fas fa-chart-line"></i> Alumni Growth</h3>
                <div class="chart-placeholder">
                    <!-- Chart will be loaded here -->
                </div>
            </div>
            
            <div class="chart-card">
                <h3><i class="fas fa-chart-pie"></i> Faculty Distribution</h3>
                <div class="chart-placeholder">
                    <!-- Chart will be loaded here -->
                </div>
            </div>
        </div>

        <!-- System Status -->
        <section class="system-status">
            <h3><i class="fas fa-server"></i> System Status</h3>
            <div class="status-grid">
                <div class="status-item">
                    <div class="status-header">
                        <h4>Database</h4>
                        <span class="status-indicator"></span>
                    </div>
                    <div class="status-value">Online</div>
                    <div class="status-label">Connection stable</div>
                </div>
                
                <div class="status-item">
                    <div class="status-header">
                        <h4>Web Server</h4>
                        <span class="status-indicator"></span>
                    </div>
                    <div class="status-value">Online</div>
                    <div class="status-label">Response time: 120ms</div>
                </div>
                
                <div class="status-item">
                    <div class="status-header">
                        <h4>Storage</h4>
                        <span class="status-indicator"></span>
                    </div>
                    <div class="status-value">85%</div>
                    <div class="status-label">15GB of 20GB used</div>
                </div>
                
                <div class="status-item">
                    <div class="status-header">
                        <h4>Last Backup</h4>
                        <span class="status-indicator"></span>
                    </div>
                    <div class="status-value">12 hours</div>
                    <div class="status-label">Next backup in 12 hours</div>
                </div>
            </div>
        </section>

        <!-- Admin Footer -->
        <footer class="admin-footer">
            <p>Admin Dashboard - University of Vavuniya Alumni System</p>
            <p>Server Time: <?php echo date('d M Y, H:i:s'); ?> | PHP Version: <?php echo phpversion(); ?></p>
        </footer>
    </div>

    <!-- Settings Modal (Example) -->
    <div id="settingsModal" class="admin-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>System Settings</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Settings form would appear here.</p>
                <!-- Settings form content -->
            </div>
        </div>
    </div>

    <script src="../js/script.js"></script>
    <script src="../js/admin.js"></script>
    
    <script>
        // Initialize with real data
        const dashboardStats = {
            totalAlumni: <?php echo $total_alumni; ?>,
            pending: <?php echo $pending; ?>,
            totalEvents: <?php echo $total_events; ?>,
            announcements: <?php echo $total_announcements; ?>
        };
    </script>
</body>
</html>
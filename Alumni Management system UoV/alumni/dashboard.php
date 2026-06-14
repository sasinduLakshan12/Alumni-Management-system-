<?php 
// Start session and include database connection
session_start();
include '../config/db.php';

// Check if alumni is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'alumni') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user info
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Get announcements
$ann_sql = "SELECT * FROM announcements ORDER BY posted_at DESC LIMIT 5";
$ann_result = $conn->query($ann_sql);

// Get upcoming events
$event_sql = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3";
$event_result = $conn->query($event_sql);

// Get network stats
$faculty_sql = "SELECT COUNT(*) as count FROM users WHERE faculty = '{$user['faculty']}' AND status = 'approved'";
$faculty_result = $conn->query($faculty_sql);
$faculty_count = $faculty_result->fetch_assoc()['count'];

$year_sql = "SELECT COUNT(*) as count FROM users WHERE graduation_year = '{$user['graduation_year']}' AND status = 'approved'";
$year_result = $conn->query($year_sql);
$year_count = $year_result->fetch_assoc()['count'];

$total_sql = "SELECT COUNT(*) as count FROM users WHERE status = 'approved' AND role = 'alumni'";
$total_result = $conn->query($total_sql);
$total_count = $total_result->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Dashboard - University of Vavuniya</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/alumni.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="logo-placeholder">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="nav-title">
                    <span class="university-name">University of Vavuniya</span>
                    <span class="system-name">Alumni Dashboard</span>
                </div>
            </div>
            <div class="nav-links">
                <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                <a href="directory.php"><i class="fas fa-users"></i> Alumni Directory</a>
                <a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <span class="notification-badge">3</span>
            </div>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Dashboard Navigation -->
    <div class="dashboard-nav">
        <div class="nav-grid">
            <a href="dashboard.php" class="nav-item active">
                <i class="fas fa-tachometer-alt nav-icon"></i>
                <span class="nav-label">Dashboard</span>
            </a>
            <a href="profile.php" class="nav-item">
                <i class="fas fa-user-edit nav-icon"></i>
                <span class="nav-label">Edit Profile</span>
            </a>
            <a href="directory.php" class="nav-item">
                <i class="fas fa-network-wired nav-icon"></i>
                <span class="nav-label">Network</span>
            </a>
            <a href="events.php" class="nav-item">
                <i class="fas fa-calendar-check nav-icon"></i>
                <span class="nav-label">Events</span>
            </a>
            <a href="messages.php" class="nav-item">
                <i class="fas fa-envelope nav-icon"></i>
                <span class="nav-label">Messages</span>
                <span class="notification-badge">3</span>
            </a>
            <a href="settings.php" class="nav-item">
                <i class="fas fa-cog nav-icon"></i>
                <span class="nav-label">Settings</span>
            </a>
        </div>
    </div>

    <div class="dashboard-container">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="welcome-content">
                <div class="welcome-text">
                    <h1>Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</h1>
                    <p>Here's what's happening with your alumni network today.</p>
                </div>
                <div class="welcome-stats">
                    <div class="stat-item">
                        <span class="stat-number" id="total-alumni">0</span>
                        <span class="stat-label">Total Alumni</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="same-faculty">0</span>
                        <span class="stat-label">Same Faculty</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="same-year">0</span>
                        <span class="stat-label">Same Year</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="connections">0</span>
                        <span class="stat-label">Connections</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- My Information Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-circle card-icon"></i> My Information</h3>
                    <a href="profile.php" class="btn-card">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
                
                <div class="profile-info">
                    <div class="info-row">
                        <span class="info-label">Registration No:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['reg_no']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Faculty:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['faculty']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Graduation Year:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['graduation_year']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Current Job:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['current_job'] ?: 'Not specified'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Company:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['company'] ?: 'Not specified'); ?></span>
                    </div>
                </div>
                
                <div class="progress-section">
                    <div class="progress-header">
                        <span class="progress-label">Profile Completeness</span>
                        <span class="progress-percentage">0%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                    <div class="profile-tips">
                        <ul>
                            <!-- Tips will be added by JavaScript -->
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-bolt card-icon"></i> Quick Actions</h3>
                </div>
                
                <ul class="quick-links">
                    <li>
                        <a href="profile.php">
                            <i class="fas fa-user-edit"></i>
                            <span>Update Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="directory.php">
                            <i class="fas fa-users"></i>
                            <span>Browse Alumni</span>
                        </a>
                    </li>
                    <li>
                        <a href="events.php">
                            <i class="fas fa-calendar-alt"></i>
                            <span>View Events</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="quickAction('connect')">
                            <i class="fas fa-handshake"></i>
                            <span>Find Connections</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="quickAction('messages')">
                            <i class="fas fa-envelope"></i>
                            <span>Check Messages</span>
                            <span class="notification-badge">3</span>
                        </a>
                    </li>
                </ul>
                
                <div class="card-actions">
                    <a href="profile.php" class="btn-card">
                        <i class="fas fa-user-edit"></i> Edit Profile
                    </a>
                    <a href="settings.php" class="btn-card secondary">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </div>
            </div>

            <!-- Upcoming Events Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-alt card-icon"></i> Upcoming Events</h3>
                    <a href="events.php" class="btn-card">
                        <i class="fas fa-eye"></i> View All
                    </a>
                </div>
                
                <div class="events-list">
                    <?php if($event_result && $event_result->num_rows > 0): ?>
                        <?php while($event = $event_result->fetch_assoc()): ?>
                            <div class="event-item">
                                <div class="event-date">
                                    <span class="day"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                                    <span class="month"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                                </div>
                                <div class="event-details">
                                    <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                                    <p><?php echo htmlspecialchars(substr($event['description'], 0, 60)); ?>...</p>
                                    <small><?php echo date('h:i A', strtotime($event['event_date'])); ?> • <?php echo htmlspecialchars($event['venue']); ?></small>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h4>No Upcoming Events</h4>
                            <p>Check back later for upcoming events.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-actions">
                    <a href="events.php" class="btn-card">
                        <i class="fas fa-calendar-plus"></i> Browse Events
                    </a>
                </div>
            </div>

            <!-- Alumni Network Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-network-wired card-icon"></i> Alumni Network</h3>
                </div>
                
                <div class="network-stats">
                    <div class="network-stat">
                        <span class="number"><?php echo $total_count; ?></span>
                        <span class="label">Total Alumni</span>
                    </div>
                    <div class="network-stat">
                        <span class="number"><?php echo $faculty_count; ?></span>
                        <span class="label">Same Faculty</span>
                    </div>
                    <div class="network-stat">
                        <span class="number"><?php echo $year_count; ?></span>
                        <span class="label">Same Year</span>
                    </div>
                    <div class="network-stat">
                        <span class="number">45</span>
                        <span class="label">Your Connections</span>
                    </div>
                </div>
                
                <div class="card-actions">
                    <a href="directory.php" class="btn-card">
                        <i class="fas fa-search"></i> Find Alumni
                    </a>
                    <a href="connect.php" class="btn-card secondary">
                        <i class="fas fa-user-plus"></i> Connect
                    </a>
                </div>
            </div>

            <!-- Recent Activity Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-history card-icon"></i> Recent Activity</h3>
                </div>
                
                <div class="activity-list">
                    <!-- Activity items will be loaded by JavaScript -->
                </div>
                
                <div class="card-actions">
                    <a href="activity.php" class="btn-card">
                        <i class="fas fa-list-ul"></i> View All Activity
                    </a>
                </div>
            </div>
        </div>

        <!-- Announcements Section -->
        <section class="announcements-section">
            <div class="section-header">
                <h3><i class="fas fa-bullhorn"></i> Latest Announcements</h3>
                <a href="announcements.php" class="view-all">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="announcements-list">
                <?php if($ann_result && $ann_result->num_rows > 0): ?>
                    <?php while($ann = $ann_result->fetch_assoc()): ?>
                        <div class="announcement-item" data-id="<?php echo $ann['id']; ?>" onclick="markAnnouncementRead(<?php echo $ann['id']; ?>)">
                            <h4><?php echo htmlspecialchars($ann['title']); ?></h4>
                            <p><?php echo htmlspecialchars(substr($ann['content'], 0, 200)); ?>...</p>
                            <div class="announcement-meta">
                                <span><i class="far fa-calendar"></i> <?php echo date('d M Y, h:i A', strtotime($ann['posted_at'])); ?></span>
                                <span><i class="fas fa-user"></i> University Admin</span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-bullhorn"></i>
                        <h4>No Announcements</h4>
                        <p>Check back later for university announcements.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-column">
                <h3>University of Vavuniya</h3>
                <p><i class="fas fa-map-marker-alt"></i> Pampaimadu, Vavuniya, Sri Lanka</p>
                <p><i class="fas fa-phone"></i> +94 24 222 2265</p>
                <p><i class="fas fa-envelope"></i> alumni@vau.ac.lk</p>
            </div>
            
            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="../index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="directory.php"><i class="fas fa-users"></i> Alumni Directory</a></li>
                    <li><a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>Help & Support</h3>
                <ul class="footer-links">
                    <li><a href="help.php"><i class="fas fa-question-circle"></i> Help Center</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact Support</a></li>
                    <li><a href="faq.php"><i class="fas fa-comments"></i> FAQ</a></li>
                    <li><a href="feedback.php"><i class="fas fa-comment-alt"></i> Feedback</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> University of Vavuniya - Alumni Management System. All rights reserved.</p>
            <p>Logged in as: <?php echo htmlspecialchars($user['name']); ?> | Last login: <?php echo date('d M Y H:i'); ?></p>
        </div>
    </footer>

    <script src="../js/script.js"></script>
    <script src="../js/alumni.js"></script>
    
    <script>
        // Initialize network stats with real data
        const networkStats = {
            'total-alumni': <?php echo $total_count; ?>,
            'same-faculty': <?php echo $faculty_count; ?>,
            'same-year': <?php echo $year_count; ?>,
            'connections': 45
        };
    </script>
</body>
</html>
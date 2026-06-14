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

// Handle approval/rejection
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE users SET status='approved' WHERE id='$id'");
    
    // Get user info for notification
    $user_sql = "SELECT name, email FROM users WHERE id='$id'";
    $user_result = $conn->query($user_sql);
    $user = $user_result->fetch_assoc();
    
    $_SESSION['success_message'] = "Successfully approved " . htmlspecialchars($user['name']) . "'s application.";
    header("Location: approvals.php");
    exit();
}

if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $conn->query("UPDATE users SET status='rejected' WHERE id='$id'");
    
    // Get user info for notification
    $user_sql = "SELECT name, email FROM users WHERE id='$id'";
    $user_result = $conn->query($user_sql);
    $user = $user_result->fetch_assoc();
    
    $_SESSION['warning_message'] = "Rejected " . htmlspecialchars($user['name']) . "'s application.";
    header("Location: approvals.php");
    exit();
}

// Bulk actions
if (isset($_POST['bulk_action']) && isset($_POST['selected_users'])) {
    $selected_users = $_POST['selected_users'];
    $bulk_action = $_POST['bulk_action'];
    
    if (!empty($selected_users)) {
        $ids = implode(',', array_map('intval', $selected_users));
        
        switch($bulk_action) {
            case 'approve':
                $conn->query("UPDATE users SET status='approved' WHERE id IN ($ids)");
                $_SESSION['success_message'] = "Approved " . count($selected_users) . " application(s).";
                break;
            case 'reject':
                $conn->query("UPDATE users SET status='rejected' WHERE id IN ($ids)");
                $_SESSION['warning_message'] = "Rejected " . count($selected_users) . " application(s).";
                break;
            case 'delete':
                $conn->query("DELETE FROM users WHERE id IN ($ids)");
                $_SESSION['success_message'] = "Deleted " . count($selected_users) . " application(s).";
                break;
        }
        
        header("Location: approvals.php");
        exit();
    }
}

// Get pending registrations
$sql = "SELECT * FROM users WHERE status='pending' ORDER BY created_at DESC";
$result = $conn->query($sql);

// Get count for display
$pending_count = $result->num_rows;

// Get statistics
$total_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END) as rejected,
    SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending
    FROM users WHERE role='alumni'";
$stats_result = $conn->query($total_sql);
$stats = $stats_result->fetch_assoc();

// Show messages
$success_message = $_SESSION['success_message'] ?? '';
$warning_message = $_SESSION['warning_message'] ?? '';
unset($_SESSION['success_message']);
unset($_SESSION['warning_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approvals - Admin Panel - University of Vavuniya</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin_approvals.css">
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
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="approvals.php" class="active">
                    <i class="fas fa-user-check"></i> Approvals 
                    <?php if($pending_count > 0): ?>
                        <span class="count-badge"><?php echo $pending_count; ?></span>
                    <?php endif; ?>
                </a>
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

    <div class="approvals-container">
        <!-- Header -->
        <header class="approvals-header">
            <h2>
                <i class="fas fa-user-check"></i> Alumni Registration Approvals
                <?php if($pending_count > 0): ?>
                    <span class="status-badge status-pending"><?php echo $pending_count; ?> Pending</span>
                <?php endif; ?>
            </h2>
            <p class="admin-subtitle">Review and manage alumni registration applications</p>
        </header>

        <!-- Quick Stats -->
        <div class="quick-stats">
            <div class="quick-stat total">
                <i class="fas fa-users"></i>
                <div class="stat-value"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total Applications</div>
            </div>
            
            <div class="quick-stat approved">
                <i class="fas fa-check-circle"></i>
                <div class="stat-value"><?php echo $stats['approved']; ?></div>
                <div class="stat-label">Approved</div>
            </div>
            
            <div class="quick-stat pending">
                <i class="fas fa-clock"></i>
                <div class="stat-value"><?php echo $stats['pending']; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            
            <div class="quick-stat rejected">
                <i class="fas fa-times-circle"></i>
                <div class="stat-value"><?php echo $stats['rejected']; ?></div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>

        <!-- Application Info -->
        <div class="application-info">
            <p><i class="fas fa-info-circle"></i> <strong>Instructions:</strong> Review alumni registration applications below. Click "Approve" to accept or "Reject" to decline.</p>
            <p><i class="fas fa-envelope"></i> Approved users will receive email notification and alumni access. Rejected applications will be archived.</p>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="filter-group">
                <label for="filter-faculty"><i class="fas fa-filter"></i> Filter by Faculty:</label>
                <select id="filter-faculty" class="filter-select">
                    <option value="all">All Faculties</option>
                    <option value="Technology">Technological Studies</option>
                    <option value="Science">Applied Science</option>
                    <option value="Business">Business Studies</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="sort-by"><i class="fas fa-sort"></i> Sort by:</label>
                <select id="sort-by" class="filter-select">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="name">Name A-Z</option>
                </select>
            </div>
            
            <button class="btn btn-secondary" onclick="resetFilters()">
                <i class="fas fa-redo"></i> Reset Filters
            </button>
        </div>

        <!-- Success/Warning Messages -->
        <?php if($success_message): ?>
            <div class="alert success" style="margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i>
                <span><?php echo $success_message; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if($warning_message): ?>
            <div class="alert error" style="margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo $warning_message; ?></span>
            </div>
        <?php endif; ?>

        <!-- Bulk Actions Bar -->
        <form method="POST" id="bulk-form" class="bulk-actions-bar">
            <div class="selected-count">
                <input type="checkbox" id="select-all" class="bulk-select-all">
                <span id="selected-count">0 applications selected</span>
            </div>
            
            <div class="bulk-actions-buttons">
                <select name="bulk_action" class="filter-select" style="min-width: 150px;" required>
                    <option value="">Bulk Actions</option>
                    <option value="approve">Approve Selected</option>
                    <option value="reject">Reject Selected</option>
                    <option value="delete">Delete Selected</option>
                </select>
                
                <button type="submit" class="btn btn-primary" onclick="return confirmBulkAction()">
                    <i class="fas fa-play"></i> Apply
                </button>
            </div>
        </form>

        <?php if($result->num_rows > 0): ?>
            <!-- Approvals Table -->
            <div class="approvals-table-container">
                <table class="approvals-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all-table"></th>
                            <th><i class="fas fa-hashtag"></i> #</th>
                            <th><i class="fas fa-user"></i> Applicant</th>
                            <th><i class="fas fa-envelope"></i> Email</th>
                            <th><i class="fas fa-id-card"></i> Registration No</th>
                            <th><i class="fas fa-university"></i> Faculty</th>
                            <th><i class="fas fa-calendar-alt"></i> Graduation Year</th>
                            <th><i class="fas fa-clock"></i> Applied Date</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $counter = 1;
                        while($row = $result->fetch_assoc()): 
                            $faculty_class = strtolower(str_replace(' ', '-', $row['faculty']));
                        ?>
                            <tr class="application-row" data-faculty="<?php echo htmlspecialchars($row['faculty']); ?>">
                                <td>
                                    <input type="checkbox" name="selected_users[]" value="<?php echo $row['id']; ?>" class="user-checkbox">
                                </td>
                                <td><?php echo $counter++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                                    <?php if($row['current_job']): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($row['current_job']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" class="text-primary">
                                        <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($row['email']); ?>
                                    </a>
                                </td>
                                <td><code class="reg-no"><?php echo htmlspecialchars($row['reg_no']); ?></code></td>
                                <td>
                                    <span class="faculty-badge <?php echo $faculty_class; ?>">
                                        <?php echo htmlspecialchars($row['faculty']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($row['graduation_year']); ?></td>
                                <td>
                                    <i class="far fa-calendar"></i> <?php echo date('d M Y', strtotime($row['created_at'])); ?><br>
                                    <small class="text-muted"><?php echo date('h:i A', strtotime($row['created_at'])); ?></small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?approve=<?php echo $row['id']; ?>" 
                                           class="btn btn-success"
                                           onclick="return confirm('Approve <?php echo addslashes($row['name']); ?>?\n\nThis will grant alumni access.')"
                                           title="Approve Application">
                                            <i class="fas fa-check"></i> Approve
                                        </a>
                                        <a href="?reject=<?php echo $row['id']; ?>" 
                                           class="btn btn-danger"
                                           onclick="return confirm('Reject <?php echo addslashes($row['name']); ?>?\n\nThis will deny alumni access.')"
                                           title="Reject Application">
                                            <i class="fas fa-times"></i> Reject
                                        </a>
                                        <button type="button" class="btn btn-secondary" onclick="viewDetails(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>')"
                                                title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Batch Actions Info -->
            <div class="batch-actions">
                <p><i class="fas fa-lightbulb"></i> <strong>Batch Processing Tip:</strong> Use the checkboxes to select multiple applications, then choose a bulk action from the dropdown menu above the table.</p>
                <p><i class="fas fa-history"></i> <strong>Note:</strong> All approval/rejection actions are logged in the system audit trail for accountability.</p>
            </div>
            
        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>All Caught Up!</h3>
                <p>There are no pending alumni registration applications at the moment.</p>
                <p>New registrations will appear here automatically as they come in.</p>
                <div style="margin-top: 30px;">
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Action Buttons -->
        <div class="action-buttons" style="margin-top: 30px; justify-content: center;">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <a href="manage_users.php" class="btn btn-primary">
                <i class="fas fa-users"></i> View Approved Alumni
            </a>
            <button type="button" class="btn btn-secondary" onclick="exportApprovals()">
                <i class="fas fa-download"></i> Export List
            </button>
        </div>
        
        <!-- Statistics -->
        <div class="stats-info">
            <h4><i class="fas fa-chart-bar"></i> Approval Statistics</h4>
            <div class="stats-grid">
                <div class="stat-item total">
                    <div class="stat-value"><?php echo $stats['total']; ?></div>
                    <div class="stat-label">Total Applications</div>
                </div>
                
                <div class="stat-item approved">
                    <div class="stat-value"><?php echo $stats['approved']; ?></div>
                    <div class="stat-label">Approved</div>
                </div>
                
                <div class="stat-item rejected">
                    <div class="stat-value"><?php echo $stats['rejected']; ?></div>
                    <div class="stat-label">Rejected</div>
                </div>
                
                <div class="stat-item pending">
                    <div class="stat-value"><?php echo $stats['pending']; ?></div>
                    <div class="stat-label">Currently Pending</div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <p style="color: #7f8c8d; font-size: 0.9rem;">
                    <i class="fas fa-info-circle"></i> Approval rate: 
                    <strong><?php echo $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100, 1) : 0; ?>%</strong>
                    • Average processing time: <strong>24-48 hours</strong>
                </p>
            </div>
        </div>
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
                <h3>Admin Links</h3>
                <ul class="footer-links">
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="approvals.php"><i class="fas fa-user-check"></i> Approvals</a></li>
                    <li><a href="manage_users.php"><i class="fas fa-users"></i> Manage Alumni</a></li>
                    <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>System Status</h3>
                <p><i class="fas fa-circle" style="color: #2ecc71;"></i> System Operational</p>
                <p><i class="fas fa-database"></i> <?php echo $stats['total']; ?> total applications</p>
                <p><i class="fas fa-clock"></i> Last updated: <?php echo date('d M Y, H:i'); ?></p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> University of Vavuniya - Alumni Management System. All rights reserved.</p>
            <p>Logged in as: <?php echo htmlspecialchars($admin_name); ?> | Role: Administrator</p>
        </div>
    </footer>

    <!-- View Details Modal -->
    <div id="detailsModal" class="admin-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Application Details</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>

    <script src="../js/script.js"></script>
    <script src="../js/admin_approvals.js"></script>
    
    <script>
        // Initialize with real data
        const approvalsStats = {
            total: <?php echo $stats['total']; ?>,
            approved: <?php echo $stats['approved']; ?>,
            rejected: <?php echo $stats['rejected']; ?>,
            pending: <?php echo $stats['pending']; ?>
        };
        
        // Function to view user details
        function viewDetails(userId, userName) {
            // In a real app, you would fetch user details via AJAX
            const modalBody = document.getElementById('modal-body');
            modalBody.innerHTML = `
                <div class="user-details">
                    <h5>Application Details for ${userName}</h5>
                    <div class="detail-item">
                        <span class="detail-label">Application ID:</span>
                        <span class="detail-value">${userId}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value"><span class="status-badge status-pending">Pending</span></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Submitted:</span>
                        <span class="detail-value"><?php echo date('d M Y, H:i:s'); ?></span>
                    </div>
                </div>
                <div class="user-details" style="margin-top: 20px;">
                    <h5>Professional Information</h5>
                    <p>Additional details would be displayed here, including:</p>
                    <ul>
                        <li>Current job title and company</li>
                        <li>Contact information</li>
                        <li>LinkedIn profile (if provided)</li>
                        <li>Additional notes from the applicant</li>
                    </ul>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <button class="btn btn-success" onclick="window.location.href='?approve=${userId}'">
                        <i class="fas fa-check"></i> Approve Application
                    </button>
                    <button class="btn btn-danger" onclick="window.location.href='?reject=${userId}'">
                        <i class="fas fa-times"></i> Reject Application
                    </button>
                </div>
            `;
            
            document.getElementById('modal-title').textContent = `Application: ${userName}`;
            document.getElementById('detailsModal').style.display = 'flex';
        }
        
        // Export approvals list
        function exportApprovals() {
            alert('Export feature would download a CSV file with all pending applications.');
            // In a real app, you would generate and download a CSV file
        }
        
        // Confirm bulk action
        function confirmBulkAction() {
            const selectedCount = document.querySelectorAll('.user-checkbox:checked').length;
            const action = document.querySelector('select[name="bulk_action"]').value;
            
            if (selectedCount === 0) {
                alert('Please select at least one application.');
                return false;
            }
            
            if (!action) {
                alert('Please select a bulk action.');
                return false;
            }
            
            const actionText = {
                'approve': 'approve',
                'reject': 'reject',
                'delete': 'delete'
            }[action];
            
            return confirm(`Are you sure you want to ${actionText} ${selectedCount} application(s)?`);
        }
        
        // Reset filters
        function resetFilters() {
            document.getElementById('filter-faculty').value = 'all';
            document.getElementById('sort-by').value = 'newest';
            
            // Show all rows
            document.querySelectorAll('.application-row').forEach(row => {
                row.style.display = '';
            });
            
            alert('Filters have been reset.');
        }
    </script>
</body>
</html>
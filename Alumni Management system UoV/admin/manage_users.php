<?php 
session_start();
include '../config/db.php'; 
?>

<?php
// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id='$id' AND role='alumni'");
    header("Location: manage_users.php");
    exit();
}

// Search and filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$faculty_filter = isset($_GET['faculty']) ? mysqli_real_escape_string($conn, $_GET['faculty']) : '';

// Build SQL query
$sql = "SELECT * FROM users WHERE role='alumni'";

// Apply filters
if (!empty($status_filter)) {
    $sql .= " AND status='$status_filter'";
}

if (!empty($faculty_filter)) {
    $sql .= " AND faculty='$faculty_filter'";
}

if (!empty($search)) {
    $sql .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR reg_no LIKE '%$search%')";
}

$sql .= " ORDER BY status, name";
$result = $conn->query($sql);

// Get counts for stats
$total_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END) as rejected
    FROM users WHERE role='alumni'";
$count_result = $conn->query($total_sql);
$counts = $count_result->fetch_assoc();

// Get unique faculties for filter dropdown
$faculty_sql = "SELECT DISTINCT faculty FROM users WHERE faculty IS NOT NULL AND faculty != '' ORDER BY faculty";
$faculty_result = $conn->query($faculty_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Alumni - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Admin Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="university-logo">
                    <i class="fas fa-users" style="font-size: 1.8rem; color: #2c3e50;"></i>
                </div>
                <div class="nav-title">
                    <span class="university-name">Admin Panel</span>
                    <span class="system-name">Manage Alumni</span>
                </div>
            </div>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="approvals.php"><i class="fas fa-check-circle"></i> Approvals</a>
                <a href="manage_users.php" class="active"><i class="fas fa-users"></i> Alumni</a>
                <a href="manage_events.php"><i class="fas fa-calendar-alt"></i> Events</a>
                <a href="manage_announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-user-graduate"></i> Manage Alumni Records</h1>
            <p>Manage and review alumni information, status, and records</p>
        </div>
        
        <!-- Statistics -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Total Alumni</h3>
                <div class="stat-number stat-total"><?php echo $counts['total']; ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-approved">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Approved</h3>
                <div class="stat-number stat-approved"><?php echo $counts['approved']; ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-pending">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Pending</h3>
                <div class="stat-number stat-pending"><?php echo $counts['pending']; ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-rejected">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h3>Rejected</h3>
                <div class="stat-number stat-rejected"><?php echo $counts['rejected']; ?></div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
            <div class="action-buttons-grid">
                <a href="add_user.php" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Add New Alumni
                </a>
                <a href="approvals.php" class="btn btn-warning">
                    <i class="fas fa-check-double"></i> Pending Approvals
                </a>
                <button type="button" class="btn btn-secondary" onclick="exportAlumni()">
                    <i class="fas fa-file-export"></i> Export Data
                </button>
                <button type="button" class="btn btn-info" onclick="printTable()">
                    <i class="fas fa-print"></i> Print List
                </button>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filter-box">
            <h4><i class="fas fa-filter"></i> Filter Alumni</h4>
            <form method="GET" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="search"><i class="fas fa-search"></i> Search</label>
                        <input type="text" id="search" name="search" 
                               placeholder="Search by name, email or registration number" 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="status"><i class="fas fa-tag"></i> Status</label>
                        <select id="status" name="status">
                            <option value="">All Status</option>
                            <option value="approved" <?php echo ($status_filter == 'approved') ? 'selected' : ''; ?>>Approved</option>
                            <option value="pending" <?php echo ($status_filter == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="rejected" <?php echo ($status_filter == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="faculty"><i class="fas fa-university"></i> Faculty</label>
                        <select id="faculty" name="faculty">
                            <option value="">All Faculties</option>
                            <?php while($faculty = $faculty_result->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($faculty['faculty']); ?>"
                                    <?php echo ($faculty_filter == $faculty['faculty']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($faculty['faculty']); ?>
                                </option>
                            <?php endwhile; ?>
                            <?php $faculty_result->data_seek(0); // Reset pointer ?>
                        </select>
                    </div>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="manage_users.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear All
                    </a>
                    <button type="button" class="btn btn-outline" onclick="resetFilters()">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
                
                <?php if(!empty($search) || !empty($status_filter) || !empty($faculty_filter)): ?>
                    <div class="active-filters">
                        <strong>Active Filters:</strong>
                        <?php if(!empty($search)): ?>
                            <span class="filter-tag">Search: "<?php echo htmlspecialchars($search); ?>"</span>
                        <?php endif; ?>
                        <?php if(!empty($status_filter)): ?>
                            <span class="filter-tag status-<?php echo $status_filter; ?>">
                                Status: <?php echo ucfirst($status_filter); ?>
                            </span>
                        <?php endif; ?>
                        <?php if(!empty($faculty_filter)): ?>
                            <span class="filter-tag">Faculty: <?php echo htmlspecialchars($faculty_filter); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Alumni Table -->
        <div class="table-section">
            <div class="table-header">
                <h4><i class="fas fa-list"></i> Alumni List</h4>
                <div class="table-info">
                    <span class="record-count">
                        <i class="fas fa-database"></i> <?php echo $result->num_rows; ?> records found
                    </span>
                    <div class="table-actions">
                        <button type="button" class="btn btn-sm" onclick="refreshPage()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
            
            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="data-table" id="alumniTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>
                                    <i class="fas fa-user"></i> Name
                                    <button class="sort-btn" onclick="sortTable(1)"><i class="fas fa-sort"></i></button>
                                </th>
                                <th>
                                    <i class="fas fa-envelope"></i> Email
                                    <button class="sort-btn" onclick="sortTable(2)"><i class="fas fa-sort"></i></button>
                                </th>
                                <th>
                                    <i class="fas fa-id-card"></i> Reg No
                                    <button class="sort-btn" onclick="sortTable(3)"><i class="fas fa-sort"></i></button>
                                </th>
                                <th>
                                    <i class="fas fa-university"></i> Faculty
                                    <button class="sort-btn" onclick="sortTable(4)"><i class="fas fa-sort"></i></button>
                                </th>
                                <th>
                                    <i class="fas fa-graduation-cap"></i> Graduation Year
                                    <button class="sort-btn" onclick="sortTable(5)"><i class="fas fa-sort"></i></button>
                                </th>
                                <th>
                                    <i class="fas fa-tag"></i> Status
                                    <button class="sort-btn" onclick="sortTable(6)"><i class="fas fa-sort"></i></button>
                                </th>
                                <th>
                                    <i class="fas fa-cogs"></i> Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $counter = 1;
                            while($row = $result->fetch_assoc()): 
                                $status_class = 'status-' . $row['status'];
                            ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <?php 
                                                $initials = getInitials($row['name']);
                                                $color_class = getColorClass($row['faculty']);
                                                ?>
                                                <span class="avatar <?php echo $color_class; ?>">
                                                    <?php echo $initials; ?>
                                                </span>
                                            </div>
                                            <div class="user-details">
                                                <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                                                <?php if($row['current_job']): ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-briefcase"></i> 
                                                        <?php echo htmlspecialchars($row['current_job']); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" class="email-link">
                                            <i class="fas fa-envelope"></i> 
                                            <?php echo htmlspecialchars($row['email']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <code class="reg-no"><?php echo htmlspecialchars($row['reg_no']); ?></code>
                                    </td>
                                    <td>
                                        <span class="faculty-badge">
                                            <i class="fas fa-university"></i>
                                            <?php echo htmlspecialchars($row['faculty']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="year-badge">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?php echo htmlspecialchars($row['graduation_year']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <i class="fas fa-circle"></i>
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit_user.php?id=<?php echo $row['id']; ?>" 
                                               class="btn btn-sm btn-warning" 
                                               title="Edit Alumni">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-info view-btn"
                                                    title="View Details"
                                                    onclick="viewAlumni(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <a href="?delete=<?php echo $row['id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               title="Delete Alumni"
                                               onclick="return confirmDelete('<?php echo addslashes($row['name']); ?>', '<?php echo addslashes($row['email']); ?>')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination (if needed) -->
                <div class="pagination">
                    <span>Showing <?php echo $result->num_rows; ?> of <?php echo $counts['total']; ?> alumni</span>
                    <div class="pagination-controls">
                        <button class="btn btn-sm" disabled><i class="fas fa-chevron-left"></i> Previous</button>
                        <span class="page-info">Page 1 of 1</span>
                        <button class="btn btn-sm" disabled>Next <i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
                
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-user-slash"></i>
                    </div>
                    <h3>No Alumni Found</h3>
                    <p>No alumni records match your search criteria.</p>
                    <div class="empty-actions">
                        <a href="manage_users.php" class="btn btn-primary">
                            <i class="fas fa-redo"></i> Reset Filters
                        </a>
                        <a href="add_user.php" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Add New Alumni
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Action Buttons Footer -->
        <div class="action-footer">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <a href="add_user.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Add New Alumni
            </a>
        </div>
        
        <!-- Admin Notes -->
        <div class="admin-notes">
            <h4><i class="fas fa-lightbulb"></i> Admin Notes:</h4>
            <div class="notes-grid">
                <div class="note-item">
                    <i class="fas fa-edit text-warning"></i>
                    <div>
                        <strong>Edit Alumni:</strong> Update alumni information and details
                    </div>
                </div>
                <div class="note-item">
                    <i class="fas fa-trash text-danger"></i>
                    <div>
                        <strong>Delete Alumni:</strong> Permanently remove alumni records
                    </div>
                </div>
                <div class="note-item">
                    <i class="fas fa-clock text-warning"></i>
                    <div>
                        <strong>Pending Approvals:</strong> Approve/reject in Approvals section
                    </div>
                </div>
                <div class="note-item">
                    <i class="fas fa-filter text-info"></i>
                    <div>
                        <strong>Filtering:</strong> Use filters to find specific alumni records
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-btn')?.addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
            this.classList.toggle('active');
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const navLinks = document.querySelector('.nav-links');
            const mobileBtn = document.querySelector('.mobile-menu-btn');
            if (navLinks.classList.contains('active') && 
                !navLinks.contains(event.target) && 
                !mobileBtn.contains(event.target)) {
                navLinks.classList.remove('active');
                mobileBtn.classList.remove('active');
            }
        });
        
        // Confirm delete
        function confirmDelete(name, email) {
            return confirm(`Are you sure you want to delete this alumni?\n\nName: ${name}\nEmail: ${email}\n\nThis action cannot be undone.`);
        }
        
        // Reset filters
        function resetFilters() {
            window.location.href = 'manage_users.php';
        }
        
        // Refresh page
        function refreshPage() {
            window.location.reload();
        }
        
        // Export alumni data (placeholder)
        function exportAlumni() {
            alert('Export feature would generate CSV/Excel file with alumni data.');
            // In real implementation: window.location.href = 'export_alumni.php';
        }
        
        // Print table
        function printTable() {
            window.print();
        }
        
        // View alumni details (placeholder)
        function viewAlumni(id) {
            alert(`Viewing alumni ID: ${id}\nThis would open a detailed view in a real implementation.`);
            // In real implementation: window.location.href = 'view_user.php?id=' + id;
        }
        
        // Simple table sorting
        let sortDirection = true;
        
        function sortTable(columnIndex) {
            const table = document.getElementById('alumniTable');
            const rows = Array.from(table.querySelectorAll('tbody tr'));
            
            rows.sort((a, b) => {
                const aValue = a.cells[columnIndex].textContent.trim();
                const bValue = b.cells[columnIndex].textContent.trim();
                
                if (sortDirection) {
                    return aValue.localeCompare(bValue);
                } else {
                    return bValue.localeCompare(aValue);
                }
            });
            
            // Clear and re-add sorted rows
            const tbody = table.querySelector('tbody');
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
            
            sortDirection = !sortDirection;
        }
    </script>
</body>
</html>

<?php
// Helper functions
function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    $count = 0;
    
    foreach ($words as $word) {
        if ($count < 2) {
            $initials .= strtoupper(substr($word, 0, 1));
            $count++;
        }
    }
    
    return $initials;
}

function getColorClass($faculty) {
    $faculties = [
        'Technological Studies' => 'avatar-blue',
        'Applied Science' => 'avatar-green',
        'Business Studies' => 'avatar-purple'
    ];
    
    return $faculties[$faculty] ?? 'avatar-default';
}
?>
<?php
// Include the database connection
include '../config/db.php';

// Check if alumni is logged in - session is already started in db.php
// REMOVED: session_start(); // This was causing the duplicate session_start error
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'alumni') {
    header("Location: login.php");
    exit();
}

// Get search parameters
$faculty = isset($_GET['faculty']) ? mysqli_real_escape_string($conn, $_GET['faculty']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Build SQL query
$sql = "SELECT * FROM users WHERE role='alumni' AND status='approved'";

if (!empty($faculty)) {
    $sql .= " AND faculty='$faculty'";
}

if (!empty($search)) {
    $sql .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR reg_no LIKE '%$search%')";
}

$sql .= " ORDER BY name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Alumni Directory</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Alumni Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="university-logo">
                    <i class="fas fa-users" style="font-size: 1.8rem; color: #2c3e50;"></i>
                </div>
                <div class="nav-title">
                    <span class="university-name">Alumni Directory</span>
                    <?php if(isset($_SESSION['name'])): ?>
                        <span class="system-name">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                <a href="directory.php" class="active"><i class="fas fa-users"></i> Directory</a>
                <a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <div class="container">
        <h2>Browse Alumni Directory</h2>
        
        <!-- Search Form -->
        <form method="GET" class="form filter-form">
            <div class="form-group">
                <input type="text" name="search" placeholder="Search by name, email or reg no" 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            
            <div class="form-group">
                <select name="faculty">
                    <option value="">All Faculties</option>
                    <option value="Technological Studies" <?php echo ($faculty == 'Technological Studies') ? 'selected' : ''; ?>>Technological Studies</option>
                    <option value="Applied Science" <?php echo ($faculty == 'Applied Science') ? 'selected' : ''; ?>>Applied Science</option>
                    <option value="Business Studies" <?php echo ($faculty == 'Business Studies') ? 'selected' : ''; ?>>Business Studies</option>
                </select>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="directory.php" class="btn btn-secondary">Clear</a>
            </div>
        </form>

        <!-- Alumni Table -->
        <div class="table-container">
            <?php if ($result && $result->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Registration No</th>
                            <th>Email</th>
                            <th>Faculty</th>
                            <th>Graduation Year</th>
                            <th>Current Job</th>
                            <th>Company</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['reg_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <span class="faculty-badge"><?php echo htmlspecialchars($row['faculty']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($row['graduation_year']); ?></td>
                            <td><?php echo htmlspecialchars($row['current_job'] ?: 'Not provided'); ?></td>
                            <td><?php echo htmlspecialchars($row['company'] ?: 'Not provided'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                
                <div style="margin-top: 20px; text-align: center;">
                    <p><strong>Found <?php echo $result->num_rows; ?> alumni records</strong></p>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <h4>No Alumni Found</h4>
                    <p>No alumni records match your search criteria.</p>
                    <p>Try different filters or clear the search to see all alumni.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        </div>
    </div>

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
        
        // Add faculty badge styling if not already in CSS
        document.addEventListener('DOMContentLoaded', function() {
            const style = document.createElement('style');
            style.textContent = `
                .faculty-badge {
                    display: inline-block;
                    padding: 4px 10px;
                    border-radius: 12px;
                    font-size: 0.85em;
                    font-weight: bold;
                    background: #e3f2fd;
                    color: #1565c0;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>
<?php
// Include the database connection
include '../config/db.php';

// Check if admin is logged in - session is already started in db.php
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission for creating/updating announcements
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Add new announcement
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $content = mysqli_real_escape_string($conn, $_POST['content']);
        $posted_by = $_SESSION['admin_id'];
        
        $sql = "INSERT INTO announcements (title, content, posted_by) 
                VALUES ('$title', '$content', '$posted_by')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Announcement added successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
    
    if (isset($_POST['update'])) {
        // Update announcement
        $id = intval($_POST['announcement_id']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $content = mysqli_real_escape_string($conn, $_POST['content']);
        
        $sql = "UPDATE announcements SET title='$title', content='$content' WHERE id='$id'";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Announcement updated successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM announcements WHERE id='$id'");
    header("Location: manage_announcements.php");
    exit();
}

// Handle edit
$edit_announcement = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM announcements WHERE id='$id'");
    if ($result && $result->num_rows > 0) {
        $edit_announcement = $result->fetch_assoc();
    }
}

// Get all announcements
$sql = "SELECT a.*, u.name as posted_by_name 
        FROM announcements a 
        LEFT JOIN users u ON a.posted_by = u.id 
        ORDER BY a.posted_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Announcements</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>
<body>
    <!-- Admin Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="university-logo">
                    <i class="fas fa-bullhorn" style="font-size: 1.8rem; color: #2c3e50;"></i>
                </div>
                <div class="nav-title">
                    <span class="university-name">Admin Panel</span>
                    <span class="system-name">Manage Announcements</span>
                </div>
            </div>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="approvals.php"><i class="fas fa-check-circle"></i> Approvals</a>
                <a href="manage_users.php"><i class="fas fa-users"></i> Alumni</a>
                <a href="manage_events.php"><i class="fas fa-calendar-alt"></i> Events</a>
                <a href="manage_announcements.php" class="active"><i class="fas fa-bullhorn"></i> Announcements</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <div class="container">
        <h2><?php echo isset($edit_announcement) ? 'Edit Announcement' : 'Add New Announcement'; ?></h2>
        
        <?php if(isset($message)): ?>
            <div class="alert success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Add/Edit Announcement Form -->
        <form method="POST" class="form">
            <?php if(isset($edit_announcement)): ?>
                <input type="hidden" name="announcement_id" value="<?php echo $edit_announcement['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label>Announcement Title:</label>
                <input type="text" name="title" 
                       value="<?php echo isset($edit_announcement) ? htmlspecialchars($edit_announcement['title']) : ''; ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label>Content:</label>
                <textarea name="content" rows="6" required><?php echo isset($edit_announcement) ? htmlspecialchars($edit_announcement['content']) : ''; ?></textarea>
            </div>
            
            <div class="form-buttons">
                <?php if(isset($edit_announcement)): ?>
                    <button type="submit" name="update" class="btn btn-primary">Update Announcement</button>
                    <a href="manage_announcements.php" class="btn btn-secondary">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="add" class="btn btn-primary">Add Announcement</button>
                <?php endif; ?>
            </div>
        </form>

        <!-- Announcements List -->
        <h3>All Announcements</h3>
        <div class="table-container">
            <?php if ($result && $result->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Posted By</th>
                            <th>Posted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars(substr($row['content'], 0, 100)) . '...'; ?></td>
                            <td><?php echo htmlspecialchars($row['posted_by_name']); ?></td>
                            <td><?php echo date('d M Y, h:i A', strtotime($row['posted_at'])); ?></td>
                            <td class="action-buttons">
                                <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                                <a href="?delete=<?php echo $row['id']; ?>" 
                                   class="btn btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this announcement?\n\nTitle: <?php echo addslashes($row['title']); ?>')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>No announcements found.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        </div>
        
        <div class="admin-notes" style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <h4>Admin Notes:</h4>
            <ul>
                <li>Announcements are visible to all alumni on their dashboard</li>
                <li>Keep announcements clear and concise</li>
                <li>Important updates should be posted as announcements</li>
                <li>Use the edit feature to update existing announcements</li>
            </ul>
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
    </script>
</body>
</html>
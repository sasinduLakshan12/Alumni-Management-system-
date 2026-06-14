<?php
// Add session_start() at the beginning
session_start();
include '../config/db.php';

// ... existing code ...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Announcement - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="university-logo">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div class="nav-title">
                    <span class="university-name">Admin Panel</span>
                    <span class="system-name">Add Announcement</span>
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
        <h2><i class="fas fa-plus-circle"></i> Add New Announcement</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="form">
            <div class="form-group">
                <label><i class="fas fa-heading"></i> Announcement Title:</label>
                <input type="text" name="title" required placeholder="Enter announcement title">
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-file-alt"></i> Content:</label>
                <textarea name="content" rows="6" required placeholder="Enter announcement content"></textarea>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Add Announcement
                </button>
                <a href="manage_announcements.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
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
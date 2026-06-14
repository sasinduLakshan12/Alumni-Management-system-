<?php
// Include the database connection
include '../config/db.php';

// Check if alumni is logged in - session is already started in db.php
// REMOVED: session_start(); // This was causing the duplicate session_start error
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'alumni') {
    header("Location: login.php");
    exit();
}

// Get all events (upcoming first)
$sql = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Events</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Alumni Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="university-logo">
                    <i class="fas fa-calendar-alt" style="font-size: 1.8rem; color: #2c3e50;"></i>
                </div>
                <div class="nav-title">
                    <span class="university-name">Alumni Events</span>
                    <?php if(isset($_SESSION['name'])): ?>
                        <span class="system-name">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                <a href="directory.php"><i class="fas fa-users"></i> Directory</a>
                <a href="events.php" class="active"><i class="fas fa-calendar-alt"></i> Events</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <div class="container">
        <h2>Upcoming Events</h2>
        
        <div class="events-container">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($event = $result->fetch_assoc()): ?>
                    <div class="event-card">
                        <div class="event-header">
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            <span class="event-date">
                                <?php echo date('d M Y', strtotime($event['event_date'])); ?>
                            </span>
                        </div>
                        
                        <div class="event-details">
                            <p><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-events">
                    <p>No upcoming events scheduled.</p>
                    <p>Check back later for new events!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Past Events Section -->
        <h3>Past Events</h3>
        <?php
        $past_sql = "SELECT * FROM events WHERE event_date < CURDATE() ORDER BY event_date DESC LIMIT 5";
        $past_result = $conn->query($past_sql);
        
        if ($past_result && $past_result->num_rows > 0): 
        ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date</th>
                            <th>Venue</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($past_event = $past_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($past_event['title']); ?></td>
                            <td><?php echo date('d M Y', strtotime($past_event['event_date'])); ?></td>
                            <td><?php echo htmlspecialchars($past_event['venue']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>No past events recorded.</p>
            </div>
        <?php endif; ?>
        
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
    </script>
</body>
</html>
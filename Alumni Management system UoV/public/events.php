<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - University of Vavuniya Alumni</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/events.css">
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
                    <span class="system-name">Alumni Events</span>
                </div>
            </div>
            <div class="nav-links">
                <a href="../index.php"><i class="fas fa-home"></i> Home</a>
                <a href="view_alumni.php"><i class="fas fa-users"></i> View Alumni</a>
                <a href="events.php" class="active"><i class="fas fa-calendar-alt"></i> Events</a>
                <a href="announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a>
                <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                <a href="../alumni/login.php"><i class="fas fa-sign-in-alt"></i> Alumni Login</a>
                <a href="../admin/login.php"><i class="fas fa-lock"></i> Admin Login</a>
            </div>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Events Header -->
    <header class="events-header">
        <div class="hero-content">
            <h1>Alumni Events & Activities</h1>
            <p class="hero-text">Stay connected with university events, reunions, workshops, and networking opportunities organized for our alumni community.</p>
        </div>
    </header>

    <div class="events-container">
        <!-- View Toggle (Optional - can be added for calendar view) -->
        <div class="view-toggle">
            <button class="view-btn active" data-view="grid">
                <i class="fas fa-th-large"></i> Grid View
            </button>
            <button class="view-btn" data-view="calendar">
                <i class="fas fa-calendar"></i> Calendar View
            </button>
        </div>
        
        <!-- Events Filter -->
        <div class="events-filter">
            <button class="filter-btn active" data-filter="all">
                <i class="fas fa-filter"></i> All Events
            </button>
            <button class="filter-btn" data-filter="upcoming">
                <i class="fas fa-clock"></i> Upcoming
            </button>
            <button class="filter-btn" data-filter="past">
                <i class="fas fa-history"></i> Past Events
            </button>
        </div>
        
        <!-- Events Grid -->
        <div class="events-grid">
            <?php
            // Fetch events from database
            $sql = "SELECT e.*, u.name as organizer 
                    FROM events e 
                    LEFT JOIN users u ON e.created_by = u.id 
                    ORDER BY e.event_date DESC";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0):
                $current_date = date('Y-m-d');
                while($row = $result->fetch_assoc()):
                    $event_date = date('Y-m-d', strtotime($row['event_date']));
                    $is_past = $event_date < $current_date;
                    $event_class = $is_past ? 'past-event' : 'upcoming-event';
                    ?>
                    <div class="event-card <?php echo $event_class; ?>" data-date="<?php echo $event_date; ?>">
                        <div class="event-date">
                            <span class="day"><?php echo date('d', strtotime($row['event_date'])); ?></span>
                            <span class="month"><?php echo date('M', strtotime($row['event_date'])); ?></span>
                            <span class="year"><?php echo date('Y', strtotime($row['event_date'])); ?></span>
                        </div>
                        <div class="event-content">
                            <h3 class="event-title">
                                <a href="event-details.php?id=<?php echo $row['id']; ?>">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </a>
                            </h3>
                            
                            <div class="event-venue">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($row['venue']); ?></span>
                            </div>
                            
                            <p class="event-description">
                                <?php echo htmlspecialchars(substr($row['description'], 0, 150)); ?>
                                <?php if (strlen($row['description']) > 150): ?>...<?php endif; ?>
                            </p>
                            
                            <div class="event-meta">
                                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($row['organizer'] ?: 'University Admin'); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($row['event_date'])); ?></span>
                            </div>
                            
                            <div class="event-actions">
                                <a href="event-details.php?id=<?php echo $row['id']; ?>" class="btn-event">
                                    <i class="fas fa-info-circle"></i> View Details
                                </a>
                                <?php if (!$is_past): ?>
                                    <button class="btn-event" onclick="registerForEvent(<?php echo $row['id']; ?>, '<?php echo addslashes($row['title']); ?>')">
                                        <i class="fas fa-user-plus"></i> Register
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            else: ?>
                <div class="no-events">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No Events Scheduled</h3>
                    <p>Check back later for upcoming events and activities. Alumni can also suggest events through their dashboard.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Calendar View (Optional) -->
        <div class="calendar-view">
            <p>Calendar view will be implemented here.</p>
        </div>
        
        <!-- Event Suggestions -->
        <div class="event-suggestions">
            <h3>Suggest an Event</h3>
            <p>Have an idea for an alumni event? <a href="../alumni/login.php">Login to your alumni account</a> to suggest events or contact us at events@vau.ac.lk.</p>
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
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="../index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="view_alumni.php"><i class="fas fa-users"></i> Alumni Directory</a></li>
                    <li><a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                    <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>Follow Us</h3>
                <p>Stay connected through our social media channels</p>
                <div class="social-icons">
                    <a href="#" class="social-icon" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-icon" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> University of Vavuniya - Alumni Management System. All rights reserved.</p>
            <p>Group Project - Advanced Web Technologies | Group 30</p>
        </div>
    </footer>

    <script src="../js/script.js"></script>
    <script src="../js/events.js"></script>
</body>
</html>
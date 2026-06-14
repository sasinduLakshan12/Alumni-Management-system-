<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - University of Vavuniya Alumni</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/announcements.css">
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
                    <span class="system-name">Announcements</span>
                </div>
            </div>
            <div class="nav-links">
                <a href="../index.php"><i class="fas fa-home"></i> Home</a>
                <a href="view_alumni.php"><i class="fas fa-users"></i> View Alumni</a>
                <a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a>
                <a href="announcements.php" class="active"><i class="fas fa-bullhorn"></i> Announcements</a>
                <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                <a href="../alumni/login.php"><i class="fas fa-sign-in-alt"></i> Alumni Login</a>
                <a href="../admin/login.php"><i class="fas fa-lock"></i> Admin Login</a>
            </div>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Announcements Header -->
    <header class="announcements-header">
        <div class="hero-content">
            <h1>Latest Announcements</h1>
            <p class="hero-text">Stay updated with important news, updates, and information from University of Vavuniya</p>
        </div>
    </header>

    <div class="announcements-container">
        <!-- Search Bar -->
        <div class="announcements-search">
            <div class="search-container">
                <input type="text" placeholder="Search announcements...">
                <i class="fas fa-search"></i>
            </div>
        </div>
        
        <!-- Category Filter -->
        <div class="announcement-categories">
            <button class="category-btn active" data-category="all">
                <i class="fas fa-layer-group"></i> All Announcements
            </button>
            <button class="category-btn" data-category="important">
                <i class="fas fa-exclamation-circle"></i> Important
            </button>
            <button class="category-btn" data-category="urgent">
                <i class="fas fa-bell"></i> Urgent
            </button>
            <button class="category-btn" data-category="info">
                <i class="fas fa-info-circle"></i> Information
            </button>
        </div>
        
        <!-- Announcements List -->
        <?php
        // Fetch announcements from database
        $sql = "SELECT a.*, u.name as posted_by_name 
                FROM announcements a 
                LEFT JOIN users u ON a.posted_by = u.id 
                ORDER BY a.posted_at DESC";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0):
            while($row = $result->fetch_assoc()):
                // Check if announcement is recent (last 7 days)
                $post_date = strtotime($row['posted_at']);
                $current_date = time();
                $days_diff = floor(($current_date - $post_date) / (60 * 60 * 24));
                $is_recent = $days_diff <= 7;
                
                // Determine announcement type based on title/content
                $announcement_type = 'info';
                $title = strtolower($row['title']);
                $content = strtolower($row['content']);
                
                if (strpos($title, 'urgent') !== false || strpos($content, 'urgent') !== false) {
                    $announcement_type = 'urgent';
                } elseif (strpos($title, 'important') !== false || strpos($content, 'important') !== false) {
                    $announcement_type = 'important';
                }
                
                // Truncate content for preview
                $preview_content = strlen($row['content']) > 200 ? 
                    substr($row['content'], 0, 200) . '...' : 
                    $row['content'];
                ?>
                
                <div class="announcement-card <?php echo $announcement_type; ?>-announcement" 
                     data-category="<?php echo $announcement_type; ?>">
                    
                    <!-- Status Indicator -->
                    <span class="announcement-status <?php echo $is_recent ? 'new' : 'old'; ?>"></span>
                    
                    <div class="announcement-header">
                        <h3 class="announcement-title">
                            <?php echo htmlspecialchars($row['title']); ?>
                            <?php if($is_recent): ?>
                                <span class="announcement-badge">NEW</span>
                            <?php endif; ?>
                        </h3>
                        <span class="announcement-date">
                            <i class="far fa-clock"></i> 
                            <?php echo date('d M Y, h:i A', strtotime($row['posted_at'])); ?>
                        </span>
                    </div>
                    
                    <div class="announcement-content">
                        <?php echo nl2br(htmlspecialchars($preview_content)); ?>
                        
                        <?php if (strlen($row['content']) > 200): ?>
                            <a href="#" class="read-more" data-id="<?php echo $row['id']; ?>">
                                Read More <i class="fas fa-arrow-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Tags (Optional - you can add tags field to database) -->
                    <div class="announcement-tags">
                        <span class="announcement-tag"><?php echo ucfirst($announcement_type); ?></span>
                        <span class="announcement-tag">University</span>
                        <span class="announcement-tag">Alumni</span>
                    </div>
                    
                    <div class="announcement-meta">
                        <div class="announcement-author">
                            <i class="fas fa-user"></i>
                            <span>Posted by: <?php echo htmlspecialchars($row['posted_by_name'] ?: 'University Admin'); ?></span>
                        </div>
                        <div class="announcement-type">
                            <i class="fas fa-bullhorn"></i>
                            <span>Announcement</span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            
            <!-- Pagination -->
            <div class="announcements-pagination">
                <button class="pagination-btn" disabled>
                    <i class="fas fa-chevron-left"></i> Previous
                </button>
                <button class="pagination-btn active">1</button>
                <button class="pagination-btn">2</button>
                <button class="pagination-btn">3</button>
                <button class="pagination-btn">
                    Next <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
        <?php else: ?>
            <div class="no-announcements">
                <i class="fas fa-bullhorn"></i>
                <h3>No Announcements Available</h3>
                <p>Check back later for important updates, news, and information from the university.</p>
                <p>Registered alumni can receive announcements directly via email.</p>
            </div>
        <?php endif; ?>
        
        <!-- Announcement Subscription -->
        <div class="announcement-subscription">
            <h3>Stay Updated</h3>
            <p>Want to receive announcements directly in your inbox? <a href="../alumni/login.php">Login to your alumni account</a> and enable email notifications, or contact us at announcements@vau.ac.lk.</p>
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
    <script src="../js/announcements.js"></script>
</body>
</html>
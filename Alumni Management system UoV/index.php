<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Management System - University of Vavuniya</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/home.css">
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
                    <span class="system-name">Alumni System</span>
                </div>
            </div>
            <div class="nav-links">
                <a href="index.php" class="active"><i class="fas fa-home"></i> Home</a>
                <a href="public/view_alumni.php"><i class="fas fa-users"></i> View Alumni</a>
                <a href="public/events.php"><i class="fas fa-calendar-alt"></i> Events</a>
                <a href="public/announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a>
                <a href="public/register.php"><i class="fas fa-user-plus"></i> Register</a>
                <a href="alumni/login.php"><i class="fas fa-sign-in-alt"></i> Alumni Login</a>
                <a href="admin/login.php"><i class="fas fa-lock"></i> Admin Login</a>
            </div>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="university-header">
                <img src="image/logo_1.png" alt="University of Vavuniya Logo" class="university-logo">
                <div class="university-name">
                    <h1>University of Vavuniya</h1>
                    <h2>Alumni Management System</h2>
                </div>
            </div>
            
            <p class="hero-text">
                Connecting generations of graduates from Faculties of Technological Studies, Applied Science, 
                and Business Studies. Stay connected with your alma mater and fellow alumni.
            </p>
            
            <div class="hero-buttons">
                <a href="public/register.php" class="btn-hero btn-hero-primary">
                    <i class="fas fa-user-graduate"></i> Register as Alumni
                </a>
                <a href="public/view_alumni.php" class="btn-hero btn-hero-secondary">
                    <i class="fas fa-search"></i> Browse Alumni Directory
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="section-title">
            <h2>Why Join Our Alumni Network?</h2>
            <p>Discover the benefits of staying connected with your university</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-network-wired"></i>
                </div>
                <h3>Professional Networking</h3>
                <p>Connect with fellow alumni across industries and build valuable professional relationships.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h3>Career Opportunities</h3>
                <p>Access exclusive job postings, internships, and career development resources.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3>Events & Reunions</h3>
                <p>Stay updated with university events, workshops, and annual alumni reunions.</p>
            </div>
        </div>
    </section>

    <!-- Faculties Section -->
    <section class="faculties-section">
        <div class="section-title">
            <h2>Our Faculties</h2>
            <p>Three distinct faculties producing exceptional graduates</p>
        </div>
        
        <div class="faculty-cards">
            <div class="faculty-card technology">
                <div class="faculty-icon">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <h3>Faculty of Technological Studies</h3>
                <p>Producing innovators and technology leaders with cutting-edge technical skills.</p>
                <div class="faculty-stats">
                    <p><i class="fas fa-users"></i> 500+ Alumni</p>
                    <p><i class="fas fa-graduation-cap"></i> Since 2016</p>
                </div>
            </div>
            
            <div class="faculty-card science">
                <div class="faculty-icon">
                    <i class="fas fa-flask"></i>
                </div>
                <h3>Faculty of Applied Science</h3>
                <p>Advancing scientific knowledge and practical applications through research.</p>
                <div class="faculty-stats">
                    <p><i class="fas fa-users"></i> 450+ Alumni</p>
                    <p><i class="fas fa-graduation-cap"></i> Since 2016</p>
                </div>
            </div>
            
            <div class="faculty-card business">
                <div class="faculty-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Faculty of Business Studies</h3>
                <p>Developing business leaders and entrepreneurs for the global marketplace.</p>
                <div class="faculty-stats">
                    <p><i class="fas fa-users"></i> 400+ Alumni</p>
                    <p><i class="fas fa-graduation-cap"></i> Since 2016</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">1350+</div>
                <p>Total Alumni</p>
            </div>
            <div class="stat-item">
                <div class="stat-number">15+</div>
                <p>Annual Events</p>
            </div>
            <div class="stat-item">
                <div class="stat-number">95%</div>
                <p>Employment Rate</p>
            </div>
            <div class="stat-item">
                <div class="stat-number">3</div>
                <p>Faculties</p>
            </div>
        </div>
    </section>

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
                    <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="public/register.php"><i class="fas fa-user-plus"></i> Alumni Registration</a></li>
                    <li><a href="public/view_alumni.php"><i class="fas fa-users"></i> Alumni Directory</a></li>
                    <li><a href="alumni/login.php"><i class="fas fa-sign-in-alt"></i> Alumni Login</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>Follow Us</h3>
                <p>Stay connected through our social media channels</p>
                <div class="social-icons">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> University of Vavuniya - Alumni Management System. All rights reserved.</p>
            <p>Group Project - Advanced Web Technologies | Group 30</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
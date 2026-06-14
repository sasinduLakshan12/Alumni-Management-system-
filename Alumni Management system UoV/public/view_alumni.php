<?php include '../config/db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Directory - University of Vavuniya</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/directory.css">
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
                    <span class="system-name">Alumni Directory</span>
                </div>
            </div>
            <div class="nav-links">
                <a href="../index.php"><i class="fas fa-home"></i> Home</a>
                <a href="view_alumni.php" class="active"><i class="fas fa-users"></i> View Alumni</a>
                <a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a>
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
    
    <div class="container">
        <h1>Alumni Directory</h1>
        <p class="directory-description">Browse our alumni community (Public View - Read Only)</p>
        
        <?php
        // Get alumni count
        $count_sql = "SELECT COUNT(*) as total FROM users WHERE role='alumni' AND status='approved'";
        $count_result = $conn->query($count_sql);
        $count_row = $count_result->fetch_assoc();
        $total_alumni = $count_row['total'];
        ?>
        
        <div class="stats-info">
            <p><strong><i class="fas fa-users"></i> Total Alumni:</strong> <?php echo $total_alumni; ?> registered members</p>
            <p><i class="fas fa-info-circle"></i> This directory shows approved alumni from all faculties.</p>
        </div>
        
        <?php
        $sql = "SELECT * FROM users WHERE role='alumni' AND status='approved' ORDER BY name";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            echo '<div class="table-container">';
            echo '<table class="data-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i></th>
                            <th><i class="fas fa-user"></i> Name</th>
                            <th><i class="fas fa-id-card"></i> Registration No</th>
                            <th><i class="fas fa-graduation-cap"></i> Graduation Year</th>
                            <th><i class="fas fa-university"></i> Faculty</th>
                            <th><i class="fas fa-briefcase"></i> Current Job</th>
                            <th><i class="fas fa-building"></i> Company</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            $counter = 1;
            while($row = $result->fetch_assoc()) {
                $faculty_class = '';
                switch(strtolower($row['faculty'])) {
                    case 'technology':
                        $faculty_class = 'technology';
                        break;
                    case 'science':
                        $faculty_class = 'science';
                        break;
                    case 'business':
                        $faculty_class = 'business';
                        break;
                }
                
                echo "<tr>
                        <td>" . $counter++ . "</td>
                        <td><strong>" . htmlspecialchars($row['name']) . "</strong></td>
                        <td>" . htmlspecialchars($row['reg_no']) . "</td>
                        <td>" . htmlspecialchars($row['graduation_year']) . "</td>
                        <td><span class='faculty-badge " . $faculty_class . "'>" . htmlspecialchars($row['faculty']) . "</span></td>
                        <td>" . htmlspecialchars($row['current_job'] ?: 'Not provided') . "</td>
                        <td>" . htmlspecialchars($row['company'] ?: 'Not provided') . "</td>
                      </tr>";
            }
            echo '</tbody></table>';
            echo '</div>';
        } else {
            echo '<div class="no-data">
                    <i class="fas fa-users-slash" style="font-size: 3rem; color: #bdc3c7; margin-bottom: 20px;"></i>
                    <p>No alumni records found in the directory.</p>
                    <p>Alumni registrations might be pending approval.</p>
                  </div>';
        }
        ?>
        
        <div class="directory-actions">
            <a href="../index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
            <?php if($total_alumni > 0): ?>
                <span class="record-count">
                    <i class="fas fa-chart-bar"></i> Showing <?php echo $total_alumni; ?> alumni records
                </span>
            <?php endif; ?>
        </div>
        
        <div class="directory-info">
            <h3><i class="fas fa-info-circle"></i> About This Directory</h3>
            <p>This public directory displays approved alumni information. Alumni can register to update their profiles and access additional features.</p>
            <ul>
                <li>Alumni can register and update their professional information</li>
                <li>All registrations require admin approval</li>
                <li>Contact information is available to registered alumni only</li>
                <li>For corrections, alumni should login and update their profiles</li>
                <li>Data is updated regularly from our alumni database</li>
            </ul>
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
                    <li><a href="register.php"><i class="fas fa-user-plus"></i> Alumni Registration</a></li>
                    <li><a href="view_alumni.php"><i class="fas fa-users"></i> Alumni Directory</a></li>
                    <li><a href="../alumni/login.php"><i class="fas fa-sign-in-alt"></i> Alumni Login</a></li>
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

    <script src="../js/script.js"></script>
</body>
</html>
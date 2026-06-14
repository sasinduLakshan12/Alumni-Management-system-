<?php include '../config/db.php'; ?>

<?php
// Check if alumni is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'alumni') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_job = mysqli_real_escape_string($conn, $_POST['current_job']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    $sql = "UPDATE users SET current_job='$current_job', company='$company', phone='$phone' 
            WHERE id='$user_id'";
    
    if ($conn->query($sql) === TRUE) {
        $message = "Profile updated successfully!";
    } else {
        $error = "Error updating profile: " . $conn->error;
    }
}

// Get current user data
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Alumni Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="university-logo">
                    <i class="fas fa-user-graduate" style="font-size: 1.8rem; color: #2c3e50;"></i>
                </div>
                <div class="nav-title">
                    <span class="university-name">Alumni Profile</span>
                    <span class="system-name">Welcome, <?php echo htmlspecialchars($user['name']); ?></span>
                </div>
            </div>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="profile.php" class="active"><i class="fas fa-user"></i> My Profile</a>
                <a href="directory.php"><i class="fas fa-users"></i> Directory</a>
                <a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>
    
    <div class="container">
        <h2>My Profile</h2>
        
        <?php if(isset($message)): ?>
            <div class="alert success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="profile-info">
            <h3>Personal Information</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Registration No:</strong> <?php echo htmlspecialchars($user['reg_no']); ?></p>
            <p><strong>Graduation Year:</strong> <?php echo htmlspecialchars($user['graduation_year']); ?></p>
            <p><strong>Faculty:</strong> <?php echo htmlspecialchars($user['faculty']); ?></p>
            <?php if($user['phone']): ?>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
            <?php endif; ?>
        </div>
        
        <h3>Update Professional Details</h3>
        <form method="POST" class="form">
            <div class="form-group">
                <label>Current Job:</label>
                <input type="text" name="current_job" value="<?php echo htmlspecialchars($user['current_job']); ?>" 
                       placeholder="Enter your current job title">
            </div>
            
            <div class="form-group">
                <label>Company:</label>
                <input type="text" name="company" value="<?php echo htmlspecialchars($user['company']); ?>" 
                       placeholder="Enter your company name">
            </div>
            
            <div class="form-group">
                <label>Phone:</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" 
                       placeholder="Enter your phone number">
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Update Profile</button>
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
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
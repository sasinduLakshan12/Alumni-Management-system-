<?php 
// Start session and include database connection
session_start();
include '../config/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $reg_no = $conn->real_escape_string($_POST['reg_no']);
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $graduation_year = $conn->real_escape_string($_POST['graduation_year']);
    $faculty = $conn->real_escape_string($_POST['faculty']);
    $current_job = $conn->real_escape_string($_POST['current_job'] ?? '');
    $company = $conn->real_escape_string($_POST['company'] ?? '');
    
    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = '$email'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $error = "This email is already registered. Please use a different email or <a href='../alumni/login.php'>login here</a>.";
    } else {
        // Check if registration number already exists
        $check_reg_sql = "SELECT id FROM users WHERE reg_no = '$reg_no'";
        $check_reg_result = $conn->query($check_reg_sql);
        
        if ($check_reg_result->num_rows > 0) {
            $error = "This registration number is already registered. Please use a different registration number or <a href='../alumni/login.php'>login here</a>.";
        } else {
            // Insert new alumni
            $sql = "INSERT INTO users (reg_no, name, email, password, graduation_year, 
                    faculty, current_job, company, role, status, created_at) 
                    VALUES ('$reg_no', '$name', '$email', '$password', '$graduation_year', 
                    '$faculty', '$current_job', '$company', 'alumni', 'pending', NOW())";
            
            if ($conn->query($sql) === TRUE) {
                $message = "🎉 Registration successful! Your account is pending admin approval. You will receive an email notification once approved.";
                
                // Clear form data
                $_POST = array();
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register as Alumni - University of Vavuniya</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/register.css">
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
                    <span class="system-name">Alumni Registration</span>
                </div>
            </div>
            <div class="nav-links">
                <a href="../index.php"><i class="fas fa-home"></i> Home</a>
                <a href="view_alumni.php"><i class="fas fa-users"></i> View Alumni</a>
                <a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a>
                <a href="announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a>
                <a href="register.php" class="active"><i class="fas fa-user-plus"></i> Register</a>
                <a href="../alumni/login.php"><i class="fas fa-sign-in-alt"></i> Alumni Login</a>
                <a href="../admin/login.php"><i class="fas fa-lock"></i> Admin Login</a>
            </div>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>
    
    <!-- Registration Header -->
    <header class="registration-header">
        <div class="hero-content">
            <h1>Join Our Alumni Network</h1>
            <p>Register as an alumnus of University of Vavuniya and stay connected with your alma mater</p>
        </div>
    </header>

    <div class="container">
        <div class="registration-content">
            <!-- Registration Steps -->
            <div class="registration-steps">
                <div class="step active">
                    <div class="step-number">1</div>
                    <div class="step-text">Personal Details</div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-text">Academic Info</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-text">Professional Info</div>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-text">Complete</div>
                </div>
            </div>
            
            <!-- Alerts -->
            <?php if($message): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $message; ?></span>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            
            <!-- Registration Form -->
            <div class="registration-form">
                <h2><i class="fas fa-user-graduate"></i> Alumni Registration Form</h2>
                
                <form method="POST" class="form">
                    <div class="form-grid">
                        <!-- Personal Information -->
                        <div class="form-section">
                            <h3><i class="fas fa-user"></i> Personal Information</h3>
                            
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" required 
                                       placeholder="Enter your full name"
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                                <div class="error-message"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required 
                                       placeholder="example@email.com"
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                <div class="error-message"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Password *</label>
                                <div style="position: relative;">
                                    <input type="password" id="password" name="password" required 
                                           placeholder="Minimum 6 characters"
                                           minlength="6">
                                    <button type="button" onclick="togglePasswordVisibility('password')" 
                                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #7f8c8d; cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="error-message"></div>
                                
                                <!-- Password Strength Indicator -->
                                <div class="password-strength">
                                    <div class="strength-bar strength-weak">
                                        <div class="strength-fill"></div>
                                    </div>
                                    <div class="strength-text">Password strength</div>
                                </div>
                                
                                <!-- Password Requirements -->
                                <div class="password-requirements">
                                    <p><strong>Password must contain:</strong></p>
                                    <ul>
                                        <li>At least 6 characters</li>
                                        <li>Uppercase and lowercase letters</li>
                                        <li>At least one number</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Academic Information -->
                        <div class="form-section">
                            <h3><i class="fas fa-graduation-cap"></i> Academic Information</h3>
                            
                            <div class="form-group">
                                <label for="reg_no">Registration Number *</label>
                                <input type="text" id="reg_no" name="reg_no" required 
                                       placeholder="e.g., 2021/ICTS/42"
                                       value="<?php echo htmlspecialchars($_POST['reg_no'] ?? ''); ?>">
                                <div class="error-message"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="graduation_year">Graduation Year *</label>
                                <input type="number" id="graduation_year" name="graduation_year" required 
                                       min="1900" max="<?php echo date('Y') + 1; ?>" 
                                       placeholder="e.g., 2021"
                                       value="<?php echo htmlspecialchars($_POST['graduation_year'] ?? date('Y') - 1); ?>">
                                <div class="error-message"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="faculty">Faculty *</label>
                                <select id="faculty" name="faculty" required>
                                    <option value="">Select Faculty</option>
                                    <option value="Technological Studies" <?php echo (($_POST['faculty'] ?? '') == 'Technological Studies') ? 'selected' : ''; ?>>Faculty of Technological Studies</option>
                                    <option value="Applied Science" <?php echo (($_POST['faculty'] ?? '') == 'Applied Science') ? 'selected' : ''; ?>>Faculty of Applied Science</option>
                                    <option value="Business Studies" <?php echo (($_POST['faculty'] ?? '') == 'Business Studies') ? 'selected' : ''; ?>>Faculty of Business Studies</option>
                                </select>
                                <div class="error-message"></div>
                            </div>
                        </div>
                        
                        <!-- Professional Information -->
                        <div class="form-section">
                            <h3><i class="fas fa-briefcase"></i> Professional Information (Optional)</h3>
                            
                            <div class="form-group">
                                <label for="current_job">Current Job Title</label>
                                <input type="text" id="current_job" name="current_job" 
                                       placeholder="e.g., Software Engineer, Marketing Manager"
                                       value="<?php echo htmlspecialchars($_POST['current_job'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="company">Company/Organization</label>
                                <input type="text" id="company" name="company" 
                                       placeholder="e.g., Tech Corporation, ABC Ltd."
                                       value="<?php echo htmlspecialchars($_POST['company'] ?? ''); ?>">
                            </div>
                            
                            <!-- Profile Picture Upload (Optional) -->
                            <div class="form-group">
                                <label>Profile Picture (Optional)</label>
                                <div class="file-upload">
                                    <input type="file" id="profile_pic" name="profile_pic" accept="image/*">
                                    <label for="profile_pic">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>Click to upload profile picture</span>
                                        <small>Max size: 2MB (JPG, PNG)</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Terms and Conditions -->
                    <div class="form-group">
                        <div class="terms-container">
                            <div class="terms-content">
                                <h4>Terms and Conditions</h4>
                                <p>By registering as an alumnus of University of Vavuniya, you agree to:</p>
                                <ul>
                                    <li>Provide accurate and truthful information</li>
                                    <li>Maintain professional conduct in all interactions</li>
                                    <li>Respect the privacy and confidentiality of fellow alumni</li>
                                    <li>Use the alumni network for professional and constructive purposes</li>
                                    <li>Receive communications from the university about alumni activities</li>
                                </ul>
                                <p>The university reserves the right to suspend or terminate access for any violation of these terms.</p>
                            </div>
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">
                                I agree to the <a href="#" onclick="showTermsModal()">Terms and Conditions</a> and 
                                <a href="#" onclick="showPrivacyModal()">Privacy Policy</a>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Register Now
                        </button>
                        <a href="../index.php" class="btn btn-secondary">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                        <a href="../alumni/login.php" class="btn btn-secondary">
                            <i class="fas fa-sign-in-alt"></i> Already Registered? Login
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Registration Information -->
            <div class="registration-info">
                <h3><i class="fas fa-info-circle"></i> Registration Information</h3>
                <ul>
                    <li>Registration is free for all University of Vavuniya graduates</li>
                    <li>All registrations require admin approval (usually within 24-48 hours)</li>
                    <li>You will receive email notification once your account is approved</li>
                    <li>Approved alumni can update their profiles, view contact details, and connect with others</li>
                    <li>Your information will be kept confidential and used only for alumni networking purposes</li>
                </ul>
                
                <h4>Need Help?</h4>
                <p>If you have any questions or need assistance with registration, please contact:</p>
                <p><strong>Alumni Relations Office:</strong> alumni@vau.ac.lk | +94 24 222 2265</p>
            </div>
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
                    <li><a href="register.php"><i class="fas fa-user-plus"></i> Alumni Registration</a></li>
                    <li><a href="../alumni/login.php"><i class="fas fa-sign-in-alt"></i> Alumni Login</a></li>
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
    <script src="../js/register.js"></script>
    
    <script>
        // Modal functions (for terms and privacy)
        function showTermsModal() {
            alert('Terms and Conditions modal would open here.');
            // In a real application, you would show a modal with full terms
        }
        
        function showPrivacyModal() {
            alert('Privacy Policy modal would open here.');
            // In a real application, you would show a modal with privacy policy
        }
    </script>
</body>
</html>
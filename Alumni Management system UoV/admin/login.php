<?php 
session_start();
include '../config/db.php'; 
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = trim($_POST['login_input']);
    $password = trim($_POST['password']);
    
    // Prevent SQL injection
    $login_input = $conn->real_escape_string($login_input);
    
    if (filter_var($login_input, FILTER_VALIDATE_EMAIL)) {
        $sql = "SELECT * FROM users WHERE email='$login_input' AND role='admin'";
    } else {
        $sql = "SELECT * FROM users WHERE name='$login_input' AND role='admin'";
    }
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // For demo purposes - accept 'admin123' as password
        if (password_verify($password, $user['password']) || $password === 'admin123') {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_role'] = $user['role'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password! Try 'admin123'";
        }
    } else {
        $error = "Admin account not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Alumni System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>
<body>
    <!-- Navigation -->
    <nav class="admin-nav">
        <div class="nav-container">
            <h2 class="nav-title">
                <i class="fas fa-shield-alt"></i> Administrator Login
            </h2>
            <a href="../index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
    </nav>

    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h2>Admin Login</h2>
                <p>Restricted Access - Administrators Only</p>
            </div>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <!-- Demo Credentials -->
            <div class="demo-credentials">
                <div class="demo-header">
                    <i class="fas fa-info-circle"></i>
                    <h4>Default Admin Credentials</h4>
                </div>
                <div class="demo-content">
                    <div class="credential-row">
                        <span class="cred-label">Email:</span>
                        <span class="cred-value">admin@alumni.edu</span>
                    </div>
                    <div class="credential-row">
                        <span class="cred-label">Username:</span>
                        <span class="cred-value">System Admin</span>
                    </div>
                    <div class="credential-row">
                        <span class="cred-label">Password:</span>
                        <span class="cred-value">admin123</span>
                    </div>
                </div>
                <p class="demo-note"><small>Use either email or username to login</small></p>
            </div>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="login_input">
                        <i class="fas fa-user-circle"></i> Email or Username
                    </label>
                    <input type="text" 
                           id="login_input" 
                           name="login_input" 
                           required 
                           placeholder="Enter email or username"
                           value="<?php echo isset($_POST['login_input']) ? htmlspecialchars($_POST['login_input']) : 'admin@alumni.edu'; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-key"></i> Password
                    </label>
                    <div class="password-wrapper">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               placeholder="Enter password"
                               value="admin123">
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-admin-login">
                        <i class="fas fa-sign-in-alt"></i> Login as Administrator
                    </button>
                </div>
            </form>
            
            <div class="security-note">
                <div class="security-header">
                    <i class="fas fa-shield-alt"></i>
                    <strong>Security Notice</strong>
                </div>
                <p>This is a demo system. Change default passwords in production environment.</p>
            </div>
            
            <div class="login-footer">
                <p>Need help? <a href="mailto:support@alumni.edu">Contact System Administrator</a></p>
                <p class="copyright">&copy; <?php echo date('Y'); ?> Alumni System</p>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Auto-focus on login input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('login_input').focus();
        });
    </script>
</body>
</html>
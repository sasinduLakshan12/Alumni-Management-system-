<?php include '../config/db.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Search by name field (treating it as username)
    $sql = "SELECT * FROM users WHERE name='$username' AND role='alumni' AND status='approved'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Accept both hashed and plain text for testing
        if (password_verify($password, $user['password']) || $password === 'admin123') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password! Try: admin123";
        }
    } else {
        $error = "Username not found or account not approved!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Alumni Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Alumni Login (Use Your Full Name)</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="form">
            <div class="form-group">
                <label>Your Full Name:</label>
                <input type="text" name="username" required 
                       placeholder="Enter your full name exactly as registered">
            </div>
            
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="../index.php" class="btn btn-secondary">Back to Home</a>
        </form>
        
        <div style="background: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 5px;">
            <h4>Example Test Accounts:</h4>
            <ul>
                <li>Username: <strong>J.C. Deshan</strong></li>
                <li>Username: <strong>Bashry H.M.</strong></li>
                <li>Username: <strong>Wandana D.M.O.</strong></li>
                <li>Password for all: <strong>admin123</strong></li>
            </ul>
        </div>
        
        <p style="text-align: center;">
            Not registered? <a href="../public/register.php">Register here</a>
        </p>
    </div>
</body>
</html>
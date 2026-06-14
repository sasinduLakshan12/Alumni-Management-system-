<?php
include '../config/db.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $event_date = mysqli_real_escape_string($conn, $_POST['event_date']);
    $venue = mysqli_real_escape_string($conn, $_POST['venue']);
    $created_by = $_SESSION['admin_id'];
    
    $sql = "INSERT INTO events (title, description, event_date, venue, created_by) 
            VALUES ('$title', '$description', '$event_date', '$venue', '$created_by')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: manage_events.php?success=1");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Event</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <h1>Add New Event</h1>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_events.php">Back to Events</a>
            <a href="../logout.php">Logout</a>
        </div>
    </nav>
    <div class="container">
        <h2>Add New Event</h2>
        <?php if(isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" class="form">
            <div class="form-group">
                <label>Event Title:</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label>Event Date:</label>
                <input type="date" name="event_date" required>
            </div>
            <div class="form-group">
                <label>Venue:</label>
                <input type="text" name="venue" required>
            </div>
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Add Event</button>
                <a href="manage_events.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
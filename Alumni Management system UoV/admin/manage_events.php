<?php
// Include the database connection
include '../config/db.php';

// Check if admin is logged in - session is already started in db.php
// REMOVED: session_start(); // This was causing the duplicate session_start error
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission for creating/updating events
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Add new event
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $event_date = mysqli_real_escape_string($conn, $_POST['event_date']);
        $venue = mysqli_real_escape_string($conn, $_POST['venue']);
        $created_by = $_SESSION['admin_id'];
        
        $sql = "INSERT INTO events (title, description, event_date, venue, created_by) 
                VALUES ('$title', '$description', '$event_date', '$venue', '$created_by')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Event added successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
    
    if (isset($_POST['update'])) {
        // Update event
        $id = intval($_POST['event_id']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $event_date = mysqli_real_escape_string($conn, $_POST['event_date']);
        $venue = mysqli_real_escape_string($conn, $_POST['venue']);
        
        $sql = "UPDATE events SET title='$title', description='$description', 
                event_date='$event_date', venue='$venue' WHERE id='$id'";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Event updated successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM events WHERE id='$id'");
    header("Location: manage_events.php");
    exit();
}

// Handle edit
$edit_event = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM events WHERE id='$id'");
    if ($result && $result->num_rows > 0) {
        $edit_event = $result->fetch_assoc();
    }
}

// Get all events
$sql = "SELECT e.*, u.name as created_by_name 
        FROM events e 
        LEFT JOIN users u ON e.created_by = u.id 
        ORDER BY e.event_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Events</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Admin Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="university-logo">
                    <i class="fas fa-calendar-alt" style="font-size: 1.8rem; color: #2c3e50;"></i>
                </div>
                <div class="nav-title">
                    <span class="university-name">Admin Panel</span>
                    <span class="system-name">Manage Events</span>
                </div>
            </div>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="approvals.php"><i class="fas fa-check-circle"></i> Approvals</a>
                <a href="manage_users.php"><i class="fas fa-users"></i> Alumni</a>
                <a href="manage_events.php" class="active"><i class="fas fa-calendar-alt"></i> Events</a>
                <a href="manage_announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <div class="container">
        <h2><?php echo isset($edit_event) ? 'Edit Event' : 'Add New Event'; ?></h2>
        
        <?php if(isset($message)): ?>
            <div class="alert success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Add/Edit Event Form -->
        <form method="POST" class="form">
            <?php if(isset($edit_event)): ?>
                <input type="hidden" name="event_id" value="<?php echo $edit_event['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label>Event Title:</label>
                <input type="text" name="title" 
                       value="<?php echo isset($edit_event) ? htmlspecialchars($edit_event['title']) : ''; ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" rows="4" required><?php echo isset($edit_event) ? htmlspecialchars($edit_event['description']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Event Date:</label>
                <input type="date" name="event_date" 
                       value="<?php echo isset($edit_event) ? $edit_event['event_date'] : ''; ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label>Venue:</label>
                <input type="text" name="venue" 
                       value="<?php echo isset($edit_event) ? htmlspecialchars($edit_event['venue']) : ''; ?>" 
                       required>
            </div>
            
            <div class="form-buttons">
                <?php if(isset($edit_event)): ?>
                    <button type="submit" name="update" class="btn btn-primary">Update Event</button>
                    <a href="manage_events.php" class="btn btn-secondary">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="add" class="btn btn-primary">Add Event</button>
                <?php endif; ?>
            </div>
        </form>

        <!-- Events List -->
        <h3>All Events</h3>
        <div class="table-container">
            <?php if ($result && $result->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Venue</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo date('d M Y', strtotime($row['event_date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['venue']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_by_name']); ?></td>
                            <td class="action-buttons">
                                <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                                <a href="?delete=<?php echo $row['id']; ?>" 
                                   class="btn btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this event?\n\nTitle: <?php echo addslashes($row['title']); ?>\nDate: <?php echo date('d M Y', strtotime($row['event_date'])); ?>')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>No events found.</p>
                </div>
            <?php endif; ?>
        </div>
        
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

<?php
/*
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "alumni_management"; 

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
session_start();
?>
*/

#<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alumni_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session if not already started
if (!isset($_SESSION)) {
    session_start();
}
?>
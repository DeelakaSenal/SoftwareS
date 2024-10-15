<?php
session_start(); // Start the session

// Display success messages in a message box if available
if (isset($_SESSION['success_message'])) {
    echo '<div class="message-box success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    unset($_SESSION['success_message']); // Clear the message after displaying it
}

// Include the database connection file
require_once __DIR__ . '/../db/connection.php';

// Display errors for debugging purposes (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    $_SESSION['error_message'] = "Please log in to access the dashboard.";
    header("Location: login.php");
    exit();
}

// Get user email and role from the session
$email = $_SESSION['email'];
$role = $_SESSION['role'];

try {
    // Get the database connection
    $conn = getDBConnection();

    // Fetch user details
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If the user exists, retrieve the username
    if ($user) {
        $username = $user['username'];
    } else {
        throw new Exception("User not found.");
    }

    // Fetch activities if the user is an admin
    $activities = [];
    if ($role === 'admin') {
        $activity_stmt = $conn->prepare("
            SELECT activity, activity_time 
            FROM activity_log 
            WHERE user_id = (SELECT id FROM users WHERE email = :email)
        ");
        $activity_stmt->bindParam(':email', $email);
        $activity_stmt->execute();
        $activities = $activity_stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Close the connection
    $conn = null;
} catch (Exception $e) {
    // Redirect with an error message if something goes wrong
    $_SESSION['error_message'] = "Error: " . $e->getMessage();
    header("Location: error_page.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
    body {
    font-family: 'Roboto', sans-serif;
    background-color: #0c1c2e; /* Dark blue background */
    color: #ffffff; /* Light text color */
    margin: 0;
    padding: 0;
}

.navbar {
    background-color: #1a2a4b; /* Darker blue for the navbar */
    padding: 15px;
    color: white;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5); /* Shadow for navbar */
}

.container {
    padding: 20px;
    max-width: 800px;
    margin: 20px auto;
    background-color: #1e2a3d; /* Darker container with blue tone */
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.1); /* Light shadow for depth */
    border-radius: 8px;
}

.welcome-message {
    font-size: 22px;
    margin-bottom: 20px;
    font-weight: bold; /* Emphasize the welcome message */
}

.details {
    font-size: 16px;
    margin-bottom: 20px;
}

.button-container {
    display: flex; /* Use flexbox for alignment */
    justify-content: flex-end; /* Align buttons to the right */
    margin-top: 20px; /* Space above buttons */
}

.button {
    display: inline-block;
    padding: 12px 24px; /* Increased padding for better touch target */
    border: none;
    border-radius: 6px; /* Slightly rounded corners */
    cursor: pointer;
    font-size: 16px;
    text-decoration: none;
    margin-left: 10px; /* Add space between buttons */
    transition: background-color 0.3s ease, transform 0.2s; /* Hover effects */
    color: #ffffff;
}

.logout-button {
    background-color: #f44336; /* Logout button */
    box-shadow: 0 2px 4px rgba(244, 67, 54, 0.5); /* Shadow for depth */
}

.logout-button:hover {
    background-color: #e53935; /* Darker red on hover */
    transform: scale(1.05); /* Slightly increase size on hover */
}

.edit-profile-button {
    background-color: #007bff; /* Edit profile button (blue) */
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.5); /* Shadow for depth */
}

.edit-profile-button:hover {
    background-color: #0056b3; /* Darker blue on hover */
    transform: scale(1.05); /* Slightly increase size on hover */
}

.message-box {
    margin: 20px auto;
    padding: 15px;
    border-radius: 5px;
    max-width: 400px;
    text-align: center;
    font-size: 16px;
}

.message-box.success {
    background-color: #005f00; /* Dark green for success message */
    color: #d4edda;
    border: 1px solid #c3e6cb;
}

.message-box.error {
    background-color: #721c24; /* Error message box */
    color: #f8d7da;
    border: 1px solid #f5c6cb;
}

.spinner {
    display: none;
    margin: 20px auto;
    border: 4px solid #424242; /* Darker spinner */
    border-radius: 50%;
    border-top: 4px solid #007bff; /* Spinner in blue */
    width: 30px;
    height: 30px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.activity-log {
    margin-top: 20px;
}

.activity-log h3 {
    margin-bottom: 10px;
    color: #007bff; /* Blue color for the heading */
}

.activity-log ul {
    list-style-type: none;
    padding: 0;
}

.activity-log ul li {
    background: #2b2b2b; /* Darker list items */
    margin: 5px 0;
    padding: 10px;
    border-radius: 4px;
    color: #ffffff; /* White text for list items */
}


    </style>
    <script>
        function confirmLogout(event) {
            if (!confirm('Are you sure you want to logout?')) {
                event.preventDefault();
            }
        }
    </script>
</head>
<body>
    <div class="navbar">
        <h1>User Dashboard</h1>
    </div>

    <div class="container">
        <p class="welcome-message">Welcome, <?php echo htmlspecialchars($username); ?>!</p>
        <p class="details">Your email: <?php echo htmlspecialchars($user['email']); ?></p>

        <a href="../auth/edit_profile.php" class="button edit-profile-button">Edit Profile</a>
        <a href="../auth/logout.php" class="button logout-button" onclick="confirmLogout(event)">Logout</a>

        <!-- Loading spinner -->
        <div class="spinner" id="loading-spinner"></div>

        <!-- Display admin activities if the user is an admin -->
        <?php if ($role === 'admin' && !empty($activities)): ?>
            <div class="activity-log">
                <h3>Activity Log</h3>
                <ul>
                    <?php foreach ($activities as $activity): ?>
                        <li><?php echo htmlspecialchars($activity['activity']) . ' at ' . htmlspecialchars($activity['activity_time']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

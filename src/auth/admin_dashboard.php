<?php
session_start(); // Start the session


// Check if the user is logged in and is an admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Load the database connection file
require_once __DIR__ . '/../db/connection.php';

// Get the database connection
$conn = getDBConnection();

// Handle logout request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Destroy the session and redirect to login
    session_destroy();
    header("Location: login.php");
    exit();
}

// Handle delete user request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    
    // Use prepared statement to avoid SQL injection
    $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
    $delete_stmt->bindParam(':id', $user_id);
    $delete_stmt->execute();
    
    // Log the deletion in activity log
    $activity = "Deleted user with ID: " . $user_id;
    $log_stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity) VALUES (:user_id, :activity)");
    $log_stmt->bindParam(':user_id', $_SESSION['user_id']); // Log the admin's activity
    $log_stmt->bindParam(':activity', $activity);
    $log_stmt->execute();
    
    $delete_stmt->closeCursor();
    
    // Redirect to refresh the page after deletion
    header("Location: admin_dashboard.php");
    exit();
}

// Handle registration request (combined signup logic)
$success_message = $error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_user'])) {
    // Sanitize user inputs
    // Replace FILTER_SANITIZE_STRING with FILTER_SANITIZE_FULL_SPECIAL_CHARS
$user = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$pass = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Hash the password before storing
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
    
    // Check if the email starts with 'admin@' and set the role accordingly
    $role = (strpos($email, 'admin@') === 0) ? 'admin' : 'staff';
    
    // Check for duplicate email or username before insertion
    $check_duplicate = $conn->prepare("SELECT username, email FROM users WHERE username = :username OR email = :email");
    $check_duplicate->bindParam(':username', $user);
    $check_duplicate->bindParam(':email', $email);
    $check_duplicate->execute();
    
    if ($check_duplicate->rowCount() > 0) {
        // Duplicate found, now check if it's username or email
        $result = $check_duplicate->fetch();
        
        if ($result['username'] == $user) {
            $error_message = "Username already exists. Please choose another username.";
        } elseif ($result['email'] == $email) {
            $error_message = "Email already exists. Please use another email.";
        }
    } else {
        // No duplicates, proceed with insertion
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
        $stmt->bindParam(':username', $user);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);
        
        if ($stmt->execute()) {
            // Get the ID of the newly registered user
            $user_id = $conn->lastInsertId();
            
            // Insert activity log for user registration
            $activity = "Registered new user: $user";
            $log_stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity) VALUES (:user_id, :activity)");
            $log_stmt->bindParam(':user_id', $_SESSION['user_id']); // Log the admin's activity
            $log_stmt->bindParam(':activity', $activity);
            $log_stmt->execute();
            
            // Set success message
            $success_message = "User registration successful. Welcome $user!";
        } else {
            $error_message = "Error occurred during registration. Please try again.";
        }
    }
    
    $check_duplicate->closeCursor();
}

// Get all users from the database
$user_stmt = $conn->prepare("SELECT id, username, email, role FROM users");
$user_stmt->execute();
$users = $user_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all activity logs from the database
$log_stmt = $conn->prepare("SELECT activity_log.id, users.username, activity_log.activity, activity_log.timestamp 
                            FROM activity_log 
                            JOIN users ON activity_log.user_id = users.id
                            ORDER BY activity_log.timestamp DESC");
$log_stmt->execute();
$activity_logs = $log_stmt->fetchAll(PDO::FETCH_ASSOC);

// Close the connection
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Styles */
        /* Dark Theme Styles */
        body {
    font-family: Arial, sans-serif;
    background-color: #0c1c2e; /* Dark blue background */
    color: #ffffff; /* Light text color */
    margin: 0;
    padding: 0;
}

.container {
    width: 80%;
    margin: 0 auto;
    padding: 20px;
    background-color: #1e2a3d; /* Darker container background */
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); /* Darker shadow */
}

h2 {
    text-align: center;
    color: #ffffff; /* White color for headings */
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid #444; /* Darker border color */
}

th, td {
    padding: 12px;
    text-align: left;
}

th {
    background-color: #1f1f2e; /* Darker header background */
}

tr:nth-child(even) {
    background-color: #222; /* Darker even rows */
}

tr:hover {
    background-color: #444; /* Slightly lighter on hover */
}

.edit-button,
.delete-button,
.logout-button {
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    margin-right: 5px;
    color: white;
}

.edit-button {
    background-color: #007bff; /* Blue */
}

.edit-button:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

.delete-button {
    background-color: #f44336; /* Red */
}

.delete-button:hover {
    background-color: #e53935; /* Darker red on hover */
}

.logout-button {
    background-color: #e53935; /* Orange */
    margin-top: 20px;
    display: block;
    width: 100%;
    text-align: center;
    font-weight: bold;
}

.logout-button:hover {
    background-color: #e53935; /* Darker orange on hover */
}

.register-form {
    margin-top: 20px;
    padding: 15px;
    border: 1px solid #444; /* Darker border for form */
    border-radius: 6px;
    background-color: #1e2a3d; /* Darker form background */
}

.register-form input,
.register-form select {
    padding: 10px;
    margin: 10px 0;
    width: 100%;
    box-sizing: border-box;
    background-color: #333; /* Dark input background */
    color: white; /* Light text for inputs */
    border: 1px solid #444; /* Dark border for inputs */
}

.register-form button {
    width: 100%;
    padding: 12px;
    background-color: #007bff; /* Blue */
    border: none;
    color: white;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
}

.register-form button:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

.error {
    color: red;
    margin-top: 10px;
    display: none;
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
    background-color: #2b542c; /* Success message background */
    color: #d4edda; /* Success message text color */
    border: 1px solid #c3e6cb; /* Success message border */
}

.message-box.error {
    background-color: #721c24; /* Error message background */
    color: #f8d7da; /* Error message text color */
    border: 1px solid #f5c6cb; /* Error message border */
}



    </style>
    <script>
        // Simple form validation
        function validateForm() {
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
            let valid = true;

            if (username === '') {
                document.getElementById('username-error').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('username-error').style.display = 'none';
            }

            if (!email.match(emailPattern)) {
                document.getElementById('email-error').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('email-error').style.display = 'none';
            }

            if (password === '') {
                document.getElementById('password-error').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('password-error').style.display = 'none';
            }

            return valid;
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>

       <!-- Logout Button -->
       <form method="post" action="../auth/logout.php" onsubmit="return confirmLogout();">
            <button type="submit" name="logout" class="logout-button">Logout</button>
        </form>

        <!-- Display Success or Error Message -->
        <?php if ($success_message): ?>
            <p style="color:green;"><?php echo $success_message; ?></p>
        <?php elseif ($error_message): ?>
            <p style="color:red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- All Users Table -->
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td>
                        <!-- Edit Button -->
                        <form action="edit_profile.php" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="edit-button" title="Edit User Profile">
                            <i class="fas fa-edit" style="margin-right: 5px;"></i> Edit
                            </button>
                            
                        </form>

                        <!-- Delete Button -->
                        <form action="admin_dashboard.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user" class="delete-button" title="Delete User">
                            <i class="fas fa-trash" style="margin-right: 5px;"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Register New User Form -->
        <h2>Register New User</h2>
        <form action="admin_dashboard.php" method="post" class="register-form" onsubmit="return validateForm()">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>
            <span class="error" id="username-error">Please enter a username.</span>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
            <span class="error" id="email-error">Please enter a valid email address.</span>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
            <span class="error" id="password-error">Please enter a password.</span>

            <label for="role">Role</label>
            <select name="role" id="role" required>
                <option value="student">Staff</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit" name="register_user">Register User</button>
        </form>

        <!-- Activity Log -->
        <h2>Activity Log</h2>
        <table>
            <tr>
                <th>Username</th>
                <th>Activity</th>
                <th>Timestamp</th>
            </tr>
            <?php foreach ($activity_logs as $log) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($log['username']); ?></td>
                    <td><?php echo htmlspecialchars($log['activity']); ?></td>
                    <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
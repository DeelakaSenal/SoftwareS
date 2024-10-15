<?php
session_start(); // Ensure session is started if you use session variables

// Load the connection file
require_once __DIR__ . '/../db/connection.php'; // Ensure this path is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the database connection
    $conn = getDBConnection();

    // Sanitize user inputs
    $user = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $pass = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Check if the inputs are not empty
    if (empty($user) || empty($email) || empty($pass)) {
        $error_message = "All fields are required.";
        echo $error_message;
        exit();
    }

    // Hash the password before storing
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    // Check if the email starts with 'admin@' and set the role accordingly
    $role = (strpos($email, 'admin@') === 0) ? 'admin' : 'staff';

    try {
        // Check for duplicate email or username before insertion
        $check_duplicate = $conn->prepare("SELECT username, email FROM users WHERE username = :username OR email = :email");
        $check_duplicate->bindParam(':username', $user);
        $check_duplicate->bindParam(':email', $email);
        $check_duplicate->execute();

        // Handle duplicate entries
        if ($check_duplicate->rowCount() > 0) {
            $result = $check_duplicate->fetch();
            if ($result['username'] == $user) {
                $error_message = "Username already exists. Please choose another username.";
            } elseif ($result['email'] == $email) {
                $error_message = "Email already exists. Please use another email.";
            }
            echo $error_message; // Output the error message
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
                $activity = "User registered";
                $log_stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity) VALUES (:user_id, :activity)");
                $log_stmt->bindParam(':user_id', $user_id);
                $log_stmt->bindParam(':activity', $activity);
                $log_stmt->execute();

                // Redirect to the login page after successful registration
                $_SESSION['success_message'] = "Registerd SuccessFully. Please Login";
                header("Location: ./../../login.php"); // Corrected redirection
                exit(); // Ensure the script stops after the redirect
            } else {
                $error_message = "Error occurred during registration. Please try again.";
                echo $error_message; // Output error message if registration fails
            }
        }

        // Close the duplicate check cursor
        $check_duplicate->closeCursor();
    } catch (PDOException $e) {
        // Output any database errors
        echo "Database Error: " . $e->getMessage();
        exit();
    }

    // Close the database connection
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-image: url('./src/signupback.png'); /* Use a relative path */
    background-size: cover; /* Cover the entire viewport */
    background-position: center; /* Center the background image */
    margin: 0;
    color: #e0e0e0; /* Light text color */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

        .register-container {
            background-color: rgba(30, 30, 30, 0.8); /* Darker container background with transparency */
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); /* Slightly stronger shadow */
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
            text-align: center; /* Center content */
        }
        .register-container img {
            width: 350px; /* Adjust logo width */
            height: auto; /* Maintain aspect ratio */
            margin-bottom: 20px; /* Space below the logo */
        }
        .register-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #e0e0e0; /* Light text color */
        }
        .register-container label {
            font-weight: bold;
            color: #e0e0e0; /* Light text color */
        }
        .register-container input[type="text"],
        .register-container input[type="password"],
        .register-container input[type="email"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0 20px 0;
            background-color: #333; /* Dark input background */
            border: 1px solid #555; /* Darker border */
            border-radius: 6px;
            box-sizing: border-box;
            color: #fff; /* White text inside inputs */
            transition: border-color 0.3s ease;
        }
        .register-container input[type="text"]:focus,
        .register-container input[type="password"]:focus,
        .register-container input[type="email"]:focus {
            border-color: #76ff03; /* Bright green for focus */
            outline: none;
        }
        .register-container button {
            width: 100%;
            padding: 14px;
            background-color: #76ff03; /* Bright green for button */
            color: #000; /* Dark text on button */
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .register-container button:hover {
            background-color: #64dd17; /* Slightly darker green on hover */
        }
        .register-container .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .register-container .login-link a {
            color: #76ff03; /* Bright green for links */
            text-decoration: none;
            font-weight: bold;
        }
        .register-container .login-link a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: #ff5252; /* Red for error messages */
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <img src="/SoftwareS/src/eventora.png" alt="Eventora Logo"> <!-- Logo above the heading -->
        <h1>Register here</h1>
        <form action="./src/auth/signup.php" method="post">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required minlength="6">

            <button type="submit">Register</button>

            <div class="login-link">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>

            <!-- Error message display -->
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>

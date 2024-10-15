<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
       body {
           font-family: Arial, sans-serif;
           background-image: url('src/loginbb.jpg'); /* Adding the background image */
           background-size: cover;
           background-position: center;
           margin: 0;
           color: #e0e0e0;
           display: flex;
           justify-content: center;
           align-items: center;
           height: 100vh;
           background-repeat: no-repeat;
       }

       .login-container {
           background-color: rgba(30, 30, 30, 0.8); /* Darker container background with transparency */
           padding: 30px;
           border-radius: 12px;
           box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); /* Slightly stronger shadow */
           width: 100%;
           max-width: 400px;
           box-sizing: border-box;
           transition: transform 0.3s ease-in-out;
           text-align: center; /* Center content */
       }

       .login-container:hover {
           transform: translateY(-5px);
       }

       .login-container img {
           width: 350px; /* Adjust logo width */
           height: auto; /* Maintain aspect ratio */
           margin-bottom: 20px; /* Space below the logo */
       }

       .login-container label {
           font-weight: bold;
           color: #e0e0e0;
           display: block;
           margin-bottom: 8px;
           font-size: 14px;
       }

       .login-container input[type="email"],
       .login-container input[type="password"] {
           width: 100%;
           padding: 12px;
           margin: 8px 0 20px 0;
           background-color: #333; /* Dark input background */
           border: 1px solid #555; /* Darker border */
           border-radius: 6px;
           box-sizing: border-box;
           font-size: 14px;
           color: #fff; /* White text inside inputs */
           transition: border-color 0.3s ease;
       }

       .login-container input[type="email"]:focus,
       .login-container input[type="password"]:focus {
           border-color: #76ff03; /* Bright green for focus */
           outline: none;
       }

       .login-container button {
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

       .login-container button:hover {
           background-color: #64dd17; /* Slightly darker green on hover */
       }

       .login-container .signup-link {
           text-align: center;
           margin-top: 20px;
       }

       .login-container .signup-link a {
           color: #76ff03; /* Bright green for links */
           text-decoration: none;
           font-weight: bold;
       }

       .login-container .signup-link a:hover {
           text-decoration: underline;
       }

       .error-message {
           color: #ff5252; /* Red for error messages */
           text-align: center;
           margin-bottom: 20px;
           font-size: 14px;
       }

       .success-message {
           color: green;
           text-align: center;
           margin-bottom: 20px;
           font-size: 14px;
       }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>PHP Authentication and Authorization System</h1>

        <p>Login to your account....</p>

        <?php
        session_start(); 
        if (isset($_SESSION['error_message'])) {
            echo "<p class='error-message'>". $_SESSION['error_message']. "</p>";
        }
        if (isset($_SESSION['success_message'])) {
            echo "<p class='success-message'>". $_SESSION['success_message']. "</p>";
        }
        ?>
        <form action="./src/auth/login.php" method="post">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required minlength="6">

            <button type="submit">Login</button>

            <div class="signup-link">
                <p>Don't have an account? <a href="signup.php">Sign up</a></p>
            </div>
        </form>
    </div>

</body>
</html>

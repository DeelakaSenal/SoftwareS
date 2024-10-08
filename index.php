<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Student Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1e1e1e; /* Dark background */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #f4f4f4; /* Light text */
        }
        .container {
            background-color: #333; /* Dark container */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #f4f4f4; /* Light heading */
        }
        p {
            font-size: 16px;
            color: #bbb; /* Slightly dimmer paragraph text */
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            padding: 12px 20px;
            font-size: 16px;
            color: white;
            background-color: #4CAF50;
            text-decoration: none;
            border-radius: 4px;
            margin: 0 10px;
        }
        .button:hover {
            background-color: #45a049;
        }
        .logo {
            width: 350px; /* Adjust the width of the logo */
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Add the logo image -->
        <img src="/SoftwareS/src/eventora.png" alt="Eventora Logo" class="logo">
        
        <h1>Welcome to the Student Portal</h1>
        <p>Please log in or register to access the student dashboard.</p>
        <a href="login.php" class="button">Login</a>
        <a href="signup.php" class="button">Register</a>
    </div>
</body>
</html>

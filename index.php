<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Authentication and Authorization System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('src/indexb.jpg'); /* Add background image */
            background-size: cover; /* Cover the entire background */
            background-position: center; /* Center the image */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #ddd; /* Light text color for contrast */
        }
        .container {
            background-color: rgba(43, 43, 43, 0.8); /* Slightly transparent for overlay effect */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .logo {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }
        h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #f4f4f4; /* Light color for the heading */
        }
        p {
            font-size: 16px;
            color: #cccccc; /* Lighter color for text */
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
    </style>
</head>
<body>
    <div class="container">
        <h1>PHP Authentication and Authorization System</h1>
        <p>Please log in or register to access the dashboard.</p>
        <a href="login.php" class="button">Login</a>
        <a href="signup.php" class="button">Register</a>
    </div>
</body>
</html>

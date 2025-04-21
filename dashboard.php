<?php
session_start(); // Start session to access user data

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to your form page
    exit();
}

// User is logged in - Welcome them!
$username = $_SESSION['username'] ?? 'User'; // Get username from session

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css"> <!-- You might want different styles here -->
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .logout-btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #f44336;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
    <p>You have successfully logged in.</p>

    <!-- Add a logout button -->
    <form action="logout.php" method="post">
        <button type="submit" class="logout-btn">Logout</button>
    </form>

    <!-- Add other dashboard content here -->

</body>
</html>
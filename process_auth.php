<?php
session_start(); // Start session handling for login status and messages

// --- Database Configuration ---
$db_host = "sql203.infinityfree.com";
$db_user = "if0_38658183";
$db_pass = "1wzsyzgrpckz"; // Be careful displaying passwords directly in code
$db_name = "if0_38658183_modelviewer";
$db_port = 3306;

// --- Establish Database Connection ---
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

// Check Connection
if (!$conn) {
    // In production, log the error instead of displaying it
    die("Connection failed: " . mysqli_connect_error());
}

// --- Helper function for redirecting with message ---
function redirectWithMessage($url, $message, $type = 'error') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = ($type === 'success' ? 'green' : 'red');
    header("Location: " . $url); // Redirect back to the form page (adjust 'index.php' if needed)
    exit(); // Stop script execution after redirect
}

// --- Determine Action (Signup or Login) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    $action = $_POST['action'];
    $form_page = 'index.php'; // The page where your form is located

    // --- SIGNUP LOGIC ---
    if ($action == "signup") {
        // Get data and perform basic validation
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['pswd'] ?? ''); // Get raw password

        if (empty($username) || empty($email) || empty($password)) {
            redirectWithMessage($form_page, "All signup fields are required.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
             redirectWithMessage($form_page, "Invalid email format.");
        }

        // **Password Hashing (Crucial!)**
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement (Prevent SQL Injection)
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password);

            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                redirectWithMessage($form_page, "Signup successful! You can now log in.", "success");
            } else {
                // Check for duplicate email error (MySQL error code 1062)
                if (mysqli_errno($conn) == 1062) {
                     redirectWithMessage($form_page, "Email address already exists.");
                } else {
                     redirectWithMessage($form_page, "Error during signup: " . mysqli_stmt_error($stmt));
                }
            }
            mysqli_stmt_close($stmt);
        } else {
            redirectWithMessage($form_page, "Database error: Could not prepare statement.");
        }

    }
    // --- LOGIN LOGIC ---
    elseif ($action == "login") {
        // Get data and perform basic validation
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['pswd'] ?? '');

        if (empty($email) || empty($password)) {
            redirectWithMessage($form_page, "Email and password are required for login.");
        }

        // Prepare SQL statement to find user by email
        $sql = "SELECT id, username, password FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result); // Fetch user data if found

            if ($user) {
                // **Verify Password**
                if (password_verify($password, $user['password'])) {
                    // Password is correct - Login successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    // Redirect to a logged-in area (e.g., dashboard.php)
                    header("Location: dashboard.php"); // Create this page
                    exit();
                } else {
                    // Incorrect password
                     redirectWithMessage($form_page, "Invalid email or password.");
                }
            } else {
                // User not found (email doesn't exist)
                redirectWithMessage($form_page, "Invalid email or password."); // Keep message generic for security
            }
            mysqli_stmt_close($stmt);
        } else {
             redirectWithMessage($form_page, "Database error: Could not prepare statement.");
        }
    }
    else {
        redirectWithMessage($form_page, "Invalid action specified.");
    }

} else {
    // If someone accesses process_auth.php directly without POST data
    header("Location: index.php"); // Redirect them back to the form
    exit();
}

// --- Close Database Connection ---
mysqli_close($conn);
?>
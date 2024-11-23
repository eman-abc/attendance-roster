<?php
// Start session to store user data after login
session_start();

// Include database configuration file
require_once('C:\xampp\htdocs\attendance-roster\config.php');

// Define variables and initialize them as empty
$email = $password = "";
$email_err = $password_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if email is empty
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($email_err) && empty($password_err)) {

        // Prepare SQL statement to check if email exists
        $sql = "SELECT id, email, role, password FROM user WHERE email = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to prepared statement
            $stmt->bind_param("s", $param_email);

            // Set parameters
            $param_email = $email;

            // Execute statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if email exists
                if ($stmt->num_rows == 1) {
                    // Bind the result to variables (id, email, password)
                    $stmt->bind_result($id, $email_from_db, $role, $password_from_db);

                    // Fetch the data for the user
                    if ($stmt->fetch()) {
                        // Debug: Show fetched user data
                        echo "User ID: " . $id . "<br>";
                        echo "User Email: " . $email_from_db . "<br>";
                        echo "User Password: " . $password_from_db . "<br>";
                        echo "Role: " . $role . "<br>";

                        // Compare the entered password with the stored password
                        if ($password === $password_from_db) {
                            // Password is correct, start a new session
                            session_start();

                            // Store session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email_from_db;
                            $_SESSION["role"] = $role;

                            // Redirect to dashboard or home page
                            header("location: login.php");
                            exit();
                        } else {
                            $password_err = "The password you entered was not correct.";
                        }
                    }
                } else {
                    $email_err = "No account found with that email.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <div>
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label for="email">Email</label>
                <input type="text" name="email" id="email" value="<?php echo $email; ?>">
                <span><?php echo $email_err; ?></span>
            </div>

            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password">
                <span><?php echo $password_err; ?></span>
            </div>

            <div>
                <input type="submit" value="Login">
            </div>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</body>

</html>
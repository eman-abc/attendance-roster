<?php
function authenticateUser($email, $password) {
    global $conn;
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            return $user;  // Return user data if authentication is successful
        }
    }
    return false;  // Return false if authentication fails
}
?>

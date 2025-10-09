<?php
include '../config.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];

    // Prepare and execute the SQL query to find the user by mobile number
    $sql = "SELECT id, mobile, password, role FROM users WHERE mobile = ?";

    // Use a prepared statement to prevent SQL injection
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $stmt->store_result();

        // Check if the user exists
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $db_mobile, $hashed_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                // Password is correct, start a new session
                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = $role; 

                // Redirect user to the homepage
                header("location: ../index.php");
                exit;
            } else {
                echo "Invalid password. <a href='login.php'>Try again</a>";
            }
        } else {
            echo "No account found with that mobile number. <a href='login.php'>Try again</a>";
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
    $conn->close();
}
?>
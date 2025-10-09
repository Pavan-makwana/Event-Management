<?php
include '../config.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form data
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];

    // Prepare and execute the SQL query to find the user by mobile number
    $sql = "SELECT id, mobile, password, role FROM users WHERE mobile = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $db_mobile, $hashed_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                // Check if the user is an admin
                if ($role == 'admin') {
                    $_SESSION['user_id'] = $id;
                    $_SESSION['role'] = $role;
                    header("location: dashboard.php");
                    exit;
                } else {
                    echo "You do not have administrative privileges.";
                }
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
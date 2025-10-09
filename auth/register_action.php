<?php
include '../config.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $city = $_POST['city'];
    $password = $_POST['password'];

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute the SQL query to insert a new user
    $sql = "INSERT INTO users (name, email, mobile, city, password) VALUES (?, ?, ?, ?, ?)";
    
    // Use a prepared statement to prevent SQL injection
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters to the statement
        $stmt->bind_param("sssss", $name, $email, $mobile, $city, $hashed_password);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Registration successful, redirect to login page
            header("location: login.php?message=success");
            exit;
        } else {
            // Error occurred
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>
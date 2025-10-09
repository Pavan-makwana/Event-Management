<?php
include '../config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Check if a booking ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $booking_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Update the booking status to 'cancelled'
    $sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $booking_id, $user_id);
        
        if ($stmt->execute()) {
            // Redirect back to the my_bookings page with a success message
            header("Location: my_bookings.php?message=cancelled");
            exit;
        } else {
            echo "Error updating booking: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    header("Location: my_bookings.php?error=invalid_id");
    exit;
}

$conn->close();
?>
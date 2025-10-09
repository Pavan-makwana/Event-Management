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

    // SQL to DELETE the record, but only if the status is 'cancelled'
    // and only if the booking belongs to the current user for security.
    $sql = "DELETE FROM bookings WHERE id = ? AND user_id = ? AND status = 'cancelled'";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $booking_id, $user_id);
        
        if ($stmt->execute()) {
            // Redirect back to the my_bookings page with a success message
            header("Location: my_bookings.php?message=deleted_permanent");
            $stmt->close();
            exit;
        } else {
            // If execution fails (e.g., status was not 'cancelled'), redirect gracefully
            header("Location: my_bookings.php?error=delete_failed");
            $stmt->close();
            exit;
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    header("Location: my_bookings.php");
    exit;
}

$conn->close();
?>
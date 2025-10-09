<!-- manage_bookings -->
<?php
include '../config.php';
include '../includes/header.php';

// Access control: check if the user is an admin
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin')) {
    header("Location: ../index.php");
    exit;
}

// Check if a booking ID and action are provided
if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['action'])) {
    $booking_id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'unbook') {
        // Unbook: change the status of a 'booked' event to 'cancelled'
        $sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ? AND status = 'booked'";
        $message = 'unbooked';
    } elseif ($action == 'delete') {
        // Delete: permanently remove a 'cancelled' event from the database
        $sql = "DELETE FROM bookings WHERE id = ? AND status = 'cancelled'";
        $message = 'deleted';
    } else {
        header("Location: dashboard.php?error=invalid_action");
        exit;
    }

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $booking_id);
        if ($stmt->execute()) {
            header("Location: dashboard.php?message=" . $message);
            exit;
        } else {
            echo "Error managing booking: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    header("Location: dashboard.php?error=invalid_request");
    exit;
}

include '../includes/footer.php';
$conn->close();
?>
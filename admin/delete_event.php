<!-- delete event -->
<?php
include '../config.php';

// Access control: check if the user is an admin
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin')) {
    header("Location: ../index.php"); // Redirect to home or a forbidden page
    exit;
}

// Check if an event ID is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $event_id = $_GET['id'];

    // First, get the image path to delete the file later
    $sql_image = "SELECT image FROM events WHERE id = ?";
    if ($stmt_image = $conn->prepare($sql_image)) {
        $stmt_image->bind_param("i", $event_id);
        $stmt_image->execute();
        $result_image = $stmt_image->get_result();
        if ($result_image->num_rows > 0) {
            $row = $result_image->fetch_assoc();
            $image_path = '../assets/images/' . $row['image'];

            // delete the event from the database.
            $sql_delete = "DELETE FROM events WHERE id = ?";
            if ($stmt_delete = $conn->prepare($sql_delete)) {
                $stmt_delete->bind_param("i", $event_id);
                if ($stmt_delete->execute()) {
                    // Delete the image file from the server
                    if (file_exists($image_path) && is_file($image_path)) {
                        unlink($image_path);
                    }
                    header("Location: dashboard.php?message=deleted");
                    exit;
                } else {
                    echo "Error deleting event: " . $stmt_delete->error;
                }
                $stmt_delete->close();
            } else {
                echo "Error preparing delete statement: " . $conn->error;
            }
        } else {
            echo "Event not found.";
        }
        $stmt_image->close();
    } else {
        echo "Error preparing image query: " . $conn->error;
    }
} else {
    echo "No event ID specified.";
}

$conn->close();
?>
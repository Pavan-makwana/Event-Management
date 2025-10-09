<?php
include '../config.php';
include '../includes/functions.php';

// Check if the form was submitted and user is logged in
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    
    $event_id = filter_var($_POST['event_id'], FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];
    $package_id = filter_var($_POST['package_id'], FILTER_VALIDATE_INT); 
    $booked_date = $_POST['booked_date'];
    $booked_start_time = $_POST['booked_start_time'];
    $booked_end_time = $_POST['booked_end_time'];

    if (!$event_id || !$package_id || empty($booked_date) || empty($booked_start_time) || empty($booked_end_time)) {
        header("Location: ../event.php?id=" . $event_id . "&error=invalid_data");
        exit;
    }

    // Check if the end time is before the start time.
    if (strtotime($booked_end_time) <= strtotime($booked_start_time)) {
        header("Location: ../event.php?id=" . $event_id . "&error=invalid_time");
        exit;
    }

    // Check if the user has already booked this exact event (User-specific overlap check)
    if (is_already_booked($conn, $event_id, $user_id, $booked_date, $booked_start_time, $booked_end_time)) {
        header("Location: my_bookings.php?error=already_booked");
        exit;
    }

    // Check capacity and general time conflict (The function returns true or the conflict time)
    $conflict_end_time = check_availability($conn, $event_id, $booked_date, $booked_start_time, $booked_end_time);

    if ($conflict_end_time !== true) { 
        header("Location: ../event.php?id=" . $event_id . "&error=full&conflict_end_time=" . urlencode($conflict_end_time));
        exit;
    }
    
    // Prepare and execute the SQL query to insert a new booking
    $sql = "INSERT INTO bookings (event_id, user_id, package_id, booked_date, booked_start_time, booked_end_time) VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // UPDATED bind_param: Added 'i' for package_id (iiisss)
        $stmt->bind_param("iiisss", $event_id, $user_id, $package_id, $booked_date, $booked_start_time, $booked_end_time);

        if ($stmt->execute()) {
            header("Location: my_bookings.php?message=success");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
    $conn->close();

} else {
    header("Location: ../index.php");
    exit;
}
?>
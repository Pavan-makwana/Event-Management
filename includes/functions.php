<?php
// Function to check if a specific event time slot is available
function check_availability($conn, $event_id, $booked_date, $booked_start_time, $booked_end_time) {
    // Check if end time is valid
    if (strtotime($booked_end_time) <= strtotime($booked_start_time)) {
        return false;
    }

    // Get event capacity
    $capacity_sql = "SELECT capacity FROM events WHERE id = ?";
    $capacity = 0;
    if ($stmt = $conn->prepare($capacity_sql)) {
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $capacity = $row['capacity'];
        }
        $stmt->close();
    }

    // This query finds the latest time to give the most helpful message to the user.
    $find_conflict_sql = "SELECT MAX(booked_end_time) FROM bookings 
                          WHERE event_id = ? AND booked_date = ? AND status = 'booked' 
                          AND booked_start_time < ? AND booked_end_time > ?";

    $booking_end_time = false;
    if ($stmt = $conn->prepare($find_conflict_sql)) {
        // Corrected bind_param call: 1 int and 3 strings
        $stmt->bind_param("isss", $event_id, $booked_date, $booked_end_time, $booked_start_time);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($booking_end_time);
            $stmt->fetch();
        }
        $stmt->close();
    }

    // 4. Count all overlapping bookings to check capacity
    $count_sql = "SELECT COUNT(*) FROM bookings 
                  WHERE event_id = ? AND booked_date = ? AND status = 'booked' 
                  AND booked_start_time < ? AND booked_end_time > ?";
    $booking_count = 0;
    if ($stmt = $conn->prepare($count_sql)) {
        $stmt->bind_param("isss", $event_id, $booked_date, $booked_end_time, $booked_start_time);
        $stmt->execute();
        $stmt->bind_result($booking_count);
        $stmt->fetch();
        $stmt->close();
    }

    // 5. If capacity is full, return the LATEST conflict end time. Otherwise, return true.
    if ($booking_count >= $capacity) {
        return $booking_end_time;
    } else {
        return true;
    }
}

// Function to check if a user has already booked a conflicting slot
function is_already_booked($conn, $event_id, $user_id, $booked_date, $booked_start_time, $booked_end_time) {
    // This check is necessary to prevent a single user from booking the same slot multiple times.
    $sql = "SELECT id FROM bookings 
            WHERE event_id = ? 
            AND user_id = ? 
            AND booked_date = ? 
            AND status = 'booked'
            AND booked_start_time < ? AND booked_end_time > ?";
            
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iisss", $event_id, $user_id, $booked_date, $booked_end_time, $booked_start_time);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
    return false;
}
?>
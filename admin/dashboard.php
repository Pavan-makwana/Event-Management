<?php
include '../config.php';
include '../includes/header.php';

// Access control: check if the user is an admin
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin')) {
    header("Location: ../index.php");
    exit;
}

// Display a success message if a booking or event was managed
$message = '';
if (isset($_GET['message'])) {
    if ($_GET['message'] == 'unbooked') {
        $message = "<div class='alert alert-success mt-3'>Booking unbooked successfully!</div>";
    } elseif ($_GET['message'] == 'deleted') {
        $message = "<div class='alert alert-success mt-3'>Item deleted successfully!</div>"; // Updated message for clarity
    } elseif ($_GET['message'] == 'updated') { 
        $message = "<div class='alert alert-success mt-3'>Event updated successfully!</div>";
    }
}
?>

<!-- Admin Dashboard -->
<div class="container">
    <h2 class="my-4">Admin Dashboard</h2>
    <?php echo $message; ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Manage Events</h4>
                    <div>
                        <a href="manage_packages.php" class="btn btn-info btn-sm me-2">Manage Packages</a>
                        <a href="add_event.php" class="btn btn-primary btn-sm">Add New Event</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    // Fetch all events
                    $sql_events = "SELECT id, title, venue, capacity, created_at FROM events ORDER BY created_at DESC";
                    $result_events = $conn->query($sql_events);

                    if ($result_events->num_rows > 0) {
                        echo "<ul class='list-group'>";
                        while($event = $result_events->fetch_assoc()) {
                            echo "<li class='list-group-item'>";
                            echo "<div class='d-flex justify-content-between align-items-center'>";
                            echo "<div><strong>" . htmlspecialchars($event['title']) . "</strong><br><small>Venue: " . htmlspecialchars($event['venue']) . " | Capacity: " . htmlspecialchars($event['capacity']) . "</small></div>";
                            
                            echo "<div>";
                            // Edit Button
                            echo "<a href='edit_event.php?id=" . $event['id'] . "' class='btn btn-warning btn-sm me-2'><i class='fas fa-edit'></i> Edit</a>";
                            // Delete Button
                            echo "<a href='delete_event.php?id=" . $event['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this event and all its bookings?\")'><i class='fas fa-trash'></i> Delete</a>";
                            echo "</div>";
                            
                            echo "</div>";
                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No events found.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>View Bookings</h4>
                </div>
                <div class="card-body">
                    <?php
                    // Fetch all bookings with user and event details
                    $sql_bookings = "SELECT b.id, b.booked_date, b.booked_start_time, b.booked_end_time, b.status, e.title AS event_title, u.name AS user_name, b.package_id
                                     FROM bookings b
                                     JOIN events e ON b.event_id = e.id
                                     JOIN users u ON b.user_id = u.id
                                     ORDER BY b.created_at DESC";
                    $result_bookings = $conn->query($sql_bookings);

                    if ($result_bookings->num_rows > 0) {
                        echo "<ul class='list-group'>";
                        while($booking = $result_bookings->fetch_assoc()) {
                            echo "<li class='list-group-item'>";
                            echo "<strong>" . htmlspecialchars($booking['event_title']) . "</strong><br>";
                            
                            echo "<small>Booked by: " . htmlspecialchars($booking['user_name']) . " | Date: " . htmlspecialchars($booking['booked_date']) . " | Time: " . htmlspecialchars(date('g:i A', strtotime($booking['booked_start_time']))) . " - " . htmlspecialchars(date('g:i A', strtotime($booking['booked_end_time']))) . "</small>";
                            
                            
                            echo "<br><span class='badge " . ($booking['status'] == 'booked' ? 'bg-success' : 'bg-danger') . "'>" . ucfirst($booking['status']) . "</span>";
                            
                            // action buttons based on booking status
                            if ($booking['status'] == 'booked') {
                                echo "<div class='float-end'>";
                                echo "<a href='manage_bookings.php?id=" . $booking['id'] . "&action=unbook' class='btn btn-warning btn-sm'>Unbook</a>";
                                echo "</div>";
                            } elseif ($booking['status'] == 'cancelled') {
                                echo "<div class='float-end'>";
                                echo "<a href='manage_bookings.php?id=" . $booking['id'] . "&action=delete' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to permanently delete this canceled booking?\")'>Delete</a>";
                                echo "</div>";
                            }
                            
                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No bookings found.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
$conn->close();
?>
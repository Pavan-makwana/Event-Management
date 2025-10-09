<?php
include '../config.php';
include '../includes/header.php';

// Check if the user is logged in. If not, redirect them to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Check if an event ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid event ID.</div>";
    include '../includes/footer.php';
    exit;
}

$event_id = $_GET['id'];

// Fetch event details
$sql = "SELECT title, venue FROM events WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>Event not found.</div>";
        include '../includes/footer.php';
        exit;
    }
    $stmt->close();
}

// NEW: Fetch packages for the current event
$sql_packages = "SELECT id, package_name, price FROM event_packages WHERE event_id = ?";
$stmt_packages = $conn->prepare($sql_packages);
$stmt_packages->bind_param("i", $event_id);
$stmt_packages->execute();
$result_packages = $stmt_packages->get_result();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Book an Event</h3>
            </div>
            <div class="card-body">
                <h4 class="text-center"><?php echo htmlspecialchars($event['title']); ?></h4>
                <p class="text-center">Venue: <?php echo htmlspecialchars($event['venue']); ?></p>

                <form action="book_action.php" method="POST">
                    <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event_id); ?>">
                    
                    <div class="mb-3">
                        <label for="package_id" class="form-label">Select Package</label>
                        <select class="form-select" id="package_id" name="package_id" required>
                            <option value="">Choose a Package...</option>
                            <?php 
                            if ($result_packages->num_rows > 0) {
                                while($package = $result_packages->fetch_assoc()) {
                                    echo "<option value='" . $package['id'] . "'>";
                                    echo htmlspecialchars($package['package_name']) . " - ₹" . number_format($package['price'], 2);
                                    echo "</option>";
                                }
                            } else {
                                 echo "<option value='' disabled>No packages available</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="booked_date" class="form-label">Select Date</label>
                        <input type="date" class="form-control" id="booked_date" name="booked_date" required>
                    </div>

                    <div class="mb-3">
                        <label for="booked_start_time" class="form-label">Select Start Time</label>
                        <input type="time" class="form-control" id="booked_start_time" name="booked_start_time" required>
                    </div>

                    <div class="mb-3">
                        <label for="booked_end_time" class="form-label">Select End Time</label>
                        <input type="time" class="form-control" id="booked_end_time" name="booked_end_time" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Proceed to Book</button>
                </form>
            </div>
            <div class="card-footer text-center">
                <a href="../event.php?id=<?php echo htmlspecialchars($event_id); ?>">Back to Event Details</a>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
$conn->close();
?>
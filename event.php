<?php
include 'config.php';
include 'includes/header.php';

// Display error message if present in the URL
$message = '';
if (isset($_GET['error'])) {
    // Check for the specific 'full' error with the end time parameter
    if ($_GET['error'] == 'full' && isset($_GET['conflict_end_time'])) {
        $conflict_end_time = htmlspecialchars($_GET['conflict_end_time']);
        $formatted_time = date('g:i A', strtotime($conflict_end_time));
        $message = "<div class='alert alert-danger'>This slot is already booked. Please try after " . $formatted_time . ".</div>";
    } elseif ($_GET['error'] == 'full') {
        $message = "<div class='alert alert-danger'>The selected time slot is already booked. Please choose a different time.</div>";
    } elseif ($_GET['error'] == 'invalid_data') {
        $message = "<div class='alert alert-danger'>Invalid data provided for booking.</div>";
    } elseif ($_GET['error'] == 'invalid_time') {
        $message = "<div class='alert alert-danger'>The end time must be after the start time.</div>";
    } elseif ($_GET['error'] == 'already_booked') {
        $message = "<div class='alert alert-danger'>You have already booked this event for a conflicting time slot.</div>";
    }
}

// Check if an event ID is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $event_id = $_GET['id'];

    // SQL query to get the event details
    $sql = "SELECT * FROM events WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $event = $result->fetch_assoc();
            ?>
            <div class="row g-0">
                <?php echo $message; ?>
            </div>
            <div class="card mb-4">
                <div class="row g-0">
                    <div class="col-md-8">
                        <img src="assets/images/<?php echo htmlspecialchars($event['image']); ?>" class="img-fluid rounded-start"
                            alt="<?php echo htmlspecialchars($event['title']); ?>"
                            style="width: 800px; height: 400px; object-fit: cover;">
                    </div>
                    <div class="col-md-4">
                        <div class="card-body">
                            <h1 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h1>
                            <p class="card-text"><i class="fa fa-map-pin" aria-hidden="true"></i>
                                &nbsp;&nbsp;&nbsp;<strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?>
                            </p>
                            <p class="card-text"><i class="fa fa-users" aria-hidden="true"></i>
                                <strong>Capacity:</strong> <?php echo htmlspecialchars($event['capacity']); ?>
                            </p>
                            <p class="card-text"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                <?php echo nl2br(htmlspecialchars($event['description'])); ?></p>

                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="booking/book.php?id=<?php echo $event['id']; ?>" class="btn btn-success mt-3">Book Now</a>
                            <?php else: ?>
                                <p class="mt-3">
                                    <a href="auth/login.php" class="btn btn-success">Login to Book</a> or
                                    <a href="auth/register.php" class="btn btn-outline-secondary">Register</a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } else {
            echo "<div class='alert alert-danger'>Event not found.</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Error preparing statement.</div>";
    }

} else {
    echo "<div class='alert alert-danger'>No event ID provided.</div>";
}

include 'includes/footer.php';
$conn->close();
?>
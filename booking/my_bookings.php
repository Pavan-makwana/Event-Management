<?php
include '../config.php';
include '../includes/header.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT b.id, b.booked_date, b.booked_start_time, b.booked_end_time, b.status, e.title, e.venue, e.image,
               p.package_name, p.price
        FROM bookings b
        JOIN events e ON b.event_id = e.id
        JOIN event_packages p ON b.package_id = p.id
        WHERE b.user_id = ?
        ORDER BY b.booked_date DESC, b.booked_start_time DESC";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check for success or error messages from the URL
    $message = '';
    if (isset($_GET['message']) && $_GET['message'] == 'success') {
        $message = "<div class='alert alert-success'>Booking successful!</div>";
    } elseif (isset($_GET['message']) && $_GET['message'] == 'cancelled') {
        $message = "<div class='alert alert-success'>Booking cancelled successfully.</div>";
    } elseif (isset($_GET['message']) && $_GET['message'] == 'deleted_permanent') { // NEW: Success message for permanent delete
        $message = "<div class='alert alert-success'>Canceled booking permanently deleted.</div>";
    } elseif (isset($_GET['error']) && $_GET['error'] == 'already_booked') {
        $message = "<div class='alert alert-danger'>You have already booked this event for a conflicting time slot. Please check your existing bookings.</div>";
    }
}
?>

<div class="container">
    <h2 class="my-4">My Bookings</h2>
    <?php echo $message; ?>

    <?php if ($result->num_rows > 0): ?>
        <div class="list-group">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="list-group-item list-group-item-action mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="../assets/images/<?php echo htmlspecialchars($row['image']); ?>"
                                class="img-fluid rounded-start" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        </div>
                        <div class="col-md-8">
                            <h5><?php echo htmlspecialchars($row['title']); ?></h5>
                            
                            <p class="mb-1"><strong>Package:</strong> <?php echo htmlspecialchars($row['package_name']); ?></p>
                            
                            <p class="mb-1"><strong>Price:</strong> <?php echo number_format($row['price'], 2); ?></p>
                            
                            <p class="mb-1"><strong>Venue:</strong> <?php echo htmlspecialchars($row['venue']); ?></p>
                            <p class="mb-1"><strong>Date:</strong> <?php echo htmlspecialchars($row['booked_date']); ?></p>
                            <p class="mb-1"><strong>Start Time:</strong> <?php echo htmlspecialchars(date('g:i A', strtotime($row['booked_start_time']))); ?></p>
                            <p class="mb-1"><strong>End Time:</strong> <?php echo htmlspecialchars(date('g:i A', strtotime($row['booked_end_time']))); ?></p>
                            <span class="badge <?php echo ($row['status'] == 'booked') ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </div>
                        <div class="col-md-2 text-end">
                            <?php if ($row['status'] == 'booked'): ?>
                                <a href="cancel_booking.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Cancel</a>
                            <?php elseif ($row['status'] == 'cancelled'): ?>
                                <a href="delete_my_booking.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Permanently delete this canceled booking history?');">Delete</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            You have no upcoming bookings.
        </div>
    <?php endif; ?>
</div>

<?php
$stmt->close();
include '../includes/footer.php';
$conn->close();
?>
<?php
include '../config.php';
include '../includes/header.php';

// Access control: check if the user is an admin
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin')) {
    header("Location: ../index.php");
    exit;
}

$message = '';
if (isset($_GET['message']) && $_GET['message'] == 'package_saved') {
    $message = "<div class='alert alert-success mt-3'>Package successfully saved!</div>";
} elseif (isset($_GET['message']) && $_GET['message'] == 'package_deleted') {
    $message = "<div class='alert alert-danger mt-3'>Package deleted successfully!</div>";
}

// Fetch all events to link to their package management page
$sql_events = "SELECT id, title, venue FROM events ORDER BY title ASC";
$result_events = $conn->query($sql_events);
?>

<div class="container">
    <h2 class="my-4">Package Management Hub</h2>
    <?php echo $message; ?>
    
    <p class="lead">Select an event below to add, edit, or delete its booking packages.</p>

    <div class="card">
        <div class="card-header">
            <h4>Events List</h4>
        </div>
        <ul class="list-group list-group-flush">
            <?php if ($result_events->num_rows > 0): ?>
                <?php while($event = $result_events->fetch_assoc()): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                            <small class="text-muted d-block"><?php echo htmlspecialchars($event['venue']); ?></small>
                        </div>
                        <a href="edit_packages.php?event_id=<?php echo $event['id']; ?>" class="btn btn-primary btn-sm">
                            Manage Packages <i class="fas fa-arrow-right"></i>
                        </a>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li class="list-group-item text-center">No events available. Please <a href="add_event.php">add an event</a> first.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php
include '../includes/footer.php';
$conn->close();
?>
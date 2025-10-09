<?php
include '../config.php';
include '../includes/header.php';

// Access control
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin')) {
    header("Location: ../index.php");
    exit;
}

$event_id = isset($_REQUEST['event_id']) ? filter_var($_REQUEST['event_id'], FILTER_VALIDATE_INT) : null;
if (!$event_id) {
    header("Location: manage_packages.php");
    exit;
}

// --- Package Action Handler (Add/Edit/Delete Logic) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $package_name = $_POST['package_name'];
    $price = $_POST['price'];
    $package_id = filter_var($_POST['package_id'] ?? null, FILTER_VALIDATE_INT);
    $action = $_POST['action'] ?? 'add';

    if ($action === 'add') {
        $sql = "INSERT INTO event_packages (event_id, package_name, price) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isd", $event_id, $package_name, $price);
        $stmt->execute();
    } elseif ($action === 'edit' && $package_id) {
        $sql = "UPDATE event_packages SET package_name = ?, price = ? WHERE id = ? AND event_id = ?";

        if ($stmt = $conn->prepare($sql)) {
            $price_str = (string) $price; // Explicitly cast price to string
            $stmt->bind_param("ssii", $package_name, $price_str, $package_id, $event_id);
            $stmt->execute();
        } else {
            // Handle DB prepare error
        }
    }

    header("Location: edit_packages.php?event_id=" . $event_id . "&message=package_saved");
    exit;
}
// Handle GET Delete Request
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM event_packages WHERE id = ? AND event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $delete_id, $event_id);
    $stmt->execute();

    header("Location: edit_packages.php?event_id=" . $event_id . "&message=package_deleted");
    exit;
}

// Fetch current event title
$sql_event = "SELECT title FROM events WHERE id = ?";
$stmt_event = $conn->prepare($sql_event);
$stmt_event->bind_param("i", $event_id);
$stmt_event->execute();
$event_title = $stmt_event->get_result()->fetch_assoc()['title'];

// Fetch existing packages for the event
$sql_packages = "SELECT id, package_name, price FROM event_packages WHERE event_id = ?";
$stmt_packages = $conn->prepare($sql_packages);
$stmt_packages->bind_param("i", $event_id);
$stmt_packages->execute();
$result_packages = $stmt_packages->get_result();

// Initial package data for the form (for editing)
$edit_package = null;
if (isset($_GET['package_id']) && is_numeric($_GET['package_id'])) {
    $edit_id = $_GET['package_id'];
    $sql_edit = "SELECT id, package_name, price FROM event_packages WHERE id = ? AND event_id = ?";
    $stmt_edit = $conn->prepare($sql_edit);
    $stmt_edit->bind_param("ii", $edit_id, $event_id);
    $stmt_edit->execute();
    $edit_package = $stmt_edit->get_result()->fetch_assoc();
}
?>

<div class="container">
    <h2 class="my-4">Manage Packages for: <?php echo htmlspecialchars($event_title); ?></h2>

    <a href="manage_packages.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="fas fa-arrow-left"></i> Back
        to Hub</a>

    <!-- <?php echo $message; ?> -->

    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <?php if ($edit_package): ?>
                        Edit Package
                    <?php else: ?>
                        Add New Package
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <form action="edit_packages.php?event_id=<?php echo $event_id; ?>" method="POST">
                        <input type="hidden" name="action" value="<?php echo $edit_package ? 'edit' : 'add'; ?>">
                        <?php if ($edit_package): ?>
                            <input type="hidden" name="package_id" value="<?php echo $edit_package['id']; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="package_name" class="form-label">Package Name</label>
                            <input type="text" class="form-control" id="package_name" name="package_name"
                                value="<?php echo htmlspecialchars($edit_package['package_name'] ?? ''); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price </label>
                            <input type="number" step="0.01" min="0" class="form-control" id="price" name="price"
                                value="<?php echo htmlspecialchars($edit_package['price'] ?? '0.00'); ?>" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <?php echo $edit_package ? 'Update Package' : 'Add Package'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    Existing Packages
                </div>
                <ul class="list-group list-group-flush">
                    <?php if ($result_packages->num_rows > 0): ?>
                        <?php while ($pkg = $result_packages->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars($pkg['package_name']); ?></strong>
                                    <span
                                        class="badge bg-info text-dark ms-2"><?php echo number_format($pkg['price'], 2); ?></span>
                                </div>
                                <div>
                                    <a href="edit_packages.php?event_id=<?php echo $event_id; ?>&package_id=<?php echo $pkg['id']; ?>"
                                        class="btn btn-sm btn-warning me-2">Edit</a>
                                    <a href="edit_packages.php?event_id=<?php echo $event_id; ?>&delete_id=<?php echo $pkg['id']; ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this package? This cannot be undone if it has existing bookings.');">Delete</a>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="list-group-item text-center text-muted">No packages defined for this event yet.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
$conn->close();
?>
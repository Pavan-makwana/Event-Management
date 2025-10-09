<?php
include '../config.php';
include '../includes/header.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin')) {
    header("Location: ../index.php");
    exit;
}

$message = '';
$event = [];
$event_id = isset($_REQUEST['id']) ? filter_var($_REQUEST['id'], FILTER_VALIDATE_INT) : null;

if (!$event_id) {
    $message = "<div class='alert alert-danger'>Invalid event ID.</div>";
    goto end_script; 
}

// --- 1. Handle Form Submission (UPDATE logic) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $venue = $_POST['venue'];
    $capacity = $_POST['capacity'];
    $current_image = $_POST['current_image'];
    
    $update_image_sql = "";
    $bind_types = "sssi"; 
    $bind_params = [
        $title,
        $description,
        $venue,
        $capacity,
    ];

    // Handle Image Upload (if a new file is provided)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/';
        $image_name = basename($_FILES['image']['name']);
        $image_path = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $new_image = $image_name;
            $update_image_sql = ", image = ?";
            
            $bind_types .= "s"; 
            $bind_params[] = $new_image;
            
            // Optionally: Delete the old image file
            if ($current_image && file_exists($upload_dir . $current_image) && is_file($upload_dir . $current_image)) {
                unlink($upload_dir . $current_image);
            }
        } else {
            $message = "<div class='alert alert-warning'>Failed to upload new image. Keeping old image.</div>";
        }
    }
    
    $bind_types .= "i"; 
    $bind_params[] = $event_id;
    
    // Build the final SQL query
    $sql = "UPDATE events SET title = ?, description = ?, venue = ?, capacity = ?" . $update_image_sql . " WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param($bind_types, ...$bind_params); 

        if ($stmt->execute()) {
            header("Location: dashboard.php?message=updated");
            exit;
        } else {
            $message = "<div class='alert alert-danger'>Error updating event: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='alert alert-danger'>Database error: " . $conn->error . "</div>";
    }
}

// --- 2. Fetch Event Data (Initial load) ---
$sql_fetch = "SELECT * FROM events WHERE id = ?";
if ($stmt = $conn->prepare($sql_fetch)) {
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $event = $result->fetch_assoc();
    } else {
        $message = "<div class='alert alert-danger'>Event not found.</div>";
        goto end_script;
    }
    $stmt->close();
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="my-4">Edit Event: <?php echo htmlspecialchars($event['title']); ?></h2>
        <?php echo $message; ?>
        <form action="edit_event.php?id=<?php echo $event_id; ?>" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($event['image']); ?>">
            
            <div class="mb-3">
                <label for="title" class="form-label">Event Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($event['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="venue" class="form-label">Venue</label>
                <input type="text" class="form-control" id="venue" name="venue" value="<?php echo htmlspecialchars($event['venue']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="capacity" class="form-label">Capacity</label>
                <input type="number" class="form-control" id="capacity" name="capacity" value="<?php echo htmlspecialchars($event['capacity']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Current Image</label><br>
                <img src="../assets/images/<?php echo htmlspecialchars($event['image']); ?>" alt="Current Event Image" style="max-width: 150px; height: auto;" class="img-thumbnail mb-2">
            </div>
            
            <div class="mb-3">
                <label for="image" class="form-label">Replace Image (Optional)</label>
                <input class="form-control" type="file" id="image" name="image">
                <small class="form-text text-muted">Leave blank to keep the current image.</small>
            </div>
            
            <button type="submit" class="btn btn-success w-100">Update Event</button>
        </form>
    </div>
</div>

<?php
end_script:
include '../includes/footer.php';
$conn->close();
?>
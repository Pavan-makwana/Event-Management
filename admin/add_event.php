<!-- add_event  -->
<?php
include '../config.php';
include '../includes/header.php';

// check if the user is an admin
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin')) {
    header("Location: ../index.php"); // Redirect to home or a forbidden page
    exit;
}

$message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $venue = $_POST['venue'];
    $capacity = $_POST['capacity'];
    $image = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/';
        $image_name = basename($_FILES['image']['name']);
        $image_path = $upload_dir . $image_name;

        // Move the uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $image = $image_name;
        } else {
            $message = "<div class='alert alert-danger'>Failed to upload image.</div>";
        }
    }

    if ($image) {
        // Insert event into database
        $sql = "INSERT INTO events (title, description, venue, capacity, image) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssis", $title, $description, $venue, $capacity, $image);
            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>Event added successfully!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Error adding event: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } else {
            $message = "<div class='alert alert-danger'>Database error: " . $conn->error . "</div>";
        }
    } else {
         $message = "<div class='alert alert-danger'>Please upload an image.</div>";
    }
}
?>

<!-- Add new event -->
<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="my-4">Add New Event</h2>
        <?php echo $message; ?>
        <form action="add_event.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Event Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="venue" class="form-label">Venue</label>
                <input type="text" class="form-control" id="venue" name="venue" required>
            </div>
            <div class="mb-3">
                <label for="capacity" class="form-label">Capacity</label>
                <input type="number" class="form-control" id="capacity" name="capacity" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Event Image</label>
                <input class="form-control" type="file" id="image" name="image" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Add Event</button>
        </form>
    </div>
</div>

<?php
include '../includes/footer.php';
$conn->close();
?>
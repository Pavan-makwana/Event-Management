<?php
include 'config.php';
include 'includes/header.php';

// Fetch all events from the database
$sql = "SELECT id, title, description, venue, image FROM events";
$result = $conn->query($sql);

?>

<div class="container">
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-4"> <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                ?>
                <div class="col">
                    <div class="card h-100"> <img src="assets/images/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($row['description'], 0, 100)) . '...'; ?></p>
                            <a href="event.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Details</a>
                        </div>
                        
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No events found.</p>";
        }
        ?>
    </div>
</div>

<?php
include 'includes/footer.php';
$conn->close();
?>
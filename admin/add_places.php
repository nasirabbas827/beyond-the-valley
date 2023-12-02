<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

$successMessage = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the form data and insert into the database
    $placeName = $_POST["placeName"];
    $description = $_POST["description"];
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];

    // Perform the database insertion here
    $insertQuery = "INSERT INTO Place (Place_Name, Description, Latitude, Longitude) 
                    VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param('ssdd', $placeName, $description, $latitude, $longitude);
    
    if ($stmt->execute()) {
        // Success message
        $successMessage = "Place added successfully!";
        // Optionally, you can redirect to another page after successful insertion
        // header("Location: success_page.php");
    } else {
        // Error message
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Your custom stylesheet -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-4">
    <h2>Add Place</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <!-- Display success message if available -->
        <?php if (!empty($successMessage)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="placeName">Place Name:</label>
            <input type="text" class="form-control" name="placeName" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" name="description" required></textarea>
        </div>

        <div class="form-group">
            <label for="latitude">Latitude:</label>
            <input type="text" class="form-control" name="latitude" placeholder="e.g., 40.7128" required>
        </div>

        <div class="form-group">
            <label for="longitude">Longitude:</label>
            <input type="text" class="form-control" name="longitude" placeholder="e.g., -74.0060" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Place</button>
     <a href="view_places.php" class="btn btn-secondary ">View Places</a>

    </form>

</div>

<!-- Bootstrap JS and Popper.js (required for Bootstrap JavaScript components) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

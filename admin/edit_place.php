<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the form data and update the database
    $editID = $_POST["edit_id"];
    $placeName = $_POST["placeName"];
    $description = $_POST["description"];
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];

    // Perform the database update here
    $updateQuery = "UPDATE Place 
                    SET Place_Name = ?, Description = ?, Latitude = ?, Longitude = ? 
                    WHERE Place_ID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('ssddi', $placeName, $description, $latitude, $longitude, $editID);
    $stmt->execute();
    $stmt->close();

    // Redirect to the view places page after updating
    header("Location: view_places.php");
}

// Retrieve place details for editing
if (isset($_GET["edit_id"])) {
    $editID = $_GET["edit_id"];

    $editQuery = "SELECT * FROM Place WHERE Place_ID = ?";
    $stmt = $conn->prepare($editQuery);
    $stmt->bind_param('i', $editID);
    $stmt->execute();
    $editResult = $stmt->get_result();
    $editRow = $editResult->fetch_assoc();
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Place</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Your custom stylesheet -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-4">
    <h2>Edit Place</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="edit_id" value="<?php echo $editRow['Place_ID']; ?>">
        <div class="form-group">
            <label for="placeName">Place Name:</label>
            <input type="text" class="form-control" name="placeName" value="<?php echo $editRow['Place_Name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" name="description" required><?php echo $editRow['Description']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="latitude">Latitude:</label>
            <input type="text" class="form-control" name="latitude" value="<?php echo $editRow['Latitude']; ?>" required>
        </div>
        <div class="form-group">
            <label for="longitude">Longitude:</label>
            <input type="text" class="form-control" name="longitude" value="<?php echo $editRow['Longitude']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Place</button>
    </form>
</div>

<!-- Bootstrap JS and Popper.js (required for Bootstrap JavaScript components) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>


<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission for updating booking
    $booking_id = $_POST['booking_id'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];
    $adults = $_POST['adults'];
    $childs = $_POST['childs'];
    $rooms = $_POST['rooms'];

// Perform the update in the database
$updateSql = "UPDATE bookings 
              SET check_in_date=?, check_out_date=?, adults=?, childs=?, rooms=?
              WHERE booking_id=?";
$stmt = $conn->prepare($updateSql);
$stmt->bind_param("ssiidi", $check_in_date, $check_out_date, $adults, $childs, $rooms, $booking_id);

    if ($stmt->execute()) {
        // Update successful
        header("Location: my_bookings.php");
        exit;
    } else {
        // Update failed
        echo "Update failed. Please try again.";
    }

    $stmt->close();
} else {
    // Display the form for updating booking
    $booking_id = $_GET['id'];
    $selectSql = "SELECT booking_id, check_in_date, check_out_date, adults, childs, rooms 
                  FROM bookings 
                  WHERE booking_id=?";
    $stmt = $conn->prepare($selectSql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->bind_result($booking_id, $check_in_date, $check_out_date, $adults, $childs, $rooms);
    $stmt->fetch();
    $stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Booking</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>

    <?php include('navbar.php'); ?>

    <div class="container mt-5 mb-5">
        <h3>Update Booking</h3>
        <form method="POST" action="update_booking.php">
            <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">

            <div class="form-group">
                <label for="check_in_date">Check-In Date:</label>
                <input type="date" class="form-control" name="check_in_date" value="<?php echo $check_in_date; ?>" required>
            </div>

            <div class="form-group">
                <label for="check_out_date">Check-Out Date:</label>
                <input type="date" class="form-control" name="check_out_date" value="<?php echo $check_out_date; ?>" required>
            </div>

            <div class="form-group">
                <label for="adults">Adults:</label>
                <input type="number" class="form-control" name="adults" value="<?php echo $adults; ?>" required>
            </div>

            <div class="form-group">
                <label for="childs">Childs:</label>
                <input type="number" class="form-control" name="childs" value="<?php echo $childs; ?>" required>
            </div>

            <div class="form-group">
                <label for="rooms">Rooms:</label>
                <input type="number" class="form-control" name="rooms" value="<?php echo $rooms; ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    <!-- Bootstrap JS and your custom JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Your custom JavaScript -->
    <script src="script.js"></script>
</body>

</html>

<?php
}
$conn->close();
?>

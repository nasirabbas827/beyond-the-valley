<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission for updating booking
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];

    // Perform the update in the database
    $updateSql = "UPDATE bookings SET status=?, check_in_date=?, check_out_date=? WHERE booking_id=?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sssi", $status, $check_in_date, $check_out_date, $booking_id);

    if ($stmt->execute()) {
        // Update successful
        header("Location: view_bookings.php");
        exit;
    } else {
        // Update failed
        echo "Update failed. Please try again.";
    }

    $stmt->close();
} else {
    // Display the form for updating booking
    $booking_id = $_GET['id'];
    $selectSql = "SELECT booking_id, hotel_id, user_id, check_in_date, check_out_date, adults, childs, rooms, payment_method, status FROM bookings WHERE booking_id=?";
    $stmt = $conn->prepare($selectSql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->bind_result($booking_id, $hotel_id, $user_id, $check_in_date, $check_out_date, $adults, $childs, $rooms, $payment_method, $status);
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
    
    <!-- Your custom stylesheet -->
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-4">
        <h2>Update Booking</h2>

        <form method="POST" action="update_booking.php">
            <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">

            <div class="form-group">
                <label for="status">Status:</label>
                <select class="form-control" name="status">
                    <option value="pending" <?php echo ($status === 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo ($status === 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="cancelled" <?php echo ($status === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <div class="form-group">
                <label for="check_in_date">Check-in Date:</label>
                <input type="date" class="form-control" name="check_in_date" value="<?php echo $check_in_date; ?>" required>
            </div>

            <div class="form-group">
                <label for="check_out_date">Check-out Date:</label>
                <input type="date" class="form-control" name="check_out_date" value="<?php echo $check_out_date; ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    <!-- Bootstrap JS and Popper.js (required for Bootstrap JavaScript components) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
}
$conn->close();
?>

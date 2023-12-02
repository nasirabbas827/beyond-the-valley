<?php
include('config.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Retrieve user's bookings from the database
$sql = "SELECT b.*, h.hotel_name
        FROM bookings b
        INNER JOIN hotel h ON b.hotel_id = h.hotel_id
        WHERE b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch bookings as an associative array
$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h3>My Bookings</h3>
    <?php if (!empty($bookings)) : ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Hotel Name</th>
                    <th>Check-In Date</th>
                    <th>Check-Out Date</th>
                    <th>Adults</th>
                    <th>Childs</th>
                    <th>Rooms</th>
                    <th>Payment Method</th>
                    <th>Booking Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking) : ?>
                    <tr>
                        <td><?php echo $booking['booking_id']; ?></td>
                        <td><?php echo $booking['hotel_name']; ?></td>
                        <td><?php echo $booking['check_in_date']; ?></td>
                        <td><?php echo $booking['check_out_date']; ?></td>
                        <td><?php echo $booking['adults']; ?></td>
                        <td><?php echo $booking['childs']; ?></td>
                        <td><?php echo $booking['rooms']; ?></td>
                        <td><?php echo $booking['payment_method']; ?></td>
                        <td><?php echo $booking['booking_date']; ?></td>
                        <td><?php echo $booking['status']; ?></td>
                        <td>
                            <?php if ($booking['status'] === 'confirmed') : ?>
                                <a class="btn btn-outline-dark" href='update_booking.php?id=<?php echo $booking['booking_id']; ?>'>Update</a>
                            <?php else : ?>
                                <!-- Display a message or leave it blank for other statuses -->
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No bookings found.</p>
    <?php endif; ?>
</div>

<!-- Bootstrap JS and your custom JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Your custom JavaScript -->
<script src="script.js"></script>
</body>
</html>

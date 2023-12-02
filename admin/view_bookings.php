<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch all bookings from the database with user information
$sql = "SELECT b.booking_id, b.hotel_id, u.username, b.check_in_date, b.check_out_date, b.adults, b.childs, b.rooms, b.payment_method, b.status
        FROM bookings b
        INNER JOIN users u ON b.user_id = u.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-4">
        <h2>Booking List</h2>

        <?php
        if ($result->num_rows > 0) {
            echo "<table class='table table-bordered'>
                    <thead class='thead-dark'>
                        <tr>
                            <th>Booking ID</th>
                            <th>Hotel ID</th>
                            <th>User Name</th>
                            <th>Check-in Date</th>
                            <th>Check-out Date</th>
                            <th>Adults</th>
                            <th>Childs</th>
                            <th>Rooms</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['booking_id']}</td>
                        <td>{$row['hotel_id']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['check_in_date']}</td>
                        <td>{$row['check_out_date']}</td>
                        <td>{$row['adults']}</td>
                        <td>{$row['childs']}</td>
                        <td>{$row['rooms']}</td>
                        <td>{$row['payment_method']}</td>
                        <td>{$row['status']}</td>
                        <td>
                            <a href='update_booking.php?id={$row['booking_id']}' class='btn btn-primary'>Update</a>
                        </td>
                    </tr>";
            }

            echo "</tbody>
                </table>";
        } else {
            echo "<p>No bookings found.</p>";
        }

        $conn->close();
        ?>
    </div>

    <!-- Bootstrap JS and Popper.js (required for Bootstrap JavaScript components) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>


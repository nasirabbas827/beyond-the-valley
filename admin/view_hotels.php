<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

$successMessage = "";

// Delete hotel
if (isset($_GET["delete_id"])) {
    $deleteID = $_GET["delete_id"];

    // Perform the database deletion here
    $deleteQuery = "DELETE FROM hotel WHERE hotel_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('i', $deleteID);
    $stmt->execute();
    $stmt->close();
    
    // Redirect to the same page after deletion
    header("Location: view_hotels.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Hotels</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Your custom stylesheet -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-4">
    <h2>View Hotels</h2>

    <!-- Display success message if available -->
    <?php if (!empty($successMessage)) : ?>
        <p class="alert alert-success"><?php echo $successMessage; ?></p>
    <?php endif; ?>

    <!-- Display hotels in a table -->
    <table class="table table-bordered mt-3">
        <thead class="thead-dark">
            <tr>
                <th>Hotel ID</th>
                <th>Hotel Name</th>
                <th>Place Name</th>
                <th>Contact Number</th>
                <th>Number of Rooms</th>
                <th>Room Types</th>
                <th>Amenities</th>
                <th>Description</th>
                <th>Hotel Picture</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>

        <?php
        // Retrieve hotels from the database
        $selectQuery = "SELECT h.hotel_id, h.hotel_name, p.Place_Name, h.contact_number, h.number_of_rooms, h.room_types, h.amenities, h.description, h.hotel_picture, h.price
                        FROM hotel h
                        JOIN Place p ON h.place_id = p.Place_ID";

        $result = $conn->query($selectQuery);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['hotel_id']}</td>";
                echo "<td>{$row['hotel_name']}</td>";
                echo "<td>{$row['Place_Name']}</td>";
                echo "<td>{$row['contact_number']}</td>";
                echo "<td>{$row['number_of_rooms']}</td>";
                echo "<td>{$row['room_types']}</td>";
                echo "<td>{$row['amenities']}</td>";
                echo "<td>{$row['description']}</td>";
                echo "<td><img src='{$row['hotel_picture']}' alt='Hotel Picture' width='100'></td>";
                echo "<td>{$row['price']}</td>";
                echo "<td>
                        <a href='edit_hotel.php?edit_id={$row['hotel_id']}' class='btn btn-primary'>Edit</a>
                        <a href='view_hotels.php?delete_id={$row['hotel_id']}' class='mt-2 btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this hotel?\");'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='11'>No hotels found</td></tr>";
        }

        $result->close();
        ?>

        </tbody>
    </table>
</div>

<!-- Bootstrap JS and Popper.js (required for Bootstrap JavaScript components) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>


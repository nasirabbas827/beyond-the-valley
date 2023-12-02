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

// Initialize variables
$hotelRow = [];

// Retrieve hotel ID from the URL
if (isset($_GET['hotel_id'])) {
    $hotel_id = $_GET['hotel_id'];

    // Fetch hotel details
    $hotelSql = "SELECT h.*, p.Place_Name 
                 FROM hotel h 
                 JOIN place p ON h.place_id = p.Place_ID
                 WHERE hotel_id = ?";
    $stmt = $conn->prepare($hotelSql);
    $stmt->bind_param("i", $hotel_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the hotel details as an associative array
    $hotelRow = $result->fetch_assoc();

    $stmt->close();
} else {
    echo "Hotel ID not provided.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Details</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h3><?php echo $hotelRow['hotel_name']; ?></h3>
    <div class="row">
        <div class="col-md-6">
            <img src="./admin/<?php echo $hotelRow['hotel_picture']; ?>" class="img-fluid" height="400px" width="400px" alt="<?php echo $hotelRow['hotel_name']; ?>">
        </div>
        <div class="col-md-6">
            <p><strong>Location:</strong> <?php echo $hotelRow['Place_Name']; ?></p>
            <p><strong>Contact Number:</strong> <?php echo $hotelRow['contact_number']; ?></p>
            <p><strong>Number of Rooms:</strong> <?php echo $hotelRow['number_of_rooms']; ?></p>
            <p><strong>Room Types:</strong> <?php echo $hotelRow['room_types']; ?></p>
            <p><strong>Amenities:</strong> <?php echo $hotelRow['amenities']; ?></p>
            <p><strong>Description:</strong> <?php echo $hotelRow['description']; ?></p>
            <p><strong>Price: Pkr </strong> <?php echo $hotelRow['price']; ?></p>
        </div>
    </div>
</div>

<div class="container mt-5 mb-5">
    <h3>Book Now</h3>
    <form action="booking.php" method="post"> <!-- Changed action to booking_process.php -->
        <input type="hidden" name="hotel_id" value="<?php echo $hotelRow['hotel_id']; ?>">

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="check_in_date">Check-In Date:</label>
                    <input type="date" class="form-control" name="check_in_date" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="check_out_date">Check-Out Date:</label>
                    <input type="date" class="form-control" name="check_out_date" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="adults">Adults:</label>
                    <input type="number" class="form-control" name="adults" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="childs">Childs:</label>
                    <input type="number" class="form-control" name="childs" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="rooms">Rooms:</label>
                    <input type="number" class="form-control" name="rooms" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="payment_method">Payment Method:</label>
                    <select class="form-control" name="payment_method" required>
                        <option value="easy_paisa">Easy Paisa</option>
                        <option value="jazz_cash">Jazz Cash</option>
                        <option value="cash">Cash</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="float-right btn btn-primary">Submit Booking</button>
            </div>
        </div>
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

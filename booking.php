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

// Fetch user details from the database
$sql = "SELECT id, username, email, age FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $fetched_id, $username, $email, $age);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $hotel_id = $_POST['hotel_id'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];
    $adults = $_POST['adults'];
    $childs = $_POST['childs'];
    $rooms = $_POST['rooms'];
    $payment_method = $_POST['payment_method'];

    // Set the booking status to "pending"
    $booking_status = "pending";

    // For example, you might insert the booking details into a database
    $insertSql = "INSERT INTO bookings (hotel_id, user_id, check_in_date, check_out_date, adults, childs, rooms, payment_method, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("iissiiiss", $hotel_id, $user_id, $check_in_date, $check_out_date, $adults, $childs, $rooms, $payment_method, $booking_status);

    // Execute the statement
    if ($stmt->execute()) {
        // Booking successful
        echo "Booking successful! Your booking is pending.";
    } else {
        // Booking failed
        echo "Booking failed. Please try again.";
    }

    $stmt->close();
    $conn->close();
} else {
    // If the form is not submitted, redirect to home page or display an error message
    header("location: home.php");
    exit;
}
?>

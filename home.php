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

// Initialize variables
$searchResults = '';
$message = '';
$places = []; // Added this line to initialize the $places array

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $placeInput = $_POST['placeInput'];

    // Perform SQL query
    $sql = "SELECT * FROM place WHERE Place_Name LIKE ?";
    $placeInput = "%$placeInput%";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $placeInput);
    $stmt->execute();
    $result = $stmt->get_result();

// Check if there are search results
if ($result->num_rows > 0) {
    // Display search results
    $searchResults .= "<div class='row'>";
    while ($row = $result->fetch_assoc()) {
        $placeID = $row['Place_ID'];
        $placeName = $row['Place_Name'];
        $description = $row['Description'];

        // Display place card
        $searchResults .= "<div class='col-md-4'>";
        $searchResults .= "<div class='card'>";
        $searchResults .= "<div class='card-body'>";
        $searchResults .= "<h5 class='card-title'>$placeName</h5>";
        $searchResults .= "<p class='card-text'>$description</p>";
        $searchResults .= "</div></div></div>";

        // Fetch and display hotels for the selected place
        $hotelQuery = "SELECT * FROM hotel WHERE place_id = $placeID";
        $hotelResult = $conn->query($hotelQuery);

        while ($hotelRow = $hotelResult->fetch_assoc()) {
            $hotelName = $hotelRow['hotel_name'];
            $contactNumber = $hotelRow['contact_number'];
            $numberOfRooms = $hotelRow['number_of_rooms'];
            $roomTypes = $hotelRow['room_types'];
            $amenities = $hotelRow['amenities'];
            $description = $hotelRow['description'];
            $hotelPicture = $hotelRow['hotel_picture'];
            $price = $hotelRow['price'];

            // Display hotel card
            $searchResults .= "<div class='mr-2 mb-3 col-md-4'>";
            $searchResults .= "<div class='card hotel-card h-100'>";
            $searchResults .= "<img src='./admin/$hotelPicture' class='card-img-top' alt='Hotel Picture'>";
            $searchResults .= "<div class='card-body'>";
            $searchResults .= "<h5 class='card-title'>$hotelName</h5>";
            $searchResults .= "<p class='card-text'>Contact: $contactNumber</p>";
            $searchResults .= "<p class='card-text'>Number of Rooms: $numberOfRooms</p>";
            $searchResults .= "<p class='card-text'>Room Types: $roomTypes</p>";
            $searchResults .= "<p class='card-text'>Amenities: $amenities</p>";
            $searchResults .= "<p class='card-text'>Description: $description</p>";
            $searchResults .= "<p class='card-text'>Price: $price</p>";
            $searchResults .= "<a href='process_booking.php?hotel_id=$placeID' class='btn btn-primary'>Book Now</a>";

            $searchResults .= "</div></div></div>";
        }
    }
    $searchResults .= "</div>";
} else {
    // No search results
    $message = "No results found for '$placeInput'.";
}



    $stmt->close();
    
    // Retrieve places for the map
    $placesQuery = "SELECT * FROM place";
    $placesResult = $conn->query($placesQuery);

    while ($placeRow = $placesResult->fetch_assoc()) {
        $places[] = $placeRow;
    }
}
 

// Fetch places from the database
$sql = "SELECT * FROM Place";
$result = $conn->query($sql);

// Fetch places as an associative array
$places = [];
while ($row = $result->fetch_assoc()) {
    $places[] = $row;
}

// Fetch hotels from the database
$hotelSql = "SELECT h.*, p.Place_Name 
              FROM hotel h 
              JOIN Place p ON h.place_id = p.Place_ID";
$hotelResult = $conn->query($hotelSql);

// Fetch hotels as an associative array
$hotels = [];
while ($hotelRow = $hotelResult->fetch_assoc()) {
    $hotels[] = $hotelRow;
}
$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Welcome, <?php echo $username; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCLMaDWCs_4X1m9hecvyhUt-6k4qxQfiWU&callback=initMap" async defer></script>

    <!-- Include any additional styles or scripts here -->
    <style>
        #map {
            height: 400px;
            width: 100%;
            margin-bottom: 20px; /* Add margin for separation */
        }

        .hotel-card {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            width: 300px;
            display: inline-block;
        }

        .search-bar {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-input {
            flex-grow: 1;
            margin-right: 10px;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .card-col {
            flex: 0 0 calc(33.33% - 20px); /* Adjust the width based on your preference */
            margin-bottom: 20px;
        }
    </style>
    <script>
        // Initialize Google Map
        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 0, lng: 0 },
                zoom: 2
            });

            // Display places on the map
            <?php foreach ($places as $place) : ?>
                var marker = new google.maps.Marker({
                    position: { lat: <?php echo $place['Latitude']; ?>, lng: <?php echo $place['Longitude']; ?> },
                    map: map,
                    title: '<?php echo $place['Place_Name']; ?>'
                });

                var infowindow = new google.maps.InfoWindow({
                    content: '<strong><?php echo $place['Place_Name']; ?></strong><br><?php echo $place['Description']; ?>'
                });

                marker.addListener('click', function () {
                    infowindow.open(map, marker);
                });
            <?php endforeach; ?>
        }
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="home.php">Beyond The Valley</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="update_profile.php">Update Profile</a></li>
            <li class="nav-item"><a class="nav-link" href="my_bookings.php">My Bookings</a></li>
            <li class="nav-item"><a class="nav-link" href="contact_support.php">Customer Support</a></li>
            <li class="nav-item"><a class="nav-link" href="view_messages.php">View Messages</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

    <div class="container mt-5">
        <h2>Welcome, <?php echo $username; ?>!</h2>

        <h2>View Places on Map</h2>
        <!-- Map container -->
        <div id="map"></div>
    </div>

        <div class="container search-bar">
            <form action="home.php" method="post" class="w-100">
                <div class="input-group">
                    <input type="text" id="placeInput" name="placeInput" class="form-control search-input" placeholder="Search by Place Name" required>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="container card-container">
        
        <?php
        // Display search results or message
        echo $searchResults;
        echo "<p>$message</p>";
        ?>
        </div>

        <div class="container card-container">
    <?php foreach ($hotels as $hotel) : ?>
        <div class="card-col">
            <div class="card hotel-card h-100 mb-3">
                <img src="./admin/<?php echo $hotel['hotel_picture']; ?>" class="card-img-top img-fluid" alt="Hotel Picture">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $hotel['hotel_name']; ?></h5>
                    <p class="card-text"><?php echo $hotel['description']; ?></p>
                    <p class="card-text">Price: $<?php echo $hotel['price']; ?></p>
                    <a href="process_booking.php?hotel_id=<?php echo $hotel['hotel_id']; ?>" class="btn btn-primary">Book Now</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

       
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

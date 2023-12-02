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
    // Process the form data and update the database
    $editID = $_POST["edit_id"];
    $hotelName = $_POST["hotelName"];
    $placeID = $_POST["placeID"];
    $contactNumber = $_POST["contactNumber"];
    $numberOfRooms = $_POST["numberOfRooms"];
    $roomTypes = $_POST["roomTypes"];
    $amenities = $_POST["amenities"];
    $description = $_POST["description"];
    $price = $_POST["price"];

    // Handle image upload
    $targetDir = "uploads/"; // Specify the directory where you want to store the uploaded images
    $targetFile = $targetDir . basename($_FILES["hotelPicture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the image file is a actual image or fake image
    $check = getimagesize($_FILES["hotelPicture"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["hotelPicture"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["hotelPicture"]["tmp_name"], $targetFile)) {
            // Perform the database update here
            $updateQuery = "UPDATE hotel 
                            SET hotel_name = ?, place_id = ?, contact_number = ?, 
                                number_of_rooms = ?, room_types = ?, amenities = ?, 
                                description = ?, hotel_picture = ?, price = ? 
                            WHERE hotel_id = ?";

            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param('sisisssssi', $hotelName, $placeID, $contactNumber, $numberOfRooms, $roomTypes, $amenities, $description, $targetFile, $price, $editID);
            
            if ($stmt->execute()) {
                // Success message
                $successMessage = "Hotel updated successfully!";
                header("location: view_hotels.php");
                exit();
            } else {
                // Error message
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Retrieve hotel details for editing
if (isset($_GET["edit_id"])) {
    $editID = $_GET["edit_id"];

    $editQuery = "SELECT * FROM hotel WHERE hotel_id = ?";
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
    <title>Edit Hotel</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Your custom stylesheet -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-4 mb-5">
    <h2>Edit Hotel</h2>

    <!-- Display success message if available -->
    <?php if (!empty($successMessage)) : ?>
        <p class="alert alert-success"><?php echo $successMessage; ?></p>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <input type="hidden" name="edit_id" value="<?php echo $editRow['hotel_id']; ?>">

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="hotelName">Hotel Name:</label>
                    <input type="text" class="form-control" name="hotelName" value="<?php echo $editRow['hotel_name']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="placeID">Place ID:</label>
                    <select class="form-control" name="placeID" required>
                        <?php
                        $placesQuery = "SELECT Place_ID, Place_Name FROM Place";
                        $placesResult = $conn->query($placesQuery);

                        if ($placesResult->num_rows > 0) {
                            while ($row = $placesResult->fetch_assoc()) {
                                $selected = ($row['Place_ID'] == $editRow['place_id']) ? "selected" : "";
                                echo "<option value='{$row['Place_ID']}' {$selected}>{$row['Place_Name']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="contactNumber">Contact Number:</label>
                    <input type="text" class="form-control" name="contactNumber" value="<?php echo $editRow['contact_number']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="numberOfRooms">Number of Rooms:</label>
                    <input type="number" class="form-control" name="numberOfRooms" value="<?php echo $editRow['number_of_rooms']; ?>" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="roomTypes">Room Types:</label>
                    <input type="text" class="form-control" name="roomTypes" value="<?php echo $editRow['room_types']; ?>">
                </div>

                <div class="form-group">
                    <label for="amenities">Amenities:</label>
                    <input type="text" class="form-control" name="amenities" value="<?php echo $editRow['amenities']; ?>">
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea class="form-control" name="description"><?php echo $editRow['description']; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="hotelPicture">Hotel Picture:</label>
                    <input type="file" class="form-control" name="hotelPicture" accept="image/*">
                    <img class="mt-1"  src="<?php echo $editRow['hotel_picture']; ?>" alt="Hotel Picture"   width="100">
                </div>

                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="text" class="form-control" name="price" value="<?php echo $editRow['price']; ?>" required>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary float-right">Update Hotel</button>
    </form>
</div>

<!-- Bootstrap JS and Popper.js (required for Bootstrap JavaScript components) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

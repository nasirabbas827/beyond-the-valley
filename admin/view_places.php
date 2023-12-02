<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Delete place
if (isset($_GET["delete_id"])) {
    $deleteID = $_GET["delete_id"];

    // Perform the database deletion here
    $deleteQuery = "DELETE FROM Place WHERE Place_ID = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('i', $deleteID);
    $stmt->execute();
    $stmt->close();
    
    // Redirect to the same page after deletion
    header("Location: view_places.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Places</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Your custom stylesheet -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-4">
    <h2>View Places</h2>

    <!-- Display places in a table -->
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Place ID</th>
                <th>Place Name</th>
                <th>Description</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>

        <?php
        // Retrieve places from the database
        $selectQuery = "SELECT * FROM Place";
        $result = $conn->query($selectQuery);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['Place_ID']}</td>";
                echo "<td>{$row['Place_Name']}</td>";
                echo "<td>{$row['Description']}</td>";
                echo "<td>{$row['Latitude']}</td>";
                echo "<td>{$row['Longitude']}</td>";
                echo "<td>
                        <a href='edit_place.php?edit_id={$row['Place_ID']}' class='btn btn-primary'>Edit</a>
                        <a href='view_places.php?delete_id={$row['Place_ID']}' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this place?\");'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No places found</td></tr>";
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

<?php
session_start();
include('../includes/db.php'); // Include DB connection

// Redirect to login if admin is not logged in
if (!isset($_SESSION['admin_logged_in']) || !isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch buses from the database (including the bus id)
$query = "SELECT id, bus_name, source, destination, departure_time FROM buses";
$result = mysqli_query($conn, $query);

// Check for errors in the query
if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Buses</title>
</head>
<body>

<h2>Manage Buses</h2>
<a href="add_bus.php">Add New Bus</a>

<table border="1">
    <tr>
        <th>Bus Name</th>
        <th>Source</th>
        <th>Destination</th>
        <th>Departure Time</th>
        <th>Action</th>
    </tr>

    <?php while ($bus = mysqli_fetch_assoc($result)) : ?>
        <tr>
            <td><?php echo $bus['bus_name']; ?></td>
            <td><?php echo $bus['source']; ?></td>
            <td><?php echo $bus['destination']; ?></td>
            <td><?php echo $bus['departure_time']; ?></td>
            <td>
                <!-- Use the 'id' field for edit and delete links -->
                <a href="edit_bus.php?id=<?php echo $bus['id']; ?>">Edit</a> | 
                <a href="manage_buses.php?delete_bus=<?php echo $bus['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

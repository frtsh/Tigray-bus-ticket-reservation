<?php
include('../includes/db.php'); // Include DB connection

// Fetch all buses from the database
$query = "SELECT * FROM buses";
$buses_result = mysqli_query($conn, $query);

// Fetch seats for each bus
if (isset($_GET['bus_id'])) {
    $bus_id = $_GET['bus_id'];
    $seat_query = "SELECT * FROM seats WHERE bus_id = $bus_id";
    $seats_result = mysqli_query($conn, $seat_query);
}
?>

<h2>Manage Seats</h2>

<form method="GET">
    <label for="bus_id">Select Bus:</label>
    <select name="bus_id" onchange="this.form.submit()">
        <option value="">-- Select Bus --</option>
        <?php while ($bus = mysqli_fetch_assoc($buses_result)) : ?>
            <option value="<?php echo $bus['id']; ?>" <?php echo (isset($_GET['bus_id']) && $_GET['bus_id'] == $bus['id']) ? 'selected' : ''; ?>><?php echo $bus['bus_name']; ?></option>
        <?php endwhile; ?>
    </select>
</form>

<?php if (isset($_GET['bus_id'])) : ?>
    <h3>Seats for Bus: <?php echo $bus_id; ?></h3>

    <table>
        <tr>
            <th>Seat Number</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($seat = mysqli_fetch_assoc($seats_result)) : ?>
            <tr>
                <td><?php echo $seat['seat_number']; ?></td>
                <td>
                    <?php echo isset($seat['status']) ? $seat['status'] : 'Status not available'; ?>
                </td>
                <td>
                    <a href="edit_seat.php?id=<?php echo $seat['id']; ?>">Edit</a> | 
                    <a href="manage_seats.php?delete_seat=<?php echo $seat['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

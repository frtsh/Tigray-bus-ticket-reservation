<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $date = $_POST['date'];

    // Fetch buses that match the search criteria
    $sql = "SELECT * FROM buses WHERE source = '$source' AND destination = '$destination' AND departure_time LIKE '$date%'";
    $result = $conn->query($sql);
}
?>

<?php include('header.php'); ?>

<h2 style="text-align: center; color: #333; font-size: 24px; margin-bottom: 20px;">Search Buses</h2>

<form method="POST" action="booking.php" style="max-width: 500px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <label for="source" style="display: block; font-weight: bold; margin-bottom: 8px;">Source:</label>
    <input type="text" id="source" name="source" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
    
    <label for="destination" style="display: block; font-weight: bold; margin-bottom: 8px;">Destination:</label>
    <input type="text" id="destination" name="destination" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
    
    <label for="date" style="display: block; font-weight: bold; margin-bottom: 8px;">Date:</label>
    <input type="date" id="date" name="date" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
    
    <button type="submit" style="width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; transition: background-color 0.3s;">
        Search
    </button>
</form>

<?php if (isset($result)): ?>
    <h3 style="text-align: center; color: #333; font-size: 22px; margin-top: 40px;">Available Buses</h3>
    <table style="width: 100%; margin: 20px auto; border-collapse: collapse; background-color: #fff; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        <tr style="background-color: #f2f2f2; color: #333;">
            <th style="padding: 10px; text-align: left;">Bus Name</th>
            <th style="padding: 10px; text-align: left;">Departure Time</th>
            <th style="padding: 10px; text-align: left;">Arrival Time</th>
            <th style="padding: 10px; text-align: left;">Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px;"><?php echo $row['bus_name']; ?></td>
                <td style="padding: 10px;"><?php echo $row['departure_time']; ?></td>
                <td style="padding: 10px;"><?php echo $row['arrival_time']; ?></td>
                <td style="padding: 10px;"><a href="seat_selection.php?bus_id=<?php echo $row['id']; ?>" style="color: #007bff; text-decoration: none; font-weight: bold;">Reserve</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

<?php include('footer.php'); ?>

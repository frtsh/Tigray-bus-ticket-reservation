<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$buses = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $source = trim($_POST['source']);
    $destination = trim($_POST['destination']);
    $date = $_POST['date']; // Format: YYYY-MM-DD

    $sql = "SELECT * FROM buses 
            WHERE TRIM(source) = ? 
            AND TRIM(destination) = ? 
            AND DATE(departure_time) = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $source, $destination, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $buses = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<?php include('header.php'); ?>

<h2 style="text-align: center; color: #333; font-size: 24px; margin-bottom: 20px;">Search Buses</h2>

<form method="POST" action="booking.php" style="max-width: 500px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <label for="source">Source:</label>
    <input type="text" id="source" name="source" required>

    <label for="destination">Destination:</label>
    <input type="text" id="destination" name="destination" required>

    <label for="date">Date:</label>
    <input type="date" id="date" name="date" required>

    <button type="submit">Search</button>
</form>

<?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
    <?php if (empty($buses)): ?>
        <p style="text-align:center; color:red;">No buses found for that route and date.</p>
    <?php else: ?>
        <h3 style="text-align: center;">Available Buses</h3>
        <table border="1" cellpadding="10" cellspacing="0" style="max-width: 500px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <tr>
                <th>Bus Name</th>
                <th>Departure Time</th>
                <th>Arrival Time</th>
                <th>Action</th>
            </tr>
            <?php foreach ($buses as $bus): ?>
                <tr>
                    <td><?php echo htmlspecialchars($bus['bus_name']); ?></td>
                    <td><?php echo htmlspecialchars($bus['departure_time']); ?></td>
                    <td><?php echo htmlspecialchars($bus['arrival_time']); ?></td>
                    <td>
                        <form action="seat_selection.php" method="GET">
                            <input type="hidden" name="bus_id" value="<?php echo $bus['id']; ?>">
                            <button type="submit">Reserve</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
<?php endif; ?>

<?php include('footer.php'); ?>

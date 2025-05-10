<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$bus_id = $_GET['bus_id'] ?? null;
$travel_date = $_GET['travel_date'] ?? date('Y-m-d'); // Default to today if not set

if (!$bus_id) {
    echo "Invalid bus selection.";
    exit();
}

$available_seats = [];

// Fetch available seats for selected date and bus
$sql = "SELECT seat_number FROM seats 
        WHERE bus_id = ? AND seat_number NOT IN (
            SELECT seat_number FROM tickets WHERE bus_id = ? AND travel_date = ?
        )";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $bus_id, $bus_id, $travel_date);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $available_seats[] = $row['seat_number'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_seats = $_POST['seats'] ?? [];
    $travel_date = $_POST['travel_date'] ?? date('Y-m-d');

    if (!empty($selected_seats)) {
        foreach ($selected_seats as $seat_number) {
            $sql_insert = "INSERT INTO tickets (user_id, bus_id, seat_number, travel_date, booking_time) 
                           VALUES (?, ?, ?, ?, NOW())";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iiss", $user_id, $bus_id, $seat_number, $travel_date);
            $stmt_insert->execute();
        }

        header("Location: payment.php?bus_id=$bus_id&seats=" . implode(',', $selected_seats) . "&travel_date=$travel_date");
        exit();
    } else {
        $error_message = "Please select at least one seat.";
    }
}
?>

<?php include('header.php'); ?>

<h2 style="text-align: center;  color: white;" >Select Seats</h2>

<?php if (isset($error_message)): ?>
    <p style="color: red; text-align: center;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="seat_selection.php?bus_id=<?php echo $bus_id; ?>&travel_date=<?php echo $travel_date; ?>" style="max-width: 500px; margin: 0 auto; color: white;">
    <label for="travel_date"><strong>Travel Date:</strong></label>
    <input type="date" name="travel_date" id="travel_date" value="<?php echo $travel_date; ?>" required onchange="this.form.submit()" style="margin-bottom: 20px; width: 100%; padding: 10px;">

    <h3>Available Seats on <?php echo $travel_date; ?>:</h3>
    <div style="display: flex; flex-wrap: wrap; background-color: white; color:black">
        <?php foreach ($available_seats as $seat): ?>
            <div style="margin: 10px;">
                <input type="checkbox" name="seats[]" value="<?php echo $seat; ?>" id="seat_<?php echo $seat; ?>" />
                <label for="seat_<?php echo $seat; ?>">Seat <?php echo $seat; ?></label>
            </div>
        <?php endforeach; ?>
        <?php if (empty($available_seats)): ?>
            <p style="color: red;">No seats available for this date.</p>
        <?php endif; ?>
    </div>

    <button type="submit" style="margin-top: 20px;">Confirm Booking</button>
</form>

<?php include('footer.php'); ?>

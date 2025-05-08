<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch bus details based on the bus_id passed in URL
$bus_id = $_GET['bus_id'];

// Prepared statement for bus details
$sql = "SELECT * FROM buses WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bus_id); // "i" means integer for bus_id
$stmt->execute();
$bus_result = $stmt->get_result();
$bus = $bus_result->fetch_assoc();

// Fetch available seats for this bus
$seats_sql = "SELECT * FROM seats WHERE bus_id = ? AND is_booked = 0";
$stmt_seats = $conn->prepare($seats_sql);
$stmt_seats->bind_param("i", $bus_id); // Bind bus_id parameter
$stmt_seats->execute();
$seats_result = $stmt_seats->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if seats are selected
    if (isset($_POST['seats']) && !empty($_POST['seats'])) {
        $selected_seats = implode(',', $_POST['seats']); // Convert selected seats array to a string

        // Redirect to the payment page with bus_id and selected seats as query parameters
        header("Location: payment.php?bus_id=$bus_id&seats=$selected_seats");
        exit();
    } else {
        echo "No seats selected!";
    }
}
?>

<?php include('header.php'); ?>

<h2 style="text-align: center; color: #333; font-size: 24px; margin-bottom: 20px;">Book Tickets for Bus: <?php echo $bus['bus_name']; ?></h2>

<form method="POST" action="seat_selection.php?bus_id=<?php echo $bus_id; ?>" style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <h3 style="text-align: center; font-size: 20px; color: #333; margin-bottom: 20px;">Select Available Seats</h3>
    
    <?php while ($seat = $seats_result->fetch_assoc()): ?>
        <label style="display: block; margin-bottom: 10px;">
            <input type="checkbox" name="seats[]" value="<?php echo $seat['seat_number']; ?>" style="margin-right: 10px;">
            Seat <?php echo $seat['seat_number']; ?>
        </label>
    <?php endwhile; ?>
    
    <button type="submit" style="width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; transition: background-color 0.3s;">
        Proceed to Payment
    </button>
</form>

<?php include('footer.php'); ?>

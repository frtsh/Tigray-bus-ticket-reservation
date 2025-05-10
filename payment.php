<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Initialize variables
$bus_id = $_GET['bus_id'];
$seats = $_GET['seats'];
$travel_date = $_GET['travel_date'] ?? date('Y-m-d'); // Default to today
$seat_array = explode(',', $seats);

// Set bus-specific static total amount
$amounts = [
    1 => 1000,
    2 => 2000,
    3 => 1500,
    4 => 1200,
    5 => 600
];
$total_amount = $amounts[$bus_id] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $card_number = $_POST['card_number'];
    $phone_number = $_POST['phone_number'];
    $upi_id = $_POST['upi_id'];
    $payment_method = $_POST['payment_method'];
    
    if (!empty($card_number) && !empty($phone_number) && !empty($upi_id)) {
        $user_id = $_SESSION['user_id'];

        // Insert tickets and mark seats as booked
        foreach ($seat_array as $seat_number) {
            $seat_number = mysqli_real_escape_string($conn, $seat_number);

            // Insert into tickets table with travel_date
            $sql_insert = "INSERT INTO tickets (user_id, bus_id, seat_number, booking_date, travel_date) 
                           VALUES (?, ?, ?, NOW(), ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iiss", $user_id, $bus_id, $seat_number, $travel_date);
            $stmt_insert->execute();

            // Mark the seat as booked for the specific date (if you track seats by date in another table)
            $sql_update = "UPDATE seats 
                           SET is_booked = 1 
                           WHERE bus_id = ? AND seat_number = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("is", $bus_id, $seat_number);
            $stmt_update->execute();
        }

        // Update booking payment status if you're using a bookings table
        $sql_payment_update = "UPDATE bookings 
                               SET payment_status = 'Paid', 
                                   payment_method = ?, 
                                   booking_date = NOW() 
                               WHERE bus_id = ? AND user_id = ?";
        $stmt_payment_update = $conn->prepare($sql_payment_update);
        $stmt_payment_update->bind_param("sii", $payment_method, $bus_id, $user_id);
        $stmt_payment_update->execute();

        // Redirect to ticket view
        header('Location: view_booked.php');
        exit();
    } else {
        $error_message = "All fields are required!";
    }
}
?>

<?php include('header.php'); ?>

<h2 style="text-align: center; color: #333; font-size: 24px; margin-bottom: 20px;">Payment Page</h2>

<?php if (isset($error_message)): ?>
    <p style="color: red; text-align: center;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="payment.php?bus_id=<?php echo $bus_id; ?>&seats=<?php echo $seats; ?>&travel_date=<?php echo $travel_date; ?>" style="max-width: 500px; margin: 0 auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <label for="card_number" style="display: block; font-weight: bold; margin-bottom: 8px;">Card Number:</label>
    <input type="text" name="card_number" id="card_number" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
    
    <label for="phone_number" style="display: block; font-weight: bold; margin-bottom: 8px;">Phone Number:</label>
    <input type="text" name="phone_number" id="phone_number" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
    
    <label for="upi_id" style="display: block; font-weight: bold; margin-bottom: 8px;">UPI ID:</label>
    <input type="text" name="upi_id" id="upi_id" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
    
    <label for="payment_method" style="display: block; font-weight: bold; margin-bottom: 8px;">Payment Method:</label>
    <select name="payment_method" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
        <option value="Telebirr">Telebirr</option>
        <option value="CBE Birr">CBE Birr</option>
    </select>
    
    <p style="font-size: 18px; font-weight: bold; color: #333; text-align: center;">Total Amount: Rs. <?php echo $total_amount; ?></p>

    <button type="submit" style="width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; transition: background-color 0.3s;">
        Complete Payment
    </button>
</form>

<?php include('footer.php'); ?>

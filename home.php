<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<?php include('header.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Bus Reservation</title>
</head>
<body style="margin: 0; font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333; display: flex; justify-content: center; align-items: center; flex-direction: column; height: 100vh; text-align: center;">

    <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); width: 100%; max-width: 600px; margin: auto;">
        <h2 style="color: #444; margin-bottom: 20px;">Welcome to the Tigray Bus Ticket Booking System</h2>
        <p style="font-size: 16px; margin-bottom: 20px;">Select your preference:</p>
        <ul style="list-style-type: none; padding: 0; text-align: left; margin-bottom: 20px;">
            <li style="margin: 10px 0;"><a href="booking.php" style="text-decoration: none; color: #007bff; font-size: 16px; font-weight: bold; transition: color 0.3s ease;">1: Book a Ticket</a></li>
            <li style="margin: 10px 0;"><a href="view_booked.php" style="text-decoration: none; color: #007bff; font-size: 16px; font-weight: bold; transition: color 0.3s ease;">2: View Booked Tickets</a></li>
            <li style="margin: 10px 0;"><a href="cancel_ticket.php" style="text-decoration: none; color: #007bff; font-size: 16px; font-weight: bold; transition: color 0.3s ease;">3: Cancel Ticket</a></li>
        </ul>
    </div>

</body>
</html>

<?php include('footer.php'); ?>

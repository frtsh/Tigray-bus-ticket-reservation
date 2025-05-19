<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_id = intval($_POST['ticket_id']); // sanitize input
    $user_id = $_SESSION['user_id'];

    // Fetch the ticket details first
    $ticket_query = "SELECT * FROM tickets WHERE ticket_id = $ticket_id AND user_id = $user_id";
    $ticket_result = $conn->query($ticket_query);

    if ($ticket_result->num_rows > 0) {
        $ticket = $ticket_result->fetch_assoc();

        // Mark the seat as available again
        $conn->query("UPDATE seats SET is_booked = 0 WHERE bus_id = " . $ticket['bus_id'] . " AND seat_number = '" . $ticket['seat_number'] . "'");

        // Delete the ticket record
        $conn->query("DELETE FROM tickets WHERE ticket_id = $ticket_id");

        echo "<p style='color: green; font-size: 16px;'>Ticket successfully canceled!</p>";
    } else {
        echo "<p style='color: red; font-size: 16px;'>Ticket not found or you're not authorized to cancel this ticket.</p>";
    }
}
?>

<!-- Add this link to return to home page -->
<div style="text-align: center; margin-bottom: 20px;">
    <a href="home.php" style="display: inline-block; padding: 8px 16px; background-color:rgb(4, 49, 88); color: white; text-decoration: none; border-radius: 4px; font-size: 14px;">Back to Home</a>
</div>

<form method="POST" action="cancel_ticket.php" style="max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; background-color: #f9f9f9;">
    <h3 style="text-align: center; color: #333;">Cancel Ticket</h3>
    <label for="ticket_id" style="display: block; font-size: 16px; margin-bottom: 8px;">Enter Ticket ID to Cancel:</label>
    <input type="text" name="ticket_id" required style="width: 100%; padding: 10px; font-size: 14px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">
    <button type="submit" style="width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; font-size: 16px;">Cancel Ticket</button>
</form>
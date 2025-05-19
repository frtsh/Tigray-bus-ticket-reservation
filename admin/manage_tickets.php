<?php
include('../includes/db.php'); // Include DB connection

// Updated query with LEFT JOIN to avoid errors when bookings are missing
$query = "SELECT tickets.*, users.name 
          FROM tickets
          LEFT JOIN bookings ON tickets.booking_id = bookings.id
          LEFT JOIN users ON tickets.user_id = users.id
          ORDER BY tickets.ticket_status DESC";

$tickets_result = mysqli_query($conn, $query);

// Handle cancellation of a ticket
if (isset($_GET['cancel_ticket'])) {
    $ticket_id = $_GET['cancel_ticket'];
    
    // Example of canceling a ticket (updating ticket_status)
    $cancel_query = "UPDATE tickets SET ticket_status = 'Canceled' WHERE ticket_id = $ticket_id";
    if (mysqli_query($conn, $cancel_query)) {
        echo "Ticket canceled successfully.";
    } else {
        echo "Error canceling ticket: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tickets</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        a {
            color: red;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2>Manage Tickets</h2>

<table>
    <tr>
        <th>Ticket ID</th>
        <th>User Name</th>
        <th>Booking ID</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($ticket = mysqli_fetch_assoc($tickets_result)) : ?>
        <tr>
            <td><?php echo $ticket['ticket_id']; ?></td>
            <td><?php echo $ticket['name'] ?? 'N/A'; ?></td>
            <td><?php echo $ticket['booking_id'] ?? 'N/A'; ?></td>
            <td><?php echo $ticket['ticket_status'] ?? 'Pending'; ?></td>
            <td>
                <a href="manage_tickets.php?cancel_ticket=<?php echo $ticket['ticket_id']; ?>" onclick="return confirm('Are you sure you want to cancel this ticket?')">Cancel Ticket</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

<?php
// Close DB connection
mysqli_close($conn);
?>

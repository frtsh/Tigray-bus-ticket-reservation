<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT tickets.*, buses.bus_name, buses.departure_time, buses.arrival_time 
        FROM tickets
        JOIN buses ON tickets.bus_id = buses.id
        WHERE tickets.user_id = $user_id";
$result = $conn->query($sql);
?>

<h2 style="text-align: center; color: #333; font-size: 24px; margin-bottom: 20px;">Your Booked Tickets</h2>

<?php if ($result->num_rows > 0): ?>
    <table style="width: 100%; border-collapse: collapse; margin: 0 auto; max-width: 1000px; background-color: #fff; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        <thead>
            <tr style="background-color: #f4f4f4; text-align: left; color: #333;">
                <th style="padding: 12px; border: 1px solid #ddd;">Ticket ID</th>
                <th style="padding: 12px; border: 1px solid #ddd;">Bus Name</th>
                <th style="padding: 12px; border: 1px solid #ddd;">Seat Number</th>
                <th style="padding: 12px; border: 1px solid #ddd;">Departure Time</th>
                <th style="padding: 12px; border: 1px solid #ddd;">Arrival Time</th>
                <th style="padding: 12px; border: 1px solid #ddd;">Booking Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr style="text-align: left; color: #333;">
                    <td style="padding: 12px; border: 1px solid #ddd;"><?php echo $row['id']; ?></td>
                    <td style="padding: 12px; border: 1px solid #ddd;"><?php echo $row['bus_name']; ?></td>
                    <td style="padding: 12px; border: 1px solid #ddd;"><?php echo $row['seat_number']; ?></td>
                    <td style="padding: 12px; border: 1px solid #ddd;"><?php echo $row['departure_time']; ?></td>
                    <td style="padding: 12px; border: 1px solid #ddd;"><?php echo $row['arrival_time']; ?></td>
                    <td style="padding: 12px; border: 1px solid #ddd;"><?php echo $array['booking_date'] ?? null; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="text-align: center; font-size: 16px; color: #777; margin-top: 20px;">You haven't booked any tickets yet.</p>
<?php endif; ?>

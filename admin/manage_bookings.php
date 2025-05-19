<?php
// Database connection
include('../includes/db.php');

// Basic admin authentication (simple version)
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Handle booking cancellation
if (isset($_GET['cancel_booking'])) {
    $booking_id = intval($_GET['cancel_booking']);
    
    $update_sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Booking #$booking_id has been cancelled";
    } else {
        $_SESSION['error'] = "Failed to cancel booking: " . $conn->error;
    }
    
    header("Location: manage_bookings.php");
    exit();
}

// Fetch all bookings with user and bus info
$query = "SELECT 
            b.id as booking_id,
            b.total_amount,
            b.status,
            b.created_at,
            u.id as user_id,
            u.name as user_name,
            u.email as user_email,
            bus.id as bus_id,
            bus.bus_name,
            bus.source,
            bus.destination,
            bus.departure_time,
            b.seats
          FROM bookings b
          JOIN users u ON b.user_id = u.id
          JOIN buses bus ON b.bus_id = bus.id
          ORDER BY b.created_at DESC";

$bookings_result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Bookings - Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { border-bottom: 1px solid #eee; padding-bottom: 10px; color: #333; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; }
        .alert-danger { background-color: #f8d7da; color: #721c24; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        table th { background-color: #f2f2f2; }
        .status-confirmed { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-cancelled { color: red; font-weight: bold; }
        .action-links a { margin-right: 10px; color: #007bff; text-decoration: none; }
        .search-filter { margin-bottom: 20px; }
    </style>
    <script>
        function searchTable() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let table = document.getElementById('bookingsTable');
            let trs = table.getElementsByTagName('tr');
            
            for (let i = 1; i < trs.length; i++) {
                let tr = trs[i];
                let text = tr.textContent.toLowerCase();
                tr.style.display = text.indexOf(filter) > -1 ? '' : 'none';
            }
        }
    </script>
</head>
<body>
<div class="container">
    <h2>Manage Bookings</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <div class="search-filter">
        <input type="text" id="searchInput" placeholder="Search bookings..." onkeyup="searchTable()">
    </div>
    
    <table id="bookingsTable">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>User</th>
                <th>Bus Details</th>
                <th>Seats</th>
                <th>Amount</th>
                <th>Booking Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($booking = mysqli_fetch_assoc($bookings_result)): ?>
            <tr>
                <td>#<?= $booking['booking_id'] ?></td>
                <td>
                    <strong><?= htmlspecialchars($booking['user_name']) ?></strong><br>
                    <small><?= htmlspecialchars($booking['user_email']) ?></small>
                </td>
                <td>
                    <strong><?= htmlspecialchars($booking['bus_name']) ?></strong><br>
                    <?= htmlspecialchars($booking['source']) ?> to <?= htmlspecialchars($booking['destination']) ?><br>
                    <small>Dep: <?= date('h:i A', strtotime($booking['departure_time'])) ?></small>
                </td>
                <td><?= htmlspecialchars($booking['seats'] ?? '') ?></td>
                <td>Rs. <?= number_format($booking['total_amount'], 2) ?></td>
                <td><?= date('M j, Y h:i A', strtotime($booking['created_at'])) ?></td>
                <td class="status-<?= htmlspecialchars($booking['status']) ?>">
                    <?= ucfirst($booking['status']) ?>
                </td>
                <td class="action-links">
                    <?php if ($booking['status'] !== 'cancelled'): ?>
                        <a href="?cancel_booking=<?= $booking['booking_id'] ?>" onclick="return confirm('Cancel booking #<?= $booking['booking_id'] ?>?');">Cancel</a>
                    <?php else: ?>
                        <span style="color: grey;">Cancelled</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

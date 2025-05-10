<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['bus_id'])) {
    echo "Invalid bus selection.";
    exit();
}

$user_id = $_SESSION['user_id'];
$bus_id = $_GET['bus_id'];

// Check if already booked
$check = $conn->prepare("SELECT id FROM tickets WHERE user_id = ? AND bus_id = ?");
$check->bind_param("ii", $user_id, $bus_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "You have already booked this bus.";
} else {
    // Insert ticket
    $stmt = $conn->prepare("INSERT INTO tickets (user_id, bus_id, booking_time) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $user_id, $bus_id);
    
    if ($stmt->execute()) {
        header("Location: view_booked.php");
        exit();
    } else {
        echo "Booking failed. Please try again.";
    }
}
?>

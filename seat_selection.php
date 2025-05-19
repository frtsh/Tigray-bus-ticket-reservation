<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$bus_id = intval($_GET['bus_id'] ?? null);
$travel_date = $_GET['travel_date'] ?? date('Y-m-d');

if (!$bus_id) {
    $_SESSION['error'] = "Invalid bus selection.";
    header('Location: booking.php');
    exit();
}

// Fetch bus details
$bus_stmt = $conn->prepare("SELECT bus_name, source, destination FROM buses WHERE id = ?");
$bus_stmt->bind_param("i", $bus_id);
$bus_stmt->execute();
$bus = $bus_stmt->get_result()->fetch_assoc();

// Fetch available seats (not booked for this date+trip)
$available_seats = [];
$sql = "SELECT s.seat_number 
        FROM seats s
        WHERE s.bus_id = ? 
        AND NOT EXISTS (
            SELECT 1 FROM tickets t 
            WHERE t.bus_id = s.bus_id 
            AND t.seat_number = s.seat_number 
            AND t.travel_date = ?
            AND t.ticket_status != 'cancelled'
        )";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $bus_id, $travel_date);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $available_seats[] = $row['seat_number'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_seats = $_POST['seats'] ?? [];
    $travel_date = $_POST['travel_date'] ?? date('Y-m-d');

    if (empty($selected_seats)) {
        $_SESSION['error'] = "Please select at least one seat.";
        header("Location: seat_selection.php?bus_id=$bus_id&travel_date=$travel_date");
        exit();
    }

    $conn->begin_transaction();
    $booked_seats = [];
    $all_success = true;

    try {
        foreach ($selected_seats as $seat_number) {
            // 1. Check if seat is still available for this date+trip
            $check_sql = "SELECT 1 FROM seats s
                         WHERE s.bus_id = ? 
                         AND s.seat_number = ?
                         AND NOT EXISTS (
                             SELECT 1 FROM tickets t 
                             WHERE t.bus_id = s.bus_id 
                             AND t.seat_number = s.seat_number 
                             AND t.travel_date = ?
                             AND t.ticket_status != 'cancelled'
                         ) FOR UPDATE";
            
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("iis", $bus_id, $seat_number, $travel_date);
            $check_stmt->execute();
            
            if (!$check_stmt->get_result()->fetch_assoc()) {
                throw new Exception("Seat $seat_number is no longer available for $travel_date.");
            }

            // 2. Create ticket
            $insert_sql = "INSERT INTO tickets 
                          (user_id, bus_id, seat_number, travel_date, booking_time, ticket_status) 
                          VALUES (?, ?, ?, ?, NOW(), 'confirmed')";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iiss", $user_id, $bus_id, $seat_number, $travel_date);
            
            if (!$insert_stmt->execute()) {
                throw new Exception("Failed to book seat $seat_number.");
            }

            $booked_seats[] = $seat_number;
        }

        $conn->commit();
        $_SESSION['success'] = "Successfully booked seats: " . implode(', ', $booked_seats);
        header("Location: payment.php?bus_id=$bus_id&seats=".implode(',', $booked_seats)."&travel_date=$travel_date");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
        header("Location: seat_selection.php?bus_id=$bus_id&travel_date=$travel_date");
        exit();
    }
}
?>

<?php include('header.php'); ?>

<div class="container">
    <h2 class="text-center">Select Seats for <?= htmlspecialchars($bus['bus_name']) ?></h2>
    <h4 class="text-center"><?= htmlspecialchars($bus['source']) ?> to <?= htmlspecialchars($bus['destination']) ?></h4>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" class="booking-form">
        <div class="form-group">
            <label for="travel_date"><strong>Travel Date:</strong></label>
            <input type="date" name="travel_date" id="travel_date" 
                   value="<?= htmlspecialchars($travel_date) ?>" required 
                   class="form-control" min="<?= date('Y-m-d') ?>">
        </div>

        <h3>Available Seats on <?= date('F j, Y', strtotime($travel_date)) ?>:</h3>
        
        <?php if (!empty($available_seats)): ?>
            <div class="seat-grid">
                <?php foreach ($available_seats as $seat): ?>
                    <div class="seat-option">
                        <input type="checkbox" name="seats[]" value="<?= htmlspecialchars($seat) ?>" 
                               id="seat_<?= htmlspecialchars($seat) ?>" />
                        <label for="seat_<?= htmlspecialchars($seat) ?>">
                            Seat <?= htmlspecialchars($seat) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <button type="submit" id="book-btn" class="btn btn-primary btn-lg">
                    Confirm Booking
                </button>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                No seats available for this date. Please try another date.
            </div>
        <?php endif; ?>
    </form>
</div>

<style>
.container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
}

.booking-form {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.seat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.seat-option {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-align: center;
}

.seat-option input[type="checkbox"] {
    display: none;
}

.seat-option input[type="checkbox"] + label {
    display: block;
    padding: 8px;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.3s;
}

.seat-option input[type="checkbox"]:checked + label {
    background-color: #28a745;
    color: white;
}

.alert {
    margin: 20px 0;
    padding: 15px;
    border-radius: 4px;
}

.form-control {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
</style>

<script>
// Auto-submit when date changes
document.getElementById('travel_date').addEventListener('change', function() {
    this.form.submit();
});

// Prevent form resubmission
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

// Disable button on submit to prevent double booking
document.querySelector('form').addEventListener('submit', function() {
    document.getElementById('book-btn').disabled = true;
    document.getElementById('book-btn').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
});
</script>

<?php include('footer.php'); ?>
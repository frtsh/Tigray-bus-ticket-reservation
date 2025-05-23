<?php
session_start();
require_once('db.php'); // Changed from include to require_once for critical files

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Validate and sanitize inputs
$bus_id = filter_var($_GET['bus_id'] ?? null, FILTER_VALIDATE_INT);
$seats = isset($_GET['seats']) ? preg_replace('/[^0-9,]/', '', $_GET['seats']) : '';
$travel_date = isset($_GET['travel_date']) ? date('Y-m-d', strtotime($_GET['travel_date'])) : date('Y-m-d');
$seat_array = !empty($seats) ? explode(',', $seats) : [];

if (!$bus_id || empty($seat_array)) {
    $_SESSION['error'] = "Invalid booking parameters";
    header('Location: booking.php');
    exit();
}

// Get bus details from database
try {
    $bus_sql = "SELECT price, bus_name, source, destination FROM buses WHERE id = ?";
    $bus_stmt = $conn->prepare($bus_sql);
    $bus_stmt->bind_param("i", $bus_id);
    $bus_stmt->execute();
    $bus = $bus_stmt->get_result()->fetch_assoc();

    if (!$bus) {
        throw new Exception("Bus not found");
    }

    $seat_price = (float)$bus['price'];
    $total_amount = $seat_price * count($seat_array);
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: booking.php');
    exit();
}

// Process payment
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method'] ?? '';
    $user_id = $_SESSION['user_id'];
    
    // Validate payment form inputs
    $card_number = isset($_POST['card_number']) ? preg_replace('/[^0-9]/', '', $_POST['card_number']) : '';
    $phone_number = isset($_POST['phone_number']) ? preg_replace('/[^0-9]/', '', $_POST['phone_number']) : '';
    $fin_id = isset($_POST['fin_id']) ? htmlspecialchars(trim($_POST['fin_id'])) : '';
    
    if (empty($payment_method) || empty($card_number) || empty($phone_number) || empty($fin_id)) {
        $error_message = "Please fill all payment details";
    } elseif (!in_array($payment_method, ['Telebirr', 'CBE Birr', 'Bank Transfer'])) {
        $error_message = "Invalid payment method selected";
    } else {
        try {
            // Set transaction isolation level BEFORE beginning transaction
            $conn->query("SET TRANSACTION ISOLATION LEVEL SERIALIZABLE");
            $conn->begin_transaction();
            
            // 1. Verify seat availability with proper locking
            $placeholders = implode(',', array_fill(0, count($seat_array), '?'));
            $check_sql = "SELECT s.seat_number 
                         FROM seats s
                         LEFT JOIN tickets t ON s.bus_id = t.bus_id 
                            AND s.seat_number = t.seat_number 
                            AND t.travel_date = ?
                            AND t.ticket_status != 'cancelled'
                         WHERE s.bus_id = ? 
                         AND s.seat_number IN ($placeholders)
                         AND (t.ticket_id IS NULL OR s.is_booked = 0)
                         FOR UPDATE";

            $check_stmt = $conn->prepare($check_sql);
            $types = str_repeat('s', count($seat_array));
            $check_stmt->bind_param("si".$types, $travel_date, $bus_id, ...$seat_array);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            $available_seats = [];
            while ($row = $result->fetch_assoc()) {
                $available_seats[] = $row['seat_number'];
            }

            // Check if any requested seats are unavailable
            $unavailable_seats = array_diff($seat_array, $available_seats);
            if (!empty($unavailable_seats)) {
                throw new Exception("Seats " . implode(', ', $unavailable_seats) . " are no longer available. Please choose different seats.");
            }

            // 2. Create booking record
            $booking_ref = 'TGR-' . strtoupper(uniqid());
            $booking_sql = "INSERT INTO bookings 
                          (user_id, bus_id, travel_date, total_amount, booking_reference)
                          VALUES (?, ?, ?, ?, ?)";
            $booking_stmt = $conn->prepare($booking_sql);
            $booking_stmt->bind_param("iisds", $user_id, $bus_id, $travel_date, $total_amount, $booking_ref);
            $booking_stmt->execute();
            $booking_id = $conn->insert_id;

            // 3. Insert tickets
            $ticket_sql = "INSERT INTO tickets 
                          (booking_id, user_id, bus_id, seat_number, travel_date, ticket_status)
                          VALUES (?, ?, ?, ?, ?, 'confirmed')";
            $ticket_stmt = $conn->prepare($ticket_sql);
            $ticket_stmt->bind_param("iiiss", $booking_id, $user_id, $bus_id, $seat_number, $travel_date);
            
            $update_sql = "UPDATE seats SET is_booked = 1 
                          WHERE bus_id = ? AND seat_number = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("is", $bus_id, $seat_number);
            
            foreach ($seat_array as $seat_number) {
                $ticket_stmt->execute();
                $update_stmt->execute();
            }

            // 4. Record payment
            $payment_sql = "INSERT INTO payments 
                           (user_id, booking_id, amount, payment_method, status, payment_details)
                           VALUES (?, ?, ?, ?, 'completed', ?)";
            $payment_details = json_encode([
                'card_number' => substr($card_number, -4), // Store only last 4 digits
                'phone_number' => $phone_number,
                'fin_id' => $fin_id
            ]);
            
            $payment_stmt = $conn->prepare($payment_sql);
            $payment_stmt->bind_param("iidss", $user_id, $booking_id, $total_amount, $payment_method, $payment_details);
            $payment_stmt->execute();

            $conn->commit();
            
            $_SESSION['booking_ref'] = $booking_ref;
            header('Location: booking_confirmation.php');
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error_message = $e->getMessage();
        }
    }
}

include('header.php');
?>

<div class="container">
    <h2 class="text-center">Complete Your Payment</h2>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error_message) ?>
            <div class="mt-2">
                <a href="seat_selection.php?bus_id=<?= htmlspecialchars($bus_id) ?>&travel_date=<?= htmlspecialchars($travel_date) ?>" 
                   class="btn btn-warning">
                    ← Back to Seat Selection
                </a>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="payment-container">
        <div class="booking-summary">
            <h3>Booking Summary</h3>
            <div class="summary-item">
                <span>Bus:</span>
                <strong><?= htmlspecialchars($bus['bus_name']) ?> (<?= htmlspecialchars($bus_id) ?>)</strong>
            </div>
            <div class="summary-item">
                <span>Route:</span>
                <strong><?= htmlspecialchars($bus['source']) ?> to <?= htmlspecialchars($bus['destination']) ?></strong>
            </div>
            <div class="summary-item">
                <span>Travel Date:</span>
                <strong><?= htmlspecialchars(date('F j, Y', strtotime($travel_date))) ?></strong>
            </div>
            <div class="summary-item">
                <span>Selected Seats:</span>
                <strong><?= htmlspecialchars(implode(', ', $seat_array)) ?></strong>
            </div>
            <div class="summary-item">
                <span>Price per Seat:</span>
                <strong>Rs. <?= htmlspecialchars(number_format($seat_price, 2)) ?></strong>
            </div>
            <div class="summary-item total">
                <span>Total Amount:</span>
                <strong>Rs. <?= htmlspecialchars(number_format($total_amount, 2)) ?></strong>
            </div>
        </div>
        
        <div class="payment-form">
            <form method="POST" id="paymentForm">
                <h3>Payment Details</h3>
                
                <div class="form-group">
                    <label>Card Number</label>
                    <input type="text" name="card_number" pattern="[0-9]{13,19}" title="Enter a valid card number" required>
                </div>
                
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone_number" pattern="[0-9]{10,15}" title="Enter a valid phone number" required>
                </div>
                
                <div class="form-group">
                    <label>FIN ID</label>
                    <input type="text" name="fin_id" required>
                </div>
                
                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method" required>
                        <option value="">Select Method</option>
                        <option value="Telebirr" <?= isset($_POST['payment_method']) && $_POST['payment_method'] === 'Telebirr' ? 'selected' : '' ?>>Telebirr</option>
                        <option value="CBE Birr" <?= isset($_POST['payment_method']) && $_POST['payment_method'] === 'CBE Birr' ? 'selected' : '' ?>>CBE Birr</option>
                        <option value="Bank Transfer" <?= isset($_POST['payment_method']) && $_POST['payment_method'] === 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-pay" onclick="window.location.href='view_booked.php'">Complete Payment</button>

                    <a href="seat_selection.php?bus_id=<?= htmlspecialchars($bus_id) ?>&travel_date=<?= htmlspecialchars($travel_date) ?>" 
                       class="btn-back">← Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.container {
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    color:black;
}

.payment-container {
    display: flex;
    gap: 30px;
    margin-top: 20px;
}

@media (max-width: 768px) {
    .payment-container {
        flex-direction: column;
    }
}

.booking-summary, .payment-form {
    flex: 1;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.booking-summary h3, .payment-form h3 {
    margin-top: 0;
    color: #333;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.summary-item.total {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
    font-size: 1.1em;
    font-weight: bold;
    color: #28a745;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn-pay {
    flex: 1;
    background: #28a745;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

.btn-pay:hover {
    background-color: #218838;
}

.btn-back {
    display: inline-block;
    padding: 12px;
    background: #f8f9fa;
    color: #333;
    text-align: center;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color 0.3s;
}

.btn-back:hover {
    background-color: #e2e6ea;
     color:black;
    
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-danger {
    background-color: #fff;
    color: #721c24;
    border: 1px solid #f5c6cb;
    
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('paymentForm');
    form.addEventListener('submit', function(e) {
        const cardNumber = form.card_number.value.replace(/\D/g, '');
        const phoneNumber = form.phone_number.value.replace(/\D/g, '');
        
        if (cardNumber.length < 13 || cardNumber.length > 19) {
            alert('Please enter a valid card number (13-19 digits)');
            e.preventDefault();
            return false;
        }
        
        if (phoneNumber.length < 10 || phoneNumber.length > 15) {
            alert('Please enter a valid phone number (10-15 digits)');
            e.preventDefault();
            return false;
        }
        
        return true;
    });
    
    // Auto-format card number input
    const cardNumberInput = form.card_number;
    cardNumberInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });
    
    // Auto-format phone number input
    const phoneNumberInput = form.phone_number;
    phoneNumberInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });
});

if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
</script>

<?php include('footer.php'); ?>
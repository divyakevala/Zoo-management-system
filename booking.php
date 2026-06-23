<?php
include("db.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

include("header.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['book'])) {
    $user_name = isset($_SESSION['user']) ? $_SESSION['user'] : null;
    $visitor_name = trim($_POST['visitor_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $visit_date = $_POST['visit_date'];
    $visit_time = $_POST['visit_time'];
    $adults = max(0, intval($_POST['adults']));
    $children = max(0, intval($_POST['children']));
    $guests = $adults + $children;
    $total_cost = ($adults * 50) + ($children * 25);
    $message = trim($_POST['message']);

    $stmt = $conn->prepare("INSERT INTO bookings (user_name, visitor_name, email, phone, visit_date, visit_time, guests, adults, children, total_cost, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssiiids", $user_name, $visitor_name, $email, $phone, $visit_date, $visit_time, $guests, $adults, $children, $total_cost, $message);

    if ($stmt->execute()) {
        $success = "Your visit booking request has been submitted successfully. Total ticket cost: ₹{$total_cost}. We will contact you soon.";

        $admin_subject = "New zoo visit booking request from {$visitor_name}";
        $admin_message = "<p>A new booking request has been submitted.</p>" .
                         "<p><strong>Visitor:</strong> {$visitor_name}<br>" .
                         "<strong>Email:</strong> {$email}<br>" .
                         "<strong>Phone:</strong> {$phone}<br>" .
                         "<strong>Visit Date:</strong> {$visit_date}<br>" .
                         "<strong>Visit Time:</strong> {$visit_time}<br>" .
                         "<strong>Guests:</strong> {$guests}<br>" .
                         "<strong>Message:</strong> " . nl2br(htmlspecialchars($message)) . "</p>";
        send_email_notification($zoo_email, $admin_subject, $admin_message);

        $user_subject = "Zoo Visit Booking Received";
        $user_message = "<p>Thank you for booking your visit with us. Your request has been received and is currently pending approval.</p>" .
                        "<p><strong>Visitor:</strong> {$visitor_name}<br>" .
                        "<strong>Visit Date:</strong> {$visit_date}<br>" .
                        "<strong>Visit Time:</strong> {$visit_time}<br>" .
                        "<strong>Guests:</strong> {$guests}</p>" .
                        "<p>We will notify you when your request is approved.</p>";
        send_email_notification($email, $user_subject, $user_message);
    } else {
        $error = "Booking failed: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Book a Zoo Visit</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5 mb-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow-sm">
          <div class="card-body">
            <h2 class="card-title text-center mb-4">Visitor Booking</h2>
            <?php if (isset($success)): ?>
              <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
              <div class="mb-3">
                <label class="form-label">Your Name</label>
                <input type="text" name="visitor_name" class="form-control" required placeholder="Enter visitor name">
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required placeholder="Enter your email">
              </div>
              <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="tel" name="phone" class="form-control" required placeholder="Enter phone number">
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Visit Date</label>
                  <input type="date" name="visit_date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Visit Time</label>
                  <input type="time" name="visit_time" class="form-control" required>
                </div>
              </div>
                <div class="mb-3">
                <p class="small text-secondary">Ticket prices: Adult ₹50, Child ₹25</p>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Adults</label>
                  <input id="adults" type="number" name="adults" class="form-control" min="0" value="1" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Children</label>
                  <input id="children" type="number" name="children" class="form-control" min="0" value="0" required>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Estimated Total Cost</label>
                <input id="total_cost" type="text" class="form-control" value="₹75.00" readonly>
                <div class="form-text">Adult ₹50, Child ₹25. Total updates automatically.</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Additional Message</label>
                <textarea name="message" class="form-control" rows="4" placeholder="Optional message"></textarea>
              </div>
              <button type="submit" name="book" class="btn btn-primary w-100">Submit Booking Request</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    const adultInput = document.getElementById('adults');
    const childInput = document.getElementById('children');
    const totalCostInput = document.getElementById('total_cost');

    function updateTotalCost() {
      const adults = Math.max(0, Number(adultInput.value));
      const children = Math.max(0, Number(childInput.value));
      const total = adults * 50 + children * 25;
      totalCostInput.value = `₹${total.toFixed(2)}`;
    }

    adultInput.addEventListener('input', updateTotalCost);
    childInput.addEventListener('input', updateTotalCost);
    updateTotalCost();
  </script>
  <?php include("footer.php"); ?>
</body>
</html>

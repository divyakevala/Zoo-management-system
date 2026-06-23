<?php
session_start();
include("db.php");
include("auth.php");
include("header.php");

$user_name = $_SESSION['user'];

$stmt = $conn->prepare("SELECT * FROM bookings WHERE user_name = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $user_name);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
  <title>My Bookings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h2 class="mb-4">My Booking Requests</h2>
  <table class="table table-striped table-bordered">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Visitor</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Visit Date</th>
        <th>Time</th>
        <th>Adults</th>
        <th>Children</th>
        <th>Total Cost</th>
        <th>Status</th>
        <th>Requested</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['visitor_name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['visit_date']) ?></td>
            <td><?= htmlspecialchars($row['visit_time']) ?></td>
            <td><?= htmlspecialchars($row['adults']) ?></td>
            <td><?= htmlspecialchars($row['children']) ?></td>
            <td>₹<?= htmlspecialchars(number_format($row['total_cost'], 2)) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="9" class="text-center">You have not booked any visits yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <?php include("footer.php"); ?>
</body>
</html>

<?php
session_start();
include("db.php");
include("auth.php");
include("header.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='alert alert-danger'>Access denied. Only admins can manage bookings.</div>";
    exit;
}

$flash = null;
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $status = null;
    $stmt = $conn->prepare("SELECT visitor_name, email, visit_date, visit_time, adults, children, total_cost FROM bookings WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $booking_result = $stmt->get_result();
    $booking = $booking_result->fetch_assoc();
    $stmt->close();

    if ($booking) {
        if ($_GET['action'] === 'approve') {
            $status = 'Approved';
        } elseif ($_GET['action'] === 'cancel') {
            $status = 'Cancelled';
        }

        if ($status) {
            $stmt = $conn->prepare("UPDATE bookings SET status=? WHERE id=?");
            $stmt->bind_param("si", $status, $id);
            $stmt->execute();
            $stmt->close();

            $subject = "Your zoo visit booking has been {$status}";
            $message = "<p>Your booking request has been <strong>{$status}</strong>.</p>" .
                       "<p><strong>Visitor:</strong> " . htmlspecialchars($booking['visitor_name']) . "<br>" .
                       "<strong>Visit Date:</strong> " . htmlspecialchars($booking['visit_date']) . "<br>" .
                       "<strong>Visit Time:</strong> " . htmlspecialchars($booking['visit_time']) . "<br>" .
                       "<strong>Adults:</strong> " . htmlspecialchars($booking['adults']) . "<br>" .
                       "<strong>Children:</strong> " . htmlspecialchars($booking['children']) . "<br>" .
                       "<strong>Total Cost:</strong> ₹" . number_format($booking['total_cost'], 2) . "</p>";
            send_email_notification($booking['email'], $subject, $message);

            $flash = "Booking {$status} and email notification sent to {$booking['email']}";
        }
    }
    $_SESSION['flash'] = $flash;
    header("Location: manage_bookings.php");
    exit;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$like = "%$search%";

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM bookings WHERE visitor_name LIKE ? OR email LIKE ?");
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$total_pages = ceil($total / $limit);

$stmt = $conn->prepare("SELECT * FROM bookings WHERE visitor_name LIKE ? OR email LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ssii", $like, $like, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Bookings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h2 class="mb-4">Manage Visitor Bookings</h2>
  <?php if (isset($_SESSION['flash']) && $_SESSION['flash']): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_SESSION['flash']) ?></div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <form method="GET" class="mb-4 d-flex">
    <input type="text" name="search" class="form-control me-2" placeholder="Search visitor or email" value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn btn-secondary">Search</button>
  </form>

  <table class="table table-bordered table-striped">
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
        <th>Submitted</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['visitor_name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['visit_date']) ?></td>
            <td><?= htmlspecialchars($row['visit_time']) ?></td>
            <td><?= htmlspecialchars($row['adults']) ?></td>
            <td><?= htmlspecialchars($row['children']) ?></td>
            <td>₹<?= htmlspecialchars(number_format($row['total_cost'], 2)) ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= $row['created_at'] ?></td>
            <td>
              <?php if ($row['status'] !== 'Approved'): ?>
                <a class="btn btn-success btn-sm mb-1" href="?action=approve&id=<?= $row['id'] ?>">Approve</a>
              <?php endif; ?>
              <?php if ($row['status'] !== 'Cancelled'): ?>
                <a class="btn btn-danger btn-sm" href="?action=cancel&id=<?= $row['id'] ?>">Cancel</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="10" class="text-center">No bookings found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <nav>
    <ul class="pagination">
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>

  <?php include("footer.php"); ?>
</body>
</html>

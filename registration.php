<?php
include("db.php");
include("header.php");
// Handle Registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // secure hash
    $role = $_POST['role'];

    // Use prepared statement for safety
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        // Show success message and redirect after 2 seconds
        echo "<div class='alert alert-success text-center'>
                Registration successful! Redirecting to login...
              </div>";
        echo "<script>
                setTimeout(function(){
                    window.location.href = 'login.php';
                }, 2000);
              </script>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>User Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Custom styles */
    .form-container {
      max-width: 400px;   /* narrower width */
      margin: 0 auto;     /* center horizontally */
    }
    .form-heading {
      color: #111010;     /* heading color */
      text-align: center; /* center the heading */
    }
  </style>
</head>
<body class="container mt-5">

  <h2 class="mb-4 form-heading">Register New User</h2>

  <div class="form-container">
    <form method="POST" class="border p-4 rounded bg-light">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" id="username" name="username" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <select id="role" name="role" class="form-select">
          <option value="staff" selected>Staff</option>
          <option value="admin">Admin</option>
          <option value="user">User</option>
        </select>
      </div>

      <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
    </form>
  </div>

  <?php include("footer.php"); ?>
</body>
</html>
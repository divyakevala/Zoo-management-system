<?php
session_start();
include("db.php");
include("auth.php"); // ensures user is logged in
include("header.php");

// Restrict access to admins only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='alert alert-danger'>Access denied. Only admins can manage users.</div>";
    exit;
}

// Handle Add User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    if (!$stmt) {
        $_SESSION['flash'] = "Prepare failed: " . $conn->error;
    } else {
        $stmt->bind_param("sss", $username, $password, $role);
                try {
            if ($stmt->execute()) {
                $_SESSION['flash'] = "User registered successfully!";
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $_SESSION['flash'] = "Name already exists! Please try a unique name.";
            } else {
                $_SESSION['flash'] = "Error adding user: " . $e->getMessage();
            }
        }

        $stmt->close();
    }
    header("Location: manage_users.php");
    exit;
}


// Handle Edit (load user data)
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $edit_result = $stmt->get_result();
        $edit_user = $edit_result->fetch_assoc();
        $stmt->close();
    } else {
        $_SESSION['flash'] = "Error loading user: " . $conn->error;
    }
}

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $username = $_POST['username'];
    $role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, password=?, role=? WHERE id=?");
        if ($stmt) {
            $stmt->bind_param("sssi", $username, $password, $role, $id);
        }
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
        if ($stmt) {
            $stmt->bind_param("ssi", $username, $role, $id);
        }
    }

    if ($stmt && $stmt->execute()) {
        $_SESSION['flash'] = "User updated successfully!";
    } else {
        $_SESSION['flash'] = "Update failed: " . ($stmt ? $stmt->error : $conn->error);
    }
    if ($stmt) $stmt->close();
    header("Location: manage_users.php");
    exit;
}

// Handle Delete (prevent deleting last admin)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("SELECT role FROM users WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $role_result = $stmt->get_result();
        $user_role = $role_result->fetch_assoc()['role'];
        $stmt->close();

        $admin_count = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='admin'")
                            ->fetch_assoc()['total'];

        if ($user_role === 'admin' && $admin_count <= 1) {
            $_SESSION['flash'] = "Cannot delete the last admin!";
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
            if ($stmt) {
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $_SESSION['flash'] = "User deleted successfully!";
                } else {
                    $_SESSION['flash'] = "Delete failed: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $_SESSION['flash'] = "Prepare failed: " . $conn->error;
            }
        }
    } else {
        $_SESSION['flash'] = "Error finding user: " . $conn->error;
    }
    header("Location: manage_users.php");
    exit;
}

// --- Search + Pagination ---
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit  = 5; // users per page
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE username LIKE ?");
$total_users = 0;
if ($stmt) {
    $like = "%$search%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $total_result = $stmt->get_result()->fetch_assoc();
    $total_users = $total_result['total'];
    $stmt->close();
}
$total_pages = ceil($total_users / $limit);

$stmt = $conn->prepare("SELECT id, username, role FROM users WHERE username LIKE ? ORDER BY id ASC LIMIT ? OFFSET ?");
if ($stmt) {
    $stmt->bind_param("sii", $like, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $result = false;
    $_SESSION['flash'] = "Error loading users: " . $conn->error;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <style>
  .form-label {
    color: maroon;
  }
</style>
</head>
<body class="container mt-5">
  <h2 class="mb-4"style="color:maroon">Manage Users (Admin Only)</h2>

  <!-- Flash Message -->
  <?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center">
      <?= htmlspecialchars($_SESSION['flash']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- Add/Edit Form -->
  <form method="POST" class="border p-4 rounded bg-light mb-4">
    <?php if (isset($edit_user)): ?>
      <h4 class="mb-3">Edit User</h4>
      <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
    <?php else: ?>
      <h4 class="mb-3">Add New User</h4>
    <?php endif; ?>

    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input type="text" id="username" name="username" class="form-control"
             value="<?= isset($edit_user) ? htmlspecialchars($edit_user['username']) : '' ?>" required>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password <?= isset($edit_user) ? '(leave blank to keep current)' : '' ?></label>
      <input type="password" id="password" name="password" class="form-control">
    </div>

    <div class="mb-3">
      <label for="role" class="form-label">Role</label>
      <select id="role" name="role" class="form-select">
        <option value="staff" <?= isset($edit_user) && $edit_user['role']=='staff' ? 'selected' : '' ?>>Staff</option>
        <option value="admin" <?= isset($edit_user) && $edit_user['role']=='admin' ? 'selected' : '' ?>>Admin</option>
        <option value="user" <?= isset($edit_user) && $edit_user['role']=='user' ? 'selected' : '' ?>>User</option>
      </select>
    </div>

    <?php if (isset($edit_user)): ?>
      <button type="submit" name="update" class="btn btn-primary w-100">Update User</button>
    <?php else: ?>
      <button type="submit" name="register" class="btn btn-success w-100">Add User</button>
    <?php endif; ?>
  </form>

  <!-- Search -->
  <form method="GET" class="mb-3 d-flex">
    <input type="text" name="search" class="form-control me-2" placeholder="Search by username" value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn btn-secondary">Search</button>
  </form>

  <!-- User List -->
  <h4 class="mb-3">Existing Users</h4>
  <table class="table table-striped table-bordered">
    <thead class="table-dark">
      <tr><th>ID</th><th>Username</th><th>Role</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php if ($result): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= $row['role'] ?></td>
            <td>
              <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
              <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                 onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4" class="text-center">No users found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <nav>
    <ul class="pagination">
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>

  <?php include("footer.php"); ?>
</body>
</html>

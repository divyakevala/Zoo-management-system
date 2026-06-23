<?php
include("db.php");
include("auth.php"); // login check only
include("header.php");

// Add new food record (admins only)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add']) && $_SESSION['role'] === 'admin') {
    $sql = "INSERT INTO food (animal_id, diet, feeding_time)
            VALUES ('{$_POST['animal_id']}','{$_POST['diet']}','{$_POST['feeding_time']}')";
    $conn->query($sql);
}

// Delete food record (admins only)
if (isset($_GET['delete']) && $_SESSION['role'] === 'admin') {
    $conn->query("DELETE FROM food WHERE id={$_GET['delete']}");
}

// Fetch food records with animal name
$result = $conn->query("SELECT food.*, animals.name AS animal_name 
                        FROM food 
                        JOIN animals ON food.animal_id = animals.id");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Food Records</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <style>
  .form-label {
    color: maroon;
  }
</style>
</head>
<body class="container mt-5">
  <h2 class="mb-4"style="color:maroon">Food Records</h2>

  <!-- Add Food Record Form (admins only) -->
  <?php if ($_SESSION['role'] === 'admin'): ?>
  <form method="POST" class="mb-4">
  <div class="mb-3">
    <label for="animal_id" class="form-label">Select Animal</label>
    <select id="animal_id" name="animal_id" class="form-select">
      <?php
      $animals = $conn->query("SELECT * FROM animals");
      while($a = $animals->fetch_assoc()) {
        echo "<option value='{$a['id']}'>{$a['name']} ({$a['species']})</option>";
      }
      ?>
    </select>
  </div>

  <div class="mb-3">
    <label for="diet" class="form-label">Diet</label>
    <input type="text" id="diet" name="diet" class="form-control" placeholder="Enter diet">
  </div>

  <div class="mb-3">
    <label for="feeding_time" class="form-label">Feeding Time</label>
    <input type="time" id="feeding_time" name="feeding_time" class="form-control">
  </div>

  <button type="submit" name="add" class="btn btn-success w-100">Add Food Record</button>
</form>
  <?php endif; ?>

  <!-- Food Records Table -->
  <table class="table table-striped table-bordered">
    <thead class="table-dark">
      <tr>
        <th>ID</th><th>Animal</th><th>Diet</th><th>Feeding Time</th><th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['animal_name'] ?></td>
        <td><?= $row['diet'] ?></td>
        <td><?= $row['feeding_time'] ?></td>
        <td>
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
          <?php else: ?>
            <span class="text-muted">View only</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <?php include("footer.php"); ?>
</body>
</html>
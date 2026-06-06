<?php
include("db.php");
include("auth.php"); // login check only
include("header.php");

// Add new health record (admins only)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add']) && $_SESSION['role'] === 'admin') {
    $sql = "INSERT INTO health_records (animal_id, diagnosis, treatment, vet_name, record_date)
            VALUES ('{$_POST['animal_id']}','{$_POST['diagnosis']}','{$_POST['treatment']}',
                    '{$_POST['vet_name']}','{$_POST['record_date']}')";
    $conn->query($sql);
}

// Delete health record (admins only)
if (isset($_GET['delete']) && $_SESSION['role'] === 'admin') {
    $conn->query("DELETE FROM health_records WHERE id={$_GET['delete']}");
}

// Fetch health records with animal name
$result = $conn->query("SELECT health_records.*, animals.name AS animal_name 
                        FROM health_records 
                        JOIN animals ON health_records.animal_id = animals.id");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Health Records</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <style>
  .form-label {
    color: maroon;
  }
</style>
</head>
<body class="container mt-5">
  <h2 class="mb-4"style="color:maroon">Health Records</h2>

  <!-- Add Health Record Form (admins only) -->
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
    <label for="diagnosis" class="form-label">Diagnosis</label>
    <input type="text" id="diagnosis" name="diagnosis" class="form-control" placeholder="Enter diagnosis">
  </div>

  <div class="mb-3">
    <label for="treatment" class="form-label">Treatment</label>
    <input type="text" id="treatment" name="treatment" class="form-control" placeholder="Enter treatment">
  </div>

  <div class="mb-3">
    <label for="vet_name" class="form-label">Vet Name</label>
    <input type="text" id="vet_name" name="vet_name" class="form-control" placeholder="Enter vet name">
  </div>

  <div class="mb-3">
    <label for="record_date" class="form-label">Record Date</label>
    <input type="date" id="record_date" name="record_date" class="form-control">
  </div>

  <button type="submit" name="add" class="btn btn-success w-100">Add Health Record</button>
</form>
  <?php endif; ?>

  <!-- Health Records Table -->
  <table class="table table-striped table-bordered">
    <thead class="table-dark">
      <tr>
        <th>ID</th><th>Animal</th><th>Diagnosis</th><th>Treatment</th><th>Vet</th><th>Date</th><th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['animal_name'] ?></td>
        <td><?= $row['diagnosis'] ?></td>
        <td><?= $row['treatment'] ?></td>
        <td><?= $row['vet_name'] ?></td>
        <td><?= $row['record_date'] ?></td>
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
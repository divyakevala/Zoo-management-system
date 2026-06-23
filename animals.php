<?php
include("db.php");
include("auth.php"); // login check only
include("header.php");

// Handle Add Animal (admins only)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add']) && $_SESSION['role'] === 'admin') {
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $imageName = basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "images/" . $imageName);
    }

    $sql = "INSERT INTO animals (name, species, age, gender, arrival_date, habitat, image)
            VALUES ('{$_POST['name']}','{$_POST['species']}','{$_POST['age']}',
                    '{$_POST['gender']}','{$_POST['arrival']}','{$_POST['habitat']}','$imageName')";
    $conn->query($sql);
}

// Handle Edit (admins only) - load animal data
if (isset($_GET['edit']) && $_SESSION['role'] === 'admin') {
    $id = intval($_GET['edit']);
    $edit_result = $conn->query("SELECT * FROM animals WHERE id=$id");
    $edit_animal = $edit_result->fetch_assoc();
}

// Handle Update (admins only)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update']) && $_SESSION['role'] === 'admin') {
    $id = intval($_POST['id']);
    $imageName = $_POST['current_image']; // keep old image if none uploaded

    if (!empty($_FILES['image']['name'])) {
        $imageName = basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "images/" . $imageName);
    }

    $sql = "UPDATE animals SET 
            name='{$_POST['name']}',
            species='{$_POST['species']}',
            age='{$_POST['age']}',
            gender='{$_POST['gender']}',
            arrival_date='{$_POST['arrival']}',
            habitat='{$_POST['habitat']}',
            image='$imageName'
            WHERE id=$id";
    $conn->query($sql);
    header("Location: animals.php"); // redirect back
    exit;
}

// Handle Delete (admins only)
if (isset($_GET['delete']) && $_SESSION['role'] === 'admin') {
    $conn->query("DELETE FROM animals WHERE id={$_GET['delete']}");
}

// Fetch all animals
$result = $conn->query("SELECT * FROM animals");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Animal Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  .form-label {
    color: maroon;
  }
</style>
</head>
<body class="container mt-5">
  <h2 class="mb-4" style="color:maroon">Animal Management</h2>

  <!-- Unified Add/Edit Form (admins only) -->
  <?php if ($_SESSION['role'] === 'admin'): ?>
  <form method="POST" enctype="multipart/form-data" class="mb-4">
    <?php if (isset($edit_animal)): ?>
      <input type="hidden" name="id" value="<?= $edit_animal['id'] ?>">
      <input type="hidden" name="current_image" value="<?= $edit_animal['image'] ?>">
    <?php endif; ?>

    <div class="mb-3">
      <label for="name" class="form-label">Animal Name</label>
      <input type="text" id="name" name="name" class="form-control"
             value="<?= isset($edit_animal) ? $edit_animal['name'] : '' ?>" placeholder="Enter name">
    </div>

    <div class="mb-3">
      <label for="species" class="form-label">Species</label>
      <input type="text" id="species" name="species" class="form-control"
             value="<?= isset($edit_animal) ? $edit_animal['species'] : '' ?>" placeholder="Enter species">
    </div>

    <div class="mb-3">
      <label for="age" class="form-label">Age</label>
      <input type="number" id="age" name="age" class="form-control"
             value="<?= isset($edit_animal) ? $edit_animal['age'] : '' ?>" placeholder="Enter age">
    </div>

    <div class="mb-3">
      <label for="gender" class="form-label">Gender</label>
      <input type="text" id="gender" name="gender" class="form-control"
             value="<?= isset($edit_animal) ? $edit_animal['gender'] : '' ?>" placeholder="Enter gender">
    </div>

    <div class="mb-3">
      <label for="arrival" class="form-label">Arrival Date</label>
      <input type="date" id="arrival" name="arrival" class="form-control"
             value="<?= isset($edit_animal) ? $edit_animal['arrival_date'] : '' ?>">
    </div>

    <div class="mb-3">
      <label for="habitat" class="form-label">Habitat</label>
      <input type="text" id="habitat" name="habitat" class="form-control"
             value="<?= isset($edit_animal) ? $edit_animal['habitat'] : '' ?>" placeholder="Enter habitat">
    </div>

    <div class="mb-3">
      <label for="image" class="form-label">Animal Image</label>
      <input type="file" id="image" name="image" class="form-control">
      <?php if (isset($edit_animal) && !empty($edit_animal['image'])): ?>
        <div class="mt-2">
          <img src="images/<?= $edit_animal['image'] ?>" alt="<?= $edit_animal['name'] ?>" style="max-height:100px;">
        </div>
      <?php endif; ?>
    </div>

    <?php if (isset($edit_animal)): ?>
      <button type="submit" name="update" class="btn btn-primary w-100">Update Animal</button>
    <?php else: ?>
      <button type="submit" name="add" class="btn btn-success w-100">Add Animal</button>
    <?php endif; ?>
  </form>
  <?php endif; ?>

  <!-- Animal Table -->
  <table class="table table-striped table-bordered">
    <thead class="table-dark">
      <tr>
        <th>ID</th><th>Name</th><th>Species</th><th>Age</th>
        <th>Gender</th><th>Arrival</th><th>Habitat</th><th>Image</th><th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['name'] ?></td>
        <td><?= $row['species'] ?></td>
        <td><?= $row['age'] ?></td>
        <td><?= $row['gender'] ?></td>
        <td><?= $row['arrival_date'] ?></td>
        <td><?= $row['habitat'] ?></td>
        <td>
          <?php if (!empty($row['image'])): ?>
            <img src="images/<?= $row['image'] ?>" alt="<?= $row['name'] ?>" style="max-height:80px;">
          <?php else: ?>
            <span class="text-muted">No image</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
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
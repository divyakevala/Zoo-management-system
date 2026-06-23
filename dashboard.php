<?php
include("db.php");
include("auth.php");
include("header.php");

// Quick stats
$animal_count = $conn->query("SELECT COUNT(*) AS total FROM animals")->fetch_assoc()['total'];
$food_count = $conn->query("SELECT COUNT(*) AS total FROM food")->fetch_assoc()['total'];
$health_count = $conn->query("SELECT COUNT(*) AS total FROM health_records")->fetch_assoc()['total'];
?>
<h2 class="mb-4 text-center">Admin Dashboard</h2>

<div class="row mb-5 justify-content-center">
  <div class="col-md-5">
    <div class="card text-bg-primary text-center">
      <div class="card-body">
        <h5 class="card-title">Animals</h5>
        <p class="card-text">Total: <?= $animal_count ?></p>
        <a href="animals.php" class="btn btn-light">Manage Animals</a>
      </div>
    </div>
  </div>
</div>

<div class="row mb-5 justify-content-center">
  <div class="col-md-5">
    <div class="card text-bg-success text-center">
      <div class="card-body">
        <h5 class="card-title">Food Records</h5>
        <p class="card-text">Total: <?= $food_count ?></p>
        <a href="food.php" class="btn btn-light">Manage Food</a>
      </div>
    </div>
  </div>
</div>

<div class="row mb-5 justify-content-center">
  <div class="col-md-5">
    <div class="card text-bg-danger text-center">
      <div class="card-body">
        <h5 class="card-title">Health Records</h5>
        <p class="card-text">Total: <?= $health_count ?></p>
        <a href="health.php" class="btn btn-light">Manage Health</a>
      </div>
    </div>
  </div>
</div>

<div class="row mb-5 justify-content-center">
  <div class="col-md-5">
    <div class="card text-bg-info text-center">
      <div class="card-body">
        <h5 class="card-title">Reports</h5>
        <p class="card-text">View zoo reports and charts</p>
        <a href="reports.php" class="btn btn-light">View Reports</a>
      </div>
    </div>
  </div>
</div>

<div class="row mb-5 justify-content-center">
  <div class="col-md-5">
    <div class="card text-bg-warning text-center">
      <div class="card-body">
        <h5 class="card-title">Bookings</h5>
        <p class="card-text">Total bookings: <?= $conn->query("SELECT COUNT(*) AS total FROM bookings")->fetch_assoc()['total'] ?></p>
        <a href="manage_bookings.php" class="btn btn-light">Manage Bookings</a>
      </div>
    </div>
  </div>
</div>

<?php include("footer.php"); ?>
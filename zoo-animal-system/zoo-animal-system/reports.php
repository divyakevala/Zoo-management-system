<?php
include("db.php");
include("auth.php");
include("header.php");

// Query: count animals by species
$species_report = $conn->query("SELECT species, COUNT(*) AS total FROM animals GROUP BY species");
$species = [];
$species_totals = [];
if ($species_report && $species_report->num_rows > 0) {
    while($row = $species_report->fetch_assoc()) {
        $species[] = $row['species'];
        $species_totals[] = $row['total'];
    }
}

// Query: count animals by habitat
$habitat_report = $conn->query("SELECT habitat, COUNT(*) AS total FROM animals GROUP BY habitat");
$habitats = [];
$habitat_totals = [];
if ($habitat_report && $habitat_report->num_rows > 0) {
    while($row = $habitat_report->fetch_assoc()) {
        $habitats[] = $row['habitat'];
        $habitat_totals[] = $row['total'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Zoo Reports</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <style>
  .form-label {
    color: maroon;
  }
</style>
</head>
<body class="container mt-5">
  <h2 class="mb-4"style="color:maroon">Zoo Reports</h2>

  <!-- Species Report -->
  <div class="row mb-5">
    <div class="col-md-6">
      <h4 style="color:maroon">Animals by Species</h4>
      <?php if (!empty($species)): ?>
        <table class="table table-striped table-bordered">
          <thead class="table-dark">
            <tr><th>Species</th><th>Total</th><th>Images</th></tr>
          </thead>
          <tbody>
            <?php foreach($species as $i => $sp): ?>
              <tr>
                <td><?= $sp ?></td>
                <td><?= $species_totals[$i] ?></td>
                <td>
                  <?php
                    $img_result = $conn->query("SELECT name, image FROM animals WHERE species='$sp'");
                    while($img_row = $img_result->fetch_assoc()):
                      if (!empty($img_row['image'])): ?>
                        <img src="images/<?= $img_row['image'] ?>" alt="<?= $img_row['name'] ?>" style="max-height:60px; margin:2px;">
                      <?php else: ?>
                        <span class="text-muted">No image</span>
                      <?php endif;
                    endwhile;
                  ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p style="color:maroon">No data available</p>
      <?php endif; ?>
    </div>
    <div class="col-md-6">
      <?php if (!empty($species)): ?>
        <canvas id="speciesChart" width="200" height="200"></canvas>
      <?php endif; ?>
    </div>
  </div>

  <!-- Habitat Report -->
  <div class="row mb-5">
    <div class="col-md-6">
      <h4 style="color:maroon">Animals by Habitat</h4>
      <?php if (!empty($habitats)): ?>
        <table class="table table-striped table-bordered">
          <thead class="table-dark">
            <tr><th>Habitat</th><th>Total</th><th>Images</th></tr>
          </thead>
          <tbody>
            <?php foreach($habitats as $i => $hb): ?>
              <tr>
                <td><?= $hb ?></td>
                <td><?= $habitat_totals[$i] ?></td>
                <td>
                  <?php
                    $img_result = $conn->query("SELECT name, image FROM animals WHERE habitat='$hb'");
                    while($img_row = $img_result->fetch_assoc()):
                      if (!empty($img_row['image'])): ?>
                        <img src="images/<?= $img_row['image'] ?>" alt="<?= $img_row['name'] ?>" style="max-height:60px; margin:2px;">
                      <?php else: ?>
                        <span class="text-muted">No image</span>
                      <?php endif;
                    endwhile;
                  ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p style="color:maroon">No data available</p>
      <?php endif; ?>
    </div>
    <div class="col-md-6">
      <?php if (!empty($habitats)): ?>
        <canvas id="habitatChart" width="200" height="200"></canvas>
      <?php endif; ?>
    </div>
  </div>

  <?php include("footer.php"); ?>

  <!-- Chart.js Scripts -->
  <script>
    <?php if (!empty($species)): ?>
    const speciesCtx = document.getElementById('speciesChart').getContext('2d');
    new Chart(speciesCtx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($species) ?>,
        datasets: [{
          label: 'Animals by Species',
          data: <?= json_encode($species_totals) ?>,
          backgroundColor: 'rgba(75, 192, 192, 0.6)',
          borderColor: 'rgba(0,0,0,0.8)',
          borderWidth: 1
        }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });
    <?php endif; ?>

    <?php if (!empty($habitats)): ?>
    const habitatCtx = document.getElementById('habitatChart').getContext('2d');
    new Chart(habitatCtx, {
      type: 'pie',
      data: {
        labels: <?= json_encode($habitats) ?>,
        datasets: [{
          label: 'Animals by Habitat',
          data: <?= json_encode($habitat_totals) ?>,
          backgroundColor: [
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)',
            'rgba(153, 102, 255, 0.6)'
          ],
          borderColor: 'rgba(0,0,0,0.8)',
          borderWidth: 1
        }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });
    <?php endif; ?>
  </script>
</body>
</html>
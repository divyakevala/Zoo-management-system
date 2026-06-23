<!-- index.php -->
 <?php
include("db.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Zoo Animal System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:url('images/zooimage.jpg') no-repeat center center fixed; background-size:cover;">
    <h1 style="text-align:center; color:black">THE ZOO WELFARE MANAGEMENT SYSTEM</h1>

  <div class="container text-center mt-5">
    <!-- Zoo Logo -->
     <h5 style="color:black">ZOO LOGO</h5>
    <img src="images/zoologo.jpg" alt="Zoo Logo" class="mb-4" style="max-width:150px;">

    <!-- Attractive Line -->
    <p class="text-light"><h2 style="color:black">“Discover the Wild, Protect the Future”</h2></p>
    <p class="text-light"><h3 style="color:black">Welcome to our Zoo Animal System — where care meets conservation.<h3></p>

    <!-- Buttons -->
    <div class="mt-4">
      <h4 style="color:black">Login to your account:</h4>
      <a href="login.php" class="btn btn-primary btn-lg me-3">Login</a><br><br>
      <h4 style="color:black">Register for new user:</h4>
      <a href="registration.php" class="btn btn-success btn-lg mb-3">New User Registration</a><br>
      <h4 style="color:black">Plan your visit:</h4>
      <a href="booking.php" class="btn btn-warning btn-lg">Book a Zoo Visit</a>
    </div>
  </div>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About Us - Zoo Animal System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f8f9fa;">
    <div class="container mt-5">
    <!-- About Us Content -->
    <div class="card shadow">
      <div class="card-body">
        <h2 class="card-title text-primary text-center mb-3">About Us</h2>
        <p class="card-text"style="text-align:center;">
          Welcome to our Zoo Animal System! Our mission is to bring the wonders of wildlife closer to you while ensuring the best care and conservation practices for our animals.  
        </p>
        <p class="card-text"style="text-align:center;">
          This system helps manage animal records, habitats, food schedules, and health information in a structured and user-friendly way. By combining technology with conservation, we aim to create a safe, educational, and enjoyable experience for visitors and staff alike.
        </p>
        <p class="card-text"style="text-align:center;">
          Whether you are an administrator managing zoo operations or a visitor exploring the animal kingdom, our platform is designed to make your journey seamless and engaging.
        </p>
      </div>
    </div>
    <div class="card shadow">
      <div class="card-body">
        <h3 class="card-title text-success text-center mb-3">Contact Us</h3>
        <p class="card-text text-center">
          📍 <strong>Address:</strong> Green Valley Zoo, Main Road, Puttur, Karnataka  
          📞 <strong>Phone:</strong> +91 89932 76385  
          📧 <strong>Email:</strong> info@zooanimalsystem.com
        </p>
        <div class="text-center mt-3">
          <a href="mailto:info@zooanimalsystem.com" class="btn btn-outline-primary me-2">Send Email</a>
          <a href="tel:+918993276385" class="btn btn-outline-success">Call Us</a>
        </div>
      </div>
    </div>
</div>
</body>
</html>

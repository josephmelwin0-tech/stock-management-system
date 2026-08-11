<?php require('session.php');?>
<?php if(logged_in()){ ?>
          <script type="text/javascript">
            window.location = "index.php";
          </script>
    <?php
    } ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Stock Control Management System - Login</title>

  <link rel="icon" href="https://www.freeiconspng.com/uploads/sales-icon-7.png">

  <!-- Custom fonts for this template-->
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  
  <!-- Custom styles for this template-->
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">
  <link href="../css/custom-theme.css" rel="stylesheet">

  <!-- Early Dark Mode Detection to Prevent Flash -->
  <script>
    if (localStorage.getItem('dark-mode') === 'true') {
      document.documentElement.classList.add('dark-mode');
      document.addEventListener('DOMContentLoaded', () => {
        document.body.classList.add('dark-mode');
      });
    }
  </script>
</head>

<body>

  <div class="login-container">
    <div class="login-card animate-fade-in-up">
      <!-- Branding Side (Left) -->
      <div class="login-branding">
        <i class="fas fa-cubes fa-4x mb-4 text-warning" style="color: #C9762C !important;"></i>
        <h2>Stock Control Management System</h2>
        <p>A comprehensive inventory, transaction processing, and stock control management system built for speed and reliability.</p>
      </div>
      
      <!-- Form Side (Right) -->
      <div class="login-form-container">
        <h3>Account Login</h3>
        <form role="form" action="processlogin.php" method="post">
          <div class="login-input-group">
            <label class="login-label" for="userInput">Username</label>
            <input class="login-input" id="userInput" placeholder="Enter your username" name="user" type="text" autofocus required autocomplete="username">
          </div>
          <div class="login-input-group">
            <label class="login-label" for="passwordInput">Password</label>
            <input class="login-input" id="passwordInput" placeholder="Enter your password" name="password" type="password" required autocomplete="current-password">
          </div>
          
          <div class="form-group mb-4">
            <div class="custom-control custom-checkbox small">
              <input type="checkbox" class="custom-control-input" id="customCheck">
              <label class="custom-control-label" for="customCheck" style="color: var(--text-muted); cursor: pointer;">Remember Me</label>
            </div>
          </div>
          
          <button class="login-btn" type="submit" name="btnlogin">Sign In</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="../js/sb-admin-2.min.js"></script>

</body>

</html>

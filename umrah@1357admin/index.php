<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config/config.php';
require 'functions/authentication.php';

$db = new dbClass();
$auth = new Authentication();

// user login check
if (isset($_REQUEST['btn_login']) && $_REQUEST['btn_login'] == 'Login') {
  $email = $db->addStr($_POST['email']);
  $pass = $db->addStr($_POST['password']);

  $result = $auth->adminLogin($email, $pass);

  if ($result == true) {

    header('Location: home.php');
    exit();
  } else {
    $_SESSION['admin_login_error'] = "Invalid username or password.";
    header('Location: index.php');
    exit();
  }
}

$login_error_message = isset($_SESSION['admin_login_error']) ? $_SESSION['admin_login_error'] : '';
unset($_SESSION['admin_login_error']);

?>




<!DOCTYPE html>
<html lang="en">
                                             
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex,nofollow" />
  <title>Admin | Umrah Planner</title>
  <!-- Favicon icon -->
  <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png" />
  <!-- Bootstrap Core CSS -->
  <link href="node_modules/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <!-- page css -->
  <link href="css/pages/login-register-lock.css" rel="stylesheet" />
  <!-- Custom CSS -->
  <link href="css/style.css" rel="stylesheet" />

  <!-- You can change the theme colors from here -->
  <link href="css/colors/default.css" id="theme" rel="stylesheet" />

</head>

<body class="card-no-border">

  <!-- Main wrapper - style you can find in pages.scss -->

  <section id="wrapper">
    <div class="login-register" style="background-image: url(images/login-bg.jpg);">
      <div class="login-box card">
        <div class="card-body">

          <div class="text-center mb-3">
            <a href="index.php"><img src="images/logo.png" width="140px" height="140px"  alt=""></a>
          </div>

          <?php if (!empty($login_error_message)): ?>
            <div class="alert alert-danger solid alert-dismissible fade show">
              <svg viewBox="0 0 24 24" width="24 " height="24" stroke="currentColor"
                stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                class="me-2">
                <polygon
                  points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2">
                </polygon>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
              </svg>
              <strong>Error! </strong> <?php echo $login_error_message; ?>.
              <button type="button" class="btn-close" data-bs-dismiss="alert"
                aria-label="btn-close">
              </button>
            </div>
          <?php endif; ?>

          <form method="post" class="form-valide-with-icon needs-validation" novalidate>
            <div class="mb-3 vertical-radius">
              <label class="text-label   form-label required"
                for="validationCustomUsername">Username</label>
              <div class="input-group">
                <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                <input type="text" name="email" class="form-control  br-style"
                  id="validationCustomUsername" placeholder="Enter a username.."
                  required>
                <div class="invalid-feedback">
                  Please Enter a Username.
                </div>
              </div>
            </div>
            <div class="mb-3 vertical-radius">
              <label class="text-label form-label required"
                for="dz-password">Password</label>
              <div class="input-group transparent-append">
                <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                <input type="password" name="password" class="form-control"
                  id="dz-password" placeholder="Choose a safe one.." required>
                <div class="invalid-feedback">
                  Please Enter Your Password.
                </div>
              </div>
            </div>
            <input type="submit" class="btn me-2 btn-primary" name="btn_login"
              value="Login">
          </form>

        </div>
      </div>
    </div>
  </section>


  <!-- End Wrapper -->

  

  <!-- All Jquery -->

  <script src="node_modules/jquery/jquery.min.js"></script>
  <!-- Bootstrap tether Core JavaScript -->
  <script src="node_modules/bootstrap/js/popper.min.js"></script>
  <script src="node_modules/bootstrap/js/bootstrap.min.js"></script>
  <!--Custom JavaScript -->

  <script>
    (function() {
      'use strict'

      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.querySelectorAll('.needs-validation')

      // Loop over them and prevent submission
      Array.prototype.slice.call(forms)
        .forEach(function(form) {
          form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
              event.preventDefault()
              event.stopPropagation()
            }

            form.classList.add('was-validated')
          }, false)
        })
    })()
  </script>


  <script type="text/javascript">
    $(function() {
      $(".preloader").fadeOut();
    });
    $(function() {
      $('[data-toggle="tooltip"]').tooltip();
    });
    // ==============================================================
    // Login and Recover Password
    // ==============================================================
    $("#to-recover").on("click", function() {
      $("#loginform").slideUp();
      $("#recoverform").fadeIn();
    });
  </script>
</body>

</html>
<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config/config.php';
require 'functions/authentication.php';

$auth = new Authentication();
$auth->checkSession();

  

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin | Umrah Planner</title>
  <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png" />
  <!-- Bootstrap Core CSS -->
  <link href="node_modules/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="node_modules/perfect-scrollbar/dist/css/perfect-scrollbar.min.css" rel="stylesheet" />
  <!-- This page CSS -->
  <!-- chartist CSS -->
  <link href="node_modules/morrisjs/morris.css" rel="stylesheet" />
  <!--c3 CSS -->
  <link href="node_modules/c3-master/c3.min.css" rel="stylesheet" />
  <!--Toaster Popup message CSS -->
  <link href="node_modules/toast-master/css/jquery.toast.css" rel="stylesheet" />
  <link rel="stylesheet" href="node_modules/dropify/dist/css/dropify.min.css" />
  <!-- Custom CSS -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- Dashboard 1 Page CSS -->
  <link href="css/pages/dashboard1.css" rel="stylesheet" />
  <!-- You can change the theme colors from here -->
  <link href="css/colors/default.css" id="theme" rel="stylesheet" />

</head>

<body class="fix-header fix-sidebar card-no-border">

  <!-- Main wrapper - style you can find in pages.scss -->

  <div id="main-wrapper">

            
        <!-- Topbar header - style you can find in pages.scss -->
        <?php require('include/header.php'); ?>
        <!-- End Topbar header -->

        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <?php require('include/sidebar.php'); ?>
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->


    <!-- Page wrapper  -->

    <div class="page-wrapper">

      <!-- Container fluid  -->

      <div class="container-fluid">

        <!-- Bread crumb and right sidebar toggle -->

        <div class="row page-titles">
          <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="javascript:void(0)">Home</a>
              </li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div>
          <div class="">
            <button class="
                  right-side-toggle
                  waves-effect waves-light
                  btn-inverse btn btn-circle btn-sm
                  pull-right
                  m-l-10
                ">
              <i class="ti-settings text-white"></i>
            </button>
          </div>
        </div>

        <!-- End Bread crumb and right sidebar toggle -->


        <div class="row mb-5">
          <!-- Column -->
          <div class="col-lg-3 col-md-6">
            <div class="card">
              <div class="card-body">
                <a href="#0">
                  <div class="d-flex flex-row">
                    <div class="round align-self-center round-info">
                      <i class="ti-help"></i>
                    </div>
                    <div class="m-l-10 align-self-center">
                      <h3 class="m-b-0">Queries</h3>
                      <h5 class="m-b-0">15</h5>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
          <!-- Column -->
          <!-- Column -->
          <div class="col-lg-3 col-md-6">
            <div class="card">
              <div class="card-body">
                <a href="#0">
                  <div class="d-flex flex-row">
                    <div class="round align-self-center round-info">
                      <i class="ti-user"></i>
                    </div>
                    <div class="m-l-10 align-self-center">
                      <h3 class="m-b-0">Users</h3>
                      <h5 class="m-b-0">950</h5>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
          <!-- Column -->
          <!-- Column -->
          <div class="col-lg-3 col-md-6">
            <div class="card">
              <div class="card-body">
                <a href="#0">
                  <div class="d-flex flex-row">
                    <div class="round align-self-center round-info">
                      <i class="ti-user"></i>
                    </div>
                    <div class="m-l-10 align-self-center">
                      <h3 class="m-b-0">Vendors</h3>
                      <h5 class="m-b-0">55</h5>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
          <!-- Column -->
          <!-- Column -->
          <div class="col-lg-3 col-md-6">
            <div class="card">
              <div class="card-body">
                <a href="#0">
                  <div class="d-flex flex-row">
                    <div class="round align-self-center round-info">
                      <i class="ti-user"></i>
                    </div>
                    <div class="m-l-10 align-self-center">
                      <h3 class="m-b-0">38</h3>
                      <h5 class="m-b-0">Agents</h5>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
          <!-- Column -->
        </div>


        <div class="row mt-3">
          <div class="col-lg-12">
          <!-- footer -->
          <?php require('include/footer.php'); ?>
                        <!-- End footer -->

          </div>

        </div>
      </div>
      <!-- End Container fluid  -->
    </div>
    <!-- End Wrapper -->


    <!-- All Jquery -->

    <script src="node_modules/jquery/jquery.min.js"></script>
    <!-- Bootstrap popper Core JavaScript -->
    <script src="node_modules/bootstrap/js/popper.min.js"></script>
    <script src="node_modules/bootstrap/js/bootstrap.min.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="node_modules/ps/perfect-scrollbar.jquery.min.js"></script>
    <!--Wave Effects -->
    <script src="js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="js/custom.min.js"></script>

    <!-- This page plugins -->

    <!--morris JavaScript -->
    <script src="node_modules/raphael/raphael.min.js"></script>
    <script src="node_modules/morrisjs/morris.min.js"></script>
    <!--c3 JavaScript -->
    <script src="node_modules/d3/d3.min.js"></script>
    <script src="node_modules/c3-master/c3.min.js"></script>
    <!-- Popup message jquery -->
    <script src="node_modules/toast-master/js/jquery.toast.js"></script>
    <!-- Chart JS -->
    <script src="js/dashboard1.js"></script>

    <!-- Style switcher -->

    <script src="node_modules/styleswitcher/jQuery.style.switcher.js"></script>

    <script src="node_modules/dropify/dist/js/dropify.min.js"></script>
    <script>
      $(function () {
        // Basic
        $(".dropify").dropify();

        // Translated
        $(".dropify-fr").dropify({
          messages: {
            default: "Glissez-déposez un fichier ici ou cliquez",
            replace: "Glissez-déposez un fichier ou cliquez pour remplacer",
            remove: "Supprimer",
            error: "Désolé, le fichier trop volumineux",
          },
        });

        // Used events
        var drEvent = $("#input-file-events").dropify();

        drEvent.on("dropify.beforeClear", function (event, element) {
          return confirm(
            'Do you really want to delete "' + element.file.name + '" ?'
          );
        });

        drEvent.on("dropify.afterClear", function (event, element) {
          alert("File deleted");
        });

        drEvent.on("dropify.errors", function (event, element) {
          console.log("Has Errors");
        });

        var drDestroy = $("#input-file-to-destroy").dropify();
        drDestroy = drDestroy.data("dropify");
        $("#toggleDropify").on("click", function (e) {
          e.preventDefault();
          if (drDestroy.isDropified()) {
            drDestroy.destroy();
          } else {
            drDestroy.init();
          }
        });
      });
    </script>
</body>

</html>
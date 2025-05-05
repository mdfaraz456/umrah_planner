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
        <link rel="icon" type="image/png" sizes="16x16"
            href="images/favicon.png" />
        <!-- Bootstrap Core CSS -->
        <link href="node_modules/bootstrap/css/bootstrap.min.css"
            rel="stylesheet" />
        <link
            href="node_modules/perfect-scrollbar/dist/css/perfect-scrollbar.min.css"
            rel="stylesheet" />
        <!-- This page CSS -->
        <!-- chartist CSS -->
        <link href="node_modules/morrisjs/morris.css" rel="stylesheet" />
        <!--c3 CSS -->
        <link href="node_modules/c3-master/c3.min.css" rel="stylesheet" />
        <!--Toaster Popup message CSS -->
        <link href="node_modules/toast-master/css/jquery.toast.css"
            rel="stylesheet" />
        <link rel="stylesheet"
            href="node_modules/dropify/dist/css/dropify.min.css" />
        <!-- Custom CSS -->
        <link href="css/style.css" rel="stylesheet" />
        <!-- Dashboard 1 Page CSS -->
        <link href="css/pages/dashboard1.css" rel="stylesheet" />
        <!-- You can change the theme colors from here -->
        <link href="css/colors/default.css" id="theme" rel="stylesheet" />
    </head>

    <style>
    .footer {
        bottom: 0;
        color: #67757c;
        left: 13px;
        padding: 17px 15px;
        position: absolute;
        right: 0;
        border-top: 1px solid rgba(120, 130, 140, 0.13);
        background: #ffffff;
    }
</style>

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
                            <h3 class="text-themecolor">Edit User </h3>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0)">Home</a>
                                </li>
                                <li class="breadcrumb-item active">Edit User </li>
                            </ol>
                        </div>
                        <div class>
                            <button
                                class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10">
                                <i class="ti-settings text-white"></i>
                            </button>
                        </div>
                    </div>

                    <!-- End Bread crumb and right sidebar toggle -->

                    <div class="row mb-5">
                        <!-- Column -->
                        <div class="col-lg-12 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Add User </h4>
                                    <form class="form pt-3">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Full Name (as on passport)</label>
                                                    <div
                                                        class="input-group mb-3">
                                                        <div
                                                            class="input-group-prepend"></div>
                                                        <input type="text"
                                                            class="form-control"
                                                            placeholder="Name"
                                                            aria-label="Name"
                                                            aria-describedby="basic-addon11" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label
                                                        for="exampleInputEmail1">Phone number</label>
                                                    <div
                                                        class="input-group mb-3">
                                                        <div
                                                            class="input-group-prepend"></div>
                                                        <input type="text"
                                                            class="form-control"
                                                            placeholder="Phone number"
                                                            aria-label="Phone number"
                                                            aria-describedby="basic-addon22" />
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>
 
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Email address</label>
                                                    <div class="input-group mb-3">
                                                        <div
                                                            class="input-group-prepend"></div>
                                                        <input type="text"
                                                            class="form-control"
                                                            placeholder="Email address"
                                                            aria-label="Email address"
                                                            aria-describedby="basic-addon33" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Date of Birth</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend"></div>
                                                        <input type="date" 
                                                               class="form-control" 
                                                               placeholder="Date of Birth" 
                                                               aria-label="Date of Birth" 
                                                               aria-describedby="basic-addon4" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
  
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Gender</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend"></div>
                                                        <select class="form-control" aria-label="Gender" aria-describedby="basic-addon4">
                                                            <option value="">Select Gender</option>
                                                            <option value="male">Male</option>
                                                            <option value="female">Female</option>
                                                            <option value="other">Other</option>
                                                        </select>
                                                    </div>
                                                </div>
    
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Nationality</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend"></div>
                                                        <select class="form-control" aria-label="Nationality" aria-describedby="basic-addon4">
                                                            <option value="">Select Nationality</option>
                                                            <option value="india">Indian</option>
                                                            <option value="us">American</option>
                                                            <option value="uk">British</option>
                                                            <option value="canada">Canadian</option>
                                                            <option value="australia">Australian</option>
                                                            <!-- Add more nationalities as needed -->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
  
                                        <div class="row"> 
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Marital Status</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend"></div>
                                                        <select class="form-control" aria-label="Marital Status" aria-describedby="basic-addon4">
                                                            <option value="">Select Marital Status</option>
                                                            <option value="single">Single</option>
                                                            <option value="married">Married</option>
                                                            <option value="divorced">Divorced</option>
                                                            <option value="widowed">Widowed</option>
                                                            <!-- You can add more options as needed -->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Passport Photo (First Page)</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend"></div>
                                                        <input type="file" 
                                                               class="form-control" 
                                                               placeholder="Choose Passport Photo" 
                                                               aria-label="Passport Photo" 
                                                               aria-describedby="basic-addon4" 
                                                               accept="image/*" />
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>

                                        <div class="row"> 
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Passport Photo (Second Page)</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend"></div>
                                                        <input type="file" 
                                                               class="form-control" 
                                                               placeholder="Choose Passport Photo" 
                                                               aria-label="Passport Photo" 
                                                               aria-describedby="basic-addon4" 
                                                               accept="image/*" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Passport Size Photo</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend"></div>
                                                        <input type="file" 
                                                               class="form-control" 
                                                               placeholder="Choose Passport Photo" 
                                                               aria-label="Passport Photo" 
                                                               aria-describedby="basic-addon4" 
                                                               accept="image/*" />
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>
 
                                            <button type="submit"
                                                class="btn btn-success mr-2">
                                                Submit
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Column -->

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
                <script
                    src="node_modules/toast-master/js/jquery.toast.js"></script>
                <!-- Chart JS -->
                <script src="js/dashboard1.js"></script>

                <!-- Style switcher -->

                <script
                    src="node_modules/styleswitcher/jQuery.style.switcher.js"></script>

                <script
                    src="node_modules/dropify/dist/js/dropify.min.js"></script>
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
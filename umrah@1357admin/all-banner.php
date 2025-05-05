<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config/config.php';
require 'functions/authentication.php';
require 'functions/umrahFunction.php';

$auth = new Authentication();
$auth->checkSession();

$bannerObj = new Banner();
$db = new dbClass();

$banners = $bannerObj->getAllBanners();

// STATUS functionality 
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $status = (int) $_GET['status']; // status will be 0 or 1

    $sqlStatus = $db->execute("UPDATE banner SET status = '$status' WHERE banner_id = '$id'");

    if ($sqlStatus == true):
        $_SESSION['msg'] = 'Status has been changed ..!!';
    else:
        $_SESSION['errmsg'] = 'Sorry !! Some Error Occurred .. Try Again.';
    endif;

    header("Location: all-banner.php");
    exit;
}


// Delete banner functionality
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
    $id = $_REQUEST['id'];

    $bannerData = $bannerObj->getBannerById($id);

    $file = $bannerData['banner'];

    $filePath = '../uploads/banners/' . $file;
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    $sqlDelete = $db->execute("DELETE FROM banner WHERE banner_id = '$id'");

    if ($sqlDelete == true):
        $_SESSION['msg'] = 'Banner deleted successfully!';
    else:
        $_SESSION['errmsg'] = 'Failed to delete banner!';
    endif;

    header("Location: all-banner.php");
    exit;
}

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

    <link rel="stylesheet" type="text/css" href="node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="node_modules/datatables.net-bs4/css/responsive.dataTables.min.css">

    <script src="js/fontawesome.js"></script>

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

    .table td,
    .table th {
        padding: .75rem;
        vertical-align: unset !important;
        border-top: 1px solid #dee2e6;
        text-align: center;
    }

    .icon-dlt-edit i {
        font-size: 20px;
    }

    .icon-dlt-edit {
        display: flex;
        gap: 20px;
    }

    .dt-buttons {
        position: absolute;
        top: 40px;
        right: 20px;
    }

    .table td {
        border: none !important;
    }

    .icon-dlt-edit {
        display: flex;
        justify-content: center;
    }

    .bannerimg {
        WIDTH: 50%;
        HEIGHT: 152PX;
        object-fit: cover;
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
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <div class="row page-titles">
                    <div class="col-md-5 align-self-center">
                        <h3 class="text-themecolor">All Banners</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="javascript:void(0)">Home</a>
                            </li>
                            <li class="breadcrumb-item active">All Banners</li>
                        </ol>
                    </div>
                    <div class="col-md-7 align-self-center text-right d-none d-md-block">
                        <a href="banner.php"> <button type="button" class="btn btn-info">
                                <i class="fa fa-plus-circle"></i> Create New
                            </button></a>
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
                <!-- ============================================================== -->
                <!-- End Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-12">
                    <?php include('include/notification.php'); ?>
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">All Banners</h4>
                                <h6 class="card-subtitle">
                                </h6>
                                <div class="table-responsive m-t-40">
                                    <table id=" " class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Banner</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($banners as $index => $banner): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><img src="../uploads/banners/<?= htmlspecialchars($banner['banner']) ?>" class="bannerimg"></td>
                                                    <td class="icon-dlt-edit">
                                                        <?php if ($banner['status'] == 1): ?>
                                                            <a href="?status=0&id=<?= $banner['banner_id'] ?>" title="Hide">
                                                                <i class="fa-solid fa-eye" style="color: #e41111;"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="?status=1&id=<?= $banner['banner_id'] ?>" title="Show">
                                                                <i class="fa-solid fa-eye-slash" style="color: #d21414;"></i>
                                                            </a>
                                                        <?php endif; ?>

                                                        <a href="banner.php?id=<?= base64_encode($banner['banner_id']) ?>&action=edit" title="Edit">
                                                            <i class="fa-solid fa-pencil" style="color: #e81111;"></i>
                                                        </a>

                                                        <a href="?action=delete&id=<?= $banner['banner_id'] ?>" onClick="return confirm('Are you sure you want to delete this banner?')" title="Delete">
                                                            <i class="fa-solid fa-trash" style="color: #f50000;"></i>
                                                        </a>
                                                    </td>

                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- End PAge Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
                <div class="right-sidebar">
                    <div class="slimscrollright">
                        <div class="rpanel-title">
                            Service Panel
                            <span><i class="ti-close right-side-toggle"></i></span>
                        </div>
                        <div class="r-panel-body">
                            <ul id="themecolors" class="m-t-20">
                                <li><b>With Light sidebar</b></li>
                                <li>
                                    <a href="javascript:void(0)" data-theme="default" class="default-theme">1</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" data-theme="green" class="green-theme">2</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" data-theme="red" class="red-theme">3</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" data-theme="blue" class="blue-theme">4</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" data-theme="purple" class="purple-theme">5</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" data-theme="megna" class="megna-theme">6</a>
                                </li>
                                <li class="d-block m-t-30"><b>With Dark sidebar</b></li>
                                <li>
                                    <a href="javascript:void(0)" data-theme="default-dark" class="default-dark-theme working">7</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" data-theme="green-dark" class="green-dark-theme">8</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" data-theme="red-dark" class="red-dark-theme">9</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" data-theme="blue-dark" class="blue-dark-theme">10</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" data-theme="purple-dark" class="purple-dark-theme">11</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" data-theme="megna-dark" class="megna-dark-theme">12</a>
                                </li>
                            </ul>
                            <ul class="m-t-20 chatonline">
                                <li><b>Chat option</b></li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/1.jpg" alt="user-img" class="img-circle">
                                        <span>Varun Dhavan
                                            <small class="text-success">online</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/2.jpg" alt="user-img" class="img-circle">
                                        <span>Genelia Deshmukh
                                            <small class="text-warning">Away</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/3.jpg" alt="user-img" class="img-circle">
                                        <span>Ritesh Deshmukh
                                            <small class="text-danger">Busy</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/4.jpg" alt="user-img" class="img-circle">
                                        <span>Arijit Sinh
                                            <small class="text-muted">Offline</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/5.jpg" alt="user-img" class="img-circle">
                                        <span>Govinda Star
                                            <small class="text-success">online</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/6.jpg" alt="user-img" class="img-circle">
                                        <span>John Abraham<small class="text-success">online</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/7.jpg" alt="user-img" class="img-circle">
                                        <span>Hritik Roshan<small class="text-success">online</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../assets/images/users/8.jpg" alt="user-img" class="img-circle">
                                        <span>Pwandeep rajan
                                            <small class="text-success">online</small></span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Right sidebar -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <?php require('include/footer.php'); ?>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
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


    <script src="node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="node_modules/datatables.net-bs4/js/dataTables.responsive.min.js"></script>


    <script src="js/jszip.min.js"></script>
    <script src="js/buttons.flash.min.js"></script>
    <script src="js/buttons.html5.min.js"></script>
    <script src="js/buttons.print.min.js"></script>
    <script src="js/dataTables.buttons.min.js"></script>
    <script src="js/pdfmake.min.js"></script>
    <script src="js/vfs_fonts.js"></script>
    <script>
        $(document).ready(function() {
            $('#example23').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [
                    [2, 'asc']
                ],
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'All']
                ],

            });
        });
    </script>
</body>

</html>
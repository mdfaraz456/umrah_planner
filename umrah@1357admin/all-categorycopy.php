<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config/config.php';
require 'functions/authentication.php';
require 'functions/umrahFunction.php';
require 'functions/fileUploader.php';

$auth = new Authentication();
$auth->checkSession();

$userObj = new Category();
$headingObj = new Heading();
$db = new dbClass();

$editId = isset($_GET['id']) ? (int)base64_decode($_GET['id']) : '';
$editData = [];

if (!empty($editId)) {
  $editData = $headingObj->getContentById($editId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $submissionType = $db->addStr($_POST['submissionType'] ?? 'Submit');
  $id = !empty($_POST['headingId']) ? (int)$_POST['headingId'] : '';
  $heading = $db->addStr($_POST['heading'] ?? '');
  $paragraph = $db->addStr($_POST['paragraph'] ?? '');
  $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
  $brandType = $db->addStr($_POST['brandType'] ?? '');

  $result = $headingObj->saveContentData($heading, $paragraph, $status, $brandType, $submissionType, $id);

  if ($result !== false) {
    $_SESSION['msg'] = ($submissionType === 'Submit') ? 'Heading added successfully.' : 'Heading updated successfully.';
    header("Location: all-category.php");
    exit;
  } else {
    $_SESSION['errmsg'] = 'Failed to save heading data.';
  }
}

// STATUS functionality for headings
if (isset($_GET['status']) && isset($_GET['sId'])) {
  $id = (int)$_GET['sId'];
  $status = (int)$_GET['status'];

  $sqlStatus = $db->execute("UPDATE all_heading SET status = '$status' WHERE id = '$id'");

  if ($sqlStatus) {
    $_SESSION['msg'] = 'Status has been changed successfully.';
  } else {
    $_SESSION['errmsg'] = 'Sorry, some error occurred. Try again.';
  }

  header("Location: all-category.php");
  exit;
}

$headings = $headingObj->getAllContent('category');

// STATUS functionality for categories
if (isset($_GET['status']) && isset($_REQUEST['cId'])) {
  $id = (int)$_REQUEST['cId'];
  $status = (int)$_REQUEST['status'];
  $sqlStatus = $db->execute("UPDATE category SET status = '$status' WHERE cat_id = '$id'");

  if ($sqlStatus) {
    $_SESSION['msg'] = 'Status has been changed ..!!';
  } else {
    $_SESSION['errmsg'] = 'Sorry !! Some Error Occurred .. Try Again in status';
  }

  header("Location: all-category.php");
  exit;
}

// Delete functionality for categories
if (isset($_REQUEST['delete']) && $_REQUEST['delete'] == 'y') {
  $id = (int)$_REQUEST['id'];

  $filename = $userObj->getCategoryById($id);

  $files = [
    'cat_img'
  ];

  foreach ($files as $file) {
    if (file_exists('../Uploads/categories/' . $filename[$file])) {
      unlink('../Uploads/categories/' . $filename[$file]);
    }
  }

  $sqlDelete = $db->execute("DELETE FROM category WHERE cat_id = '$id'");
  if ($sqlDelete) {
    $_SESSION['msg'] = 'Record Successfully Deleted ..!!';
  } else {
    $_SESSION['errmsg'] = 'Sorry !! Some Error Occurred .. Try Again in delete';
  }
  header("Location: all-category.php");
  exit;
}

$userData = $userObj->getAllCategories();
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

  .icon-dlt-edit {
    display: flex;
    justify-content: center;
  }

  .dt-buttons {
    position: absolute;
    top: 40px;
    right: 20px;
  }

  .table td {
    border-top: none !important;
  }

  .catimg {
    width: 200px;
  }
</style>

<body class="fix-header fix-sidebar card-no-border">
  <div id="main-wrapper">
    <!-- Topbar header -->
    <?php require('include/header.php'); ?>
    <!-- Left Sidebar -->
    <?php require('include/sidebar.php'); ?>

    <div class="page-wrapper">
      <div class="container-fluid">
        <div class="row page-titles">
          <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">All Category</h3>
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="javascript:void(0)">Home</a>
              </li>
              <li class="breadcrumb-item active">All Category</li>
            </ol>
          </div>
          <div class="col-md-7 align-self-center text-right d-none d-md-block">
            <a href="user.php">
              <button type="button" class="btn btn-info">
                <i class="fa fa-plus-circle"></i> Create New
              </button>
            </a>
          </div>
          <div class="">
            <button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10">
              <i class="ti-settings text-white"></i>
            </button>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <div class="card">
              <?php include('include/notification.php'); ?>
              <?php if (!empty($editId)): ?>
                <div class="card-body">
                  <?php include('include/notification.php'); ?>
                  <div class="row">
                    <div class="col-6">
                      <form method="POST" id="headingForm">
                        <input type="hidden" name="headingId" value="<?php echo htmlspecialchars($editId ?? ''); ?>">
                        <input type="hidden" name="brandType" value="category">
                        <div class="row">
                          <div class="col-12">
                            <div class="form-group">
                              <label>Heading</label>
                              <input type="text" name="heading" class="form-control"
                                value="<?php echo isset($editData['heading']) ? htmlspecialchars($editData['heading']) : ''; ?>"
                                required>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="form-group">
                              <label>Paragraph</label>
                              <textarea name="paragraph" class="form-control"><?php echo htmlspecialchars($editData['paragraph'] ?? ''); ?></textarea>
                            </div>
                          </div>
                        </div>

                        <label for="">Status</label>
                        <div class="flex1">
                          <fieldset class="controls">
                            <div class="custom-control custom-radio">
                              <input type="radio" value="1" name="status" required id="styled_radio1" class="custom-control-input" <?php echo (isset($editData['status']) && $editData['status'] == 1) ? 'checked' : ''; ?>>
                              <label class="custom-control-label" for="styled_radio1">Active</label>
                            </div>
                          </fieldset>
                          <fieldset>
                            <div class="custom-control custom-radio">
                              <input type="radio" value="0" name="status" id="styled_radio2" class="custom-control-input" <?php echo (isset($editData['status']) && $editData['status'] == 0) ? 'checked' : ''; ?>>
                              <label class="custom-control-label" for="styled_radio2">Inactive</label>
                            </div>
                          </fieldset>
                        </div>
                        <div class="row">
                          <div class="col-lg-2">
                            <input name="submissionType" value="Update" type="submit" class="btn btn-success mt-3 w-100">
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                  <hr>
                </div>
              <?php endif; ?>
            </div>

            <div class="card-body bg-white mt-5">
              <h4>All Headings</h4>
              <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
    
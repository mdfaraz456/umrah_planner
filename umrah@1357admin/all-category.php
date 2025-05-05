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
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Heading</th>
                    <th>Paragraph</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($headings as $index => $heading): ?>
                    <tr>
                      <td><?php echo $index + 1; ?></td>
                      <td><?php echo htmlspecialchars($heading['heading']); ?></td>
                      <td><?php echo htmlspecialchars($heading['paragraph']); ?></td>
                      <td class="icon-dlt-edit">
                        <?php if ($heading['status'] == 1): ?>
                          <a href="?sId=<?php echo $heading['id']; ?>&status=0" title="Hide">
                            <i class="fa-solid fa-eye" style="color: #e41111;"></i>
                          </a>
                        <?php else: ?>
                          <a href="?sId=<?php echo $heading['id']; ?>&status=1" title="Show">
                            <i class="fa-solid fa-eye-slash" style="color: #d21414;"></i>
                          </a>
                        <?php endif; ?>
                        <a href="all-category.php?id=<?php echo base64_encode($heading['id']); ?>" title="Edit">
                          <i class="fa-solid fa-pencil" style="color: #e81111;"></i>
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <?php if (empty($editId)): ?>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">All Category</h4>
                  <div class="table-responsive m-t-40">
                    <table id="example24" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>S No</th>
                          <th>Images</th>
                          <th>Name</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr>
                          <th>S No</th>
                          <th>Images</th>
                          <th>Name</th>
                          <th>Action</th>
                        </tr>
                      </tfoot>
                      <tbody>
                        <?php
                        $i = 1;
                        foreach ($userData as $users): ?>
                          <tr>
                            <td><?php echo $i++; ?></td>
                            <td><img src="../Uploads/categories/<?= htmlspecialchars($users['cat_img']) ?>" class="catimg"></td>
                            <td><?php echo htmlspecialchars($users['cat_name'] ?? ''); ?></td>
                            <td class="icon-dlt-edit">
                              <?php if ($users['status'] ?? '' == 1): ?>
                                <a href="?status=0&cId=<?php echo $users['cat_id'] ?? ''; ?>">
                                  <i class="fa-solid fa-user" style="color: #e41111;"></i>
                                </a>
                              <?php else: ?>
                                <a href="?status=1&cId=<?php echo $users['cat_id'] ?? ''; ?>">
                                  <i class="fa-solid fa-user-slash" style="color: #d21414;"></i>
                                </a>
                              <?php endif; ?>
                              <a href="category.php?id=<?php echo base64_encode($users['cat_id'] ?? '') ?>"><i class="fa-solid fa-pencil" style="color: #e81111;"></i></a>
                              <a href="?id=<?php echo $users['cat_id']; ?>&delete=y" onClick="return confirm('Are you sure !! Record will be delete permanently ..!!')"><i class="fa-solid fa-trash" style="color: #f50000;"></i></a>
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
        <?php endif; ?>

        <div class="right-sidebar">
          <div class="slimscrollright">
            <div class="rpanel-title">
              Service Panel
              <span><i class="ti-close right-side-toggle"></i></span>
            </div>
            <div class="r-panel-body">
              <ul id="themecolors" class="m-t-20">
                <li><b>With Light sidebar</b></li>
                <li><a href="javascript:void(0)" data-theme="default" class="default-theme">1</a></li>
                <li><a href="javascript:void(0)" data-theme="green" class="green-theme">2</a></li>
                <li><a href="javascript:void(0)" data-theme="red" class="red-theme">3</a></li>
                <li><a href="javascript:void(0)" data-theme="blue" class="blue-theme">4</a></li>
                <li><a href="javascript:void(0)" data-theme="purple" class="purple-theme">5</a></li>
                <li><a href="javascript:void(0)" data-theme="megna" class="megna-theme">6</a></li>
                <li class="d-block m-t-30"><b>With Dark sidebar</b></li>
                <li><a href="javascript:void(0)" data-theme="default-dark" class="default-dark-theme working">7</a></li>
                <li><a href="javascript:void(0)" data-theme="green-dark" class="green-dark-theme">8</a></li>
                <li><a href="javascript:void(0)" data-theme="red-dark" class="red-dark-theme">9</a></li>
                <li><a href="javascript:void(0)" data-theme="blue-dark" class="blue-dark-theme">10</a></li>
                <li><a href="javascript:void(0)" data-theme="purple-dark" class="purple-dark-theme">11</a></li>
                <li><a href="javascript:void(0)" data-theme="megna-dark" class="megna-dark-theme">12</a></li>
              </ul>
              <ul class="m-t-20 chatonline">
                <li><b>Chat option</b></li>
                <li><a href="javascript:void(0)"><img src="../assets/images/users/1.jpg" alt="user-img" class="img-circle"><span>Varun Dhavan <small class="text-success">online</small></span></a></li>
                <li><a href="javascript:void(0)"><img src="../assets/images/users/2.jpg" alt="user-img" class="img-circle"><span>Genelia Deshmukh <small class="text-warning">Away</small></span></a></li>
                <li><a href="javascript:void(0)"><img src="../assets/images/users/3.jpg" alt="user-img" class="img-circle"><span>Ritesh Deshmukh <small class="text-danger">Busy</small></span></a></li>
                <li><a href="javascript:void(0)"><img src="../assets/images/users/4.jpg" alt="user-img" class="img-circle"><span>Arijit Sinh <small class="text-muted">Offline</small></span></a></li>
                <li><a href="javascript:void(0)"><img src="../assets/images/users/5.jpg" alt="user-img" class="img-circle"><span>Govinda Star <small class="text-success">online</small></span></a></li>
                <li><a href="javascript:void(0)"><img src="../assets/images/users/6.jpg" alt="user-img" class="img-circle"><span>John Abraham <small class="text-success">online</small></span></a></li>
                <li><a href="javascript:void(0)"><img src="../assets/images/users/7.jpg" alt="user-img" class="img-circle"><span>Hritik Roshan <small class="text-success">online</small></span></a></li>
                <li><a href="javascript:void(0)"><img src="../assets/images/users/8.jpg" alt="user-img" class="img-circle"><span>Pwandeep rajan <small class="text-success">online</small></span></a></li>
              </ul>
            </div>
          </div>
        </div>

        <?php require('include/footer.php'); ?>
      </div>
    </div>

    <script src="node_modules/jquery/jquery.min.js"></script>
    <script src="node_modules/bootstrap/js/popper.min.js"></script>
    <script src="node_modules/bootstrap/js/bootstrap.min.js"></script>
    <script src="node_modules/ps/perfect-scrollbar.jquery.min.js"></script>
    <script src="js/waves.js"></script>
    <script src="js/sidebarmenu.js"></script>
    <script src="js/custom.min.js"></script>
    <script src="node_modules/raphael/raphael.min.js"></script>
    <script src="node_modules/morrisjs/morris.min.js"></script>
    <script src="node_modules/d3/d3.min.js"></script>
    <script src="node_modules/c3-master/c3.min.js"></script>
    <script src="node_modules/toast-master/js/jquery.toast.js"></script>
    <script src="js/dashboard1.js"></script>
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
          buttons: [],
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
    <script>
      $(document).ready(function() {
        $('#example24').DataTable({
          dom: 'lBfrtip',
          buttons: [],
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
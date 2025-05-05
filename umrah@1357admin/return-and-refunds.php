<?php
session_start();
require '../config/config.php';
require 'functions/authentication.php';
require 'functions/umrahFunction.php';
require 'fckeditor/fckeditor_php5.php';

$auth = new Authentication();
$auth->checkSession();

$privacyObj = new Policy();
$db = new dbClass();

$editId = isset($_GET['id']) ? (int)base64_decode($_GET['id']) : '';
$editData = []; // Initialize editData
if (!empty($editId)) {
    $editData = $privacyObj->getPolicyById($editId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = !empty($_POST['privacyId']) ? (int)$_POST['privacyId'] : '';
    $policyType = $db->addStr($_POST['policyType'] ?? 'default');
    $status = $_POST['status'] ?? '';
    $privacyContent = $_POST['privacy'] ?? '';

    if (empty($privacyContent)) {
        $_SESSION['errmsg'] = 'Return And Refunds Content is required.';
    } else {
        $result = $privacyObj->savePolicyData($privacyContent,  $status, $policyType, $id);

        if ($result !== false) {
            $_SESSION['msg'] =  'Return And Refunds updated successfully.';
            header("Location: return-and-refunds.php");
            exit;
        } else {
            $_SESSION['errmsg'] = 'Failed to save Return And Refunds.';
        }
    }
}

// STATUS functionality
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = (int)$_GET['status'];

    $sqlStatus = $db->execute("UPDATE policy SET status = '$status' WHERE privacy_id = '$id'");

    if ($sqlStatus) {
        $_SESSION['msg'] = 'Status has been changed successfully.';
    } else {
        $_SESSION['errmsg'] = 'Sorry, some error occurred. Try again.';
    }

    header("Location: return-and-refunds.php");
    exit;
}

 

// Get all privacy policies
$privacyPolicies = $privacyObj->getAllPolicies('Refund');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin | Umrah Planner</title>
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png" />
    <link href="node_modules/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="node_modules/perfect-scrollbar/dist/css/perfect-scrollbar.min.css" rel="stylesheet" />
    <link href="node_modules/morrisjs/morris.css" rel="stylesheet" />
    <link href="node_modules/c3-master/c3.min.css" rel="stylesheet" />
    <link href="node_modules/toast-master/css/jquery.toast.css" rel="stylesheet" />
    <link href="node_modules/dropify/dist/css/dropify.min.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/pages/dashboard1.css" rel="stylesheet" />
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

    .error {
        color: #dc3545 !important;
        font-size: 12px;
        margin-top: 5px;
    }

    .form-group {
        position: relative;
        margin-bottom: 30px;
    }

    .is-invalid {
        border-color: #dc3545 !important;
    }

    .dropify-wrapper .dropify-preview .dropify-render img {
        width: 100% !important;
        height: auto;
        object-fit: unset !important;
    }

    .table td,
    .table th {
        padding: .75rem;
        vertical-align: middle;
        border-top: 1px solid #dee2e6;
        text-align: center;
    }

    .icon-dlt-edit i {
        font-size: 20px;
    }

    .icon-dlt-edit {
        display: flex;
        gap: 20px;
        justify-content: center;
    }

    .bannerimg {
        width: 100px;
        height: auto;
        object-fit: cover;
    }

    .table td {
        border: none !important;
    }
</style>

<body class="fix-header fix-sidebar card-no-border">
    <div id="main-wrapper">
        <?php require('include/header.php'); ?>
        <?php require('include/sidebar.php'); ?>
        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="row page-titles">
                    <div class="col-md-5 align-self-center">
                        <h3 class="text-themecolor">
                            <?php echo $editId ? 'Update Return And Refunds' : 'Add Return And Refunds' ?>
                        </h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active"><?php echo $editId ? 'Update Return And Refunds' : 'Add Return And Refunds' ?></li>
                        </ol>
                    </div>
                    <div>
                        <button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10">
                            <i class="ti-settings text-white"></i>
                        </button>
                    </div>
                </div>

                <?php include('include/notification.php'); ?>

                <?php if (!empty($editId)): ?>
                    <div class="row mb-5">
                        <div class="col-lg-12 col-md-6">
                            <div class="card">
                                <div class="card-body"> 
                                    <form method="POST" id="privacyForm" enctype="multipart/form-data">
                                        <input type="hidden" name="privacyId" value="<?php echo htmlspecialchars($editId ?? '') ?>">
                                        <input type="hidden" name="policyType" value="Refund">

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <?php
                                                    $privacy = $editData['privacy'] ?? '';
                                                    $sBasePath = 'fckeditor/';
                                                    $oFCKeditor = new FCKeditor('privacy');
                                                    $oFCKeditor->BasePath = $sBasePath;
                                                    $oFCKeditor->Value = $privacy;
                                                    $oFCKeditor->Width = '100%';
                                                    $oFCKeditor->Height = '400';
                                                    $oFCKeditor->Create();
                                                    ?>
                                                </div>
                                            </div>
                                        </div>

                                        <label for="">Status</label>
                                        <div class="flex1">
                                            <fieldset class="controls">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" value="1" name="status" required id="styled_radio1" class="custom-control-input" <?php echo (isset($editData['status']) && $editData['status'] == 1) ? 'checked' : ''; ?> />
                                                    <label class="custom-control-label" for="styled_radio1">Active</label>
                                                </div>
                                            </fieldset>
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" value="0" name="status" id="styled_radio2" class="custom-control-input" <?php echo (isset($editData['status']) && $editData['status'] == 0) ? 'checked' : ''; ?> />
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
                        </div>
                    </div>

                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">All Return And Refunds</h4>
                                <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Return And Refunds</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($privacyPolicies as $index => $policy): ?>
                                            <tr>
                                                <td><?php echo $index + 1 ?></td>
                                                <td><?php echo substr(strip_tags($policy['privacy']), 0, 100) . (strlen($policy['privacy']) > 100 ? '...' : ''); ?></td>
                                                <td class="icon-dlt-edit">
                                                    <?php if ($policy['status'] == 1): ?>
                                                        <a href="?id=<?php echo $policy['privacy_id'] ?>&status=0" title="Hide">
                                                            <i class="fa-solid fa-eye" style="color: #e41111;"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="?id=<?php echo $policy['privacy_id'] ?>&status=1" title="Show">
                                                            <i class="fa-solid fa-eye-slash" style="color: #d21414;"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="return-and-refunds.php?id=<?php echo base64_encode($policy['privacy_id']) ?>" title="Edit">
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
                </div>

                <div class="row mt-3">
                    <div class="col-lg-12">
                        <?php require('include/footer.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="node_modules/jquery/jquery.min.js"></script>
    <script src="js/jquery-validation/jquery.validate.min.js"></script>
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
    <script src="js/dashboard1.js"></script>
    <script src="node_modules/styleswitcher/jQuery.style.switcher.js"></script>
    <script type="text/javascript" src="fckeditor/fckeditor.js"></script>

    <script>
        $(document).ready(function() {
            console.log('Privacy Form Ready');

            // Initialize Dropify (even though not used, kept for consistency)
            $('.dropify').dropify();

            // Initialize validation
            $("#privacyForm").validate({
                rules: {
                    privacy: {
                        required: true
                    },
                    status: {
                        required: true
                    }
                },
                messages: {
                    privacy: {
                        required: "Please enter the Return And Refunds content."
                    },
                    status: {
                        required: "Please select a status."
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") === "privacy") {
                        error.insertAfter(element.closest('.fckeditor')).addClass('error');
                    } else {
                        error.insertAfter(element).addClass('error');
                    }
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    // Update FCKeditor content before form submit
                    if (typeof FCKeditorAPI !== 'undefined') {
                        for (var instance in FCKeditorAPI.__Instances) {
                            FCKeditorAPI.__Instances[instance].UpdateLinkedField();
                        }
                    }

                    console.log('Privacy form valid, submitting...');
                    form.submit();
                }
            });

            // Initialize DataTable
            $('#example23').DataTable({
                dom: 'lBfrtip',
                buttons: [],
                order: [
                    [0, 'asc']
                ],
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'All']
                ],
                responsive: true
            });
        });
    </script>
</body>

</html>
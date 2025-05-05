<?php
session_start();
require '../config/config.php';
require 'functions/authentication.php';
require 'functions/umrahFunction.php';
require 'functions/fileUploader.php';

$auth = new Authentication();
$auth->checkSession();

$brandObj = new Brand();
$db = new dbClass();

$editId = isset($_GET['id']) ? (int)base64_decode($_GET['id']) : '';
$editData = []; // Initialize editData
if (!empty($editId)) {
    $editData = $brandObj->getBrandById($editId); // Fetch brand data for editing
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submissionType = $db->addStr($_POST['submissionType'] ?? 'Submit');
    $id = !empty($_POST['brandId']) ? (int)$_POST['brandId'] : '';
    $status = $_POST['status'] ?? '';
    $brandType = $db->addStr($_POST['brandType'] ?? '');

    // File upload handling
    $dest = "../uploads/brands/";
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    $brandImage = uploadFileHandler('brand_image', $editData['images'] ?? '', $dest, '300', '200');

    $result = $brandObj->saveBrandData($brandImage, $submissionType, $status, $brandType, $id);

    if ($result !== false) {
        $_SESSION['msg'] = ($submissionType === 'Submit') ? 'Brand added successfully.' : 'Brand updated successfully.';
        header("Location: airlines.php");
        exit;
    } else {
        $_SESSION['errmsg'] = 'Failed to save brand data.';
    }
}
// STATUS functionality 
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = (int)$_GET['status'];

    $sqlStatus = $db->execute("UPDATE brands SET status = '$status' WHERE brands_id = '$id'");

    if ($sqlStatus) {
        $_SESSION['msg'] = 'Status has been changed successfully.';
    } else {
        $_SESSION['errmsg'] = 'Sorry, some error occurred. Try again.';
    }

    header("Location: airlines.php");
    exit;
}

// Delete brand
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id > 0) {

        $brandData = $brandObj->getBrandById($id);
        $sqlDelete = $db->execute("DELETE FROM brands WHERE brands_id = '$id'");
        if ($sqlDelete) {

            if (!empty($brandData['brands']) && file_exists("../uploads/brands/" . $brandData['brands'])) {
                unlink("../uploads/brands/" . $brandData['brands']);
            }
            $_SESSION['msg'] = 'Banner deleted successfully!';
        } else {
            $_SESSION['errmsg'] = 'Failed to delete banner!';
        }
    } else {
        $_SESSION['errmsg'] = 'Invalid brand ID!';
    }
    header("Location: airlines.php");
    exit;
}

// Get all brands
$brands = $brandObj->getAllBrands('Airlines');
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
        width: 50%;
        height: 152px;
        object-fit: cover;
    }

    .dropify-wrapper .dropify-preview .dropify-render img {
        width: 100% !important;
        height: auto;
        object-fit: unset !important;
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
                        <h3 class="text-themecolor">Airlines</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                            <li class="breadcrumb-item active">Airlines</li>
                        </ol>
                    </div>
                    <div class="col-md-7 align-self-center text-right d-none d-md-block">
                        <a href="brands.php"><button type="button" class="btn btn-info">
                                <i class="fa fa-plus-circle"></i> Create New
                            </button></a>
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
                            <div class="card-body"> 
                                <?php include('include/notification.php'); ?>
                                <div class="row">
                                    <div class="col-6">
                                        <form method="POST" id="brandForm" enctype="multipart/form-data">
                                            <input type="hidden" name="brandId" value="<?php echo htmlspecialchars($editId ?? '') ?>">
                                            <input type="hidden" name="brandType" value="Airlines">
                                            <div class="row">
                                                <div class="col-6">
                                                <label for="">Images (300 x 200)</label>
                                                    <div class="form-group">
                                                        <input type="file" name="brand_image" class="dropify"
                                                            <?php echo empty($editId) ? 'required' : '' ?>
                                                            data-default-file="<?php echo !empty($editData['images']) ? '../uploads/brands/' . htmlspecialchars($editData['images']) : ''; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <label for="">Status</label>
                                            <div class="flex1">
                                                <fieldset class="controls">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" value="1" name="status" required id="styled_radio1" class="custom-control-input" <?php echo (isset($editData['status']) && $editData['status'] == 1) || !isset($editData['status']) ? 'checked' : ''; ?> />
                                                        <label class="custom-control-label" for="styled_radio1">Active</label>
                                                    </div>
                                                </fieldset>

                                                <fieldset>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" value="0" name="status" id="styled_radio2" class="custom-control-input" <?php echo isset($editData['status']) && $editData['status'] == 0 ? 'checked' : ''; ?> />
                                                        <label class="custom-control-label" for="styled_radio2">Inactive</label>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <?php if (empty($editId)): ?>
                                                        <input name="submissionType" value="Submit" type="submit" class="btn btn-success mt-3 w-100">
                                                    <?php else: ?>
                                                        <input name="submissionType" value="Update" type="submit" class="btn btn-success mt-3 w-100">
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </form>

                                    </div>
                                </div>

                                <hr>

                            </div>

                        </div>

                        <div class="card-body bg-white mt-5">
                            <h4>All Airlines</h4>
                            <table id="example23" class="
                            display
                            nowrap
                            table table-hover table-striped table-bordered
                          " cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Airlines Image</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($brands as $index => $brand): ?>
                                        <tr>
                                            <td><?php echo $index + 1 ?></td>
                                            <td><img src="../uploads/brands/<?php echo htmlspecialchars($brand['images']) ?>" class="bannerimg"></td>
                                            <td class="icon-dlt-edit">
                                                <?php if ($brand['status'] == 1): ?>
                                                    <a href="?id=<?php echo $brand['brands_id'] ?>&status=0" title="Hide">
                                                        <i class="fa-solid fa-eye" style="color: #e41111;"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="?id=<?php echo $brand['brands_id'] ?>&status=1" title="Show">
                                                        <i class="fa-solid fa-eye-slash" style="color: #d21414;"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="airlines.php?id=<?php echo base64_encode($brand['brands_id']) ?>" title="Edit">
                                                    <i class="fa-solid fa-pencil" style="color: #e81111;"></i>
                                                </a>
                                                <a href="?action=delete&id=<?php echo $brand['brands_id'] ?>" onclick="return confirm('Delete this brand?')" title="Delete">
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
        </div>

        <?php require('include/footer.php'); ?>
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
            console.log('Brand Form Ready');

            // Initialize Dropify
            $('.dropify').dropify();

            // Determine if edit mode and image exists
            const isBrandEditMode = '<?php echo !empty($editId) ? 'true' : 'false'; ?>';
            const existingImages = {
                brand_image: '<?php echo !empty($editData['images']) ? 'true' : 'false'; ?>'
            };

            console.log('Is Brand Edit Mode:', isBrandEditMode);
            console.log('Existing Brand Image:', existingImages.brand_image);

            // Flag to track if brand_image has changed
            let brandImageChanged = false;

            // Initialize validation on Sexually transmitted disease
            $("#brandForm").validate({
                rules: {
                    brand_image: {
                        required: function() {
                            return isBrandEditMode !== 'true' || existingImages.brand_image !== 'true' || brandImageChanged;
                        },
                        extension: "jpg|jpeg|png|webp"
                    },
                    status: {
                        required: true
                    }
                },
                messages: {
                    brand_image: {
                        required: "Please upload a brand image.",
                        extension: "Only image files (jpg, jpeg, png, webp) are allowed."
                    },
                    status: {
                        required: "Please select a status."
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.attr("type") === "file") {
                        error.insertAfter(element.closest('.dropify-wrapper')).addClass('error');
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
                    console.log('Brand form valid, submitting...');
                    form.submit();
                }
            });

            // Detect changes to the brand_image input
            $('.dropify').on('change', function() {
                brandImageChanged = true; // Set flag when image changes
                $(this).valid(); // Trigger validation
            });

            // Dropify delete confirmation and events
            var drEvent = $('.dropify').dropify();
            drEvent.on('dropify.beforeClear', function(event, element) {
                return confirm('Do you really want to delete "' + element.file.name + '" ?');
            });
            drEvent.on('dropify.afterClear', function(event, element) {
                brandImageChanged = true; // Set flag when image is cleared
                alert('Brand image deleted');
                $('.dropify').valid(); // Trigger validation after clearing
            });
            drEvent.on('dropify.errors', function(event, element) {
                console.log('Dropify Errors:', element);
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

    <script>
        $(document).ready(function() {
            $('#example23').DataTable({
                dom: 'lBfrtip',
                buttons: [

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
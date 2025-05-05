<?php
session_start();
require '../config/config.php';
require 'functions/authentication.php';
require 'functions/umrahFunction.php';
require 'functions/fileUploader.php';

$auth = new Authentication();
$auth->checkSession();

$categoryObj = new Category();
$db = new dbClass();

$id = isset($_GET['id']) ? (int)base64_decode($_GET['id']) : '';

$editival = $categoryObj->getCategoryById($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submissionType = $db->addStr($_POST['submissionType'] ?? 'Submit');
    $id = !empty($_POST['catId']) ? (int)$_POST['catId'] : '';
    $cat_name = isset($_POST['cat_name']) ? trim($_POST['cat_name']) : '';
    $status = $_POST['status'] ?? '';

    // File upload handling
    $dest = "../uploads/categories/";
    $catImage = uploadFileHandler('cat_image', $editival['cat_img'] ?? '', $dest, '400', '300');

    // Validate required fields
    if (empty($cat_name)) {
        $_SESSION['errmsg'] = 'Category name is required.';
    } elseif (empty($catImage) && empty($editival['cat_img'])) {
        $_SESSION['errmsg'] = 'Category image is required.';
    } elseif (!isset($_POST['status'])) {
        $_SESSION['errmsg'] = 'Status is required.';
    } else {
        $result = $categoryObj->saveCategoryData($cat_name, $catImage, $status, $submissionType, $id);

        if ($result !== false) {
            $_SESSION['msg'] = ($submissionType === 'Submit') ? 'Category added successfully.' : 'Category updated successfully.';
            header("Location: all-category.php");
            exit;
        } else {
            $_SESSION['errmsg'] = 'Failed to save category data.';
        }
    }
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
    <link href="node_modules/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="node_modules/perfect-scrollbar/dist/css/perfect-scrollbar.min.css" rel="stylesheet" />
    <link href="node_modules/morrisjs/morris.css" rel="stylesheet" />
    <link href="node_modules/c3-master/c3.min.css" rel="stylesheet" />
    <link href="node_modules/toast-master/css/jquery.toast.css" rel="stylesheet" />
    <link href="node_modules/dropify/dist/css/dropify.min.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/pages/dashboard1.css" rel="stylesheet" />
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

    .flex1 {
        display: flex;
        gap: 20px;
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
                            <?php echo $id ? 'Update Category' : 'Add Category' ?>
                        </h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active"><?php echo $id ? 'Update Category' : 'Add Category' ?></li>
                        </ol>
                    </div>
                    <div>
                        <button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10">
                            <i class="ti-settings text-white"></i>
                        </button>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-lg-12 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title"><?php echo empty($id) ? 'Add Category' : 'Edit Category'; ?></h4>
                                <?php include('include/notification.php'); ?>
                                <form method="POST" id="category" enctype="multipart/form-data">
                                    <input type="hidden" name="catId" value="<?php echo htmlspecialchars($id) ?>">

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Category Image</label>
                                            <input type="file" name="cat_image" class="dropify"
                                                <?php echo empty($editival['cat_img']) ? 'required' : '' ?>
                                                data-default-file="<?php echo !empty($editival['cat_img']) ? '../Uploads/categories/' . htmlspecialchars($editival['cat_img']) : ''; ?>">

                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Category Name</label>
                                            <input type="text" name="cat_name" class="form-control"
                                                value="<?php echo isset($editival['cat_name']) ? htmlspecialchars($editival['cat_name']) : ''; ?>"
                                                required>
                                        </div>
                                    </div>

                                    <label for="">Status</label>
                                    <div class="flex1">
                                        <fieldset class="controls">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" value="1" name="status" required id="styled_radio1" class="custom-control-input" <?php echo (isset($editival['status']) && $editival['status'] == 1) ? 'checked' : ''; ?> />
                                                <label class="custom-control-label" for="styled_radio1">Active</label>
                                            </div>
                                        </fieldset>
                                        <fieldset>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" value="0" name="status" id="styled_radio2" class="custom-control-input" <?php echo (isset($editival['status']) && $editival['status'] == 0) ? 'checked' : ''; ?> />
                                                <label class="custom-control-label" for="styled_radio2">Inactive</label>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-2">
                                            <?php if (empty($id)): ?>
                                                <input name="submissionType" value="Submit" type="submit" class="btn btn-success mt-3 w-100">
                                            <?php else: ?>
                                                <input name="submissionType" value="Update" type="submit" class="btn btn-success mt-3 w-100">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </form>
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
    <script src="js/dashboard1.js"></script>
    <script src="node_modules/styleswitcher/jQuery.style.switcher.js"></script>

    <script>
        $(document).ready(function() {
            console.log('Category Form Ready');

            // Initialize Dropify
            $('.dropify').dropify();

            // Initialize validation on the category form
            $("#category").validate({
                rules: {
                    cat_name: {
                        required: true,
                        minlength: 2
                    },
                    cat_image: {
                        required: <?php echo empty($editival['cat_img']) ? 'true' : 'false'; ?>,
                        extension: "jpg|jpeg|png|webp"
                    },
                    status: {
                        required: true
                    }
                },
                messages: {
                    cat_name: {
                        required: "Please enter a category name.",
                        minlength: "Category name must be at least 2 characters long."
                    },
                    cat_image: {
                        required: "Please upload a category image.",
                        extension: "Only image files (jpg, jpeg, png, webp) are allowed."
                    },
                    status: {
                        required: "Please select a status."
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.attr("type") === "file") {
                        error.insertAfter(element.closest('.dropify-wrapper')).addClass('error');
                    } else if (element.attr("type") === "radio") {
                        error.insertAfter(element.closest('.flex1')).addClass('error');
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
                    console.log('Category form valid, submitting...');
                    form.submit();
                }
            });

            // Trigger validation when file changes
            $('.dropify').on('change', function() {
                $(this).valid();
            });

            // Dropify delete confirmation and events
            var drEvent = $('.dropify').dropify();
            drEvent.on('dropify.beforeClear', function(event, element) {
                return confirm('Do you really want to delete "' + element.file.name + '" ?');
            });
            drEvent.on('dropify.afterClear', function(event, element) {
                alert('Category image deleted');
                // Make cat_image required after clearing
                $('#cat_image').attr('required', true);
                $('#category').validate().element('#cat_image');
            });
            drEvent.on('dropify.errors', function(event, element) {
                console.log('Dropify Errors:', element);
            });
        });
    </script>

</body>

</html>
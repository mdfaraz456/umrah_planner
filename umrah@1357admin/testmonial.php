<?php
session_start();
require '../config/config.php';
require 'functions/authentication.php';
require 'functions/umrahFunction.php';
require 'functions/fileUploader.php';

$auth = new Authentication();
$auth->checkSession();

$testimonialObj = new Testimonial();
$db = new dbClass();

$id = isset($_GET['id']) ? (int)base64_decode($_GET['id']) : '';

$editival = $testimonialObj->getTestimonialById($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submissionType = $db->addStr($_POST['submissionType'] ?? 'Submit');
    $id = !empty($_POST['testimonialId']) ? (int)$_POST['testimonialId'] : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $feedback_msg = isset($_POST['feedback_msg']) ? trim($_POST['feedback_msg']) : '';
    $status = $_POST['status'] ?? '';

    // File upload handling
    $dest = "../uploads/testimonials/";
    $images = uploadFileHandler('images', $editival['images'] ?? '', $dest, '400', '300');

    // Save data without validation
    $result = $testimonialObj->saveTestimonialData($name, $feedback_msg, $images, $status, $submissionType, $id);

    if ($result !== false) {
        $_SESSION['msg'] = ($submissionType === 'Submit') ? 'Testimonial added successfully.' : 'Testimonial updated successfully.';
        header("Location: all-testimonials.php");
        exit;
    } else {
        $_SESSION['errmsg'] = 'Failed to save testimonial data.';
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
                            <?php echo $id ? 'Update Testimonial' : 'Add Testimonial' ?>
                        </h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active"><?php echo $id ? 'Update Testimonial' : 'Add Testimonial' ?></li>
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
                                <?php include('include/notification.php'); ?>
                                <form method="POST" id="testimonial" enctype="multipart/form-data">
                                    <input type="hidden" name="testimonialId" value="<?php echo htmlspecialchars($id) ?>">

                                    <div class="col-6">
                                        <div class="form-group">
                                        <label>Images (400 x 300)</label>
                                            <input type="file" name="images" id="images" class="dropify"
                                                <?php echo empty($editival['images']) ? 'required' : '' ?>
                                                data-default-file="<?php echo !empty($editival['images']) ? '../uploads/testimonials/' . htmlspecialchars($editival['images']) : ''; ?>">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="name" class="form-control"
                                                value="<?php echo isset($editival['name']) ? htmlspecialchars($editival['name']) : ''; ?>"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Feedback Message</label>
                                            <textarea name="feedback_msg" class="form-control" rows="5"
                                                required><?php echo isset($editival['feedback_msg']) ? htmlspecialchars($editival['feedback_msg']) : ''; ?></textarea>
                                        </div>
                                    </div>


                                    <div class="col-6">
                                    <label for="">Status</label>
                                    <div class="flex1">
                                        <fieldset class="controls">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" value="1" name="status" required id="styled_radio1" class="custom-control-input" <?php echo (isset($editival['status']) && $editival['status'] == 1) || !isset($editival['status']) ? 'checked' : ''; ?> />
                                                <label class="custom-control-label" for="styled_radio1">Active</label>
                                            </div>
                                        </fieldset>

                                        <fieldset>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" value="0" name="status" id="styled_radio2" class="custom-control-input" <?php echo isset($editival['status']) && $editival['status'] == 0 ? 'checked' : ''; ?> />
                                                <label class="custom-control-label" for="styled_radio2">Inactive</label>
                                            </div>
                                        </fieldset>
                                    </div>
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
            console.log('Testimonial Form Ready');

            // Initialize Dropify
            $('.dropify').dropify();

            // Initialize validation on the testimonial form
            $("#testimonial").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 2
                    },
                    feedback_msg: {
                        required: true,
                        minlength: 10
                    },
                    images: {
                        required: <?php echo empty($editival['images']) ? 'true' : 'false'; ?>,
                        extension: "jpg|jpeg|png|webp"
                    },
                    status: {
                        required: true
                    }
                },
                messages: {
                    name: {
                        required: "Please enter a name.",
                        minlength: "Name must be at least 2 characters long."
                    },
                    feedback_msg: {
                        required: "Please enter a feedback message.",
                        minlength: "Feedback message must be at least 10 characters long."
                    },
                    images: {
                        required: "Please upload a testimonial image.",
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
                    console.log('Testimonial form valid, submitting...');
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
                alert('Testimonial image deleted');
                $('#images').attr('required', true);
                $('#testimonial').validate().element('#images');
            });
            drEvent.on('dropify.errors', function(event, element) {
                console.log('Dropify Errors:', element);
            });
        });
    </script>

</body>

</html>
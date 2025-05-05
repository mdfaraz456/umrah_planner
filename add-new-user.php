<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Generate CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require '../config/config.php';
require 'functions/authentication.php';
require 'functions/umrahFunction.php';
require 'functions/fileUploader.php';

$auth = new Authentication();
$auth->checkSession();

$db = new dbClass();
$umrahObj = new Customer();

// Get and validate ID
$id = isset($_GET['id']) ? base64_decode($_GET['id']) : NULL;
if ($id !== NULL && !is_numeric($id)) {
    $id = NULL;
}

$editival = $umrahObj->getUserById($id) ?? [];
$mailPhone = $umrahObj->getAllUsers() ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['errmsg'] = 'Invalid CSRF token.';
        header("Location: all-users.php");
        exit;
    }

    // Handle form submission
    if (isset($_POST['submissionType']) && in_array($_POST['submissionType'], ['Submit', 'Update'])) {
        $id = isset($_POST['eId']) ? $_POST['eId'] : NULL;
        $submissionType = $db->addStr($_POST['submissionType'] ?? '');
        $fullname = $db->addStr($_POST['fullname'] ?? '');
        $phonenumber = $db->addStr($_POST['phonenumber'] ?? '');
        $emailaddress = $db->addStr($_POST['emailaddress'] ?? '');
        $dob = $_POST['dob'] ?? NULL;
        $gender = $db->addStr($_POST['gender'] ?? '');
        $marital = $db->addStr($_POST['marital'] ?? '');
        $country = $db->addStr($_POST['country'] ?? '');
        $state = $db->addStr($_POST['state'] ?? '');
        $district = $db->addStr($_POST['dist'] ?? '');
        $address = $db->addStr($_POST['address'] ?? '');

        // Directory for uploads
        $dest = "../uploads/customer/";
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        // Call the uploadFileHandler function for each file
    $passportFirst = uploadFileHandler('passport_first', $editival['passport_photo_first_page'] ?? '', $dest, $allowedTypes);
    $passportSecond = uploadFileHandler('passport_second', $editival['passport_photo_second_page'] ?? '', $dest, $allowedTypes);
    $passportPhoto = uploadFileHandler('passport_photo', $editival['customer_photo'] ?? '', $dest, $allowedTypes);
    $visaStamp = uploadFileHandler('visa_stamp', $editival['visa_stamp_page'] ?? '', $dest, $allowedTypes);
        // Save the user data
        $result = $umrahObj->saveUserData(
            $passportFirst,
            $passportSecond,
            $passportPhoto,
            $visaStamp,
            $fullname,
            $phonenumber,
            $emailaddress,
            $dob,
            $gender,
            $marital,
            $country,
            $state,
            $district,
            $address,
            'CUMP',
            $submissionType,
            $id
        );

        if ($result !== false) {
            $_SESSION['msg'] = ($submissionType == 'Submit') ? 'User has been added successfully.' : 'User has been updated successfully.';

            // Email Setup
            $subject = "Umrah Registration: $fullname";
            $to = defined('NOTIFICATION_EMAIL') ? : $emailaddress; // Use config or user email

            $passwordInfo = '';
            $password = '';

            if ($submissionType == 'Submit' && is_string($result)) {
                $password = $result;
                $passwordInfo = "<tr><td class='label'>Password:</td><td>$password</td></tr>";
            }

            $emailBody = "
            <html>
            <head>
                <style>
                    body {
                        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                        background-color: #f3f6fb;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        max-width: 680px;
                        margin: 40px auto;
                        background-color: #ffffff;
                        border-radius: 10px;
                        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
                        overflow: hidden;
                    }
                    .header {
                        background: linear-gradient(135deg, #0d4c72, #086a82);
                        padding: 20px 30px;
                        color: #ffffff;
                        text-align: center;
                    }
                    .header h1 {
                        margin: 0;
                        font-size: 26px;
                        font-weight: 700;
                        letter-spacing: 1px;
                    }
                    .header p {
                        margin: 6px 0 0;
                        font-size: 14px;
                        font-weight: 300;
                    }
                    .body {
                        padding: 30px 35px;
                        color: #333333;
                        font-size: 15px;
                        line-height: 1.7;
                    }
                    .body h2 {
                        font-size: 20px;
                        color: #086a82;
                        margin-bottom: 15px;
                    }
                    .info-table {
                        width: 100%;
                        border-collapse proving; collapse;
                        margin-top: 10px;
                    }
                    .info-table td {
                        padding: 10px 0;
                        vertical-align: top;
                    }
                    .info-table .label {
                        color: #555555;
                        font-weight: bold;
                        width: 150px;
                    }
                    .thank-you {
                        background-color: #e7f4f8;
                        padding: 20px;
                        margin-top: 25px;
                        border-left: 5px solid #0d4c72;
                        border-radius: 5px;
                        color: #0d4c72;
                        font-weight: 500;
                    }
                    .footer {
                        text-align: center;
                        font-size: 12px;
                        color: #888888;
                        padding: 18px;
                        background-color: #f0f0f0;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>UMRAH PLANNER</h1>
                        <p>Your Trusted Partner for a Spiritual Journey</p>
                    </div>
                    <div class='body'>
                        <h2>Thank you for registering, $fullname!</h2>
                        <p>We’ve successfully received your Umrah registration. Below are your submitted details:</p>
                        <table class='info-table'>
                            <tr>
                                <td class='label'>Full Name:</td>
                                <td>$fullname</td>
                            </tr>
                            <tr>
                                <td class='label'>Email Address:</td>
                                <td>$emailaddress</td>
                            </tr>
                            $passwordInfo
                        </table>
                        <div class='thank-you'>
                            We’re honored to guide you on this sacred journey. One of our representatives will contact you shortly with the next steps.
                        </div>
                    </div>
                    <div class='footer'>
                        © " . date('Y') . " UMRAH PLANNER. This is an automated email — please do not reply.
                    </div>
                </div>
            </body>
            </html>
            ";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: Umrah Portal <noreply@yourdomain.com>" . "\r\n";

            mail($to, $subject, $emailBody, $headers);
        } else {
            $_SESSION['errmsg'] = 'Sorry, some error occurred.';
        }

        header("Location: all-users.php");
        exit;
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
    <!-- Dropify CSS -->
    <link href="node_modules/dropify/dist/css/dropify.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet" />
    <!-- Dashboard 1 Page CSS -->
    <link href="css/pages/dashboard1.css" rel="stylesheet" />
    <!-- Theme colors -->
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
        color: red;
    }

    .pl-0 {
        padding-left: 0px !important;
        padding-top: 0px !important;
    }

    .dropify-wrapper .dropify-preview .dropify-render img {
        width: 100% !important;
        height: auto;
        object-fit: unset !important;
    }
</style>

<body class="fix-header fix-sidebar card-no-border">
    <div id="main-wrapper">
        <!-- Topbar header -->
        <?php require('include/header.php'); ?>
        <!-- End Topbar header -->

        <!-- Left Sidebar -->
        <?php require('include/sidebar.php'); ?>
        <!-- End Left Sidebar -->

        <!-- Page wrapper -->
        <div class="page-wrapper">
            <!-- Container fluid -->
            <div class="container-fluid">
                <!-- Bread crumb and right sidebar toggle -->
                <div class="row page-titles">
                    <div class="col-md-5 align-self-center">
                        <?php if (empty($id)): ?>
                            <h3 class="text-themecolor">Add New Users</h3>
                        <?php else: ?>
                            <h3 class="text-themecolor">Update Users</h3>
                        <?php endif; ?>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                            <?php if (empty($id)): ?>
                                <li class="breadcrumb-item active">Add New Users</li>
                            <?php else: ?>
                                <li class="breadcrumb-item active">Update Users</li>
                            <?php endif; ?>
                        </ol>
                    </div>
                    <div>
                        <button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10">
                            <i class="ti-settings text-white"></i>
                        </button>
                    </div>
                </div>
                <!-- End Bread crumb and right sidebar toggle -->

                <div class="row mb-5">
                    <div class="col-lg-12 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <?php if (empty($id)): ?>
                                    <h4 class="card-title">Add New Users</h4>
                                <?php else: ?>
                                    <h4 class="card-title">Update Users</h4>
                                <?php endif; ?>

                                <?php include('include/notification.php'); ?>
                                <form class="form pt-3" id="user-form" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="eId" value="<?php echo htmlspecialchars($id ?? ''); ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                                    <div class="row">
                                        <!-- Passport Files -->
                                        <div class="col-lg-6 col-md-6">
                                            <div class="card-body pl-0">
                                                <label class="card-title">Passport Photo (First Page)</label>
                                                <input type="file" id="passport_first" name="passport_first" class="dropify"
                                                    data-default-file="<?php echo '../uploads/customer/' . htmlspecialchars($editival['passport_photo_first_page'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="card-body pl-0">
                                                <label class="card-title">Passport Photo (Second Page)</label>
                                                <input type="file" id="passport_second" name="passport_second" class="dropify"
                                                    data-default-file="<?php echo '../uploads/customer/' . htmlspecialchars($editival['passport_photo_second_page'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="card-body pl-0">
                                                <label class="card-title">Passport Size Photo</label>
                                                <input type="file" id="passport_photo" name="passport_photo" class="dropify"
                                                    data-default-file="<?php echo '../uploads/customer/' . htmlspecialchars($editival['customer_photo'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="card-body pl-0">
                                                <label class="card-title">Visa Stamp Page (Photo)</label>
                                                <input type="file" id="visa_stamp" name="visa_stamp" class="dropify"
                                                    data-default-file="<?php echo '../uploads/customer/' . htmlspecialchars($editival['visa_stamp_page'] ?? ''); ?>">
                                            </div>
                                        </div>

                                        <!-- Text Fields -->
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Full Name (as on passport)</label>
                                                <input type="text" class="form-control" placeholder="Name" name="fullname"
                                                    value="<?php echo htmlspecialchars($editival['customer_name'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Phone number</label>
                                                <input type="text" class="form-control" placeholder="Phone number" name="phonenumber"
                                                    value="<?php echo htmlspecialchars($editival['customer_number'] ?? ''); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Email address</label>
                                                <input type="text" class="form-control" placeholder="Email address" name="emailaddress"
                                                    value="<?php echo htmlspecialchars($editival['customer_email'] ?? ''); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Date of Birth</label>
                                                <input type="date" class="form-control" name="dob"
                                                    value="<?php echo htmlspecialchars($editival['customer_dob'] ?? ''); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Gender</label>
                                                <select class="form-control" name="gender">
                                                    <option value="">Select Gender</option>
                                                    <option value="male" <?php echo ($editival['customer_gender'] ?? '') == 'male' ? 'selected' : ''; ?>>Male</option>
                                                    <option value="female" <?php echo ($editival['customer_gender'] ?? '') == 'female' ? 'selected' : ''; ?>>Female</option>
                                                    <option value="other" <?php echo ($editival['customer_gender'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Marital Status</label>
                                                <select class="form-control" name="marital">
                                                    <option value="">Select Marital Status</option>
                                                    <option value="single" <?php echo ($editival['customer_marital_status'] ?? '') == 'single' ? 'selected' : ''; ?>>Single</option>
                                                    <option value="married" <?php echo ($editival['customer_marital_status'] ?? '') == 'married' ? 'selected' : ''; ?>>Married</option>
                                                    <option value="divorced" <?php echo ($editival['customer_marital_status'] ?? '') == 'divorced' ? 'selected' : ''; ?>>Divorced</option>
                                                    <option value="widowed" <?php echo ($editival['customer_marital_status'] ?? '') == 'widowed' ? 'selected' : ''; ?>>Widowed</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Nationality</label>
                                                <select class="form-control" name="country">
                                                    <option value="">Select Country</option>
                                                    <option value="india" <?php echo ($editival['customer_nationality'] ?? '') == 'india' ? 'selected' : ''; ?>>Indian</option>
                                                    <option value="us" <?php echo ($editival['customer_nationality'] ?? '') == 'us' ? 'selected' : ''; ?>>American</option>
                                                    <option value="uk" <?php echo ($editival['customer_nationality'] ?? '') == 'uk' ? 'selected' : ''; ?>>British</option>
                                                    <option value="canada" <?php echo ($editival['customer_nationality'] ?? '') == 'canada' ? 'selected' : ''; ?>>Canadian</option>
                                                    <option value="australia" <?php echo ($editival['customer_nationality'] ?? '') == 'australia' ? 'selected' : ''; ?>>Australian</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>State</label>
                                                <select class="form-control" name="state">
                                                    <option value="">Select State</option>
                                                    <option value="Andhra Pradesh" <?php echo ($editival['customer_state'] ?? '') == 'Andhra Pradesh' ? 'selected' : ''; ?>>Andhra Pradesh</option>
                                                    <option value="Arunachal Pradesh" <?php echo ($editival['customer_state'] ?? '') == 'Arunachal Pradesh' ? 'selected' : ''; ?>>Arunachal Pradesh</option>
                                                    <option value="Assam" <?php echo ($editival['customer_state'] ?? '') == 'Assam' ? 'selected' : ''; ?>>Assam</option>
                                                    <option value="Bihar" <?php echo ($editival['customer_state'] ?? '') == 'Bihar' ? 'selected' : ''; ?>>Bihar</option>
                                                    <option value="Chhattisgarh" <?php echo ($editival['customer_state'] ?? '') == 'Chhattisgarh' ? 'selected' : ''; ?>>Chhattisgarh</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>District</label>
                                                <select class="form-control" name="dist">
                                                    <option value="">Select District</option>
                                                    <option value="Gopalganj" <?php echo ($editival['customer_district'] ?? '') == 'Gopalganj' ? 'selected' : ''; ?>>Gopalganj</option>
                                                    <option value="Araria" <?php echo ($editival['customer_district'] ?? '') == 'Araria' ? 'selected' : ''; ?>>Araria</option>
                                                    <option value="Arwal" <?php echo ($editival['customer_district'] ?? '') == 'Arwal' ? 'selected' : ''; ?>>Arwal</option>
                                                    <option value="Aurangabad" <?php echo ($editival['customer_district'] ?? '') == 'Aurangabad' ? 'selected' : ''; ?>>Aurangabad</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Local Address</label>
                                                <input type="text" class="form-control" name="address" placeholder="Enter Your Local Address"
                                                    value="<?php echo htmlspecialchars($editival['customer_local_address'] ?? ''); ?>" />
                                            </div>
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

                <!-- Footer -->
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <?php require('include/footer.php'); ?>
                    </div>
                </div>
            </div>
            <!-- End Container fluid -->
        </div>
        <!-- End Page wrapper -->
    </div>
    <!-- End Wrapper -->

    <!-- All Jquery -->
    <script src="node_modules/jquery/jquery.min.js"></script>
    <script src="js/jquery-validation/jquery.validate.min.js"></script>
    <script src="node_modules/dropify/dist/js/dropify.min.js"></script>
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

    <script>
        $(document).ready(function() {
    const existingUsers = <?php echo json_encode($mailPhone); ?>;
    const currentUserId = '<?php echo $id ?? ''; ?>';

    const allEmails = existingUsers
        .filter(user => user.id != currentUserId)
        .map(user => (user.customer_email || '').toLowerCase().trim());

    const allPhones = existingUsers
        .filter(user => user.id != currentUserId)
        .map(user => (user.customer_number || '').trim());

    // Dropify init
    $('.dropify').dropify();

    // ✅ Custom rule for Dropify fields
    $.validator.addMethod("dropifyRequired", function(value, element) {
        const file = $(element).get(0).files;
        const defaultFile = $(element).attr('data-default-file');
        return (file.length > 0 || defaultFile); // valid if file uploaded OR has default
    }, "This file is required.");

    // ✅ Apply validation
    $("#user-form").validate({
        rules: {
            passport_first: {
                dropifyRequired: true,
                extension: "jpg|jpeg|png"
            },
            passport_second: {
                dropifyRequired: true,
                extension: "jpg|jpeg|png"
            },
            passport_photo: {
                dropifyRequired: true,
                extension: "jpg|jpeg|png"
            },
            visa_stamp: {
                dropifyRequired: true,
                extension: "jpg|jpeg|png"
            },
            fullname: {
                required: true,
                maxlength: 100
            },
            phonenumber: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 15
            },
            emailaddress: {
                required: true,
                email: true
            },
            dob: {
                required: true
            },
            gender: {
                required: true
            },
            marital: {
                required: true
            },
            country: {
                required: true
            },
            state: {
                required: true
            },
            dist: {
                required: true
            },
            address: {
                required: true,
                maxlength: 255
            }
        },
        messages: {
            passport_first: "Please upload the first page of your passport.",
            passport_second: "Please upload the second page of your passport.",
            passport_photo: "Please upload a passport-size photo.",
            visa_stamp: "Please upload the visa stamp photo.",
            fullname: "Enter your full name.",
            phonenumber: {
                required: "Enter a valid phone number."
            },
            emailaddress: {
                required: "Enter a valid email address."
            },
            dob: "Select your date of birth.",
            gender: "Select your gender.",
            marital: "Select your marital status.",
            country: "Select your nationality.",
            state: "Select your state.",
            dist: "Select your district.",
            address: "Enter your local address."
        },
        errorPlacement: function(error, element) {
            if (element.attr("type") === "file") {
                error.insertAfter(element.closest('.dropify-wrapper'));
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        },
        submitHandler: function(form) {
            $('.duplicate-error').remove(); // Remove old messages

            const email = $('[name="emailaddress"]').val().trim().toLowerCase();
            const phone = $('[name="phonenumber"]').val().trim();

            let hasError = false;

            if (allEmails.includes(email)) {
                $('[name="emailaddress"]').addClass('is-invalid')
                    .after('<span class="text-danger duplicate-error">This email already exists.</span>');
                hasError = true;
            }

            if (allPhones.includes(phone)) {
                $('[name="phonenumber"]').addClass('is-invalid')
                    .after('<span class="text-danger duplicate-error">This phone number already exists.</span>');
                hasError = true;
            }

            if (!hasError) {
                form.submit();
            }
        }
    });

    // Validate live on typing
    function liveCheck(input, list, type) {
        const val = input.val().trim().toLowerCase();
        const exists = list.includes(val);
        input.next('.duplicate-error').remove();
        if (exists) {
            input.addClass('is-invalid')
                .after(`<span class="text-danger duplicate-error">${type} already exists.</span>`);
        } else {
            input.removeClass('is-invalid');
        }
    }

    $('[name="emailaddress"]').on('input paste', function() {
        liveCheck($(this), allEmails, 'Email');
    });

    $('[name="phonenumber"]').on('input paste', function() {
        liveCheck($(this), allPhones, 'Phone number');
    });

    // Validate file on change
    $('.dropify').on('change', function() {
        $(this).valid();
    });
});

    </script>
</body>

</html>
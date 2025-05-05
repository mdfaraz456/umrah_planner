<?php
ob_start();
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config/config.php';
require 'functions/authentication.php';
require 'functions/umrahFunction.php';

$auth = new Authentication();
$auth->checkSession();

$db = new dbClass();
$vendorObj = new User();

$id = isset($_REQUEST['id']) ? base64_decode($_REQUEST['id']) : '';


$editival = $vendorObj->getUserById($id);
$mailPhone = $vendorObj->getAllUser('Vendor');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submissionType']) && in_array($_POST['submissionType'], ['Submit', 'Update'])) {
        $id = !empty($_POST['eId']) ? $_POST['eId'] : null;
        $name = $db->addStr($_POST['name'] ?? '');
        $email = $db->addStr($_POST['email'] ?? '');
        $phone = $db->addStr($_POST['phone'] ?? '');
        $altPhone = $db->addStr($_POST['alt-phone'] ?? '');
        $account = $db->addStr($_POST['account'] ?? '');
        $ifsc = $db->addStr($_POST['ifsc'] ?? '');
        $branch = $db->addStr($_POST['branch'] ?? '');
        $country = $db->addStr($_POST['country'] ?? '');
        $state = $db->addStr($_POST['state'] ?? '');
        $status = $_POST['status'] ?? '';
        $district = $db->addStr($_POST['district'] ?? '');
        $address = $db->addStr($_POST['address'] ?? '');
        $submissionType = $_POST['submissionType'];

        $emailError = '';
        $phoneError = '';
        foreach ($mailPhone as $user) {
            if (!empty($id)) {
                if ($user['user_email'] === $email && $user['user_id'] !== $id && $email !== ($editival['user_email'] ?? '')) {
                    $emailError = 'This email address is already registered.';
                }
                if ($user['user_mobile'] === $phone && $user['user_id'] !== $id && $phone !== ($editival['user_mobile'] ?? '')) {
                    $phoneError = 'This phone number is already registered.';
                }
            } else {
                if ($user['user_email'] === $email) {
                    $emailError = 'This email address is already registered.';
                }
                if ($user['user_mobile'] === $phone) {
                    $phoneError = 'This phone number is already registered.';
                }
            }
        }

        if (!$emailError && !$phoneError) {
            $result = $vendorObj->saveUserData(
                $name,
                $email,
                $phone,
                $altPhone,
                $account,
                $ifsc,
                $branch,
                $country,
                $state,
                $status,
                $district,
                $address,
                'VUMP',
                'VNDRUMP',
                'Vendor',
                $submissionType,
                $id
            );

            if ($result !== false) {
                $_SESSION['msg'] = ($submissionType === 'Submit') ? 'Vendor has been added successfully.' : 'Vendor has been updated successfully.';

                $subject = "Vendor Registration: $name";
                $to = 'dzimran234@gmail.com';

                $passwordInfo = '';
                if ($submissionType === 'Submit' && is_string($result)) {
                    $password = $result;
                    $passwordInfo = "<tr><td class='label'>Password:</td><td>$password</td></tr>";
                }

                $year = date('Y');
                $emailBody = <<<EOD
                <html>
                <head>
                <style>
                    body { font-family: 'Helvetica Neue', sans-serif; background-color: #f3f6fb; }
                    .container { max-width: 680px; margin: 40px auto; background: #fff; border-radius: 10px; box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
                    .header { background: linear-gradient(135deg, #0d4c72, #086a82); padding: 20px 30px; color: #fff; text-align: center; }
                    .body { padding: 30px 35px; font-size: 15px; line-height: 1.7; color: #333; }
                    .info-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    .info-table td { padding: 10px 0; }
                    .info-table .label { font-weight: bold; color: #555; width: 150px; }
                    .thank-you { background: #e7f4f8; padding: 20px; margin-top: 25px; border-left: 5px solid #0d4c72; border-radius: 5px; }
                    .footer { text-align: center; font-size: 12px; color: #888; padding: 18px; background: #f0f0f0; }
                </style>
                </head>
                <body>
                <div class="container">
                    <div class="header">
                        <h1>UMRAH PLANNER</h1>
                        <p>Your Trusted Partner for a Spiritual Journey</p>
                    </div>
                    <div class="body">
                        <h2>Thank you for registering, $name!</h2>
                        <p>We’ve successfully received your vendor registration. Below are your submitted details:</p>
                        <table class="info-table">
                            <tr><td class="label">Full Name:</td><td>$name</td></tr>
                            <tr><td class="label">Email Address:</td><td>$email</td></tr> 
                            $passwordInfo
                        </table>
                        <div class="thank-you">We’re excited to have you on board. One of our representatives will contact you shortly with the next steps.</div>
                    </div>
                    <div class="footer">© $year UMRAH PLANNER. This is an automated email — please do not reply.</div>
                </div>
                </body>
                </html>
                EOD;

                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8\r\n";
                $headers .= "From: Umrah Planner <noreply@yourdomain.com>\r\n";

                mail($to, $subject, $emailBody, $headers);

                header("Location: all-vendor.php");
                exit;
            } else {
                $_SESSION['errmsg'] = 'Sorry, some error occurred while saving vendor data.';
                error_log('Failed to save vendor data: ' . print_r($_POST, true));
            }
        } else {
            $_SESSION['form_errors'] = [
                'email' => $emailError,
                'phone' => $phoneError
            ];
        }
    }
}


unset($_SESSION['form_errors']);
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
</style>

<body class="fix-header fix-sidebar card-no-border">
    <div id="main-wrapper">
        <?php require('include/header.php'); ?>
        <?php require('include/sidebar.php'); ?>
        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="row page-titles">
                    <div class="col-md-5 align-self-center">
                        <?php if (empty($id)): ?>
                            <h3 class="text-themecolor">Add New Vendor</h3>
                        <?php else: ?>
                            <h3 class="text-themecolor">Update Vendor</h3>
                        <?php endif; ?>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                            <?php if (empty($id)): ?>
                                <li class="breadcrumb-item active">Add New Vendor</li>
                            <?php else: ?>
                                <li class="breadcrumb-item active">Update Vendor</li>
                            <?php endif; ?>
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
                                <form id="user-vendor" class="form pt-3" method="POST" enctype="multipart/form-data">

                                    <input type="hidden" name="eId" value="<?php echo htmlspecialchars($id ?? ''); ?>">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" id="name" name="name" class="form-control" placeholder="Name" value="<?php echo htmlspecialchars($editival['user_name'] ?? ''); ?>" required />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" id="email" name="email" class="form-control <?php echo !empty($_SESSION['form_errors']['email']) ? 'is-invalid' : ''; ?>" placeholder="Email" value="<?php echo htmlspecialchars($editival['user_email'] ?? ''); ?>" required />
                                                <?php if (!empty($_SESSION['form_errors']['email'])): ?>
                                                    <span class="error"><?php echo $_SESSION['form_errors']['email']; ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="phone">Phone Number</label>
                                                <input type="text" id="phone" name="phone" class="form-control <?php echo !empty($_SESSION['form_errors']['phone']) ? 'is-invalid' : ''; ?>" placeholder="Phone Number" value="<?php echo htmlspecialchars($editival['user_mobile'] ?? ''); ?>" required pattern="\d{10}" />
                                                <?php if (!empty($_SESSION['form_errors']['phone'])): ?>
                                                    <span class="error"><?php echo $_SESSION['form_errors']['phone']; ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="alt-phone">Alternate Number</label>
                                                <input type="text" id="alt-phone" name="alt-phone" class="form-control" placeholder="Alternate Number" value="<?php echo htmlspecialchars($editival['user_alternate_number'] ?? ''); ?>" pattern="\d{10}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="account">Account Number</label>
                                                <input type="text" id="account" name="account" class="form-control" placeholder="Account Number" value="<?php echo htmlspecialchars($editival['user_account_number'] ?? ''); ?>" required />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="ifsc">IFSC</label>
                                                <input type="text" id="ifsc" name="ifsc" class="form-control" placeholder="IFSC" style="text-transform:uppercase;" value="<?php echo htmlspecialchars($editival['ifsc_code'] ?? ''); ?>" required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="branch">Branch</label>
                                                <input type="text" id="branch" name="branch" class="form-control" placeholder="Branch" value="<?php echo htmlspecialchars($editival['branch'] ?? ''); ?>" required />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="country">Country</label>
                                                <select class="form-control" name="country" required>
                                                    <option value="">Select Country</option>
                                                    <option value="United States" <?php echo ($editival['user_country'] ?? '') == 'United States' ? 'selected' : ''; ?>>United States</option>
                                                    <option value="Canada" <?php echo ($editival['user_country'] ?? '') == 'Canada' ? 'selected' : ''; ?>>Canada</option>
                                                    <option value="India" <?php echo ($editival['user_country'] ?? '') == 'India' ? 'selected' : ''; ?>>India</option>
                                                    <option value="Australia" <?php echo ($editival['user_country'] ?? '') == 'Australia' ? 'selected' : ''; ?>>Australia</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="state">State</label>
                                                <select class="form-control" name="state" required>
                                                    <option value="">Select State</option>
                                                    <option value="Andhra Pradesh" <?php echo ($editival['user_state'] ?? '') == 'Andhra Pradesh' ? 'selected' : ''; ?>>Andhra Pradesh</option>
                                                    <option value="Arunachal Pradesh" <?php echo ($editival['user_state'] ?? '') == 'Arunachal Pradesh' ? 'selected' : ''; ?>>Arunachal Pradesh</option>
                                                    <option value="Assam" <?php echo ($editival['user_state'] ?? '') == 'Assam' ? 'selected' : ''; ?>>Assam</option>
                                                    <option value="Bihar" <?php echo ($editival['user_state'] ?? '') == 'Bihar' ? 'selected' : ''; ?>>Bihar</option>
                                                    <option value="Chhattisgarh" <?php echo ($editival['user_state'] ?? '') == 'Chhattisgarh' ? 'selected' : ''; ?>>Chhattisgarh</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="district">District</label>
                                                <select class="form-control" name="district" required>
                                                    <option value="">Select District</option>
                                                    <option value="Gopalganj" <?php echo ($editival['user_district'] ?? '') == 'Gopalganj' ? 'selected' : ''; ?>>Gopalganj</option>
                                                    <option value="Araria" <?php echo ($editival['user_district'] ?? '') == 'Araria' ? 'selected' : ''; ?>>Araria</option>
                                                    <option value="Arwal" <?php echo ($editival['user_district'] ?? '') == 'Arwal' ? 'selected' : ''; ?>>Arwal</option>
                                                    <option value="Aurangabad" <?php echo ($editival['user_district'] ?? '') == 'Aurangabad' ? 'selected' : ''; ?>>Aurangabad</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="address">Local Address</label>
                                                <input type="text" id="address" name="address" class="form-control" value="<?php echo htmlspecialchars($editival['user_address'] ?? ''); ?>" placeholder="Enter Your Local Address" required />
                                            </div>
                                        </div>
                                    </div>
                                    <label for="">Status</label>
                                    <div class="flex1">
                                        <fieldset class="controls">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" value="1" name="status" required id="styled_radio1" class="custom-control-input" <?php echo (isset($editival['is_active']) && $editival['is_active'] == 1) || !isset($editival['is_active']) ? 'checked' : ''; ?> />
                                                <label class="custom-control-label" for="styled_radio1">Active</label>
                                            </div>
                                        </fieldset>

                                        <fieldset>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" value="0" name="status" id="styled_radio2" class="custom-control-input" <?php echo isset($editival['is_active']) && $editival['is_active'] == 0 ? 'checked' : ''; ?> />
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
    <script src="js/dashboard1.js"></script>
    <script src="node_modules/styleswitcher/jQuery.style.switcher.js"></script>

    <script>
        $(document).ready(function() {
            console.log('Document Ready');

            // Existing users from PHP
            let existingUsers = <?php echo json_encode($mailPhone); ?>;
            const currentUserId = '<?php echo $id ?? ''; ?>';
            const isEditMode = '<?php echo !empty($id) ? 'true' : 'false'; ?>';
            const editivalEmail = '<?php echo isset($editival['user_email']) ? strtolower(trim($editival['user_email'])) : ''; ?>';
            const editivalPhone = '<?php echo isset($editival['user_mobile']) ? trim($editival['user_mobile']) : ''; ?>';

            // Track original values and change status
            let originalPhone = editivalPhone;
            let originalEmail = editivalEmail;

            let phoneChanged = false;
            let emailChanged = false;

            console.log('Is Edit Mode:', isEditMode);
            console.log('Editival Email:', editivalEmail);
            console.log('Editival Phone:', editivalPhone);
            console.log('Current User ID:', currentUserId);

            function validatePhoneNumber() {
                const phoneInput = $('[name="phone"]');
                const phone = phoneInput.val().trim();
                let phoneError = false;

                phoneInput.removeClass('is-invalid');
                $('.phone-error').remove();

                if (phone && existingUsers.some(user => user.user_mobile === phone && user.user_id !== currentUserId)) {
                    phoneInput.addClass('is-invalid')
                        .after('<span class="error phone-error">This phone number is already registered.</span>');
                    phoneError = true;
                }

                return phoneError;
            }

            function validateEmail() {
                const emailInput = $('[name="email"]');
                const email = emailInput.val().trim().toLowerCase();
                let emailError = false;

                emailInput.removeClass('is-invalid');
                $('.email-error').remove();

                if (email && existingUsers.some(user => user.user_email === email && user.user_id !== currentUserId)) {
                    emailInput.addClass('is-invalid')
                        .after('<span class="error email-error">This email address is already registered.</span>');
                    emailError = true;
                }

                return emailError;
            }

            function validateDuplicateFields() {
                let phoneError = false;
                let emailError = false;

                if (phoneChanged) phoneError = validatePhoneNumber();
                if (emailChanged) emailError = validateEmail();

                return phoneError || emailError;
            }

            // Real-time phone validation
            $('[name="phone"]').on('input change', function() {
                const phoneInput = $(this);
                const phone = phoneInput.val().trim();

                phoneChanged = (phone !== originalPhone);

                if (!phoneChanged) {
                    phoneInput.removeClass('is-invalid');
                    $('.phone-error').remove();
                    return;
                }

                validatePhoneNumber();
            });

            // Real-time email validation
            $('[name="email"]').on('input change', function() {
                const emailInput = $(this);
                const email = emailInput.val().trim().toLowerCase();

                emailChanged = (email !== originalEmail);

                if (!emailChanged) {
                    emailInput.removeClass('is-invalid');
                    $('.email-error').remove();
                    return;
                }

                validateEmail();
            });

            // jQuery Validate for form
            $("#user-vendor").validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 100
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    phone: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 15
                    },
                    'alt-phone': {
                        digits: true,
                        minlength: 10,
                        maxlength: 15
                    },
                    account: {
                        required: true,
                        minlength: 9,
                        maxlength: 18
                    },
                    ifsc: {
                        required: true
                    },
                    branch: {
                        required: true
                    },
                    country: {
                        required: true
                    },
                    state: {
                        required: true
                    },
                    district: {
                        required: true
                    },
                    address: {
                        required: true,
                        maxlength: 255
                    }
                },
                messages: {
                    name: "Enter your name.",
                    email: "Enter a valid email address.",
                    phone: "Enter a valid phone number.",
                    'alt-phone': "Enter a valid alternate phone number.",
                    account: "Enter your account number.",
                    ifsc: "Enter your IFSC code.",
                    branch: "Enter your branch name.",
                    country: "Please select a country.",
                    state: "Please select a state.",
                    district: "Please select a district.",
                    address: "Enter your local address."
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element).addClass('error');
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    console.log('Form validation passed. Checking for duplicates...');
                    if (!validateDuplicateFields()) {
                        $(form).find('button[type="submit"]').prop('disabled', true).text('Submitting...');
                        form.submit();
                    } else {
                        console.log('Duplicate values found. Form submission stopped.');
                    }
                }
            });
        });
    </script>





</body>

</html>
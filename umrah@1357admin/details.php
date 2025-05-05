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

$db = new dbClass();

$userObj = new User(); 

$id = isset($_REQUEST['id']) ? base64_decode($_REQUEST['id']) : NULL;

$details = $userObj->getUserById($id);

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

    <link href="node_modules/Magnific-Popup-master/dist/magnific-popup.css" rel="stylesheet">

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

    .img-flex {
        display: flex;
        justify-content: space-between;
    }

    .card-title {
        background: linear-gradient(to right, rgb(108, 213, 243) 0, #faf5f5 100%);
        padding: 10px;
        padding-left: 5px;
        border-radius: 3px;
        color: white;
    }

    @media print {

        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print {
            display: none !important;
        }

        .col-md-3 {
            width: 25%;
        }

        h5 {
            line-height: 18px;
            font-size: 24px !important;
            font-weight: 400;
        }

        p {
            font-size: 20px !important;
            color: #455a64;
        }

        h4 {
            font-size: 24px !important;
        }


    }

    .m-t-60{
        margin-top: 70px;
    }

    .img-responsive {
        width: 100%;
        height: 100% !important;
        display: inline-block;
    }

    .col-4 h5{
        display: flex;
        gap: 10px;
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

                <div class="row page-titles">
                    <div class="col-md-6 align-self-center">
                        <h3 class="text-themecolor">Customer Details</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                            <li class="breadcrumb-item active">Customer Details</li>
                        </ol>
                    </div>

                    <div class="col-md-6 align-self-center printer">
                        <img src="images/printer.png" alt="" onclick="printContent()">
                    </div>

                    <div>
                        <button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10">
                            <i class="ti-settings text-white"></i>
                        </button>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->

                <div class="row"> 
                    <div class="col-lg-12 col-xlg-12 col-md-8" id="printyContent">
                        <div class="card">
                            <div class="card-body">
                                
                                <h4 class="card-title">
                                    Customer Details
                                </h4>

                                <div class="row mt-30 paragrph">
                                    <div class="col-4">
                                        <h5><span><img src="images/name.png" alt="" width="18px"></span> Full Name</h5>
                                        <p><?php echo $details['user_name'] ?? ''; ?></p>
                                    </div>
                                    <div class="col-4">
                                        <h5><span><img src="images/email.png" alt="" width="18px"></span> Email</h5>
                                        <p><?php echo $details['user_email'] ?? ''; ?></p>
                                    </div>
                                    <div class="col-4">
                                        <h5> <span><img src="images/phone.png" alt="" width="18px"></span> Phone Number</h5>
                                        <p><?php echo $details['user_mobile'] ?? ''; ?></p>
                                    </div>

                                    <div class="col-4 pt-3">
                                        <h5> <span><img src="images/phone.png" alt="" width="18px"></span> Alternate Number</h5>

                                        <p><?php echo $details['user_alternate_number'] ?? ''; ?></p>

                                    </div>
                                    <div class="col-4 pt-3">
                                        <h5> <span><img src="images/bank.png" alt="" width="18px"></span> Account Number</h5> 
                                        <p><?php echo $details['user_account_number'] ?? ''; ?></p>

                                    </div>
                                    <div class="col-4 pt-3">
                                        <h5> <span><img src="images/online-banking.png" alt="" width="18px"></span> IFSC</h5> 
                                        <p><?php echo $details['ifsc_code'] ?? ''; ?></p> 
                                    </div>

                                    <div class="col-4 pt-3">
                                        <h5> <span><img src="images/bank.png" alt="" width="18px"></span> Branch</h5> 
                                        <p><?php echo $details['branch'] ?? ''; ?></p> 
                                    </div>

                                    <div class="col-4 pt-3">
                                        <h5> <span><img src="images/countries.png" alt="" width="18px"></span> Country</h5> 
                                        <p><?php echo $details['user_country'] ?? ''; ?></p> 
                                    </div>

                                    <div class="col-4 pt-3">
                                        <h5> <span><img src="images/state.png" alt="" width="18px"></span> State</h5> 
                                        <p><?php echo $details['user_state'] ?? ''; ?></p> 
                                    </div>

                                    <div class="col-4 pt-3">
                                        <h5> <span><img src="images/dist.png" alt="" width="18px"></span> District</h5> 
                                        <p><?php echo $details['user_district'] ?? ''; ?></p> 
                                    </div>

                                    <div class="col-4 pt-3">
                                        <h5> <span><img src="images/local.png" alt="" width="18px"></span> Local Address</h5> 
                                        <p><?php echo $details['user_address'] ?? ''; ?></p> 
                                    </div>
                                </div>

                                
                                </div>
                            </div>
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
    <script src="js/sticky.js"></script>
    <script src="js/popup-min.js"></script>
    <script src="js/popup-init.js"></script>
    <script>
        // This is for the sticky sidebar
        $(".stickyside").stick_in_parent({
            offset_top: 100,
        });
        $(".stickyside a").on("click", function() {
            $("html, body").animate({
                    scrollTop: $($(this).attr("href")).offset().top - 100,
                },
                500
            );
            return false;
        });
        // This is auto select left sidebar
        // Cache selectors
        // Cache selectors
        var lastId,
            topMenu = $(".stickyside"),
            topMenuHeight = topMenu.outerHeight(),
            // All list items
            menuItems = topMenu.find("a"),
            // Anchors corresponding to menu items
            scrollItems = menuItems.map(function() {
                var item = $($(this).attr("href"));
                if (item.length) {
                    return item;
                }
            });

        // Bind click handler to menu items

        // Bind to scroll
        $(window).scroll(function() {
            // Get container scroll position
            var fromTop = $(this).scrollTop() + topMenuHeight - 250;

            // Get id of current scroll item
            var cur = scrollItems.map(function() {
                if ($(this).offset().top < fromTop) return this;
            });
            // Get the id of the current element
            cur = cur[cur.length - 1];
            var id = cur && cur.length ? cur[0].id : "";

            if (lastId !== id) {
                lastId = id;
                // Set/remove active class
                menuItems
                    .removeClass("active")
                    .filter("[href='#" + id + "']")
                    .addClass("active");
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#download123').DataTable({
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

    <script>
        function printContent() {
            const printyContent = document.getElementById('printyContent').innerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = printyContent;

            window.print();

            document.body.innerHTML = originalContent;

            window.location.reload();
        }
    </script>

    <script>
        document.querySelectorAll('.download-icon').forEach(icon => {
            icon.addEventListener('click', function() {
                const imageUrl = this.getAttribute('data-image');
                const link = document.createElement('a');
                link.href = imageUrl;
                link.download = 'passport_photo.jpg'; // Default filename
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        });
    </script>



</body>

</html>
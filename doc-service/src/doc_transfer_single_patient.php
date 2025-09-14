<?php
session_start();
include('assets/inc/config.php');

if (isset($_POST['transfer_patient'])) {
    $t_pat_number = $_POST['t_pat_number'];
    $t_pat_name = $_POST['t_pat_name'];
    $t_date = $_POST['t_date'];
    $t_hospital = $_POST['t_hospital'];
    $t_status = $_POST['t_status'];

    // SQL to insert captured values
    $query = "INSERT INTO patient_transfers (t_pat_number, t_pat_name, t_date, t_hospital, t_status) VALUES(?,?,?,?,?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('sssss', $t_pat_number, $t_pat_name, $t_date, $t_hospital, $t_status);
    $stmt->execute();

    // Success or failure message
    if ($stmt) {
        $success = "Patient Transferred Successfully!";
    } else {
        $err = "Please Try Again Or Try Later.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<!-- Head Section -->
<?php include('assets/inc/head.php'); ?>

<body>
    <!-- Begin page -->
    <div id="wrapper">

        <!-- Topbar Start -->
        <?php include("assets/inc/nav.php"); ?>
        <!-- end Topbar -->

        <!-- Left Sidebar Start -->
        <?php include("assets/inc/sidebar.php"); ?>
        <!-- Left Sidebar End -->

        <!-- Start Page Content here -->
        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- Start Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="doc_dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Patients</a></li>
                                        <li class="breadcrumb-item active">Transfer Patients</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Transfer Patient To A Referral Facility</h4>
                            </div>
                        </div>
                    </div>     
                    <!-- end page title -->

                    <!-- Form Row -->
                    <?php
                    // Fetch patient details based on the patient number passed in the URL
                    if (isset($_GET['pat_number']) && !empty($_GET['pat_number'])) {
                        $pat_number = $_GET['pat_number'];

                        $ret = "SELECT * FROM patients WHERE pat_number = ?";
                        $stmt = $mysqli->prepare($ret);
                        $stmt->bind_param('i', $pat_number);
                        $stmt->execute();
                        $res = $stmt->get_result();

                        // Check if the patient exists
                        if ($res->num_rows > 0) {
                            // Fetch patient data
                            $row = $res->fetch_object();
                    ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Fill All Fields</h4>
                                    <!-- Patient Transfer Form -->
                                    <form method="post">
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label for="inputEmail4" class="col-form-label">Patient Name</label>
                                                <input type="text" required="required" value="<?php echo $row->pat_fname . ' ' . $row->pat_lname; ?>" name="t_pat_name" class="form-control" id="inputEmail4" placeholder="Patient's Full Name">
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputEmail4" class="col-form-label">Referral Hospital</label>
                                                <input type="text" required="required" name="t_hospital" class="form-control" id="inputEmail4" placeholder="Referral/Transfer Hospital">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputPassword4" class="col-form-label">Transfer Date</label>
                                                <input required="required" type="date" name="t_date" class="form-control" id="inputPassword4" placeholder="DD/MM/YYYY">
                                            </div>
                                            <div class="form-group col-md-6" style="display:none">
                                                <label for="inputPassword4" class="col-form-label">Patient Number</label>
                                                <input required="required" type="text" name="t_pat_number" value="<?php echo $row->pat_number; ?>" class="form-control" id="inputPassword4" placeholder="">
                                            </div>
                                        </div>

                                        <div class="form-group" style="display:none">
                                            <label for="inputAddress" class="col-form-label">Transfer Status</label>
                                            <input required="required" type="text" value="Success" class="form-control" name="t_status" id="inputAddress" placeholder="Patient's Address">
                                        </div>

                                        <button type="submit" name="transfer_patient" class="ladda-button btn btn-success" data-style="expand-right">Transfer Patient</button>

                                    </form>
                                    <!-- End Patient Form -->
                                </div> <!-- end card-body -->
                            </div> <!-- end card -->
                        </div> <!-- end col -->
                    </div>
                    <?php
                        } else {
                            // Patient not found
                            echo "<div class='alert alert-danger'>No patient found with the provided patient number.</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Invalid patient number.</div>";
                    }
                    ?>
                    <!-- end row -->

                </div> <!-- container -->

            </div> <!-- content -->

            <!-- Footer Start -->
            <?php include('assets/inc/footer.php'); ?>
            <!-- end Footer -->

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- Vendor js -->
    <script src="assets/js/vendor.min.js"></script>

    <!-- App js-->
    <script src="assets/js/app.min.js"></script>

    <!-- Loading buttons js -->
    <script src="assets/libs/ladda/spin.js"></script>
    <script src="assets/libs/ladda/ladda.js"></script>

    <!-- Buttons init js-->
    <script src="assets/js/pages/loading-btn.init.js"></script>

</body>

</html>

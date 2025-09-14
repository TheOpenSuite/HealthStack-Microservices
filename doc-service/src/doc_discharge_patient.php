<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$doc_id = $_SESSION['doc_id'];

// If the doctor wants to discharge a patient, update the patient's discharge status
if (isset($_GET['pat_id'])) {
    $pat_id = $_GET['pat_id'];

    // Update the discharge status of the patient to 'Discharged' if they are being discharged
    if (isset($_GET['action']) && $_GET['action'] == 'discharge') {
        $discharge_query = "UPDATE patients SET pat_discharge_status = 'Discharged' WHERE pat_id = ?";
        $stmt = $mysqli->prepare($discharge_query);
        $stmt->bind_param('i', $pat_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $success_msg = "Patient discharged successfully.";
        } else {
            $error_msg = "Failed to discharge patient. Please try again.";
        }
    }

    // If the patient is returning, update their status to 'Inpatient' or 'Returned'
    elseif (isset($_GET['action']) && $_GET['action'] == 'return') {
        // You can update the status to 'Inpatient' or create a new record for the patient
        $return_query = "UPDATE patients SET pat_discharge_status = 'Returned' WHERE pat_id = ?";
        $stmt = $mysqli->prepare($return_query);
        $stmt->bind_param('i', $pat_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $success_msg = "Patient has returned and re-admitted successfully.";
        } else {
            $error_msg = "Failed to re-admit patient. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    
<?php include('assets/inc/head.php');?>

<body>

    <!-- Begin page -->
    <div id="wrapper">

        <!-- Topbar Start -->
        <?php include('assets/inc/nav.php');?>
        <!-- end Topbar -->

        <!-- ========== Left Sidebar Start ========== -->
        <?php include("assets/inc/sidebar.php");?>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Patients</a></li>
                                        <li class="breadcrumb-item active">Discharge / Re-admit Patients</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Discharge or Re-admit Patients</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title --> 

                    <!-- Show success or error message -->
                    <?php if (isset($success_msg)) { ?>
                        <div class="alert alert-success">
                            <?php echo $success_msg; ?>
                        </div>
                    <?php } elseif (isset($error_msg)) { ?>
                        <div class="alert alert-danger">
                            <?php echo $error_msg; ?>
                        </div>
                    <?php } ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card-box">
                                <h4 class="header-title">List of Patients Assigned to You</h4>
                                <div class="mb-2">
                                    <div class="row">
                                        <div class="col-12 text-sm-center form-inline">
                                            <div class="form-group mr-2" style="display:none">
                                                <select id="demo-foo-filter-status" class="custom-select custom-select-sm">
                                                    <option value="">Show all</option>
                                                    <option value="Discharged">Discharged</option>
                                                    <option value="OutPatients">OutPatients</option>
                                                    <option value="InPatients">InPatients</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <input id="demo-foo-search" type="text" placeholder="Search" class="form-control form-control-sm" autocomplete="on">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="demo-foo-filtering" class="table table-bordered toggle-circle mb-0" data-page-size="7">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th data-toggle="true">Patient Name</th>
                                                <th data-hide="phone">Patient Number</th>
                                                <th data-hide="phone">Patient Address</th>
                                                <th data-hide="phone">Patient Category</th>
                                                <th data-hide="phone">Discharge Status</th>
                                                <th data-hide="phone">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                // Fetch all patients assigned to the doctor
                                                $ret = "SELECT * FROM patients WHERE pat_doc_id = ?"; 
                                                $stmt = $mysqli->prepare($ret);
                                                $stmt->bind_param('i', $doc_id); 
                                                $stmt->execute();
                                                $res = $stmt->get_result();
                                                $cnt = 1;
                                                while ($row = $res->fetch_object()) {
                                            ?>
                                            <tr>
                                                <td><?php echo $cnt; ?></td>
                                                <td><?php echo $row->pat_fname; ?> <?php echo $row->pat_lname; ?></td>
                                                <td><?php echo $row->pat_number; ?></td>
                                                <td><?php echo $row->pat_addr; ?></td>
                                                <td><?php echo $row->pat_type; ?></td>
                                                <td><?php echo $row->pat_discharge_status; ?></td>
                                                <td>
                                                    <?php if ($row->pat_discharge_status != 'Discharged') { ?>
                                                        <a href="?pat_id=<?php echo $row->pat_id; ?>&action=discharge" class="badge badge-primary">
                                                            <i class="mdi mdi-check-box-outline"></i> Discharge
                                                        </a>
                                                    <?php } else { ?>
                                                        <a href="?pat_id=<?php echo $row->pat_id; ?>&action=return" class="badge badge-warning">
                                                            <i class="mdi mdi-refresh"></i> Re-admit
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                                $cnt++;
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div> <!-- end .table-responsive-->
                            </div> <!-- end card-box -->
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                </div> <!-- container -->

            </div> <!-- content -->

            <!-- Footer Start -->
            <?php include('assets/inc/footer.php');?>
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

    <!-- Footable js -->
    <script src="assets/libs/footable/footable.all.min.js"></script>

    <!-- Init js -->
    <script src="assets/js/pages/foo-tables.init.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>
    
</body>

</html>

<?php
  session_start();
  include('assets/inc/config.php');
  include('assets/inc/checklogin.php');
  check_login();

  $doc_id=$_SESSION['doc_id'];
  $pat_number = $_GET['pat_number']; // Patient number from the URL
?>

<!DOCTYPE html>
<html lang="en">

<?php include('assets/inc/head.php');?> <!-- Include head -->

<body>

    <!-- Begin page -->
    <div id="wrapper">

        <!-- Topbar Start -->
        <?php include("assets/inc/nav.php");?>
        <!-- end Topbar -->

        <!-- Left Sidebar Start -->
        <?php include("assets/inc/sidebar.php");?>
        <!-- Left Sidebar End -->

                    <?php
                    // Fetch the patient details
                    $ret = "SELECT * FROM patients WHERE pat_number = ?";
                    $stmt = $mysqli->prepare($ret);
                    $stmt->bind_param("s", $pat_number); // Ensure correct binding type
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $row = $res->fetch_object();
                    ?>
                            <!-- Start Page Content here -->
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
                                        <li class="breadcrumb-item active">View Patient Profile</li>
                                    </ol>
                                </div>
                                <h4 class="page-title"><?php echo $row->pat_fname;?> <?php echo $row->pat_lname;?>'s Profile</h4>
                                </div>
                        </div>
                    </div>
                    <!-- end page title -->
                    <div class="row">
                        <div class="col-lg-4 col-xl-4">
                            <div class="card-box text-center">
                                <img src="assets/images/users/patient.png" class="rounded-circle avatar-lg img-thumbnail" alt="profile-image">
                                <div class="text-left mt-3">
                                    <p class="text-muted mb-2 font-13"><strong>Full Name :</strong> <span class="ml-2"><?php echo $row->pat_fname . ' ' . $row->pat_lname;?></span></p>
                                    <p class="text-muted mb-2 font-13"><strong>Mobile :</strong><span class="ml-2"><?php echo $row->pat_phone;?></span></p>
                                    <p class="text-muted mb-2 font-13"><strong>Address :</strong> <span class="ml-2"><?php echo $row->pat_addr;?></span></p>
                                    <p class="text-muted mb-2 font-13"><strong>Date Of Birth :</strong> <span class="ml-2"><?php echo $row->pat_dob;?></span></p>
                                    <p class="text-muted mb-2 font-13"><strong>Age :</strong> <span class="ml-2"><?php echo $row->pat_age;?> Years</span></p>
                                    <p class="text-muted mb-2 font-13"><strong>Ailment :</strong> <span class="ml-2"><?php echo $row->pat_ailment;?></span></p>
                                    <hr>
                                    <p class="text-muted mb-2 font-13"><strong>Date Recorded :</strong> <span class="ml-2"><?php echo date("d/m/Y - h:i", strtotime($row->pat_date_joined));?></span></p>
                                    <hr>
                                </div>
                            </div> <!-- end card-box -->
                        </div> <!-- end col-->

                        <!-- Prescriptions, Vitals, and Lab Records Tab -->
                        <div class="col-lg-8 col-xl-8">
                            <div class="card-box">
                                <ul class="nav nav-pills navtab-bg nav-justified">
                                    <li class="nav-item"><a href="#aboutme" data-toggle="tab" aria-expanded="false" class="nav-link active">Prescription</a></li>
                                    <li class="nav-item"><a href="#timeline" data-toggle="tab" aria-expanded="true" class="nav-link ">Vitals</a></li>
                                    <li class="nav-item"><a href="#settings" data-toggle="tab" aria-expanded="false" class="nav-link">Lab Records</a></li>
                                </ul>

                                <!-- Prescription Tab Content -->
                                <div class="tab-content">
                                    <div class="tab-pane show active" id="aboutme">
                                        <ul class="list-unstyled timeline-sm">
                                            <?php
                                            $pres_ret = "SELECT * FROM prescriptions WHERE pres_pat_number = ?";
                                            $stmt = $mysqli->prepare($pres_ret);
                                            $stmt->bind_param("s", $pat_number);
                                            $stmt->execute();
                                            $pres_res = $stmt->get_result();
                                            while ($pres_row = $pres_res->fetch_object()) {
                                                $presDate = $pres_row->pres_date;
                                            ?>
                                                <li class="timeline-sm-item">
                                                    <span class="timeline-sm-date"><?php echo date("Y-m-d", strtotime($presDate));?></span>
                                                    <h5 class="mt-0 mb-1"><?php echo $pres_row->pres_pat_ailment;?></h5>
                                                    <p class="text-muted mt-2"><?php echo $pres_row->pres_ins;?></p>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>

                                    <!-- Vitals Tab Content -->
                                    <div class="tab-pane show " id="timeline">
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Body Temperature</th>
                                                        <th>Heart Rate/Pulse</th>
                                                        <th>Respiratory Rate</th>
                                                        <th>Blood Pressure</th>
                                                        <th>Date Recorded</th>
                                                    </tr>
                                                </thead>
                                                <?php
                                                $vit_ret = "SELECT * FROM vitals WHERE vit_pat_number = ?";
                                                $stmt = $mysqli->prepare($vit_ret);
                                                $stmt->bind_param("s", $pat_number);
                                                $stmt->execute();
                                                $vit_res = $stmt->get_result();
                                                while ($vit_row = $vit_res->fetch_object()) {
                                                    $vitDate = $vit_row->vit_daterec;
                                                ?>
                                                    <tbody>
                                                        <tr>
                                                            <td><?php echo $vit_row->vit_bodytemp;?>°C</td>
                                                            <td><?php echo $vit_row->vit_heartpulse;?> BPM</td>
                                                            <td><?php echo $vit_row->vit_resprate;?> bpm</td>
                                                            <td><?php echo $vit_row->vit_bloodpress;?> mmHg</td>
                                                            <td><?php echo date("Y-m-d", strtotime($vitDate));?></td>
                                                        </tr>
                                                    </tbody>
                                                <?php } ?>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Lab Records Tab Content -->
                                    <div class="tab-pane" id="settings">
                                        <ul class="list-unstyled timeline-sm">
                                            <?php
                                            $lab_ret = "SELECT * FROM laboratory WHERE lab_pat_number = ?";
                                            $stmt = $mysqli->prepare($lab_ret);
                                            $stmt->bind_param("s", $pat_number);
                                            $stmt->execute();
                                            $lab_res = $stmt->get_result();
                                            while ($lab_row = $lab_res->fetch_object()) {
                                                $labDate = $lab_row->lab_date_rec;
                                            ?>
                                                <li class="timeline-sm-item">
                                                    <span class="timeline-sm-date"><?php echo date("Y-m-d", strtotime($labDate));?></span>
                                                    <h3 class="mt-0 mb-1"><?php echo $lab_row->lab_pat_ailment;?></h3>
                                                    <hr>
                                                    <h5>Laboratory Tests</h5>
                                                    <p class="text-muted mt-2"><?php echo $lab_row->lab_pat_tests;?></p>
                                                    <hr>
                                                    <h5>Laboratory Results</h5>
                                                    <p class="text-muted mt-2"><?php echo $lab_row->lab_pat_results;?></p>
                                                    <hr>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div> <!-- end card-box-->
                        </div> <!-- end col -->
                    </div> <!-- end row -->
                </div> <!-- container -->
            </div> <!-- content -->

            <!-- Footer Start -->
            <?php include('assets/inc/footer.php');?>
            <!-- end Footer -->
        </div> <!-- END wrapper -->

        <!-- Vendor js -->
        <script src="assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="assets/js/app.min.js"></script>

    </body>
</html>

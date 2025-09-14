<?php
session_start();
include('assets/inc/config.php');

// Get the logged-in doctor's ID
$doc_id = $_SESSION['doc_id']; 

if(isset($_POST['add_patient_lab_test'])) {
    $lab_pat_name = $_POST['lab_pat_name'];
    $lab_pat_ailment = $_POST['lab_pat_ailment'];
    $lab_pat_number  = $_POST['lab_pat_number'];
    $lab_pat_tests = $_POST['lab_pat_tests'];
    $lab_number  = $_POST['lab_number'];

    // SQL query to insert captured values along with the doctor's ID (lab_doc_id)
    $query = "INSERT INTO laboratory (lab_pat_name, lab_pat_ailment, lab_pat_number, lab_pat_tests, lab_number, lab_doc_id) 
              VALUES (?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = $mysqli->prepare($query);

    // Bind the parameters (s = string, i = integer)
    $stmt->bind_param('sssssi', $lab_pat_name, $lab_pat_ailment, $lab_pat_number, $lab_pat_tests, $lab_number, $doc_id);

    // Execute the query
    $stmt->execute();

    // Check if the insert was successful
    if($stmt) {
        $success = "Patient Laboratory Tests Added Successfully.";
    } else {
        $err = "Please Try Again Or Try Later.";
    }
}
?>


<!-- HTML Content for the Page -->
<!DOCTYPE html>
<html lang="en">
    
    <!-- Head -->
    <?php include('assets/inc/head.php');?>

    <body>

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Topbar Start -->
            <?php include("assets/inc/nav.php");?>
            <!-- end Topbar -->

            <!-- ========== Left Sidebar Start ========== -->
            <?php include("assets/inc/sidebar.php");?>
            <!-- Left Sidebar End -->

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
            <?php
                // Get the patient number from the URL parameter
                $pat_number = $_GET['pat_number'];

                // SQL query to fetch patient details based on the patient number
                $ret="SELECT * FROM patients WHERE pat_number=?";
                $stmt= $mysqli->prepare($ret);
                $stmt->bind_param('s',$pat_number);
                $stmt->execute();
                $res=$stmt->get_result();
                
                while($row=$res->fetch_object()) {
            ?>
            
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
                                            <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Laboratory</a></li>
                                            <li class="breadcrumb-item active">Add Lab Test</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Add Lab Test</h4>
                                </div>
                            </div>
                        </div>     
                        <!-- end page title --> 

                        <!-- Form row -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title">Fill all fields</h4>
                                        <!-- Add Patient Form -->
                                        <form method="post">
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputEmail4" class="col-form-label">Patient Name</label>
                                                    <input type="text" required="required" readonly name="lab_pat_name" value="<?php echo $row->pat_fname;?> <?php echo $row->pat_lname;?>" class="form-control" id="inputEmail4" placeholder="Patient's First Name">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="inputPassword4" class="col-form-label">Patient Ailment</label>
                                                    <input required="required" type="text" readonly name="lab_pat_ailment" value="<?php echo $row->pat_ailment;?>" class="form-control"  id="inputPassword4" placeholder="Patient`s Last Name">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-12">
                                                    <label for="inputEmail4" class="col-form-label">Patient Number</label>
                                                    <input type="text" required="required" readonly name="lab_pat_number" value="<?php echo $row->pat_number;?>" class="form-control" id="inputEmail4" placeholder="DD/MM/YYYY">
                                                </div>
                                            </div>

                                            <hr>
                                            <div class="form-row">
                                                <div class="form-group col-md-2" style="display:none">
                                                    <?php 
                                                        $length = 5;    
                                                        $pres_no =  substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, $length);
                                                    ?>
                                                    <label for="inputZip" class="col-form-label">Lab Test Number</label>
                                                    <input type="text" name="lab_number" value="<?php echo $pres_no;?>" class="form-control" id="inputZip">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="inputAddress" class="col-form-label">Laboratory Tests</label>
                                                <textarea required="required" type="text" class="form-control" name="lab_pat_tests" id="editor"></textarea>
                                            </div>

                                            <button type="submit" name="add_patient_lab_test" class="ladda-button btn btn-success" data-style="expand-right">Add Laboratory Test</button>

                                        </form>
                                    </div> <!-- end card-body -->
                                </div> <!-- end card -->
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->

                    </div> <!-- container -->

                </div> <!-- content -->

                <!-- Footer Start -->
                <?php include('assets/inc/footer.php');?>
                <!-- end Footer -->

            </div>
            <?php }?>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->
        </div>
        <!-- END wrapper -->

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>
        <script src="//cdn.ckeditor.com/4.6.2/basic/ckeditor.js"></script>
        <script type="text/javascript">
        CKEDITOR.replace('editor')
        </script>

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

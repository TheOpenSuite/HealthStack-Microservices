<?php
session_start();
include('assets/inc/config.php');

if(isset($_POST['update_doc'])) {
    $doc_fname = $_POST['doc_fname'];
    $doc_lname = $_POST['doc_lname'];
    $doc_number = $_GET['doc_number'];
    $doc_email = $_POST['doc_email'];
    
    // Build SET clause components
    $setParts = array(
        "doc_fname=?",
        "doc_lname=?",
        "doc_email=?"
    );
    
    $params = array($doc_fname, $doc_lname, $doc_email);
    $types = 'sss';

    // Handle password update
    if (!empty($_POST['doc_pwd'])) {
        $setParts[] = "doc_pwd=?";
        $params[] = password_hash($_POST['doc_pwd'], PASSWORD_DEFAULT);
        $types .= 's';
    }

    // Add to $setParts and $params
    $setParts[] = "doc_start_time=?";
    $params[] = $_POST['doc_start_time'];
    $types .= 's';

    $setParts[] = "doc_end_time=?";
    $params[] = $_POST['doc_end_time'];
    $types .= 's';

    // Add WHERE clause parameter
    $params[] = $doc_number;
    $types .= 's';

    // Build final query
    $setClause = implode(', ', $setParts);
    $query = "UPDATE docs SET $setClause WHERE doc_number = ?";

    // Prepare and execute statement
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param($types, ...$params);
    $result = $stmt->execute();

    if ($result) {
        $success = "Employee Details Updated Successfully";
    } else {
        $err = "Error updating record: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!--End Server Side-->
<!DOCTYPE html>
<html lang="en">
    
    <!--Head-->
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
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Employee</a></li>
                                            <li class="breadcrumb-item active">Manage Employee</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Update Employee Details</h4>
                                </div>
                            </div>
                        </div>     
                        <!-- end page title --> 
                        <!-- Form row -->
                        <?php
                            $doc_number=$_GET['doc_number'];
                            $ret="SELECT  * FROM docs WHERE doc_number=?";
                            $stmt= $mysqli->prepare($ret) ;
                            $stmt->bind_param('s',$doc_number);
                            $stmt->execute() ;//ok
                            $res=$stmt->get_result();
                            //$cnt=1;
                            while($row=$res->fetch_object())
                            {
                        ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title">Fill all fields</h4>
                                        <!--Add Patient Form-->
                                        <form method="post" enctype="multipart/form-data">
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputEmail4" class="col-form-label">First Name</label>
                                                    <input type="text" required="required" value="<?php echo $row->doc_fname;?>" name="doc_fname" class="form-control" id="inputEmail4" >
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="inputPassword4" class="col-form-label">Last Name</label>
                                                    <input required="required" type="text" value="<?php echo $row->doc_lname;?>" name="doc_lname" class="form-control"  id="inputPassword4">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="inputAddress" class="col-form-label">Email</label>
                                                <input required="required" type="email" value="<?php echo $row->doc_email;?>" class="form-control" name="doc_email" id="inputAddress">
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputCity" class="col-form-label">Password</label>
                                                    <input type="password" name="doc_pwd" class="form-control" id="inputCity">
                                                </div> 
                                                
                                            </div>
                                            
                                            <!-- Add after password field -->
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label>Start Time</label>
                                                    <input type="time" required name="doc_start_time" class="form-control" 
                                                        value="<?php echo $row->doc_start_time; ?>">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>End Time</label>
                                                    <input type="time" required name="doc_end_time" class="form-control"
                                                        value="<?php echo $row->doc_end_time; ?>">
                                                </div>
                                            </div>

                                            <button type="submit" name="update_doc" class="ladda-button btn btn-success" data-style="expand-right">Update Employee</button>

                                        </form>
                                        <!--End Patient Form-->
                                    </div> <!-- end card-body -->
                                </div> <!-- end card-->
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->
                        <?php }?>

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

        <!-- App js-->
        <script src="assets/js/app.min.js"></script>

        <!-- Loading buttons js -->
        <script src="assets/libs/ladda/spin.js"></script>
        <script src="assets/libs/ladda/ladda.js"></script>

        <!-- Buttons init js-->
        <script src="assets/js/pages/loading-btn.init.js"></script>
        
    </body>

</html>
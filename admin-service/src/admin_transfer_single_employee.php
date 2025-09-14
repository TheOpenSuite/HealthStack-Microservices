<?php
	session_start();
	include('assets/inc/config.php');

    $dept_query = "SELECT * FROM departments";
    $dept_result = $mysqli->query($dept_query);
		if(isset($_POST['trans_dept']))
		{
            $doc_dept=$_POST['doc_dept'];
            $doc_number = $_GET['doc_number'];

            if ($doc_dept == 'Reception') {
                // Fetch current employee details before deleting from `docs`
                $query = "SELECT doc_fname, doc_lname, doc_email, doc_pwd FROM docs WHERE doc_number = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('s', $doc_number);
                $stmt->execute();
                $result = $stmt->get_result();
                $employee = $result->fetch_assoc();
            
                if ($employee) {
                    // Insert into `receptionists`
                    $insert_query = "INSERT INTO receptionists (receptionist_fname, receptionist_lname, receptionist_email, receptionist_pwd) VALUES (?, ?, ?, ?)";
                    $insert_stmt = $mysqli->prepare($insert_query);
                    $insert_stmt->bind_param('ssss', $employee['doc_fname'], $employee['doc_lname'], $employee['doc_email'], $employee['doc_pwd']);
                    $insert_stmt->execute();
            
                    // Remove from `docs`
                    $delete_query = "DELETE FROM docs WHERE doc_number = ?";
                    $delete_stmt = $mysqli->prepare($delete_query);
                    $delete_stmt->bind_param('s', $doc_number);
                    $delete_stmt->execute();
            
                    if ($insert_stmt && $delete_stmt) {
                        $success = "Employee Transferred to Reception Successfully!";
                    } else {
                        $err = "Error. Could not transfer to Reception.";
                    }
                } else {
                    $err = "Employee not found!";
                }
            } else {
                // Normal department transfer
                $query = "UPDATE docs SET doc_dept=? WHERE doc_number = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('ss', $doc_dept, $doc_number);
                $stmt->execute();
            
                if ($stmt) {
                    $success = "Employee Transferred Successfully!";
                } else {
                    $err = "Please Try Again Later.";
                }
            }
            
			/*
			*Use Sweet Alerts Instead Of This Fucked Up Javascript Alerts
			*echo"<script>alert('Successfully Created Account Proceed To Log In ');</script>";
			*/ 
			//declare a varible which will be passed to alert function
			if($stmt)
			{
				$success = "Employee Transfered";
			}
			else {
				$err = "Please Try Again Or Try Later";
			}
			
			
		}
?>
<!--End Server Side-->
<!--End Patient Registration-->
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
                                            <li class="breadcrumb-item active">Transfer Employee</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Transfer Employee From One Department To Another</h4>
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

                            // Fetch receptionist details (no doc_number, so fetch using receptionist_id)
                            $receptionist_query = "SELECT * FROM receptionists WHERE receptionist_id=?";
                            $receptionist_stmt = $mysqli->prepare($receptionist_query);
                            $receptionist_stmt->bind_param('s', $doc_number); // Using doc_number as an identifier here for simplicity
                            $receptionist_stmt->execute();
                            $receptionist_result = $receptionist_stmt->get_result();
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
                                                    <input type="text" required="required" readonly value="<?php echo $row->doc_fname;?>" name="doc_fname" class="form-control" id="inputEmail4" >
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="inputPassword4" class="col-form-label">Last Name</label>
                                                    <input required="required" type="text" readonly value="<?php echo $row->doc_lname;?>" name="doc_lname" class="form-control"  id="inputPassword4">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="inputAddress" class="col-form-label">Email</label>
                                                <input required="required" type="email" readonly value="<?php echo $row->doc_email;?>" class="form-control" name="doc_email" id="inputAddress">
                                            </div>

                                            <div class="form-group">
                                                <label for="inputAddress" class="col-form-label">Employee Number</label>
                                                <input required="required" type="email" readonly value="<?php echo $row->doc_number;?>" class="form-control" name="doc_email" id="inputAddress">
                                            </div>

                                            <div class="form-group">
                                                <label for="inputAddress" class="col-form-label">Employee Current Working Department </label>
                                                <input required="required" type="email" readonly value="<?php echo $row->doc_dept;?>" class="form-control" name="doc_email" id="inputAddress">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="inputState" class="col-form-label">Transfer Department</label>
                                                <select id="inputState" required="required" name="doc_dept" class="form-control">
                                                    <option value="">Choose Department</option>
                                                    <?php
                                                    while ($dept = $dept_result->fetch_object()) {
                                                        echo "<option value='$dept->dept_name'>$dept->dept_name</option>";
                                                    }
                                                    ?>
                                                    <option value="Reception">Reception</option> <!-- Ensure Reception is an option -->
                                                </select>
                                            </div>                                 

                                            <button type="submit" name="trans_dept" class="ladda-button btn btn-success" data-style="expand-right">Transfer Employee</button>

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
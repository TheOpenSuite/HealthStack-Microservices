<?php
session_start();
include('assets/inc/config.php');

// Fetch departments for the dropdown
$dept_query = "SELECT * FROM departments";
$dept_result = $mysqli->query($dept_query);

// Server-side code to handle Employee Registration and Department Assignment
if (isset($_POST['add_employee'])) {
    $doc_fname = $_POST['doc_fname'];
    $doc_lname = $_POST['doc_lname'];
    $doc_number = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 5); // Random employee number
    $doc_email = $_POST['doc_email'];
    $doc_pwd = $_POST['doc_pwd']; // Plain text password from form
    $doc_dept = $_POST['doc_dept']; // Department

    // Hash the password using password_hash
    $hashed_password = password_hash($doc_pwd, PASSWORD_DEFAULT);

    // Check if the department is "Reception"
    if ($doc_dept == 'Reception') {
        // Insert into the `receptionists` table if the department is "Reception"
        $insert_receptionist_query = "INSERT INTO receptionists (receptionist_fname, receptionist_lname, receptionist_email, receptionist_pwd) 
                                      VALUES (?, ?, ?, ?)";
        $receptionist_stmt = $mysqli->prepare($insert_receptionist_query);
        $receptionist_stmt->bind_param('ssss', $doc_fname, $doc_lname, $doc_email, $hashed_password);
        $receptionist_stmt->execute();

        if ($receptionist_stmt) {
            $success = "Receptionist Details Added Successfully";
        } else {
            $err = "Error. Could not add to Receptionists table.";
        }
    } else {
        // If not a Reception, insert into the `docs` table
        $query = "INSERT INTO docs (doc_fname, doc_lname, doc_number, doc_email, doc_pwd, doc_dept, doc_start_time, doc_end_time) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssssssss', $doc_fname, $doc_lname, $doc_number, $doc_email, $hashed_password, $doc_dept, $_POST['doc_start_time'], $_POST['doc_end_time']);
        $stmt->execute();

        if ($stmt) {
            $success = "Employee Details Added Successfully";
        } else {
            $err = "Error. Please Try Again Or Try Later";
        }
    }
}

// Add new department functionality
if (isset($_POST['add_dept'])) {
    $new_dept = $_POST['new_dept'];
    if (!empty($new_dept)) {
        // Insert the new department into the database
        $insert_dept_query = "INSERT INTO departments (dept_name) VALUES (?)";
        $stmt = $mysqli->prepare($insert_dept_query);
        $stmt->bind_param('s', $new_dept);
        $stmt->execute();
        $dept_added = "New Department Added Successfully";
    } else {
        $dept_error = "Please enter a department name.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include('assets/inc/head.php'); ?>

<body>
    <div id="wrapper">

        <!-- Topbar Start -->
        <?php include("assets/inc/nav.php"); ?>
        <!-- end Topbar -->

        <!-- Left Sidebar Start -->
        <?php include("assets/inc/sidebar.php"); ?>
        <!-- Left Sidebar End -->

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <!-- Start Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Employee</a></li>
                                        <li class="breadcrumb-item active">Add Employee</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Add Employee Details</h4>
                            </div>
                        </div>
                    </div>
                    <!-- End Page Title -->

                    <!-- Form Row -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Fill all fields</h4>

                                    <!-- Add Employee Form -->
                                    <form method="post">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputEmail4" class="col-form-label">First Name</label>
                                                <input type="text" required="required" name="doc_fname" class="form-control" id="inputEmail4">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputPassword4" class="col-form-label">Last Name</label>
                                                <input required="required" type="text" name="doc_lname" class="form-control" id="inputPassword4">
                                            </div>
                                        </div>

                                        <div class="form-group col-md-2" style="display:none">
                                            <label for="inputZip" class="col-form-label">Employee Number</label>
                                            <input type="text" name="doc_number" value="<?php echo $doc_number; ?>" class="form-control" id="inputZip">
                                        </div>

                                        <div class="form-group">
                                            <label for="inputAddress" class="col-form-label">Email</label>
                                            <input required="required" type="email" class="form-control" name="doc_email" id="inputAddress">
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputCity" class="col-form-label">Password</label>
                                                <input required="required" type="password" name="doc_pwd" class="form-control" id="inputCity">
                                            </div>
                                        </div>

                                        <!-- Department selection -->
                                        <div class="form-group">
                                            <label for="inputState" class="col-form-label">Department</label>
                                            <select id="inputState" required="required" name="doc_dept" class="form-control">
                                                <option value="">Choose Department</option>
                                                <?php
                                                while ($dept = $dept_result->fetch_object()) {
                                                    echo "<option value='$dept->dept_name'>$dept->dept_name</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <!-- Add after department selection -->
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Start Time</label>
                                                <input type="time" required name="doc_start_time" class="form-control">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>End Time</label>
                                                <input type="time" required name="doc_end_time" class="form-control">
                                            </div>
                                        </div>

                                        <button type="submit" name="add_employee" class="ladda-button btn btn-success" data-style="expand-right">Add Employee</button>

                                    </form>
                                    <!-- End Add Employee Form -->

                                    <!-- New Department Form -->
                                    <h4 class="header-title mt-4">Add New Department</h4>
                                    <form method="post">
                                        <div class="form-group">
                                            <label for="inputState" class="col-form-label">New Department</label>
                                            <input type="text" class="form-control" name="new_dept" placeholder="Enter Department Name">
                                        </div>
                                        <button type="submit" name="add_dept" class="btn btn-primary">Add Department</button>
                                    </form>
                                    <?php
                                    if (isset($dept_added)) {
                                        echo "<div class='alert alert-success mt-4'>$dept_added</div>";
                                    }
                                    if (isset($dept_error)) {
                                        echo "<div class='alert alert-danger mt-4'>$dept_error</div>";
                                    }
                                    ?>

                                </div> <!-- end card-body -->
                            </div> <!-- end card -->
                        </div> <!-- end col -->
                    </div> <!-- end row -->

                </div> <!-- container -->
            </div> <!-- content -->

            <!-- Footer Start -->
            <?php include('assets/inc/footer.php'); ?>
            <!-- end Footer -->

        </div>

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

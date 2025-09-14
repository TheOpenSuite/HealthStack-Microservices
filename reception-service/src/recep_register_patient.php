<?php

include('assets/inc/config.php');

if(isset($_POST['add_patient'])) {
    // Capture form data
    $pat_fname = $_POST['pat_fname'];
    $pat_lname = $_POST['pat_lname'];
    $pat_number = $_POST['pat_number'];
    $pat_phone = $_POST['pat_phone'];
    $pat_type = $_POST['pat_type'];
    $pat_addr = $_POST['pat_addr'];
    $pat_age = $_POST['pat_age'];
    $pat_dob = $_POST['pat_dob'];
    $pat_ailment = $_POST['pat_ailment'];
    $pat_dept = $_POST['pat_dept'];  // New field for department
    $pat_doc_id = $_POST['pat_doc_id'];  // New field for doctor

    // Handle "Random" doctor selection
    if ($pat_doc_id === 'random') {
        $stmt = $mysqli->prepare("
            SELECT d.doc_id, COUNT(p.pat_id) AS patient_count 
            FROM docs d 
            LEFT JOIN patients p ON d.doc_id = p.pat_doc_id 
            WHERE d.doc_dept = ? 
            AND CURTIME() BETWEEN d.doc_start_time AND d.doc_end_time
            GROUP BY d.doc_id 
            ORDER BY patient_count ASC, d.doc_id ASC 
            LIMIT 1
        ");
        $stmt->bind_param('s', $pat_dept);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            // Check the next available time
            $nextAvailableStmt = $mysqli->prepare("
                SELECT MIN(doc_start_time) AS next_start
                FROM docs 
                WHERE doc_dept = ? AND doc_start_time > CURTIME()
            ");
            $nextAvailableStmt->bind_param('s', $pat_dept);
            $nextAvailableStmt->execute();
            $nextResult = $nextAvailableStmt->get_result();
            $nextRow = $nextResult->fetch_assoc();
        
            if ($nextRow['next_start']) {
                $nextTime = date("h:i A", strtotime($nextRow['next_start']));
                $err = "No doctors available. Next slot: " . $nextTime . ". Schedule an appointment?";
            } else {
                $earliestStmt = $mysqli->prepare("
                    SELECT MIN(doc_start_time) AS earliest_start
                    FROM docs 
                    WHERE doc_dept = ?
                ");
                $earliestStmt->bind_param('s', $pat_dept);
                $earliestStmt->execute();
                $earliestResult = $earliestStmt->get_result();
                $earliestRow = $earliestResult->fetch_assoc();
                $nextTime = date("h:i A", strtotime($earliestRow['earliest_start']));
                $err = "No doctors today. Next available: tomorrow at " . $nextTime . ". Schedule?";
            }
            
            header("Location: recep_register_patient.php?err=" . urlencode($err));
            exit();
        }
        $row = $result->fetch_assoc();
        $pat_doc_id = $row['doc_id'];
    }

    if ($pat_doc_id !== 'random') {
        $stmt = $mysqli->prepare("
            SELECT doc_start_time, doc_end_time 
            FROM docs 
            WHERE doc_id = ?
        ");
        $stmt->bind_param('i', $pat_doc_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $doc = $result->fetch_assoc();
            $current_time = date('H:i:s');
    
            if ($current_time < $doc['doc_start_time'] || $current_time > $doc['doc_end_time']) {
                $err = "Doctor is unavailable at this time. Please choose another doctor or schedule an appointment.";
                header("Location: recep_register_patient.php?err=" . urlencode($err));
                exit();
            }
        }
    }
    
    

    // Insert patient into database
    $query = "INSERT INTO patients (pat_fname, pat_ailment, pat_lname, pat_age, pat_dob, pat_number, pat_phone, pat_type, pat_addr, pat_dept, pat_doc_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $rc = $stmt->bind_param('ssssssssssi', $pat_fname, $pat_ailment, $pat_lname, $pat_age, $pat_dob, $pat_number, $pat_phone, $pat_type, $pat_addr, $pat_dept, $pat_doc_id);
    $stmt->execute();

    if($stmt) {
        $success = "Patient Details Added";
    } else {
        $err = "Please Try Again Or Try Later";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<!--Head-->
<?php include('assets/inc/head.php');?>

<body>

    <!-- Begin page -->
    <div id="wrapper">


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
                                        <li class="breadcrumb-item"><a href="../auth-service/src/backend/reception/indexRecep.php">Login</a></li>
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Patients</a></li>
                                        <li class="breadcrumb-item active">Add Patient</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Add Patient Details</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->
                     <!-- Button for existing patient -->
                    <div class="row">
                        <div class="col-auto">
                            <a href="existing_patient.php" class="btn btn-warning">Existing Patient?</a>
                        </div>
                        <div class="col-auto">
                            <a href="schedule_appointment.php" class="btn btn-warning">Schedule  Patient</a>
                        </div>
                    </div>
                    

                    <?php if(isset($_GET['err'])) { ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($_GET['err']); ?>
                            <a href="schedule_appointment.php?dept=<?php echo urlencode($_POST['pat_dept'] ?? ''); ?>" class="btn btn-secondary ml-2">Schedule Appointment</a>
                        </div>
                    <?php } ?>

                    <!-- Form row -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Fill all fields</h4>
                                    <!--Add Patient Form-->
                                    <form method="post">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputEmail4" class="col-form-label">First Name</label>
                                                <input type="text" required="required" name="pat_fname" class="form-control" id="inputEmail4" placeholder="Patient's First Name">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputPassword4" class="col-form-label">Last Name</label>
                                                <input required="required" type="text" name="pat_lname" class="form-control" id="inputPassword4" placeholder="Patient's Last Name">
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputDob" class="col-form-label">Date of Birth</label>
                                                <input type="date" required name="pat_dob" class="form-control" id="inputDob" value="<?php echo $patient['pat_dob']; ?>">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputPassword4" class="col-form-label">Age</label>
                                                <input required="required" type="text" name="pat_age" class="form-control" id="inputPassword4" placeholder="Patient's Age">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="inputAddress" class="col-form-label">Address</label>
                                            <input required="required" type="text" class="form-control" name="pat_addr" id="inputAddress" placeholder="Patient's Address">
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="inputCity" class="col-form-label">Mobile Number</label>
                                                <input required="required" type="text" name="pat_phone" class="form-control" id="inputCity">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputCity" class="col-form-label">Patient Ailment</label>
                                                <input required="required" type="text" name="pat_ailment" class="form-control" id="inputCity">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputState" class="col-form-label">Patient's Type</label>
                                                <select id="inputState" required="required" name="pat_type" class="form-control">
                                                    <option>Choose</option>
                                                    <option>InPatient</option>
                                                    <option>OutPatient</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style="display:none">
                                                <?php
                                                    $length = 5;
                                                    $patient_number = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,$length);
                                                ?>
                                                <label for="inputZip" class="col-form-label">Patient Number</label>
                                                <input type="text" name="pat_number" value="<?php echo $patient_number;?>" class="form-control" id="inputZip">
                                            </div>
                                        </div>

                                        <!-- Add Department Dropdown -->
                                        <div class="form-group col-md-6">
                                            <label for="inputDepartment" class="col-form-label">Department</label>
                                            <select id="inputDepartment" name="pat_dept" class="form-control" required="required">
                                                <option value="">Select Department</option>
                                                <?php
                                                    // Fetch departments dynamically from the departments table
                                                    $deptQuery = "SELECT dept_name FROM departments WHERE dept_name != 'Reception' AND dept_name != 'Pharmacy'";
                                                    $deptResult = $mysqli->query($deptQuery);
                                                    while($row = $deptResult->fetch_assoc()) {
                                                        echo "<option value='" . $row['dept_name'] . "'>" . $row['dept_name'] . "</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- Add Doctor Dropdown -->
                                        <div class="form-group col-md-6">
                                            <label for="inputDoctor" class="col-form-label">Doctor</label>
                                            <select id="inputDoctor" name="pat_doc_id" class="form-control" required="required">
                                                <option value="">Select Doctor</option>
                                            </select>
                                        </div>

                                        <button type="submit" name="add_patient" class="ladda-button btn btn-primary" data-style="expand-right">Add Patient</button>
                                    </form>
                                    <!-- End Patient Form -->
                                </div> <!-- end card-body -->
                            </div> <!-- end card-->
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->
                </div> <!-- container -->
            </div> <!-- content -->

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

    <script type="text/javascript">
    $(document).ready(function() {
        // Trigger when department is selected
        $('#inputDepartment').change(function() {
            var dept = $(this).val();  // Get the selected department
            
            // If department is selected, fetch doctors for that department
            if (dept != '') {
                $.ajax({
                    url: 'fetch_doctors.php',  // Send request to fetch_doctors.php
                    type: 'GET',
                    data: { dept: dept },
                    success: function(response) {
                        // Populate the doctors dropdown with the response
                        $('#inputDoctor').html(response);
                    }
                });
            } else {
                // If no department is selected, reset the doctors dropdown
                $('#inputDoctor').html('<option value="">Select Doctor</option>');
            }
        });
    });

    </script>

</body>

</html>




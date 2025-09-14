<?php
session_start();
include('assets/inc/config.php');
// Initialize the patient variable
$patient = null;

if (isset($_POST['search_patient'])) {
    // Search patient by phone number
    $pat_phone = $_POST['pat_phone_search'];
    $query = "SELECT * FROM patients WHERE pat_phone = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $pat_phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
    } else {
        $err = "No patient found with that phone number.";
    }
}

if (isset($_POST['update_patient'])) {
    // Capture form data
    $pat_id = $_POST['pat_id']; // Primary key for the patient
    $fieldsToUpdate = [];
    $values = [];

    // Dynamically add fields to update only if they are provided
    if (!empty($_POST['pat_fname'])) {
        $fieldsToUpdate[] = "pat_fname = ?";
        $values[] = $_POST['pat_fname'];
    }
    if (!empty($_POST['pat_lname'])) {
        $fieldsToUpdate[] = "pat_lname = ?";
        $values[] = $_POST['pat_lname'];
    }
    if (!empty($_POST['pat_phone'])) {
        $fieldsToUpdate[] = "pat_phone = ?";
        $values[] = $_POST['pat_phone'];
    }
    if (!empty($_POST['pat_dob'])) {
        $fieldsToUpdate[] = "pat_dob = ?";
        $values[] = $_POST['pat_dob'];
    }
    if (!empty($_POST['pat_age'])) {
        $fieldsToUpdate[] = "pat_age = ?";
        $values[] = $_POST['pat_age'];
    }
    if (!empty($_POST['pat_addr'])) {
        $fieldsToUpdate[] = "pat_addr = ?";
        $values[] = $_POST['pat_addr'];
    }
    if (!empty($_POST['pat_type'])) {
        $fieldsToUpdate[] = "pat_type = ?";
        $values[] = $_POST['pat_type'];
    }
    if (!empty($_POST['pat_ailment'])) {
        $fieldsToUpdate[] = "pat_ailment = ?";
        $values[] = $_POST['pat_ailment'];
    }
    if (!empty($_POST['pat_dept'])) {
        $fieldsToUpdate[] = "pat_dept = ?";
        $values[] = $_POST['pat_dept'];
    }
    if (!empty($_POST['pat_doc_id'])) {
        $fieldsToUpdate[] = "pat_doc_id = ?";
        $values[] = $_POST['pat_doc_id'];
    }

    // If there are fields to update
    if (!empty($fieldsToUpdate)) {
        $query = "UPDATE patients SET " . implode(', ', $fieldsToUpdate) . " WHERE pat_id = ?";
        $stmt = $mysqli->prepare($query);
        $values[] = $pat_id; // Add the patient ID for the WHERE clause
        $types = str_repeat('s', count($values) - 1) . 'i'; // Define types for bind_param
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            $success = "Patient details updated successfully!";
        } else {
            $err = "Error: Unable to update patient details.";
        }
    } else {
        $err = "No fields were provided to update.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include('assets/inc/head.php');?>

<body>
    <!-- Begin page -->
    <div id="wrapper">
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <!-- Start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="../reception/indexRecep.php">Login</a></li>
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Patients</a></li>
                                        <li class="breadcrumb-item active">Search and Update Patient</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Search and Update Existing Patient</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->
                    <!-- Button for existing patient -->
                    <div class="row">
                        <div class="col-md-12">
                            <a href="recep_register_patient.php" class="btn btn-warning">Back</a>
                        </div>
                    </div>

                    <!-- Search Patient Form -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Search Patient by Phone Number</h4>
                                    <form method="POST">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="pat_phone_search" class="col-form-label">Enter Patient's Phone Number</label>
                                                <input type="text" class="form-control" name="pat_phone_search" id="pat_phone_search" placeholder="Patient's Phone Number" required="required">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <button type="submit" name="search_patient" class="btn btn-primary mt-4">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                    <?php if (isset($err)): ?>
                                        <div class="alert alert-danger mt-3"><?php echo $err; ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success mt-3"><?php echo $success; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Patient Details Form (Appears when patient is found) -->
                    <?php if ($patient): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title">Update Patient Details</h4>
                                        <form method="POST">
                                        <input type="hidden" name="pat_id" value="<?php echo $patient['pat_id']; ?>">
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="pat_fname" class="col-form-label">First Name</label>
                                                    <input type="text" name="pat_fname" class="form-control" value="<?php echo $patient['pat_fname']; ?>" required="required">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="pat_lname" class="col-form-label">Last Name</label>
                                                    <input type="text" name="pat_lname" class="form-control" value="<?php echo $patient['pat_lname']; ?>" required="required">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputDob" class="col-form-label">Date of Birth</label>
                                                    <input type="date" required name="pat_dob" class="form-control" id="inputDob" value="<?php echo $patient['pat_dob']; ?>">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="pat_age" class="col-form-label">Age</label>
                                                    <input type="text" name="pat_age" class="form-control" value="<?php echo $patient['pat_age']; ?>" required="required">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="pat_addr" class="col-form-label">Address</label>
                                                <input type="text" name="pat_addr" class="form-control" value="<?php echo $patient['pat_addr']; ?>" required="required">
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label for="pat_phone" class="col-form-label">Phone Number</label>
                                                    <input type="text" name="pat_phone" class="form-control" value="<?php echo $patient['pat_phone']; ?>" required="required">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="pat_ailment" class="col-form-label">Ailment</label>
                                                    <input type="text" name="pat_ailment" class="form-control" value="<?php echo $patient['pat_ailment']; ?>" required="required">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="pat_type" class="col-form-label">Patient Type</label>
                                                    <select name="pat_type" class="form-control" required="required">
                                                        <option value="InPatient" <?php echo $patient['pat_type'] == 'InPatient' ? 'selected' : ''; ?>>InPatient</option>
                                                        <option value="OutPatient" <?php echo $patient['pat_type'] == 'OutPatient' ? 'selected' : ''; ?>>OutPatient</option>
                                                    </select>
                                                </div>
                                            </div>
                                        <!-- Add Department Dropdown -->
                                        <div class="form-group col-md-6">
                                            <label for="inputDepartment" class="col-form-label">Department</label>
                                            <select id="inputDepartment" name="pat_dept" class="form-control" required="required">
                                                <option value="">Select Department</option>
                                                <?php
                                                    // Fetch departments dynamically from the departments table
                                                    $deptQuery = "SELECT dept_name FROM departments";
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

                                            <button type="submit" name="update_patient" class="btn btn-primary">Update Patient</button>
                                        </form>
                                    </div> <!-- end card-body -->
                                </div> <!-- end card-->
                            </div> <!-- end col -->
                        </div>
                    <?php endif; ?>

                </div> <!-- container -->
            </div> <!-- content -->
        </div> <!-- content-page -->
    </div> <!-- wrapper -->

    <!-- Vendor js -->
    <script src="assets/js/vendor.min.js"></script>

    <!-- App js-->
    <script src="assets/js/app.min.js"></script>

    <!-- Loading buttons js -->
    <script src="assets/libs/ladda/spin.js"></script>
    <script src="assets/libs/ladda/ladda.js"></script>

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

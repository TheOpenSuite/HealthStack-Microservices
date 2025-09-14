<?php

session_start();
include('assets/inc/config.php');

// Automatically delete appointments older than 30 minutes
$autoDeleteQuery = "DELETE FROM appointment WHERE schedule_time <= NOW() - INTERVAL 30 MINUTE AND status = 'pending'";
$mysqli->query($autoDeleteQuery);
/* 
To have full automation for the deletion. You can use a cron job to run this script every 30 minutes by make a php file with the following code and upload it to your server.
*/

// Handle appointment scheduling
if (isset($_POST['schedule_appointment'])) {
    // Capture all form fields
    $pat_fname = $_POST['pat_fname'];
    $pat_lname = $_POST['pat_lname'];
    $pat_phone = $_POST['pat_phone'];
    $pat_dept = $_POST['pat_dept'];
    $pat_doc_id = $_POST['pat_doc_id'];
    $schedule_type = $_POST['schedule_type'];
    $schedule_time = ($schedule_type == 'specific') ? $_POST['schedule_time'] : date('Y-m-d H:i:s');
    $pat_dob = $_POST['pat_dob'];
    $pat_age = $_POST['pat_age'];
    $pat_addr = $_POST['pat_addr'];
    $pat_ailment = $_POST['pat_ailment'];
    $pat_type = $_POST['pat_type'];
    $pat_number = $_POST['pat_number'];

    if ($pat_doc_id === 'random') {
        $stmt = $mysqli->prepare("
            SELECT d.doc_id 
            FROM docs d 
            LEFT JOIN patients p ON d.doc_id = p.pat_doc_id 
            WHERE d.doc_dept = ? 
            AND CURTIME() BETWEEN d.doc_start_time AND d.doc_end_time
            GROUP BY d.doc_id 
            ORDER BY COUNT(p.pat_id) ASC, d.doc_id ASC 
            LIMIT 1
        ");
        $stmt->bind_param('s', $pat_dept);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $_SESSION['err'] = "No available doctors in $pat_dept. Schedule for another time.";
            header("Location: schedule_appointment.php");
            exit(); // Critical: Stop execution to avoid inserting invalid data
        }
        $row = $result->fetch_assoc();
        $pat_doc_id = $row['doc_id']; // Assign valid doctor ID
    }

    // Proceed only if $pat_doc_id is valid (not 'random' or 0)
    if (!is_numeric($pat_doc_id) || $pat_doc_id <= 0) {
        $_SESSION['err'] = "Invalid doctor selection.";
        header("Location: schedule_appointment.php");
        exit();
    }

    // Insert appointment into the database
    $query = "INSERT INTO appointment (pat_fname, pat_lname, pat_phone, pat_dept, pat_doc_id, schedule_time, status, pat_dob, pat_age, pat_addr, pat_ailment, pat_type, pat_number) 
              VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ssssssssisss', $pat_fname, $pat_lname, $pat_phone, $pat_dept, $pat_doc_id, $schedule_time, $pat_dob, $pat_age, $pat_addr, $pat_ailment, $pat_type, $pat_number);
    $stmt->execute();

    if ($stmt) {
        $success = "Appointment Scheduled Successfully";
    } else {
        $err = "Error Scheduling Appointment";
    }

}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM appointment WHERE id = ?";
    $stmt = $mysqli->prepare($deleteQuery);
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();

    if ($stmt) {
        $success = "Appointment Deleted Successfully";
    } else {
        $err = "Error Deleting Appointment";
    }
}

// Handle appointment confirmation
if (isset($_GET['confirm_id'])) {
    $confirm_id = $_GET['confirm_id'];
    $confirmQuery = "INSERT INTO patients (pat_fname, pat_lname, pat_phone, pat_dept, pat_doc_id, pat_dob, pat_age, pat_addr, pat_ailment, pat_type, pat_number) 
                     SELECT pat_fname, pat_lname, pat_phone, pat_dept, pat_doc_id, pat_dob, pat_age, pat_addr, pat_ailment, pat_type, pat_number 
                     FROM appointment WHERE id = ?";
    $stmt = $mysqli->prepare($confirmQuery);
    $stmt->bind_param('i', $confirm_id);
    $stmt->execute();

    if ($stmt) {
        $deleteQuery = "DELETE FROM appointment WHERE id = ?";
        $stmt = $mysqli->prepare($deleteQuery);
        $stmt->bind_param('i', $confirm_id);
        $stmt->execute();
        $success = "Appointment Confirmed";
    } else {
        $err = "Error Confirming Appointment";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('assets/inc/head.php'); ?>
</head>
<body>
    <div id="wrapper">
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <h4 class="page-title">Schedule Appointment</h4>
                    <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
                    <?php if (isset($err)) echo "<div class='alert alert-danger'>$err</div>"; ?>

                    <!-- Appointment Form -->
                    <form method="post">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>First Name</label>
                                <input type="text" name="pat_fname" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Last Name</label>
                                <input type="text" name="pat_lname" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Date of Birth</label>
                                <input type="date" name="pat_dob" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Age</label>
                                <input type="number" name="pat_age" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="pat_addr" class="form-control" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Phone</label>
                                <input type="text" name="pat_phone" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Ailment</label>
                                <input type="text" name="pat_ailment" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Patient Type</label>
                            <select name="pat_type" class="form-control" required>
                                <option value="">Choose</option>
                                <option>InPatient</option>
                                <option>OutPatient</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Department</label>
                                <select name="pat_dept" class="form-control" required>
                                    <option value="">Select</option>
                                    <?php
                                    $deptQuery = "SELECT dept_name FROM departments WHERE dept_name != 'Reception' AND dept_name != 'Pharmacy'";
                                    $result = $mysqli->query($deptQuery);
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['dept_name']}'>{$row['dept_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Doctor</label>
                                <select name="pat_doc_id" class="form-control" required>
                                    <option value="">Select</option>
                                    <!-- Populate via AJAX -->
                                </select>
                            </div>
                        </div>

                        <!-- Schedule Type -->
                        <div class="form-group">
                            <label>Schedule Type</label>
                            <select name="schedule_type" class="form-control" required>
                                <option value="asap">ASAP</option>
                                <option value="specific">Specific Time</option>
                            </select>
                        </div>
                        <div class="form-group" id="scheduleTime" style="display: none;">
                            <label>Schedule Time</label>
                            <input type="datetime-local" name="schedule_time" class="form-control">
                        </div>

                        <!-- Hidden Patient Number -->
                        <div class="form-group" style="display: none;">
                            <?php
                            $length = 5;
                            $patient_number = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, $length);
                            ?>
                            <input type="text" name="pat_number" value="<?php echo $patient_number; ?>" class="form-control">
                        </div>

                        <button type="submit" name="schedule_appointment" class="btn btn-primary">Schedule</button>
                        <a href="recep_register_patient.php" class="btn btn-warning">Register Patient</a>
                    </form>

                    <!-- Pending Appointments Table -->
                    <h4 class="mt-4">Pending Appointments</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group" style="width: 175px;">
                                <input id="demo-foo-search" type="text" placeholder="Search" class="form-control form-control-sm" autocomplete="on">
                            </div>
                            <table class="table table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Department</th>
                                        <th>Dr. Name</th>
                                        <th>Doctor</th>
                                        <th>Time</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $appointmentQuery = "SELECT a.*, d.doc_fname, d.doc_lname 
                                    FROM appointment a 
                                    LEFT JOIN docs d ON a.pat_doc_id = d.doc_id
                                    WHERE a.status = 'pending'";
                                    $result = $mysqli->query($appointmentQuery);
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                            <td>{$row['pat_fname']} {$row['pat_lname']}</td>
                                            <td>{$row['pat_phone']}</td>
                                            <td>{$row['pat_dept']}</td>
                                            <td>{$row['doc_fname']} {$row['doc_lname']}</td>
                                            <td>{$row['pat_doc_id']}</td>
                                            <td>{$row['schedule_time']}</td>
                                            <td><a href='schedule_appointment.php?confirm_id={$row['id']}' class='btn btn-success'>Confirm</a>
                                            <a href='schedule_appointment.php?delete_id={$row['id']}' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this appointment?\");'>Delete</a>
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/vendor.min.js"></script>
    <script src="assets/js/app.min.js"></script>
    <script>
    // Show/hide specific time input based on schedule type
    document.querySelector('[name="schedule_type"]').addEventListener('change', function() {
        document.getElementById('scheduleTime').style.display = this.value === 'specific' ? 'block' : 'none';
    });

    // AJAX to load doctors based on department
    $(document).ready(function() {
        $('[name="pat_dept"]').change(function() {
            var dept = $(this).val();
            if (dept) {
                $.ajax({
                    url: 'fetch_doctors.php',
                    type: 'GET',
                    data: { dept: dept },
                    success: function(response) {
                        $('[name="pat_doc_id"]').html(response);
                    }
                });
            } else {
                $('[name="pat_doc_id"]').html('<option value="">Select Doctor</option>');
            }
        });
    });
    </script>
</body>
</html>
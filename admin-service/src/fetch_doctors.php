<?php
// fetch_doctors.php

// Include your database connection
include('assets/inc/config.php');

// Check if department is passed via GET
if(isset($_GET['dept'])) {
    $dept = $_GET['dept'];

    // Query to fetch doctors from the selected department
    $query = "SELECT doc_id, doc_fname, doc_lname FROM docs WHERE doc_dept = ?";
    $stmt = $mysqli->prepare($query);

    // Ensure the query executes safely with the correct parameter
    $stmt->bind_param('s', $dept);  // 's' indicates a string parameter for dept
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if doctors exist in this department
    if($result->num_rows > 0) {
        // Loop through the doctors and create <option> elements
        while($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['doc_id'] . "'>" . $row['doc_fname'] . " " . $row['doc_lname'] . "</option>";
        }
    } else {
        // If no doctors found, show a "No Doctors Available" message
        echo "<option value=''>No Doctors Available</option>";
    }
} else {
    // If no department is selected, show a "No Doctors Available" message
    echo "<option value=''>Select a Department First</option>";
}
?>

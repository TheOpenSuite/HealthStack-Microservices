<?php
session_start();
include('../../assets/inc/config.php'); // Get configuration file

// Receptionist Login
if (isset($_POST['doc_login'])) {
    $reception_email = $_POST['doc_number']; // Assuming doc_number is the email field for receptionists
    $reception_pwd = $_POST['doc_pwd']; // Password entered by user

    // Query the receptionists table to check if the user exists
    $stmt = $mysqli->prepare("SELECT receptionist_email, receptionist_pwd, receptionist_id FROM receptionists WHERE receptionist_email = ?");
    $stmt->bind_param('s', $reception_email); // Bind email parameter
    $stmt->execute();
    $stmt->bind_result($stored_email, $stored_pwd, $receptionist_id);
    $stmt->fetch();

    // Check if the email exists and password matches
    if ($stored_email && password_verify($reception_pwd, $stored_pwd)) {
        // If login is successful, set session variables
        $_SESSION['receptionist_id'] = $receptionist_id;
        $_SESSION['receptionist_email'] = $stored_email;

        // New logic to redirect with a token
        $_SESSION['user_authenticated'] = true;
        $_SESSION['user_role'] = 'reception';
        $_SESSION['login_time'] = time();
        $token = session_id();

        // Redirect to the Reception Service on port 8085, passing the token
        header("Location: http://localhost/reception/recep_register_patient.php");
        exit();
    } else {
        // If login fails
        $err = "Access Denied. Please check your credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Hospital Management Information System - A Super Responsive Information System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta content="" name="MartDevelopers" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="../../assets/images/favicon.ico">

    <!-- App css -->
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/css/app.min.css" rel="stylesheet" type="text/css" />

    <script src="../../assets/js/swal.js"></script>

    <?php if(isset($success)) {?>
    <script>
        setTimeout(function () {
            swal("Success", "<?php echo $success; ?>", "success");
        }, 100);
    </script>
    <?php } ?>

    <?php if(isset($err)) {?>
    <script>
        setTimeout(function () {
            swal("Failed", "<?php echo $err; ?>", "error");
        }, 100);
    </script>
    <?php } ?>
</head>

<body>

<div class="account-pages mt-5 mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-pattern">
                    <div class="card-body p-4">

                        <div class="text-center w-75 m-auto">
                            <a href="index.php">
                                <span><img src=".../../assets/images/logo-dark.png" alt="Hospital Logo" height="75"></span>
                            </a>
                            <p class="text-muted mb-4 mt-3">Enter your email address and password to access Reception panel.</p>
                        </div>

                        <!-- Receptionist Login Form -->
                        <form method='post'>

                            <div class="form-group mb-3">
                                <label for="emailaddress">Receptionist Email</label>
                                <input class="form-control" name="doc_number" type="text" id="emailaddress" required="" placeholder="Enter your email">
                            </div>

                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input class="form-control" name="doc_pwd" type="password" required="" id="password" placeholder="Enter your password">
                            </div>

                            <div class="form-group mb-0 text-center">
                                <button class="btn btn-primary btn-block" name="doc_login" type="submit"> Log In </button>
                            </div>

                        </form>

                    </div> <!-- end card-body -->
                </div>
                <!-- end card -->
                <!-- end row -->

            </div> <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</div>
<!-- end page -->
 
<!-- New 'Back to Index' button -->
<div class="text-center mt-4">
    <button onclick="window.location.href='../../index.php'" class="btn btn-secondary">Back to Home</button>
</div>

<?php include ("../../assets/inc/footer1.php");?>

<!-- Vendor js -->
<script src="../../assets/js/vendor.min.js"></script>

<!-- App js -->
<script src="../../assets/js/app.min.js"></script>

</body>

</html>

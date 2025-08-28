<?php 
$ferrormsg = '';
$lerrormsg = '';
$msg = '';
$errormsg = '';

include  '../include/config.php';

if(isset($_POST['Submit'])){
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $mobNumber = $_POST['mobNumber'];
    $address = $_POST['address'];
    $preplacement = $_POST['preplacement'];
    $password = md5($_POST['password']);
    $status = 1;

    // Validate required fields
    if (empty($fname)) {
        $ferrormsg = "Please Enter First Name";
    } elseif (empty($lname)) {
        $lerrormsg = "Please Enter Last Name";
    } elseif (empty($preplacement)) {
        $errormsg = "Please Select Preplacement";
    } else {
        // Insert the data into tbluser along with placeid
        try {
            $sql = "INSERT INTO tbluser (name, email, mobile, address, password, placeid) 
                    VALUES (:name, :email, :mobNumber, :address, :password, :placeid)";
            $query = $dbh->prepare($sql);

            $fullname = $fname . " " . $lname;
            $query->bindParam(':name', $fullname, PDO::PARAM_STR);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->bindParam(':mobNumber', $mobNumber, PDO::PARAM_STR);
            $query->bindParam(':address', $address, PDO::PARAM_STR);
            $query->bindParam(':password', $password, PDO::PARAM_STR);
            $query->bindParam(':placeid', $preplacement, PDO::PARAM_INT);
            
            // Execute the query
            $query->execute();
            
            // Check if rows were inserted
            if ($query->rowCount() > 0) {
                $msg = "Information Added Successfully";
            } else {
                $errormsg = "Data not inserted successfully";
            }
        } catch (PDOException $e) {
            $errormsg = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="description" content="Vali is a">
    <title>Health Assessment for Fitness to Work</title>
    <link rel="icon" type="image/png" href="../../img/petronas.gif">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="../../css/main.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="app sidebar-mini rtl">
    <!-- Navbar-->
    <?php include '../include/header.php'; ?>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include '../include/sidebar.php'; ?>
    <main class="app-content">
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h2 align="center"> Add User</h2>
                    <hr />
                    <!-- Success Message -->
                    <?php if ($msg) { ?>
                    <div class="alert alert-success" role="alert">
                        <strong>Well done!</strong> <?php echo htmlentities($msg); ?>
                    </div>
                    <?php } ?>

                    <!-- Error Message -->
                    <?php if ($errormsg) { ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>Oh snap!</strong> <?php echo htmlentities($errormsg); ?></div>
                    <?php } ?>

                    <div class="tile-body">
                        <form class="row" method="post" enctype="multipart/form-data">
                            <!-- <div class="form-group col-md-12">
                                <label class="control-label">Emp ID</label>
                                <input name="empcode" id="empcode" onBlur="checkAvailabilityEmpid()" class="form-control" type="text" autocomplete="off" required placeholder="Enter your EmpId">
                                <span id="empid-availability" style="font-size:12px;"></span>
                            </div> -->

                            <div class="form-group col-md-6">
                                <label class="control-label">First Name</label>
                                <input class="form-control" name="fname" id="fname" type="text" placeholder="Enter your First Name" autocomplete="off" required>
                                <span style="color: red;"><?php echo $ferrormsg; ?></span>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Last Name</label>
                                <input class="form-control" name="lname" id="lname" type="text" placeholder="Enter your Last Name" autocomplete="off" required>
                                <span style="color: red;"><?php echo $lerrormsg; ?></span>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Email ID</label>
                                <!-- <input name="email" type="email" id="email" class="form-control" placeholder="Enter your Email ID" onBlur="checkAvailabilityEmailid()" autocomplete="off" required> -->
                                <input name="email" type="email" id="email" class="form-control" placeholder="Enter your Email ID" autocomplete="off" required>
                                <!-- <span id="emailid-availability" style="font-size:12px;"></span> -->
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Mobile No</label>
                                <input type="text" name="mobNumber" id="mobNumber" class="form-control" placeholder="Enter your Mobile No" maxlength="10" autocomplete="off" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Address</label>
                                <textarea name="address" id="address" placeholder="Enter your Address" class="form-control" autocomplete="offs" required></textarea>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Preplacement</label>
                                <select class="form-control" name="preplacement" id="preplacement" required>
                                    <option value="">Select Preplacement</option>
                                    <?php
                                        try {
                                            // Query to get data from tblpreplacement
                                            $sql = "SELECT id, preplacement FROM tblpreplacement";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();

                                            // Populate the dropdown with preplacement values
                                            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                echo "<option value='" . htmlentities($row['id']) . "'>" . htmlentities($row['preplacement']) . "</option>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "<option value=''>Error loading preplacements</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label class="control-label">Password</label>
                                <input type="Password" name="password" id="Password" placeholder="Enter your Password" class="form-control" autocomplete="off" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Confirm Password</label>
                                <input type="password" name="confirmpassword" id="confirmpassword" class="form-control" placeholder="Confirm Password" required>
                                <span id="password-match-error" style="color: red; font-size: 14px;"></span>
                            </div>

                            <div class="form-group col-md-4 align-self-end">
                                <input type="Submit" name="Submit" id="Submit" class="btn btn-primary" value="Submit">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Essential javascripts for application to work-->
    <script src="../../js/jquery-3.2.1.min.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <script src="../../js/main.js"></script>
    <script src="../../js/plugins/pace.min.js"></script>
</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const passwordField = document.getElementById('Password');
    const confirmPasswordField = document.getElementById('confirmpassword');
    const errorSpan = document.getElementById('password-match-error');

    function checkPasswordMatch() {
        if (confirmPasswordField.value.length > 0) {
            if (passwordField.value !== confirmPasswordField.value) {
                errorSpan.textContent = "Passwords do not match";
            } else {
                errorSpan.textContent = "";
            }
        } else {
            errorSpan.textContent = "";
        }
    }

    passwordField.addEventListener('input', checkPasswordMatch);
    confirmPasswordField.addEventListener('input', checkPasswordMatch);
});
</script>




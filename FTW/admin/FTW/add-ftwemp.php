<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../include/config.php';

$errormsg = "";
$msg = "";
$lastInsertId = "";

if (isset($_POST['Submit'])) {
    $staffid        = $_POST['staffic'];
    $fullname       = $_POST['fullname'];
    $contactno      = $_POST['contactno'];
    $address        = $_POST['address'];
    $bod            = $_POST['bod'];
    $gender         = $_POST['gender'];

    $stmt = $dbh->prepare("SELECT COUNT(*) FROM tblftwworker WHERE StaffICPassport = :staffid");
    $stmt->bindParam(':staffid', $staffid, PDO::PARAM_STR);
    $stmt->execute();
    $exists = $stmt->fetchColumn();

    if (!preg_match("/^[a-zA-Z\s]+$/", $fullname)) {
        $errormsg = "Full Name can only contain alphabetic characters and spaces.";
    }

    elseif (!preg_match("/^\d+$/", $contactno)) {
        $errormsg = "Contact No can only contain numbers.";
    }

    elseif (!preg_match("/^[a-zA-Z0-9\s,.-]+$/", $address)) {
        $errormsg = "Address can only contain alphanumeric characters, spaces, commas, dots, and hyphens.";
    }

    if ($errormsg == "") {
        $sql = "INSERT INTO tblftwworker (
            StaffICPassport, fullname, contactno, address, BOD, gender
        ) VALUES (
            :staffid, :fullname, :contactno, :address, :bod, :gender
        )";

        $query = $dbh->prepare($sql);
        $query->bindParam(':staffid', $staffid, PDO::PARAM_STR);
        $query->bindParam(':fullname', $fullname, PDO::PARAM_STR);
        $query->bindParam(':contactno', $contactno, PDO::PARAM_STR);
        $query->bindParam(':address', $address, PDO::PARAM_STR);
        $query->bindParam(':bod', $bod, PDO::PARAM_STR);
        $query->bindParam(':gender', $gender, PDO::PARAM_STR);
        
        if ($query->execute()) {
            $lastInsertId = $dbh->lastInsertId();
            if ($lastInsertId > 0) {
                $msg = "Information added successfully!";
            } else {
                $errormsg = "Data insertion failed.";
            }
        } else {
            $errInfo = $query->errorInfo();
            $errormsg = "SQL Error: " . $errInfo[2];
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
    <link rel="stylesheet" type="text/css" href="../../css/main.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  </head>
  <body class="app sidebar-mini rtl">
   <?php include '../include/header.php'; ?>
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include '../include/sidebar.php'; ?>
    <main class="app-content">

      <div class="row">
        <div class="col-md-12">
          <div class="tile">
                  <h3 align="center">Fitness to Work Assessment</h3>
              <hr />
          
          <?php if($msg){ ?>
          <div class="alert alert-success" role="alert">
            <strong>Well done!</strong> <?php echo htmlentities($msg);?>
          </div>
          <div style="margin-bottom: 20px;">
            <a href="find.php" class="btn btn-cancel">Back </a>
            <a href="add-ftws.php?empid=<?php echo $lastInsertId ?>" class="btn btn-success">Add Assessment </a>
          </div>
          <?php } ?>

          <!---Error Message--->
          <?php if($errormsg){ ?>
          <div class="alert alert-danger" role="alert">
          <strong>Oh snap!</strong> <?php echo htmlentities($errormsg);?></div>
          <?php } ?>

            <div class="tile-body">
              <form class="row" method="post" enctype="multipart/form-data">

                <div class="form-group col-md-12">
                  <h5>Worker Details</h5>
                </div>

                 <div class="form-group col-md-6">
                  <label class="control-label">IC/Passport No</label>
                  <input  name="staffic" id="staffic" class="form-control" type="text" autocomplete="off" required placeholder="Enter IC/Passport">
                </div>

                <div class="form-group col-md-6">
                  <label class="control-label">Full Name</label>
                  <input class="form-control" name="fullname" id="fullname" type="text" placeholder="As in the I/C or Passport" autocomplete="off" required>
                </div>

                <div class="form-group col-md-6">
                  <label class="control-label">Contact No</label>
                  <input class="form-control" name="contactno" id="contactno" type="text" placeholder="Enter Contact Number" autocomplete="off" required>
                </div>

                 <div class="form-group col-md-6">
                  <label class="control-label">Home/Company Address</label>
                 <input  name="address" id="address" class="form-control" placeholder="Enter Address" autocomplete="off" required>
                 </div>

                <div class="form-group col-md-6">
                  <label class="control-label">Birth Date</label>
                  <input type="date"  name="bod" id="bod" class="form-control" autocomplete="off" required>
                   </div>

                 <div class="form-group col-md-6">
                    <label class="control-label">Gender</label>
                     <select name="gender" id="gender" class="form-control" required>
                        <option value="">Choose Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                      </select>
                </div>

                <!-- Submit Button -->
                <div class="form-group col-md-12 align-self-end">
                  <input type="Submit" name="Submit" id="Submit" class="btn btn-primary" value="Submit">
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </main>

    <script src="../../js/jquery-3.2.1.min.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <script src="../../js/main.js"></script>

  </body>
</html>

<?php 
session_start();
error_reporting(0);
require_once('../include/config.php');

// Check if session is valid
if(strlen($_SESSION["id"]) == 0) {   
    header('location:../index.php');
} else {
    $empid = $_SESSION['id'];

    if(isset($_POST['Submit'])){
        $name = $_POST['name'];
        $address = $_POST['address'];
        $placeid = $_POST['placeid'];

        // Error handling for name
        if (empty($name)) {
            $ferrormsg = "Please Enter Full Name";
        }

        // SQL query to update the profile excluding the fields you mentioned
        $sql = "UPDATE tbluser 
                SET name=:name, address=:address, placeid=:placeid 
                WHERE id=:empid";

        $query = $dbh->prepare($sql);
        $query->bindParam(':empid', $empid, PDO::PARAM_STR);
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':address', $address, PDO::PARAM_STR);
        $query->bindParam(':placeid', $placeid, PDO::PARAM_INT); 
        $lastInsertId = $query->execute();

        if($lastInsertId) {
            $msg = "Information Updated Successfully";
        } else {
            $errormsg = "Information Update Failed";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>My Profile</title>
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
            <h2 align="center">Update Employee</h2>
            <hr />
            <!-- Success Message-->  
            <?php if($msg) { ?>
            <div class="alert alert-success" role="alert">
              <strong>Well done!</strong> <?php echo htmlentities($msg); ?>
            </div>
            <?php } ?>

            <!-- Error Message-->
            <?php if($errormsg) { ?>
            <div class="alert alert-danger" role="alert">
              <strong>Oh snap!</strong> <?php echo htmlentities($errormsg); ?>
            </div>
            <?php } ?>
            
            <div class="tile-body">
              <?php
                  $sql = "SELECT u.id, u.name, u.email, u.mobile, u.address, u.placeid, p.preplacement 
                          FROM tbluser u
                          LEFT JOIN tblpreplacement p ON u.placeid = p.id
                          WHERE u.id=:empid";
                  $query = $dbh->prepare($sql);
                  $query->bindParam(':empid', $empid, PDO::PARAM_STR);
                  $query->execute();
                  $results = $query->fetchAll(PDO::FETCH_OBJ);
                  if($query->rowCount() > 0) {
                      foreach($results as $result) { 
              ?>
              <form class="row" method="post" enctype="multipart/form-data">
                  <div class="form-group col-md-12">
                      <label class="control-label">Emp ID</label>
                      <input class="form-control" name="EmpId" id="EmpId" type="text" placeholder="Employee ID" autocomplete="off" value="<?php echo $result->id;?>" readonly>
                  </div>

                  <div class="form-group col-md-6">
                      <label class="control-label">Full Name</label>
                      <input class="form-control" name="name" id="name" type="text" placeholder="Enter your Full Name" autocomplete="off" value="<?php echo $result->name;?>">
                      <span style="color: red;"><?php echo $ferrormsg;?></span>
                  </div>

                  <div class="form-group col-md-6">
                      <label class="control-label">Email ID</label>
                      <input class="form-control" type="text" name="email" id="email" placeholder="Enter your Email" readonly autocomplete="off" value="<?php echo $result->email;?>">
                  </div>

                  <div class="form-group col-md-6">
                      <label class="control-label">Mobile No</label>
                      <input type="text" name="mobNumber" id="mobNumber" class="form-control" placeholder="Enter your Mobile No" maxlength="10" autocomplete="off" value="<?php echo $result->mobile;?>" readonly>
                  </div>

                  <div class="form-group col-md-12">
                      <label class="control-label">Address</label>
                      <textarea name="address" id="address" placeholder="Enter your Address" class="form-control" autocomplete="off"><?php echo $result->address;?></textarea> 
                  </div>

                  <div class="form-group col-md-6">
                      <label class="control-label">Preplacement</label>
                      <select class="form-control" name="placeid" required>
                          <option value="">Select Preplacement</option>
                          <?php
                          $preplacements = $dbh->prepare("SELECT id, preplacement FROM tblpreplacement");
                          $preplacements->execute();
                          $placements = $preplacements->fetchAll(PDO::FETCH_OBJ);

                          foreach ($placements as $placement) {
                              $selected = $placement->id == $result->placeid ? 'selected' : '';
                              echo "<option value='" . $placement->id . "' " . $selected . ">" . $placement->preplacement . "</option>";
                          }
                          ?>
                      </select>
                  </div>

                  <div class="form-group col-md-4 align-self-end">
                      <input type="Submit" name="Submit" id="Submit" class="btn btn-primary" value="Update">
                  </div>
              </form>
              <?php } } ?>
            </div>
          </div>
        </div>
      </div>
    </main>
    <script src="../../js/jquery-3.2.1.min.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <script src="../../js/main.js"></script>
    <script src="../../js/plugins/pace.min.js"></script>
  </body>
</html>

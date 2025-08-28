<?php
// Include configuration for database connection
include '../include/config.php';

$empid = $_GET['empid'];

// Query to fetch employee details including placeid
$sql = "SELECT u.id, u.name, u.email, u.mobile, u.address, u.placeid, p.preplacement
        FROM tbluser u
        LEFT JOIN tblpreplacement p ON u.placeid = p.id
        WHERE u.id = :empid";
        
$query = $dbh->prepare($sql);
$query->bindParam(':empid', $empid, PDO::PARAM_STR);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

$cnt = 1;
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
            <div class="tile-body">
              <?php
              // If employee details are found, display them
              if($query->rowCount() > 0) {
                foreach($results as $result) {
              ?>
              <h2 class="text-center text-primary">User Details: <?php echo $result->name; ?> (ID: <?php echo $result->id; ?>)</h2>
              <hr class="my-4">

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="empId" class="font-weight-bold">User ID</label>
                    <input type="text" class="form-control" id="empId" value="<?php echo $result->id; ?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="name" class="font-weight-bold">Name</label>
                    <input type="text" class="form-control" id="name" value="<?php echo $result->name; ?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="email" class="font-weight-bold">Email Address</label>
                    <input type="email" class="form-control" id="email" value="<?php echo $result->email; ?>" readonly>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="mobile" class="font-weight-bold">Mobile Number</label>
                    <input type="text" class="form-control" id="mobile" value="<?php echo $result->mobile; ?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="address" class="font-weight-bold">Address</label>
                    <textarea class="form-control" id="address" rows="4" readonly><?php echo $result->address; ?></textarea>
                  </div>
                  <div class="form-group">
                    <label for="placeid" class="font-weight-bold">Preplacement</label>
                    <input type="text" class="form-control" id="placeid" value="<?php echo $result->preplacement; ?>" readonly>
                  </div>
                </div>
              </div>

              <div class="form-group text-center mt-4">
                <a href="edit-user.php?empid=<?php echo htmlentities($result->id);?>" class="btn btn-success">Edit Details</a>
              </div>

              <?php  
                $cnt = $cnt + 1; 
                } 
              } else {
                  echo "<p>No results found for EmpId: " . $empid . "</p>";
              }
              ?>
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
    <script type="text/javascript" src="../../js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="../../js/plugins/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">$('#sampleTable').DataTable();</script>
  </body>
</html>

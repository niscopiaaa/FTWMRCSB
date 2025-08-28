<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../include/config.php';

$msg = "";
$errormsg = "";
$ferrormsg = "";
$lerrormsg = "";

if (!isset($_GET['empid'])) {
    echo "Employee ID is not provided.";
    exit;
}

$empid = $_GET['empid'];

if (isset($_POST['Submit'])) {
    $name = $_POST['fname'];
    $email = $_POST['email'];
    $mobNumber = $_POST['mobNumber'];
    $address = $_POST['address'];
    $placeid = $_POST['placeid']; // New field for placeid

    // Validate required fields
    if (empty($name)) {
        $ferrormsg = "Please Enter First Name";
    } else {
        // Update query to include the placeid field
        $sql = "UPDATE tbluser SET name = :name,
                email = :email, mobile = :mobNumber, address = :address, placeid = :placeid
                WHERE id = :empid";

        // Prepare and execute the query
        $query = $dbh->prepare($sql);
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':mobNumber', $mobNumber, PDO::PARAM_STR);
        $query->bindParam(':address', $address, PDO::PARAM_STR);
        $query->bindParam(':placeid', $placeid, PDO::PARAM_INT); // Bind the placeid
        $query->bindParam(':empid', $empid, PDO::PARAM_INT);
        $query->execute();

        if ($query->rowCount() > 0) {
            $msg = "Information Successfully Updated";
        } else {
            $errormsg = "Data not updated successfully";
        }
    }
}

// Fetch current employee data for the update form
$sql = "SELECT id, name, email, mobile, address, placeid 
        FROM tbluser 
        WHERE id = :empid";  

$query = $dbh->prepare($sql);
$query->bindParam(':empid', $empid, PDO::PARAM_INT);
$query->execute();

// Check if data is returned
if ($query->rowCount() == 0) {
    echo "No results found for EmpId: " . $empid;
    exit;
}

$results = $query->fetchAll(PDO::FETCH_OBJ);

// Fetch available placements for the dropdown
$preplacements = $dbh->prepare("SELECT id, preplacement FROM tblpreplacement");
$preplacements->execute();
$placements = $preplacements->fetchAll(PDO::FETCH_OBJ);
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
                    <h2 align="center">Update User Details</h2>
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
                        <strong>Oh snap!</strong> <?php echo htmlentities($errormsg); ?>
                    </div>
                    <?php } ?>

                    <div class="tile-body">
                        <?php
                        // Populate form with fetched employee data
                        foreach ($results as $result) {
                        ?>
                        <form class="row" method="post" enctype="multipart/form-data">
                            <div class="form-group col-md-12">
                                <label class="control-label">User ID</label>
                                <input name="empcode" id="empcode" class="form-control" type="text" readonly value="<?php echo $result->id; ?>">
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">First Name</label>
                                <input class="form-control" name="fname" type="text" value="<?php echo $result->name; ?>">
                                <span style="color: red;"><?php echo $ferrormsg; ?></span>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Email ID</label>
                                <input class="form-control" name="email" type="text" readonly value="<?php echo $result->email; ?>">
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Mobile No</label>
                                <input class="form-control" name="mobNumber" type="text" value="<?php echo $result->mobile; ?>">
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Address</label>
                                <textarea class="form-control" name="address" placeholder="Enter your Address"><?php echo $result->address; ?></textarea>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Preplacement</label>
                                <select class="form-control" name="placeid" required>
                                    <option value="">Select Preplacement</option>
                                    <?php
                                    foreach ($placements as $placement) {
                                        // Check if the current placement matches the user's current placeid
                                        $selected = $placement->id == $result->placeid ? 'selected' : '';
                                        echo "<option value='" . $placement->id . "' " . $selected . ">" . $placement->preplacement . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group col-md-4 align-self-end">
                                <input type="Submit" name="Submit" class="btn btn-primary" value="Update">
                            </div>
                        </form>
                        <?php 
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
</body>
</html>

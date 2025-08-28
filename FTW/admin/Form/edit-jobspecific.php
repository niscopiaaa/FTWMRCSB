<?php 
error_reporting(0);
include  '../include/config.php';
$jobid=$_GET['jobid'];
if(isset($_POST['submit'])){
    $JobSpecific = $_POST['JobSpecific'];
    $sql="update tbljobspecific set jobspecific=:JobSpecific where id=:jobid";
    $query = $dbh -> prepare($sql);
    $query->bindParam(':JobSpecific',$JobSpecific,PDO::PARAM_STR);
    $query->bindParam(':jobid',$jobid,PDO::PARAM_STR);
    $query -> execute();
    if ($query->rowCount() > 0) {
        $msg = "Job Specific update Successfully";
    } else {
        $errormsg = "Job Specific not update Successfully";
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
         
        <div class="col-md-6">
            <div class="tile">
                <h2 align="center"> Update Job Specific Type</h2>
                <hr />
            <!---Success Message--->  
            <?php if($msg){ ?>
            <div class="alert alert-success" role="alert">
                <strong>Well done!</strong> <?php echo htmlentities($msg);?>
            </div>
            <?php } ?>

            <!---Error Message--->
            <?php if($errormsg){ ?>
            <div class="alert alert-danger" role="alert">
                <strong>Oh snap!</strong> <?php echo htmlentities($errormsg);?>
            </div>
            <?php } ?>

           
            <div class="tile-body">
               <?php
                  
                $sql="SELECT * FROM tbljobspecific where id=:jobid";


                $query= $dbh->prepare($sql);
                $query->bindParam(':jobid',$jobid, PDO::PARAM_STR);
                $query-> execute();
                $results = $query -> fetchAll(PDO::FETCH_OBJ);
                $cnt=1;
                if($query -> rowCount() > 0)
                {
                foreach($results as $result)
                {
                ?>
              <form  method="post">
                <div class="form-group col-md-12">
                  <label class="control-label">Update Job Specific Name</label>
                  <input class="form-control" name="JobSpecific" id="JobSpecific" type="text" placeholder="Enter Add Leave Name" value="<?php echo $result->JobSpecific;?>">
                </div>
                <div class="form-group col-md-4 align-self-end">
                
                  <input type="submit" name="submit" id="submit" class="btn btn-primary" value=" Submit">
                </div>
              </form>
                <?php  $cnt=$cnt+1; } } ?>
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
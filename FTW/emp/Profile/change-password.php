<?php 
session_start();
error_reporting(E_ALL);
require_once('../include/config.php');
$error = "";
$msg = "";

if(strlen( $_SESSION["id"])==0){   
    header('location:../index.php');
    exit();
}
else{
if (isset($_POST['submit'])) {
    $password = md5($_POST['password']); 
    $newpassword = md5($_POST['newpassword']); 
    $id = $_SESSION['id'];

    // Check current password
    $sql = "SELECT password FROM tbluser WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    if ($result) {
        if ($result->password === $password) {
            $updateSql = "UPDATE tbluser SET password = :newpassword WHERE id = :id";
            $updateQuery = $dbh->prepare($updateSql);
            $updateQuery->bindParam(':id', $id, PDO::PARAM_INT);
            $updateQuery->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
            $updateQuery->execute();

            $msg = "Your password has been successfully changed.";
        } else {
            $error = "Your current password is not valid.";
        }
    } else {
        $error = "User not found.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
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
              <h3 align="center">Change Password</h3>
              <hr />
             <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
        else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
            
            <div class="tile-body">
              <form class="row" method="post" name="chngpwd" onsubmit="return valid();">
                <div class="form-group col-md-12">
                  <label class="control-label">Old Password</label>
                <input type="password" name="password" id="password" placeholder="Old Password" class="form-control" autocomplete="off" required>
                </div>
                <div class="form-group col-md-12">
                  <label class="control-label">New Password</label>
                <input type="password" name="newpassword" id="newpassword" class="form-control" placeholder="New Password" autocomplete="off" required>
                </div>
                 <div class="form-group col-md-12">
                  <label class="control-label">Confirm Password</label>
                  <input type="password" name="confirmpassword" id="confirmpassword" placeholder="Confirm Password" autocomplete="off" class="form-control" required>

                </div>
                 
                <div class="form-group col-md-4 align-self-end">
                  <input type="Submit" name="submit" id="submit" class="btn btn-primary" value="Submit">
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
    <!-- The javascript plugin to display page loading on top-->
    <script src="../../js/plugins/pace.min.js"></script>
    <!-- Page specific javascripts-->
    <!-- Data table plugin-->
    <script type="text/javascript" src="../../js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="../../js/plugins/dataTables.bootstrap.min.js"></script>
  <script type="text/javascript">

function valid(){
    if(document.chngpwd.newpassword.value!= document.chngpwd.confirmpassword.value){
      alert("New Password and Confirm Password do not match!");
      document.chngpwd.confirmpassword.focus();
      return false;
    }
return true;
}
</script>
  <style>
    .errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
    </style>
  </body>
</html>
<?php } ?>
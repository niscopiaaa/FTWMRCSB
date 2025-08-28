<?php
session_start();
error_reporting(0);
require_once('include/config.php');
$msg = ""; 

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = md5(trim($_POST['password']));  
    
    if ($email != "" && $password != "") {
        try {
            $query = "SELECT id, name, email, mobile, address, password, placeid 
                      FROM tbluser 
                      WHERE email = :email AND password = :password";
            
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->execute();
            
            $count = $stmt->rowCount();  
            $row = $stmt->fetch(PDO::FETCH_ASSOC);  
            
            if ($count == 1 && !empty($row)) {
                
                $_SESSION['id'] = $row['id'];
                $_SESSION['name'] = $row['name'];  
                $_SESSION['email'] = $row['email'];
                $_SESSION['mobile'] = $row['mobile']; 
                
                header("location: FTW/find.php");
            } else {
                $msg = "Invalid email or password!";
            }
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage();
        }
    } else {
        $msg = "Both fields are required!";
    }
}

if (isset($_POST['reset'])) {
    $email = $_POST['email'];
    $mobile = $_POST['fmobno'];
    $newpassword = md5($_POST['newpwd']);  

    $sql = "SELECT id FROM tbluser WHERE email = :email AND mobile = :mobile";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        $con = "UPDATE tbluser SET password = :newpassword WHERE email = :email AND mobile = :mobile";
        $chngpwd1 = $dbh->prepare($con);
        $chngpwd1->bindParam(':email', $email, PDO::PARAM_STR);
        $chngpwd1->bindParam(':mobile', $mobile, PDO::PARAM_STR);
        $chngpwd1->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
        $chngpwd1->execute();
        
        echo "<script>alert('Your password has been successfully changed');</script>";
    } else {
        echo "<script>alert('Email or Mobile number is invalid');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Login - Health Assessment for Fitness to Work</title>
    <link rel="icon" type="image/png" href="img/petronas.gif">
  </head>
  <body>
    <section class="material-half-bg">
      <div class="cover"></div>
    </section>
    <section class="login-content" style="height:500px;">
      <div class="logo">
        <h1>Health Assessment for Fitness to Work</h1>
      </div>
      <div class="login-box">
        <form class="login-form" method="post">
          <h6 class="login-head"><i class=""></i> Health Assessment for Fitness to Work</h6>
          
          <?php if($msg){ ?>
          <div class="alert alert-danger" role="alert">
            <strong>Oh snap!</strong> <?php echo htmlentities($msg);?>
          </div>
          <?php } ?>

          <div class="form-group">
            <label class="control-label">USERNAME</label>
            <input class="form-control" name="email" id="email" type="text" placeholder="Email" autofocus value="<?php echo $email;?>">
          </div>

          <div class="form-group">
            <label class="control-label">PASSWORD</label>
            <input class="form-control" name="password" id="password" type="password" placeholder="Password">
          </div>

          <div class="form-group">
            <div class="utility">
              <p class="semibold-text mb-2"><a href="#" data-toggle="flip">Forgot Password ?</a></p>
            </div>
          </div>

          <div class="form-group btn-container">
            <input type="submit" name="submit" id="submit" value="SIGN IN" class="btn btn-primary btn-block">
          </div>
        </form>

        <form class="forget-form" method="post">
          <h3 class="login-head"><i class="fa fa-lg fa-fw fa-lock"></i> Forgot Password?</h3>
          <div class="form-group">
            <label class="control-label">Email</label>
            <input class="form-control" type="text" placeholder="Email" name="email" required>
          </div>

          <div class="form-group">
            <label class="control-label">Mobile No</label>
            <input class="form-control" type="text" placeholder="Mobile Number" name="fmobno" required>
          </div>

          <div class="form-group">
            <label class="control-label">New Password</label>
            <input class="form-control" type="text" placeholder="New password" name="newpwd" required>
          </div>

          <div class="form-group btn-container">
            <button class="btn btn-primary btn-block" type="submit" name="reset"><i class="fa fa-unlock fa-lg fa-fw"></i> RESET</button>
          </div>

          <div class="form-group mt-3">
            <p class="semibold-text mb-0"><a href="#" data-toggle="flip"><i class="fa fa-angle-left fa-fw"></i> Back to Login</a></p>
          </div>
        </form>
      </div>
    </section>
    
    <script src="../js/jquery-3.2.1.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    <script src="../js/plugins/pace.min.js"></script>
    <script type="text/javascript">
      $('.login-content [data-toggle="flip"]').click(function() {
        $('.login-box').toggleClass('flipped');
        return false;
      });
    </script>
  </body>
</html>

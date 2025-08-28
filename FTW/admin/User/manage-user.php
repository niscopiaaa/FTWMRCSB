<?php
//delete assessment and related jobs
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    try {
        include '../include/config.php';

        $stmt1 = $dbh->prepare("DELETE FROM tbluser WHERE id = :id");
        $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt1->execute();

        echo "<script>alert('Successfully delete the user');</script>";
        echo "<script>window.location.href='../User/manage-user.php'</script>";
    } catch (Exception $e) {
        $dbh->rollBack();
        echo "<script>alert('Error deleting assessment: " . $e->getMessage() . "');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="description" content="Vali is a responsive">
    <title>Health Assessment for Fitness to Work</title>
    <link rel="icon" type="image/png" href="../../img/petronas.gif">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../../css/main.css">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        #sampleTable th, #sampleTable td 
        {
        text-align: center;
        }
    </style>
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
                        <h2 align="center">Manage User</h2>
                        <hr />
                        <table class="table table-hover table-bordered" id="sampleTable">
                            <thead>
                                <tr>
                                    <th>Sr.No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
   
                            <?php
                            include '../include/config.php';
                            $sql = "SELECT id, name, email, mobile, address, create_date FROM tbluser";
                            $query = $dbh->prepare($sql);
                            $query->execute();
                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                            $cnt = 1;
                            if ($query->rowCount() > 0) {
                                foreach ($results as $result) {
                            ?>
                                    <tr>
                                        <td><?php echo($cnt); ?></td>
                                        <td><?php echo htmlentities($result->name); ?></td>
                                        <td><?php echo htmlentities($result->email); ?></td>
                                        <td><?php echo htmlentities($result->mobile); ?></td>
                                        <td>
                                            <div class="text-center">
                                                <a href="user-details.php?empid=<?php echo htmlentities($result->id); ?>" class="btn btn-info me-2">
                                                    <i class="bi bi-eye me-1"></i> 
                                                </a>

                                                <a href="manage-user.php?del=<?php echo htmlentities($result->id); ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger">
                                                    <i class="bi bi-trash me-1"></i> 
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                    $cnt++;
                                }
                            }
                            ?>
                     </tbody>
                        </table>
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
    <script type="text/javascript">$('#sampleTable').DataTable();</script>
</body>
</html>

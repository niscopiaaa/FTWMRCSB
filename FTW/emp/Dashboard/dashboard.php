<?php 
session_start();
error_reporting(0);
require_once('../include/config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Health Assessment for Fitness to Work</title>
    <link rel="icon" type="image/png" href="../../img/petronas.gif">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../../css/main.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .app-sidebar__overlay {
            background-color: rgba(0, 0, 0, 0.5);
        }
        .widget-small {
            border-radius: 50px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .widget-small:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }
        .widget-small .icon {
            background: #f1f1f1;
            padding: 20px;
            border-radius: 50%;
            font-size: 2.5em;
            color: #fff;
            margin-right: 20px;
        }
        .widget-small .info {
            padding: 20px;
        }
        .widget-small h4 {
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .widget-small p {
            font-size: 1.5em;
            color: #555;
        }
        .primary .icon { background-color: #4e73df; }
        .warning .icon { background-color: #f6c23e; }
        .danger .icon { background-color: #e74a3b; }
        .coloured-icon .info a {
            text-decoration: none;
            color: inherit;
            font-weight: 500;
        }
        .breadcrumb-item a {
            color: #007bff;
        }
        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="app sidebar-mini">
    <?php include '../include/header.php'; ?>
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include '../include/sidebar.php'; ?>
    <main class="app-content">
        <div class="row">
            <?php
                $ret=$dbh->prepare("SELECT DISTINCT id FROM tblftwworker");
                $ret-> execute();
                $resultss = $ret -> fetchAll(PDO::FETCH_OBJ);
                $listeddept=$ret -> rowCount();
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="widget-small warning coloured-icon"><i class="icon fa fa-files-o fa-3x"></i>
                    <div class="info">
                        <a href="../FTW/find.php">
                            <h4>List FTW Registered</h4>
                            <p><b><?php echo $listeddept;?></b></p>
                        </a>
                    </div>
                </div>
            </div>
            <?php
                $sql=$dbh->prepare("SELECT id FROM tblftw_assessment_job");
                $sql-> execute();
                $result = $sql -> fetchAll(PDO::FETCH_OBJ);
                $listedleavetype=$sql -> rowCount();
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="widget-small danger coloured-icon"><i class="icon fa fa-star fa-3x"></i>
                    <div class="info">  
                        <a href="../Report/ftw-report.php">
                            <h4>Reports</h4>
                            <p><b><?php echo $listedleavetype;?></b></p>
                        </a>
                    </div>
                </div>
            </div>
            <?php
                $query=$dbh->prepare("SELECT id FROM tbluser");
                $query-> execute();
                $results = $query -> fetchAll(PDO::FETCH_OBJ);
                $regemp=$query -> rowCount();
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
                    <div class="info">
                        <a href="manage-employee.php">
                            <h4>Registered Users</h4>
                            <p><b><?php echo $regemp;?></b></p>
                        </a>
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
    <script type="text/javascript" src="../../js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="../../js/plugins/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">$('#sampleTable').DataTable();</script>
    <script type="text/javascript" src="../../js/plugins/chart.js"></script>
</body>
</html>
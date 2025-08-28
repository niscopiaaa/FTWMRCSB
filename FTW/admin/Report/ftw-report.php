<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../include/config.php';

// Handle search query
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Handling date selection
if (isset($_POST['Submit'])) {
    $fdate = $_POST['fdate'];
    $tdate = $_POST['todate'];
}

// Delete record if `del` parameter is set
if (isset($_GET['del'])) {
    $assessment_id = $_GET['del'];

    try {
        $dbh->beginTransaction();

        $stmt1 = $dbh->prepare("DELETE FROM tblftw_assessment_job WHERE assessment_id = :assessment_id");
        $stmt1->bindParam(':assessment_id', $assessment_id, PDO::PARAM_INT);
        $stmt1->execute();

        // Then delete the main assessment
        $stmt2 = $dbh->prepare("DELETE FROM tblftw_assessment WHERE id = :assessment_id");
        $stmt2->bindParam(':assessment_id', $assessment_id, PDO::PARAM_INT);
        $stmt2->execute();

        $dbh->commit();

        echo "<script>alert('Assessment and related job(s) deleted successfully.');</script>";
        echo "<script>window.location.href='ftw-report.php'</script>";
    } catch (Exception $e) {
        $dbh->rollBack();
        echo "<script>alert('Error deleting assessment: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Health Assessment for Fitness to Work">
    <title>Health Assessment for Fitness to Work</title>
    <link rel="icon" type="image/png" href="../../img/petronas.gif">
    <link rel="stylesheet" type="text/css" href="../../css/main.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Custom color classes for Result FTW */
        .result-pass {
            background-color: #28a745;
            color: white;
            text-align: center;
            padding: 5px;
            border-radius: 5px;
        }
        .result-fail {
            background-color: #dc3545;
            color: white;
            text-align: center;
            padding: 5px;
            border-radius: 5px;
        }
        .result-pending {
            background-color: #ffc107;
            color: black;
            text-align: center;
            padding: 5px;
            border-radius: 5px;
        }

        /* Custom table styles */
        .table th, .table td {
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #e0e0e0;
        }
        .table th {
            background-color: #f9f9f9;
        }
        .table tbody tr:hover {
            background-color: #fafafa;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .table th, .table td {
                padding: 8px 10px;
            }
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
                    <h3 class="tile-title">FTW Report</h3>
                    <div class="tile-body">
                        <form method="POST">
                            <div class="form-group col-md-6">
                                <label class="control-label">From Date</label>
                                <input class="form-control" type="date" name="fdate" id="fdate" placeholder="Enter From Date">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">To Date</label>
                                <input class="form-control" type="date" name="todate" id="todate" placeholder="Enter To Date">
                            </div>
                            <div class="form-group col-md-4 align-self-end">
                                <input type="submit" name="Submit" class="btn btn-primary" value="Submit">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php if (isset($fdate) && isset($tdate)) { ?>
        <div class="row">
                <div class="col-md-12">
                    <div class="tile">
                        <div class="tile-body">
                            <h2 align="center">FTW Report from <?php echo date("d-m-Y", strtotime($fdate)); ?> To <?php echo date("d-m-Y", strtotime($tdate)); ?></h2>
                            <hr />
                            <table class="table table-hover table-bordered" id="sampleTable">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Staff IC/Passport</th>
                                        <th>Full Name</th>
                                        <th>Contact No</th>
                                        <th>Gender</th>
                                        <th>Place of Exam</th>
                                        <th>Date of Exam</th>
                                        <th>Job Specific</th>
                                        <th>Status FTW</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT 
                                        a.id AS assessment_id,
                                        a.workerid AS emp_id,
                                        w.fullname,
                                        w.StaffICPassport,
                                        w.contactno,
                                        w.gender,
                                        w.BOD,
                                        (SELECT preplacement FROM tblpreplacement WHERE id = j.place_id) AS place_exam,
                                        j.exam_date,
                                        (SELECT jobspecific FROM tbljobspecific WHERE id = j.job_id) AS JobSpecific,
                                        j.status_ftw
                                    FROM 
                                        tblftwworker w
                                    JOIN 
                                        tblftw_assessment a ON w.id = a.workerid
                                    JOIN 
                                        tblftw_assessment_job j ON a.id = j.assessment_id
                                    WHERE 
                                        (w.StaffICPassport LIKE :search OR w.fullname LIKE :search)
                                        AND DATE(j.exam_date) BETWEEN :fdate AND :tdate
                                    ORDER BY 
                                        j.exam_date DESC, w.fullname ASC";
                                    
                                    
                                    $query = $dbh->prepare($sql);
                                    $query->bindValue(':search', '%' . $searchTerm . '%', PDO::PARAM_STR);
                                    $query->bindParam(':fdate', $fdate, PDO::PARAM_STR);
                                    $query->bindParam(':tdate', $tdate, PDO::PARAM_STR);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                    $cnt = 1;
                                    if ($query->rowCount() > 0) {
                                        foreach ($results as $result) {
                                            $statusClass = ''; 
                                        if ($result->status_ftw == 'Fit') {
                                            $statusClass = 'result-pass';  // Green (Fit)
                                        } elseif ($result->status_ftw == 'Unfit') {
                                            $statusClass = 'result-fail';  // Red (Unfit)
                                        } elseif ($result->status_ftw == 'Fit with Exception' || $result->status_ftw == 'Fit with Restriction') {
                                            $statusClass = 'result-pending';  // Yellow (Fit with Exception or Fit with Restriction)
                                        }
                                    ?>
                                            <tr>
                                                <td><?php echo($cnt); ?></td>
                                                <td><?php echo htmlentities($result->StaffICPassport); ?></td>
                                                <td><?php echo htmlentities($result->fullname); ?></td>
                                                <td><?php echo htmlentities($result->contactno); ?></td>
                                                <td><?php echo htmlentities($result->gender); ?></td>
                                                <td><?php echo htmlentities($result->place_exam); ?></td>
                                                <td><?php echo htmlentities($result->exam_date); ?></td>
                                                <td><?php echo htmlentities($result->JobSpecific); ?></td>
                                                <td class="<?php echo $statusClass; ?>"><?php echo htmlentities($result->status_ftw); ?></td>
                                                <td>
                                                    <a href="edit-report.php?StaffICPassport=<?php echo htmlentities($result->StaffICPassport); ?>&assessment_id=<?php echo htmlentities($result->assessment_id); ?>"
                                                    class="btn btn-success">
                                                    <i class="bi bi-pencil-square"></i> 
                                                    </a>

                                                    <a href="ftw-report.php?del=<?php echo htmlentities($result->assessment_id); ?>"
                                                    onclick="return confirm('Are you sure you want to delete this record?');"
                                                    class="btn btn-danger">
                                                    <i class="bi bi-trash3"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                    <?php 
                                            $cnt++;
                                        }
                                    } else { 
                                    ?>
                                        <tr>
                                            <th colspan="10" style="color:red">No record found</th>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php }?>
    </main>

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

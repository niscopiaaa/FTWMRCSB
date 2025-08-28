<?php 
require_once '../include/config.php';

// Show errors while debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);


//delete assessment and related jobs
if (isset($_GET['del']) && isset($_GET['StaffICPassport'])) {
    $assessment_id = $_GET['del'];
    $staffICPassport = $_GET['StaffICPassport'];

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
        echo "<script>window.location.href='edit-ftw.php?StaffICPassport=" . urlencode($staffICPassport) . "'</script>";
    } catch (Exception $e) {
        $dbh->rollBack();
        echo "<script>alert('Error deleting assessment: " . $e->getMessage() . "');</script>";
    }
}


// Check if StaffICPassport is passed via GET
if (!isset($_GET['StaffICPassport']) || empty($_GET['StaffICPassport'])) {
    echo "<script>alert('No StaffICPassport provided.');</script>";
    echo "<script>window.location.href='find.php'</script>";
    exit;
}

$staffICPassport = $_GET['StaffICPassport'];

$sql = "SELECT * FROM tblftwworker WHERE StaffICPassport = :staffICPassport";
$query = $dbh->prepare($sql);
$query->bindParam(':staffICPassport', $staffICPassport, PDO::PARAM_STR);
$query->execute();
$employee = $query->fetch(PDO::FETCH_OBJ);

// Check if the employee exists
if (!$employee) {
    echo "<script>alert('Employee not found!');</script>";
    echo "<script>window.location.href='find.php'</script>";
    exit;
}

$sql_all = "SELECT 
            a.id as result_id,
            (SELECT preplacement FROM tblpreplacement WHERE id = r.place_id) as place_exam,
            r.exam_date as date_exam,
            (SELECT jobspecific FROM tbljobspecific WHERE id = r.job_id) as JobSpecific,
            r.status_ftw
            FROM tblftw_assessment a
            LEFT JOIN tblftw_assessment_job r ON a.id = r.assessment_id
            WHERE a.workerid = :empid
            ORDER BY r.exam_date DESC
";
$query_all = $dbh->prepare($sql_all);
$query_all->bindParam(':empid', $employee->id, PDO::PARAM_INT);
$query_all->execute();
$assessments = $query_all->fetchAll(PDO::FETCH_OBJ);
if (isset($_POST['update']) && !empty($_POST['gender'])) {
    $fullname = $_POST['fullname'];
    $contactno = $_POST['contactno'];
    $address = $_POST['address'];
    $BOD = $_POST['BOD'];
    $gender = $_POST['gender'];

    $sql_emp = "UPDATE tblftwworker SET fullname=:fullname, contactno=:contactno, address=:address, 
                BOD=:BOD, gender=:gender WHERE StaffICPassport=:staffICPassport";
    $query_emp = $dbh->prepare($sql_emp);
    $query_emp->bindParam(':fullname', $fullname);
    $query_emp->bindParam(':contactno', $contactno);
    $query_emp->bindParam(':address', $address);
    $query_emp->bindParam(':BOD', $BOD);
    $query_emp->bindParam(':gender', $gender);
    $query_emp->bindParam(':staffICPassport', $staffICPassport);

    if ($query_emp->execute()) {
        echo "<script>alert('Employee record updated successfully.');</script>";
        echo "<script>window.location.href='edit-ftw.php?StaffICPassport=" . urlencode($staffICPassport) . "'</script>";
    } else {
        echo "<script>alert('Error updating employee record.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Worker Record</title>
    <link rel="icon" type="image/png" href="../../img/petronas.gif">
    <link rel="stylesheet" type="text/css" href="../../css/main.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* General Table Styling */
        #sampleTable th, #sampleTable td 
        {
            text-align: center;
        }

        .table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #e0e0e0;
        }

        .table th {
            background-color: #f9f9f9;
            font-weight: 500;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }

        /* Input Styling */
        input[type="text"], input[type="date"], textarea {
            width: 100%;
            padding: 8px 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            height: 60px;
        }

        /* Button Styling */
        .btn-success, .btn-cancel {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            margin: 5px;
        }

        .btn-success {
            background-color: #28a745;
            color: #fff;
            border: none;
        }

        .btn-success:hover {
            opacity: 0.9;
            transform: scale(1.03);
        }

        .btn-cancel {
            background-color: #dc3545;
            color: #fff;
            border: none;
        }

        .btn-cancel:hover {
            opacity: 0.9;
            transform: scale(1.03);
        }

        /* Styling for Result Status */
        .result-pass {
            background-color: #28a745; /* Green */
            color: white;
        }

        .result-fail {
            background-color: #dc3545; /* Red */
            color: white;
        }

        .result-pending {
            background-color: #ffc107; /* Yellow */
            color: white;
        }

        /* Styling for Action Buttons */
        .action-btns {
            text-align: center; /* Center the buttons */
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
                    <h3 align="center">Update Record </h3>
                    <hr />
                    <div class="table-container">
                        <form method="post" action="">
                            <!-- Personal Details Form -->
                            <h4 align="">Personal Details</h4>
                            <table class="table">
                                <tr><th>Staff IC/Passport</th><td><input type="text" class="form-control" name="StaffICPassport" value="<?= htmlentities($employee->StaffICPassport); ?>" readonly></td></tr>
                                <tr><th>Full Name</th><td><input type="text" name="fullname" value="<?= htmlentities($employee->fullname); ?>"></td></tr>
                                <tr><th>Contact No</th><td><input type="text" name="contactno" value="<?= htmlentities($employee->contactno); ?>"></td></tr>
                                <tr><th>Address</th><td><textarea name="address"><?= htmlentities($employee->address); ?></textarea></td></tr>
                                <tr><th>Birth Date</th><td><input type="date" name="BOD" value="<?= htmlentities($employee->BOD); ?>"></td></tr>
                                <tr>
                                    <th>Gender</th>
                                    <td>
                                        <select name="gender" class="form-control" required>
                                            <option value="" >Select Gender</option>
                                            <option value="Male" <?= $employee->gender == 'Male' ? 'selected' : '' ?>>Male</option>
                                            <option value="Female" <?= $employee->gender == 'Female' ? 'selected' : '' ?>>Female</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>

                            <div align="center">
                                <button type="submit" name="update" class="btn btn-success">Update Employee Details</button>
                                <a href="add-ftws.php?empid=<?= htmlentities($employee->id); ?>" class="btn btn-success">Add New</a>
                                <a href="find.php" class="btn btn-cancel">Cancel</a>
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

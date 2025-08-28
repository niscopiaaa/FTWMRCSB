<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../include/config.php';

$errormsg = "";
$msg = "";

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['id'];

$stmt = $dbh->prepare("SELECT placeid FROM tbluser WHERE id = :id");
$stmt->bindParam(':id', $userId, PDO::PARAM_INT);
$stmt->execute();
$userPlace = $stmt->fetch(PDO::FETCH_ASSOC);

if ($userPlace) {
    $placeid = $userPlace['placeid'];
} else {
    $placeid = null;  
}

if (isset($_POST['SubmitAssessment'])) {
    if (empty($_POST['placeExam']) || empty($_POST['dateExam']) || !isset($_POST['Department']) || !is_array($_POST['Department'])) {
        $errormsg = "Please Fill in all required fields.";
    } else {
        $placeExam = $_POST['placeExam'];
        $dateExam = $_POST['dateExam'];
        
        try {
            $dbh->beginTransaction();

            $workerid = $_GET['empid'];  
            $placeid = $placeExam;

            $stmt = $dbh->prepare("INSERT INTO tblftw_assessment (workerid, created_by, created_at) 
                                    VALUES (:workerid, :placeid, NOW())");
            $stmt->bindParam(':workerid', $workerid);  
            $stmt->bindParam(':placeid', $placeid);  
            $stmt->execute();
            $assessment_id = $dbh->lastInsertId();

            foreach ($_POST['Department'] as $job_id) {
                if (empty($_POST['status'][$job_id])) {
                    throw new Exception("Please select a status for the selected Job Specific.");
                }
                $status_ftw = $_POST['status'][$job_id];

                $stmt2 = $dbh->prepare("INSERT INTO tblftw_assessment_job (assessment_id, job_id, place_id, exam_date, status_ftw) 
                                        VALUES (:assessment_id, :job_id, :place_id, :exam_date, :status_ftw)");
                $stmt2->bindParam(':assessment_id', $assessment_id, PDO::PARAM_INT);
                $stmt2->bindParam(':job_id', $job_id, PDO::PARAM_INT);
                $stmt2->bindParam(':place_id', $placeExam, PDO::PARAM_INT);
                $stmt2->bindParam(':exam_date', $dateExam);
                $stmt2->bindParam(':status_ftw', $status_ftw);
                $stmt2->execute();
            }

            $dbh->commit();
            $msg = "Information added successfully! You can now proceed to add more assessments or click Done.";
        } catch (Exception $e) {
            $dbh->rollBack();
            $errormsg = $e->getMessage();
        }
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
    <link rel="stylesheet" type="text/css" href="../../css/main.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .form-group {
            margin-bottom: 20px;
        }
        .form-check {
            margin-bottom: 10px;
        }
        .status-options {
            margin-top: 10px;
        }
        .col-md-6, .col-md-12 {
            padding: 10px;
        }
        .alert {
            margin-bottom: 20px;
        }

        .checkbox-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .checkbox-box {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #ddd;
            width: 100%;
            max-width: 300px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .checkbox-box:hover {
            border-color: rgb(0, 169, 141);
        }

        .checkbox-box input[type="checkbox"] {
            display: none;
        }

        .checkbox-box input[type="checkbox"]:checked + label:before {
            background-color: #3498db;
            border-color: #3498db;
        }

        .checkbox-box label {
            position: relative;
            cursor: pointer;
            font-size: 16px;
            display: block;
            padding-left: 30px;
        }

        .checkbox-box label:before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 20px;
            height: 20px;
            border: 2px solid #3498db;
            border-radius: 50%;
            background-color: white;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .checkbox-box input[type="checkbox"]:checked + label:after {
            content: "\f00c"; /* FontAwesome check mark */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            color: white;
            position: absolute;
            left: 4px;
            top: 0px;
        }

        .status-options {
            margin-top: 10px;
            display: none;
            padding-left: 30px;
        }

        .checkbox-box input[type="checkbox"]:checked ~ .status-options {
            display: block;
        }

        .status-options label {
            margin-right: 10px;
            font-size: 16px;
        }

        /* User-friendly Button Styles */
        .btn-primary, .btn-back {
            background-color: #3498db;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover, .btn-back:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }

        .btn-primary:focus, .btn-back:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.6);
        }

        @media (max-width: 768px) {
            .btn-primary, .btn-back {
                width: 100%;
            }
        }

        .button-container {
            display: flex;
            gap: 20px;
        }

        .button-container .btn {
            flex: 1;
        }

        .btn-back {
            width: 150px;
            margin-left: auto;
        }
    </style>

</head>
<body class="app sidebar-mini rtl">
    <?php include '../include/header.php'; ?>
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include '../include/sidebar.php'; ?>
    <main class="app-content">
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h3 align="center">Fitness to Work Assessment</h3>
                    <hr/>
                    <?php if ($msg): ?>
                        <div class="alert alert-success"><?= htmlentities($msg) ?></div>
                        <div style="margin-bottom: 20px;">
                            <a href="find.php" class="btn btn-success">Done</a>
                        </div>
                    <?php endif; ?>
                    <?php if ($errormsg): ?>
                        <div class="alert alert-danger"><?= htmlentities($errormsg) ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="form-group col-md-12">
                            <label class="form-label">Select Job Specific(s)</label>
                            <div class="checkbox-container">
                                <?php
                                $stmt = $dbh->prepare("SELECT * FROM tbljobspecific ORDER BY jobspecific");
                                $stmt->execute();
                                $jobs = $stmt->fetchAll();
                                foreach ($jobs as $job) {
                                    $id = $job['id'];
                                    $name = $job['jobspecific'];
                                    echo "
                                    <div class='checkbox-box'>
                                        <input type='checkbox' name='Department[]' id='job_$id' value='$id' onclick='toggleStatus($id)' class='clearable'>
                                        <label for='job_$id'><strong>$name</strong></label>
                                        <div class='status-options' id='status_$id'>
                                            <label><input type='radio' name='status[$id]' value='Fit'> Fit</label><br>
                                            <label><input type='radio' name='status[$id]' value='Unfit'> Unfit</label><br>
                                            <label><input type='radio' name='status[$id]' value='Fit with Restriction'> Fit with Restriction</label>
                                        </div>
                                    </div>";
                                }
                                ?>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Place of Examination</label>
                                <select name="placeExam" id="placeExam" class="form-control" required>
                                    <option value="NA" disabled selected>Select one</option>
                                    <?php
                                        if ($placeid) {
                                            $stmt = $dbh->prepare("SELECT * FROM tblpreplacement WHERE id = :placeid ORDER BY preplacement");
                                            $stmt->bindParam(':placeid', $placeid, PDO::PARAM_INT);
                                            $stmt->execute();
                                            $preplacements = $stmt->fetchAll();

                                            if ($preplacements) {
                                                foreach ($preplacements as $placement) {
                                                    echo "<option value='" . $placement['id'] . "'>" . $placement['preplacement'] . "</option>";
                                                }
                                            } else {
                                                echo "<option value='NA'>No places found</option>";
                                            }
                                        } else {
                                            echo "<option value='NA'>Employee's place not found</option>";
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="form-label">Date of Examination</label>
                                <input type="date" name="dateExam" class="form-control" required>
                            </div>
                        </div>
                        <div class="button-container col-md-2">
                            <button type="submit" name="SubmitAssessment" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
    function toggleStatus(selectedId) {
        document.querySelectorAll('.status-options').forEach(function(el) {
            el.style.display = 'none';
            el.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(function(input) {
                input.checked = false;
            });
        });

        const selectedElement = document.getElementById('status_' + selectedId);
        if (selectedElement) {
            selectedElement.style.display = 'block';
            const input = selectedElement.querySelector('input[type="radio"]');
            if (input) {
                input.checked = true;
            }
        }
    }
    </script>

</body>
</html>

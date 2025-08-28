<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../include/config.php';

$errormsg = "";
$msg = "";

$assessment = [];

// Check if the form is submitted
if (isset($_POST['SubmitAssessment']) && empty($errormsg) ) {
    $placeExam = $_POST['placeExam'];
    $dateExam = $_POST['dateExam'];
    $assessmentid = $_GET['assessment_id'];

    // Process Job Specific and Status data
    if (isset($_POST['Department']) && !empty($_POST['Department']) && !empty($placeExam)) {
        $selectedJobSpecifics = implode(',', $_POST['Department']);

        $selectedDepartment = $_POST['Department'][0]; // Only one will be checked
        $status_ftw = isset($_POST['status'][$selectedDepartment]) ? $_POST['status'][$selectedDepartment] : '';

        // Update statement
        $update = $dbh->prepare("
            UPDATE tblftw_assessment_job
            SET job_id = :job_specific,
                status_ftw   = :status_ftw,
                place_id   = :place_exam,
                exam_date    = :date_exam
            WHERE assessment_id = :assessment_id
        ");

        // $debugQuery = "
        //     UPDATE tblftw_assessment_job
        //     SET job_id = '{$selectedJobSpecifics}',
        //         status_ftw = '{$status_ftw}',
        //         place_id = '{$placeExam}',
        //         exam_date = '{$dateExam}'
        //     WHERE assessment_id = '{$assessmentid}'
        // ";

        $update->bindParam(':job_specific', $selectedJobSpecifics, PDO::PARAM_STR);
        $update->bindParam(':status_ftw', $status_ftw, PDO::PARAM_STR);
        $update->bindParam(':place_exam', $placeExam, PDO::PARAM_STR);
        $update->bindParam(':date_exam', $dateExam, PDO::PARAM_STR);
        $update->bindParam(':assessment_id', $assessmentid, PDO::PARAM_INT);

        //echo "<script>alert(" . json_encode($debugQuery) . ");</script>";

        if ($update->execute()) {
            $msg = "Information updated successfully!";
        } else {
            $errormsg = "Failed to update record.";
        }
        
        // If no errors occurred, set the success message
        if (empty($errormsg)) {
            $msg = "Information updated successfully!";
        }
    } else {
        $errormsg = "Please fill in all required fields.";
    }
}
if (isset($_GET['StaffICPassport']) && isset($_GET['assessment_id'])) {
    $staffic = $_GET['StaffICPassport']; // Get the Staff IC/Passport
    $assessmentid = $_GET['assessment_id']; // Get the assessment ID

    // Fetch existing data for the employee
    $stmt = $dbh->prepare("SELECT * FROM tblftw_assessment_job WHERE assessment_id = :assessment_id");
    $stmt->bindParam(':assessment_id', $assessmentid, PDO::PARAM_INT);
    $stmt->execute();
    $assessment = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the existing data
} else {
    $errormsg = "Staff IC/Passport and Assessment ID are required.";
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
      /* Styling for checkboxes and radio buttons */
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
        border-color:rgb(0, 169, 141);
      }

      .checkbox-box input[type="checkbox"] {
        display: none;
      }

      .checkbox-box input[type="checkbox"]:checked + label:before {
        background-color: #3498db; /* Change to a blue color when checked */
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
        display: block;  /* Only show radio options when the checkbox is checked */
      }

      .status-options label {
        margin-right: 10px;
        font-size: 16px;
      }

      /* User-friendly Button Styles */
      .btn-primary {
        background-color: #3498db; /* Friendly blue color */
        border: none;
        padding: 12px 24px;
        font-size: 16px;
        color: white;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
      }

      /* Hover effect for the button */
      .btn-primary:hover {
        background-color: #2980b9;
        transform: scale(1.05); /* Slight scale effect to make the button feel interactive */
      }

      /* Focus effect for accessibility */
      .btn-primary:focus {
        outline: none;
        box-shadow: 0 0 5px rgba(52, 152, 219, 0.6);
      }

      /* Responsive button for mobile devices */
      @media (max-width: 768px) {
        .btn-primary {
          width: 100%; /* Make the button full width on smaller screens */
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
      <!-- Main content -->
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <h3 align="center">Fitness to Work Assessment</h3>
            <hr/>

            <!-- Success Message -->
            <?php if($msg){ ?>
            <div class="alert alert-success" role="alert">
              <strong>Well done!</strong> <?php echo htmlentities($msg); ?>
            </div>
            <div style="margin-bottom: 20px;">
              <a href="ftw-report.php" class="btn btn-success">Done</a>
            </div>
            <?php } ?>

            <!-- Error Message -->
            <?php if($errormsg){ ?>
            <div class="alert alert-danger" role="alert">
              <strong>Oh snap!</strong> <?php echo htmlentities($errormsg); ?></div>
            <?php } ?>

            <div class="tile-body">
              <form class="row" method="post" enctype="multipart/form-data">
                <!-- Job Specific Checkboxes -->
                <div class="form-group col-md-12">
                  <label class="control-label">FTW Assessment</label>
                  <div class="checkbox-container">
                    <?php
                    // Fetch job-specific options from the database
                    $stmt = $dbh->prepare("SELECT * FROM tbljobspecific ORDER BY jobspecific");
                    $stmt->execute();
                    $JobSpecific = $stmt->fetchAll();
                    foreach ($JobSpecific as $jobspecific) {
                        echo '<div class="checkbox-box">';
                        $checked = (in_array($jobspecific['jobspecific'], explode(',', $assessment['job_specific'] ?? ''))) ? 'checked' : '';
                        echo '<input type="checkbox" name="Department[]" id="dept_'.$jobspecific['id'].'" value="'.$jobspecific['id'].'" onChange="toggleRadioButtons(this);" '.$checked.' class="clearable">';
                        echo '<label for="dept_'.$jobspecific['id'].'">'.$jobspecific['jobspecific'].'</label>';

                        // Nested Radio options for each Job Specific (hidden initially)
                        echo '<div id="statusOptions_'.$jobspecific['id'].'" class="status-options">';
                        $statusChecked = ($assessment['status_ftw'] ?? '') == 'Fit' ? 'checked' : '';
                        echo '<label><input type="radio" name="status['.$jobspecific['id'].']" value="Fit" '.$statusChecked.'> Fit</label>';
                        $statusChecked = ($assessment['status_ftw'] ?? '') == 'Unfit' ? 'checked' : '';
                        echo '<label><input type="radio" name="status['.$jobspecific['id'].']" value="Unfit" '.$statusChecked.'> Unfit</label>';
                        $statusChecked = ($assessment['status_ftw'] ?? '') == 'Fit with Restriction' ? 'checked' : '';
                        echo '<label><input type="radio" name="status['.$jobspecific['id'].']" value="Fit with Restriction" '.$statusChecked.'> Fit with Restriction</label>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                    
                  </div>
                </div>
                
                <!-- Place of Examination Field -->
                <div class="form-group col-md-6">
                <label class="control-label">Place of Examination</label>
                        <select name="placeExam" id="placeExam" class="form-control" onChange="getdistrict(this.value);" required>
                          <option value="">Select one</option>
                            <?php
                            $stmt = $dbh->prepare("SELECT * FROM tblpreplacement ORDER BY preplacement");
                            $stmt->execute();
                            $PrePlacement = $stmt->fetchAll();
                            foreach($PrePlacement as $placementname){
                            $selected = $placementname['id'] == ($assessment['place_exam'] ?? '') ? 'selected' : '';
                            echo "<option value='".$placementname['id']."' $selected>".$placementname['preplacement']."</option>";
                            }
                            ?>
                        </select>
                  </div>

                <!-- Date of Examination Field -->
                <div class="form-group col-md-6">
                  <label class="control-label">Date of Examination</label>
                  <input type="date" name="dateExam" id="dateExam" value="<?= htmlentities($assessment['date_exam'] ?? ''); ?>" class="form-control" autocomplete="off" required>
                </div>

                <!-- Submit & Cancel Button -->
                <div class="form-group col-md-12 align-self-end">
                  <button type="cancel" class="btn btn-primary" onclick="window.location.href='ftw-report.php';">Cancel</button>
                  <button type="submit" name="SubmitAssessment" id="SubmitAssessment" class="btn btn-primary">Update</button>
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
    <script src="../../js/plugins/pace.min.js"></script>

    <script type="text/javascript">
      <script type="text/javascript">
function toggleStatus(selectedId) {
    // Hide all status options and uncheck all checkboxes and radios
    document.querySelectorAll('.status-options').forEach(function(el) {
        el.style.display = 'none';
        el.querySelectorAll('input[type="radio"]').forEach(function(input) {
            input.checked = false;
        });
    });
    document.querySelectorAll('.clearable').forEach(function(cb) {
        if (cb.id !== 'dept_' + selectedId) cb.checked = false;
    });

    // Show the selected one and check its checkbox
    const selectedCheckbox = document.getElementById('dept_' + selectedId);
    if (selectedCheckbox) {
        selectedCheckbox.checked = true;
    }
    const selectedElement = document.getElementById('statusOptions_' + selectedId);
    if (selectedElement) {
        selectedElement.style.display = 'block';
    }
}

// Attach toggleStatus to all checkboxes
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.clearable').forEach(function(el) {
        el.addEventListener('change', function() {
            if (el.checked) {
                toggleStatus(el.id.split('_')[1]);
            } else {
                // If unchecked, hide its status radio group
                document.getElementById('statusOptions_' + el.id.split('_')[1]).style.display = 'none';
            }
        });
    });
});
</script>
      
    </script>
    <script>
    document.querySelectorAll('.clearable').forEach(el => {
        el.addEventListener('change', function() {
            if (this.checked) {
                document.querySelectorAll('.clearable').forEach(cb => {
                    if (cb !== this) cb.checked = false;
                });
            }
        });
    });
</script>

  </body>
</html>

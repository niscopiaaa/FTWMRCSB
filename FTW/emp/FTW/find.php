<?php
session_start();
error_reporting(0);
require_once('../include/config.php');

$errormsg = "";
$msg = "";
$results = [];

$empid = $_SESSION['id'];

if (strlen($empid) == 0) {   
    header('location:../index.php');
    exit();
} else {
    // Ambil placeid milik user yang login
    $stmt = $dbh->prepare("SELECT placeid FROM tbluser WHERE id = :empid");
    $stmt->bindParam(':empid', $empid, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $placeid = $user['placeid'];  
    } else {
        $errormsg = "User not found.";
        $placeid = null;
    }

    if ($placeid !== null) {
        if (isset($_POST['Submit'])) {
            $searchkey = trim($_POST['searchkey'] ?? '');

            if (!empty($searchkey)) {
                // Base query: ambil assessment TERBARU per worker untuk created_by = :placeid
                // Lalu filter berdasarkan IC/Passport atau nama.
                if (preg_match("/^\d+$/", $searchkey)) {
                    // Pencarian berdasarkan IC/Passport (numeric penuh)
                    $sql = "
                        SELECT 
                            e.StaffICPassport,
                            e.fullname,
                            e.contactno,
                            e.address,
                            e.BOD,
                            w.id AS assessment_id,
                            w.created_at
                        FROM tblftwworker e
                        INNER JOIN (
                            SELECT a.workerid, MAX(a.created_at) AS max_created_at
                            FROM tblftw_assessment a
                            WHERE a.created_by = :placeid1
                            GROUP BY a.workerid
                        ) lm ON lm.workerid = e.id
                        INNER JOIN tblftw_assessment w
                            ON w.workerid = lm.workerid
                            AND w.created_at = lm.max_created_at
                            AND w.created_by = :placeid2
                        WHERE e.StaffICPassport = :searchkey
                        ORDER BY w.created_at DESC
                    ";
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindParam(':searchkey', $searchkey, PDO::PARAM_STR);
                } else {
                    // Pencarian berdasarkan nama (LIKE)
                    $sql = "
                        SELECT 
                            e.StaffICPassport,
                            e.fullname,
                            e.contactno,
                            e.address,
                            e.BOD,
                            w.id AS assessment_id,
                            w.created_at
                        FROM tblftwworker e
                        INNER JOIN (
                            SELECT a.workerid, MAX(a.created_at) AS max_created_at
                            FROM tblftw_assessment a
                            WHERE a.created_by = :placeid1
                            GROUP BY a.workerid
                        ) lm ON lm.workerid = e.id
                        INNER JOIN tblftw_assessment w
                            ON w.workerid = lm.workerid
                            AND w.created_at = lm.max_created_at
                            AND w.created_by = :placeid2
                        WHERE e.fullname LIKE :searchkey
                        ORDER BY w.created_at DESC
                    ";
                    $stmt = $dbh->prepare($sql);
                    $like = "%".$searchkey."%";
                    $stmt->bindParam(':searchkey', $like, PDO::PARAM_STR);
                }

                // Bind placeid dua kali karena dipakai di dua tempat (:placeid1 dan :placeid2)
                $stmt->bindParam(':placeid1', $placeid, PDO::PARAM_INT);
                $stmt->bindParam(':placeid2', $placeid, PDO::PARAM_INT);

                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($results) > 0) {
                    $msg = "Results found!";
                } else {
                    $errormsg = "No matching data found. Please add new data.";
                }
            } else {
                $errormsg = "Please enter a search key.";
            }
        } else {
            // Tampilkan semua pekerja dengan assessment TERBARU per worker untuk placeid ini
            $sql = "
                SELECT 
                    e.StaffICPassport,
                    e.fullname,
                    e.contactno,
                    e.address,
                    e.BOD,
                    w.id AS assessment_id,
                    w.created_at
                FROM tblftwworker e
                INNER JOIN (
                    SELECT a.workerid, MAX(a.created_at) AS max_created_at
                    FROM tblftw_assessment a
                    WHERE a.created_by = :placeid1
                    GROUP BY a.workerid
                ) lm ON lm.workerid = e.id
                INNER JOIN tblftw_assessment w
                    ON w.workerid = lm.workerid
                    AND w.created_at = lm.max_created_at
                    AND w.created_by = :placeid2
                ORDER BY w.created_at DESC
            ";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':placeid1', $placeid, PDO::PARAM_INT);
            $stmt->bindParam(':placeid2', $placeid, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .search-box {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            background-color: #f8f9fa;
            margin-bottom: 30px;
        }
        .table thead th { white-space: nowrap; }
        .action-btns .btn { margin-right: 6px; }
    </style>
</head>
<body>
    <?php include '../include/header.php'; ?>
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include '../include/sidebar.php'; ?>

    <main class="app-content">
        <div class="container">
            <h4 class="text-center">Fitness to Work Assessment</h4><br><br>

            <?php if ($msg) { ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlentities($msg); ?>
                </div>
            <?php } ?>

            <?php if ($errormsg) { ?>
                <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="errorModalLabel">No Matching Data Found</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <?php echo htmlentities($errormsg); ?>
                      </div>
                      <div class="modal-footer">
                        <a href="add-ftwemp.php" class="btn btn-warning">Add New Data</a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>
            <?php } ?>

            <div class="search-box">
                <form class="form-inline text-center" method="POST" action="">
                    <div class="form-group">
                        <label for="searchkey" class="mr-2">Search by IC/Passport No or Name</label>
                        <input type="text" class="form-control" name="searchkey" id="searchkey" placeholder="Enter IC/Passport or Name" required />
                    </div>
                    <button type="submit" name="Submit" class="btn btn-primary ml-2">Search</button>
                </form>
            </div>

            <div class="mt-4">
                <?php if (isset($results) && count($results) > 0) { ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Worker IC/Passport No</th>
                                    <th>Full Name</th>
                                    <th>Contact No</th>
                                    <th>Address</th>
                                    <th>Birth Date</th>
                                    <th>Assessment ID</th>
                                    <th>Assessment Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $row) { ?>
                                    <tr>
                                        <td><?php echo htmlentities($row['StaffICPassport']); ?></td>
                                        <td><?php echo htmlentities($row['fullname']); ?></td>
                                        <td><?php echo htmlentities($row['contactno']); ?></td>
                                        <td><?php echo htmlentities($row['address']); ?></td>
                                        <td><?php echo htmlentities($row['BOD']); ?></td>
                                        <td><?php echo htmlentities($row['assessment_id']); ?></td>
                                        <td><?php echo htmlentities($row['created_at']); ?></td>
                                        <td class="action-btns">
                                            <a href="edit-ftw.php?StaffICPassport=<?php echo urlencode($row['StaffICPassport']); ?>" class="btn btn-success btn-sm">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <p>No results found for your search criteria.</p>
                <?php } ?>
            </div>

        </div>
    </main>

    <script src="../../js/jquery-3.2.1.min.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <script src="../../js/main.js"></script>
    <script>
        <?php if ($errormsg) { ?>
            $('#errorModal').modal('show');
        <?php } ?>
    </script>
</body>
</html>

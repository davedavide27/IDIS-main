<?php
session_start();

// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ids_database";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$subjectCode = $subjectName = $units = $hours = $department = $schoolYearStart = $schoolYearEnd = '';
$gradingPeriod = $gradingQuarterStart = $gradingQuarterEnd = $totalCompetenciesDepEd = $totalCompetenciesSMCC = '';
$totalInstitutionalCompetencies = $totalCompetenciesBAndC = $totalCompetenciesImplemented = $totalCompetenciesNotImplemented = '';
$percentageCompetenciesImplemented = $preparedBy = $checkedBy = $notedBy = '';
$status = 'PENDING';  // Default status
$competencies = [];

// Fetch data based on subject_code and subject_name from GET
if (isset($_GET['subject_code']) && isset($_GET['subject_name'])) {
    $subjectCode = $_GET['subject_code'];
    $subjectName = $_GET['subject_name'];

    // Fetch all relevant data for the selected subject
    $sql = "SELECT * FROM competencies WHERE subject_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $subjectCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch data dynamically from the database
        while ($row = $result->fetch_assoc()) {
            $units = $row['units'];
            $hours = $row['hours'];
            $department = $row['department'];
            $schoolYearStart = $row['school_year_start'];
            $schoolYearEnd = $row['school_year_end'];
            $gradingPeriod = $row['grading_period'];
            $gradingQuarterStart = $row['grading_quarter_start'];
            $gradingQuarterEnd = $row['grading_quarter_end'];
            $totalCompetenciesDepEd = $row['total_competencies_deped_tesda_ched'];
            $totalCompetenciesSMCC = $row['total_competencies_smcc'];
            $totalInstitutionalCompetencies = $row['total_institutional_competencies'];
            $totalCompetenciesBAndC = $row['total_competencies_b_and_c'];
            $totalCompetenciesImplemented = $row['total_competencies_implemented'];
            $totalCompetenciesNotImplemented = $row['total_competencies_not_implemented'];
            $percentageCompetenciesImplemented = $row['percentage_competencies_implemented'];
            $preparedBy = $row['prepared_by'];
            $checkedBy = $row['checked_by'];
            $notedBy = $row['noted_by'];
            $status = $row['status'];  // Fetch current status

            // Add competencies description and remarks to array
            $competencies[] = [
                'competency_description' => $row['competency_description'],
                'remarks' => $row['remarks']
            ];
        }
    }
    $stmt->close();
}

// Handle "Approve" button click
if (isset($_POST['approve'])) {
    // Update the status to "APPROVED"
    $sqlUpdateStatus = "UPDATE competencies SET status = 'APPROVED' WHERE subject_code = ?";
    $stmtUpdate = $conn->prepare($sqlUpdateStatus);
    $stmtUpdate->bind_param("s", $subjectCode);

    if ($stmtUpdate->execute()) {
        // Trigger JavaScript alert after successful approval
        echo "<script>alert('Subject code $subjectCode is approved');</script>";
        // Refresh the page to reflect the updated status
        echo "<script>window.location.href = '?subject_code=$subjectCode&subject_name=$subjectName';</script>";
    } else {
        echo "Error updating status: " . $stmtUpdate->error;
    }

    $stmtUpdate->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dean UI - View Competencies</title>
    <link rel="stylesheet" href="view_competencies.css">
    <style>
        @media print {

            .print-button,
            .back-button,
            .status-container,
            .status-button {
                display: none;
            }
        }

        /* Style for the status button */
        .status-button {
            padding: 5px;
            font-size: 12px;
            color: white;
            border: none;
            cursor: default;
        }

        /* Red for PENDING */
        .status-button.pending {
            background-color: red;
        }

        /* Green for APPROVED */
        .status-button.approved {
            background-color: green;
        }

        /* Approve button */
        .approve-button {
            padding: 5px;
            background-color: blue;
            color: white;
            border: none;
            cursor: pointer;
            align-items: left;
        }
    </style>
</head>

<body>
    <button class="no-print" onclick="hidePrintHeaders()">Print this page</button>
    <button class="no-print" onclick="window.location.href='index.php';">Back</button>

    <h2 style="text-align: center;">COMPETENCY IMPLEMENTATION</h2>

    <!-- Approve button (only show if status is PENDING) -->
    <?php if ($status === 'PENDING'): ?>
        <form method="post" class="status-container" style="text-align: center; margin-bottom: 20px;" onsubmit="return confirmApprove()">
            <button class="approve-button" type="submit" name="approve">Approve Competency</button>
        </form>
    <?php endif; ?>

    <table class="header-info">
        <tr>
            <th class="status-container">Status</th>
            <td> <button class="status-button <?php echo strtolower($status); ?>">
                    <?php echo htmlspecialchars($status); ?>
                </button></td>
        </tr>
        <tr>
            <th>I. Subject code:</th>
            <td><?php echo htmlspecialchars($subjectCode); ?></td>
        </tr>
        <tr>
            <th>II. Subject title:</th>
            <td><?php echo htmlspecialchars($subjectName); ?></td>
        </tr>
        <tr>
            <th>III. Units:</th>
            <td><?php echo htmlspecialchars($units); ?></td>
        </tr>
        <tr>
            <th>IV. Hours:</th>
            <td><?php echo htmlspecialchars($hours); ?></td>
        </tr>
        <tr>
            <th>V. Department:</th>
            <td><?php echo htmlspecialchars($department); ?></td>
        </tr>
        <tr>
            <th>VI. School year:</th>
            <td><?php echo htmlspecialchars($schoolYearStart . ' - ' . $schoolYearEnd); ?></td>
        </tr>
        <tr>
            <th>VII. Grading period/quarter:</th>
            <td>
                <?php
                echo htmlspecialchars($gradingPeriod) . ' <strong>FROM</strong> ' . htmlspecialchars($gradingQuarterStart) . ' <strong>TO</strong> ' . htmlspecialchars($gradingQuarterEnd);
                ?>
            </td>
        </tr>
    </table>

    <table class="competency-table">
        <thead>
            <tr>
                <th>SMCC Competencies</th>
                <th>Remarks On Class</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($competencies)): ?>
                <?php foreach ($competencies as $competency): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($competency['competency_description']); ?></td>
                        <td><?php echo htmlspecialchars($competency['remarks']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">No competencies found for this subject.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td>Total Competencies DepEd/TESDA/CHED:</td>
            <td><?php echo htmlspecialchars($totalCompetenciesDepEd); ?></td>
        </tr>
        <tr>
            <td>Total Competencies SMCC:</td>
            <td><?php echo htmlspecialchars($totalCompetenciesSMCC); ?></td>
        </tr>
        <tr>
            <td>Total Institutional Competencies:</td>
            <td><?php echo htmlspecialchars($totalInstitutionalCompetencies); ?></td>
        </tr>
        <tr>
            <td>Total B and C Competencies:</td>
            <td><?php echo htmlspecialchars($totalCompetenciesBAndC); ?></td>
        </tr>
        <tr>
            <td>Total Competencies Implemented:</td>
            <td><?php echo htmlspecialchars($totalCompetenciesImplemented); ?></td>
        </tr>
        <tr>
            <td>Total Competencies NOT Implemented:</td>
            <td><?php echo htmlspecialchars($totalCompetenciesNotImplemented); ?></td>
        </tr>
        <tr>
            <td>% Competencies Implemented:</td>
            <td><?php echo htmlspecialchars($percentageCompetenciesImplemented); ?>%</td>
        </tr>
    </table>

    <div class="sign-section">
        <table style="width: 100%; text-align: center; border-spacing: 40px 0;">
            <tr>
                <td><strong>Prepared by:</strong></td>
                <td><strong>Checked by:</strong></td>
                <td><strong>Noted by:</strong></td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid black;"><?php echo htmlspecialchars($preparedBy); ?></td>
                <td style="border-bottom: 1px solid black;"><?php echo htmlspecialchars($checkedBy); ?></td>
                <td style="border-bottom: 1px solid black;"><?php echo htmlspecialchars($notedBy); ?></td>
            </tr>
            <tr>
                <td>
                    <h4>Subject Teacher</h4>
                </td>
                <td>
                    <h4>Subject Coordinator</h4>
                </td>
                <td>
                    <h4>Dean</h4>
                </td>
            </tr>
        </table>
    </div>

    <script>
        function hidePrintHeaders() {
            // Trigger print without headers
            window.print();
        }

        function confirmApprove() {
            return confirm('Approve Competency?');
        }
    </script>
</body>

</html>
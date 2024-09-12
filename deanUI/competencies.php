<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ids_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$subjectCode = '';
$subjectName = '';
$units = '';
$hours = '';
$department = '';
$schoolYearStart = '';
$schoolYearEnd = '';
$gradingPeriod = '';
$gradingQuarterStart = '';
$gradingQuarterEnd = '';
$totalCompetenciesDepEd = '';
$totalCompetenciesSMCC = '';
$totalInstitutionalCompetencies = '';
$totalCompetenciesBAndC = '';
$totalCompetenciesImplemented = '';
$totalCompetenciesNotImplemented = '';
$percentageCompetenciesImplemented = '';
$preparedBy = '';
$checkedBy = '';
$notedBy = '';
$competencies = [];

// Check if subject code is passed from the instructorUI/index.php
if (isset($_GET['subject_code']) && isset($_GET['subject_name'])) {
    $subjectCode = $_GET['subject_code'];
    $subjectName = $_GET['subject_name'];

    // Fetch all relevant data for the selected subject
    $sql = "SELECT subject_code, subject_name, units, hours, department, school_year_start, school_year_end,
            grading_period, grading_quarter_start, grading_quarter_end, total_competencies_deped_tesda_ched, 
            total_competencies_smcc, total_institutional_competencies, total_competencies_b_and_c, 
            total_competencies_implemented, total_competencies_not_implemented, percentage_competencies_implemented,
            prepared_by, checked_by, noted_by, competency_description, remarks
            FROM competencies 
            WHERE subject_code = ?";
    
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

            // Add competencies description and remarks to array
            $competencies[] = [
                'competency_description' => $row['competency_description'],
                'remarks' => $row['remarks']
            ];
        }
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dean UI - View Competencies</title>
</head>
<body>
    <button class="no-print" onclick="hidePrintHeaders()">Print this page</button>
    <button class="no-print" onclick="window.history.back()">Back</button>
    <h2 style="text-align: center;">COMPETENCY IMPLEMENTATION</h2>
    <link rel="stylesheet" href="view_competencies.css">
    <table class="header-info">
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
            <td><?php echo htmlspecialchars($gradingPeriod . ' From ' . $gradingQuarterStart . ' To ' . $gradingQuarterEnd); ?></td>
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
            <td>Total number of Competencies DepEd/TESDA/CHED:</td>
            <td><?php echo htmlspecialchars($totalCompetenciesDepEd); ?></td>
        </tr>
        <tr>
            <td>Total Number of Competencies SMCC based on DepEd/TESDA/CHED:</td>
            <td><?php echo htmlspecialchars($totalCompetenciesSMCC); ?></td>
        </tr>
        <tr>
            <td>Total Number of Institutional Competencies:</td>
            <td><?php echo htmlspecialchars($totalInstitutionalCompetencies); ?></td>
        </tr>
        <tr>
            <td>Total Number of B and C:</td>
            <td><?php echo htmlspecialchars($totalCompetenciesBAndC); ?></td>
        </tr>
        <tr>
            <td>Total Number of Competencies Implemented:</td>
            <td><?php echo htmlspecialchars($totalCompetenciesImplemented); ?></td>
        </tr>
        <tr>
            <td>Total Number of Competencies NOT Implemented:</td>
            <td><?php echo htmlspecialchars($totalCompetenciesNotImplemented); ?></td>
        </tr>
        <tr>
            <td>% Number of Competencies Implemented:</td>
            <td><?php echo htmlspecialchars($percentageCompetenciesImplemented); ?>%</td>
        </tr>
    </table>

    <div class="sign-section">
    <table style="width: 100%; text-align: center; border-collapse: separate; border-spacing: 40px 0;">
        <tr>
            <td style="width: 33.3%; padding-bottom: 10px;">
                <strong>Prepared by:</strong>
            </td>
            <td style="width: 33.3%; padding-bottom: 10px;">
                <strong>Checked by:</strong>
            </td>
            <td style="width: 33.3%; padding-bottom: 10px;">
                <strong>Noted by:</strong>
            </td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding-bottom: 10px;">
                <?php echo htmlspecialchars($preparedBy); ?>
            </td>
            <td style="border-bottom: 1px solid black; padding-bottom: 10px;">
                <?php echo htmlspecialchars($checkedBy); ?>
            </td>
            <td style="border-bottom: 1px solid black; padding-bottom: 10px;">
                <?php echo htmlspecialchars($notedBy); ?>
            </td>
        </tr>
        <tr>
            <td style="width: 33.3%; padding-bottom: 10px;">
                <h4>Subject Teacher</h4>
            </td>
            <td style="width: 33.3%; padding-bottom: 10px;">
                <h4>Subject Coordinator</h4>
            </td>
            <td style="width: 33.3%; padding-bottom: 10px;">
                <h4>Dean</h4>
            </td>
        </tr>
    </table>
</div>
<script>
    function hidePrintHeaders() {
        // Save the current document title
        const originalTitle = document.title;

        // Temporarily change the title
        document.title = 'Print Competencies';

        // Trigger print
        window.print();

        // Restore the original title after printing
        setTimeout(() => {
            document.title = originalTitle;
        }, 1000);
    }
</script>
</body>
</html>
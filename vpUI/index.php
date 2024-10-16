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

$vpFullName = '';
$instructors = [];
$subjects = [];
$competenciesCount = 0; // Default value for competencies count
$totalCompetencies = 0; // Default value for total competencies count

// Check if the vp is logged in
if (isset($_SESSION['user_ID']) && $_SESSION['user_type'] == 'vp') {
    $vpId = $_SESSION['user_ID'];

    // Fetch vp's full name based on the vp ID
    $sql = "SELECT vp_fname, vp_mname, vp_lname FROM vp WHERE vp_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vpId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $vpFullName = $row['vp_fname'] . ' ' . $row['vp_mname'] . ' ' . $row['vp_lname'];
        $_SESSION['user_fullname'] = $vpFullName; // Store the full name in session
    } else {
        $vpFullName = 'Unknown User';
    }

    $stmt->close();

    // Fetch all instructors
    $sql = "SELECT instructor_ID, instructor_fname, instructor_mname, instructor_lname FROM instructor";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $instructors[] = $row;
        }
    }

    // If an instructor is selected, fetch their subjects
    if (isset($_GET['instructor_ID'])) {
        $selectedInstructorID = $_GET['instructor_ID'];

        $sql = "SELECT subject_code, subject_name FROM subject WHERE instructor_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $selectedInstructorID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $subjects[] = $row;
            }
        }

        $stmt->close();
        // Fetch the total number of competencies for the selected instructor's subjects
        $sql = "SELECT COUNT(*) as total FROM competencies WHERE subject_code IN (SELECT subject_code FROM subject WHERE instructor_ID = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $selectedInstructorID);
        $stmt->execute();
        $result = $stmt->get_result();
        $competenciesCount = $result->fetch_assoc()['total'];
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDIS</title>
    <link rel="stylesheet" href="vp.css">
    <script src="vp.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

        * {
            margin: 0%;
            font-family: 'Montserrat', sans-serif;
        }

        .logout-message {
            display: none;
            color: green;
            font-weight: bold;
        }

        .selected-subject {
            background-color: #1e90ff;
            border-color: #badbcc;
        }

        .tablinks {
            font-weight: 800;
            font-size: 1rem;
        }

        .navtab button:hover {
            cursor: pointer;
            background-color: burlywood;
        }

        h6 {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .planCard {
            background-color: whitesmoke;
            width: 270pt;
            height: 280pt;
            margin: 20pt;
            border-radius: 10pt;
            box-shadow: 1pt 1pt 15pt 1pt;
        }

        .tabcontent {
            display: none;
            text-align: center;
            height: 498pt;
        }

        #containerPlans {
            display: flex;
            justify-content: center;
        }

        .containerPlans p {
            color: black;
        }

        .navSubject .selectIns select {
            /*display: none;*/
            text-align: center;
            border-radius: 10pt;
            width: 70%;
            margin-left: 15%;
            justify-self: center;
            border-radius: 10pt;
            width: 82%;
            margin-left: 10%;
            padding: 10px 20px;
        }

        .tabcontent {
            display: none;
            text-align: center;
            height: 250pt;
        }

        main {
            background-color: burlywood;
            height: 60%;
            width: 100%;
            border-bottom-right-radius: 10pt;
            box-shadow: 10px 20px 20px;
        }

        ul {
            font-weight: 900;
            text-align: left;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        h4 {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="containerOfAll">
        <div class="subjectsContainer">
            <nav class="navSubject">
                <div class="logo">
                    <img src="logo.png" alt="sample logo">
                </div>
                <div>
                    <ul>Name: <?php echo htmlspecialchars($vpFullName); ?></ul>
                    <ul>ID: <?php echo htmlspecialchars($vpId); ?></ul>
                </div>

                <!-- Static display of competencies count -->
                <h4 style="text-align: center;">
                    Competencies:
                    <span id="competenciesCount"><?php echo $competenciesCount; ?> out of <?php echo $competenciesCount; ?></span>
                </h4>

                <div class="selectIns">
                    <form method="get" action="">
                        <select name="instructor_ID" id="showSelect" onchange="fetchCompetencies(this.value); this.form.submit();">
                            <option value="">Select Instructor</option>
                            <?php foreach ($instructors as $instructor): ?>
                                <option value="<?php echo $instructor['instructor_ID']; ?>" <?php echo isset($selectedInstructorID) && $selectedInstructorID == $instructor['instructor_ID'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($instructor['instructor_fname'] . ' ' . $instructor['instructor_mname'] . ' ' . $instructor['instructor_lname']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <br>
                <h4 style="text-align: center;">Subjects: <?php echo count($subjects); ?> out of <?php echo count($subjects); ?></h4>
                <div class="subsContainer">
                    <div class="subjects">
                        <?php if (!empty($subjects)): ?>
                            <?php foreach ($subjects as $subject): ?>
                                <div class="btnSubjects">
                                    <button type="button" onclick="selectSubject('<?php echo htmlspecialchars($subject['subject_code']); ?>', '<?php echo htmlspecialchars($subject['subject_name']); ?>', this)">
                                        <?php echo htmlspecialchars($subject['subject_name']); ?>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <form action="../logout.php" method="post">
                    <button class="logout_btn" type="submit">Logout</button>
                </form>

            </nav>
            <div class="implementContainer">
                <header>
                    <h5>Instructional Delivery Implementation System (IDIS)</h5>
                    <p>Saint Michael College of Caraga (SMCC)</p>
                    <div></div>
                    <div>
                        <nav class="navtab">
                            <button class="tablinks" onclick="openTab(event, 'ILOs')">Plans</button>
                        </nav>
                    </div>
                </header>
                <main>
                    <div class="filesContainer">
                        <div id="ILOs" class="tabcontent">
                            <h6><br>View for Signatures</h6>
                            <div id="containerPlans">
                                <div class="planCard" id="syllabusCard" style="display: none;">
                                    <a href="#" id="syllabusLink" onclick="printSyllabus()">
                                        <p>Syllabus</p>
                                    </a>
                                </div>
                                <div class="planCard" id="competenciesCard" style="display: none;">
                                    <a href="view_competencies.php?subject_code=" id="competenciesLink">
                                        <p>Competencies</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <script>
        // Function to switch between tabs
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        // Function to select a subject and display syllabus and competencies
        function selectSubject(subjectCode, subjectName, buttonElement) {
            // Highlight the selected subject button
            document.querySelectorAll('.btnSubjects button').forEach(function(button) {
                button.classList.remove('selected-subject');
            });
            buttonElement.classList.add('selected-subject');

            // Fetch competencies for the selected subject
            document.getElementById("syllabusCard").style.display = "block";
            fetchCompetencies(subjectCode);

            // Store selected subject in sessionStorage
            sessionStorage.setItem('selectedSubjectCode', subjectCode);
            sessionStorage.setItem('selectedSubjectName', subjectName);

            // Display the plan cards for Syllabus and Competencies
            document.getElementById('syllabusCard').style.display = 'block';
            document.getElementById('competenciesCard').style.display = 'block';

            // Update the competencies link with the selected subject
            document.getElementById('competenciesLink').href = 'view_competencies.php?subject_code=' + subjectCode;
            // Set the subject code and name dynamically in the Competencies link
            document.getElementById(
                "syllabusLink"
            ).href = `display_syllabus.php?subject_code=${subjectCode}&subject_name=${subjectName}`;

        }

        // Function to fetch the competencies from PHP
        function fetchCompetencies(subjectCode) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'display_total_comp.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    document.getElementById('competenciesCount').innerText = response.subject_competencies + " out of " + response.total_competencies;
                }
            };
            xhr.send('subject_code=' + subjectCode);
        }

        // Initialize the page and auto-select the previously selected subject
        document.addEventListener('DOMContentLoaded', function() {
            // Check if a subject was selected before
            var selectedSubjectCode = sessionStorage.getItem('selectedSubjectCode');
            var selectedSubjectName = sessionStorage.getItem('selectedSubjectName');

            if (selectedSubjectCode && selectedSubjectName) {
                // Auto-select the subject if previously selected
                var subjectButton = document.querySelector(`.btnSubjects button[onclick*="${selectedSubjectCode}"]`);
                if (subjectButton) {
                    selectSubject(selectedSubjectCode, selectedSubjectName, subjectButton);
                }
            }
        });
    </script>


</body>

</html>
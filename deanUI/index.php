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

$deanFullName = '';

// Check if the Dean is logged in
if (isset($_SESSION['user_ID']) && $_SESSION['user_type'] == 'dean') {
    $deanId = $_SESSION['user_ID'];

    // Fetch Dean's full name based on the Dean ID
    $sql = "SELECT dean_fname, dean_mname, dean_lname FROM dean WHERE dean_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $deanId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $deanFullName = $row['dean_fname'] . ' ' . $row['dean_mname'] . ' ' . $row['dean_lname'];
    } else {
        $deanFullName = 'Unknown Dean';
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
    <title>IDIS</title>
    <link rel="stylesheet" href="style.css">
    <script src="main.js"></script>
</head>
<body>
    <div class="containerOfAll">
        <div class="subjectsContainer">
            <nav class="navSubject">
                <div class="logo">
                    <img src="logo.png" alt="sample logo">
                </div>
                <div>
                    <ul>Name: <?php echo htmlspecialchars($deanFullName); ?></ul>
                    <ul>ID:<?php echo htmlspecialchars($deanId); ?></ul>
                </div>
                <div>
                <form action="../logout.php" method="post">
                        <button type="submit">Logout</button>
                    </form>
                </div>
                <h4 style="text-align: center;">00 out of 00</h4>
                <br>
                <div class="selectIns">
                    <select name="" id="showSelect" placeholder="da">
                        <option value="">Program Chair</option>
                        <option value="">Subject Coordinator</option>
                    </select>
                </div>
                <br><br>
                <h4 style="text-align: center;">00 out of 00</h4>
                <div class="selectIns">
                    <select name="" id="showSelect" placeholder="da">
                        <option value="">Instructor1</option>
                        <option value="">Instructor2</option>
                        <option value="">Instructor3</option>
                        <option value="">Instructor4</option>
                    </select>
                </div>
                <br><br>
                <h4 style="text-align: center;">00 out of 00</h4>
                <div class="subsContainer">
                    <div class="subjects">
                        <div><h4>Subjects:</h4></div>
                        <div class="btnSubjects">
                            <button >ADGEC 1</button>
                        </div>
                        <div class="btnSubjects">
                            <button >FIL 102</button>
                        </div>
                        <div class="btnSubjects">
                            <button >GEC 1</button>
                        </div>
                        <div class="btnSubjects">
                            <button >GEC 2</button>
                        </div>
                        <div class="btnSubjects">
                            <button >GEC ELECT 1</button>
                        </div>
                    </div>
                </div>
            </nav>
            <div class="implementContainer">
                <header><h5>Instructional Delivery Implementation System (IDIS)</h5><p>Saint Micheal College of Caraga (SMCC)</p>
                    <div></div>
                    <div>
                        <nav class="navtab">
                                <button class="tablinks" onclick="openTab(event, 'ILOs')">Print plans</button>
                                
                                <button class="tablinks" onclick="openTab(event, 'Topics')">Competencies</button>
                        </nav>
                    </div>
                </header>
                <main>
                    <div class="filesContainer">
                        <div id="ILOs" class="tabcontent">
                            <h6><br>Implement</h6>
                            <div id="container">
                                <div class="planCard">
                                    <a href=""><p>Syllabus</p></a>
                                </div>
                                <div class="planCard">
                                    <a href=""><p>Competencies</p></a>
                                </div>
                            </div>
                        </div>
                          
                        <div id="Topics" class="tabcontent">
                            <h6><br>The table below concludes all inputs.</h6>
                            <div id="container">
                                <table class="remarksTable">
                                    <tr>
                                        <th>Competencies</th>
                                        <th>Teacher's remarks</th>
                                        <th>Students' ratings</th>
                                        <th>Interpretation</th>
                                    </tr>
                                    <tr>
                                        <td>... </td>
                                        <td><p>Impemented</p></td>
                                        <td><p>70%</p></td>
                                        <td><p>Impemented</p></td>
                                    </tr>
                                    <tr>
                                        <td>... </td>
                                        <td><p>Not impemented</p></td>
                                        <td><p>15%</p></td>
                                        <td><p>Not impemented</p></td>
                                    </tr>
                                    <tr>
                                        <td>... </td>
                                        <td><p>Impemented</p</td>
                                        <td><p>70%</p></td>
                                        <td><p>Impemented</p></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                          
                    </div>
                </main>               
            </div>
        </div>
    </div>
</body>
</html>
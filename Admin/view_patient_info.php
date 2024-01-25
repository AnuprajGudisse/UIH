<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Patient Information</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <!-- Logo and website name on the left -->
        <a class="navbar-brand" href="dashboard.php">
            <img src="../logo.png" width="100" height="100" class="d-inline-block align-top" alt="">
        </a>
        <a class="navbar-brand" href="dashboard.php">
            <span class="website-name">UI Health</span>
        </a>


        <!-- Toggler/collapsible Button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar list on the right -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php">Home <span class="sr-only">(current)</span></a>
                </li>                
                <li class="nav-item active">
                    <a class="nav-link" href="#">About Us <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </div>
    </nav>

    <div class="container">
        <h2 class="mt-4">Search Patient Information</h2>
        <form action="view_patient_info.php" method="post" class="mb-4">
            <div class="form-group">
                <label for="searchTerm">Search by SSN, ID, or Full Name:</label>
                <input type="text" class="form-control" id="searchTerm" name="searchTerm" required>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            include '../connection.php'; // Adjust the path as necessary

            $searchTerm = $_POST["searchTerm"];
            $currentDate = date("Y-m-d");

            $stmt = $conn->prepare("SELECT * FROM Patient WHERE SSN = ? OR PatientID = ? OR CONCAT(FirstName, ' ', LastName) = ?");
            $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
            $stmt->execute();
            $patientResult = $stmt->get_result();

            if ($patientResult && $patientResult->num_rows > 0) {
                $row = $patientResult->fetch_assoc();
                echo '<div class="card mb-3"><div class="card-body">';
                echo "<h5 class='card-title'>Patient Information</h5>";
                echo "<p>Name: " . htmlspecialchars($row["FirstName"]) . " " . htmlspecialchars($row["MiddleInitial"]) . " " . htmlspecialchars($row["LastName"]) . "</p>";
                echo "<p>SSN: ***-**-" . substr(htmlspecialchars($row["SSN"]), -4) . "</p>";
                echo "<p>Age: " . htmlspecialchars($row["Age"]) . "</p>";
                echo "<p>Gender: " . htmlspecialchars($row["Gender"]) . "</p>";
                echo "<p>Race: " . htmlspecialchars($row["Race"]) . "</p>";
                echo "<p>Occupation Class: " . htmlspecialchars($row["OccupationClass"]) . "</p>";
                echo "<p>Medical History: " . htmlspecialchars($row["MedicalHistory"]) . "</p>";
                echo "<p>Phone Number: " . htmlspecialchars($row["PhoneNumber"]) . "</p>";
                echo "<p>Address: " . htmlspecialchars($row["Address"]) . "</p>";

                // More patient details here...
                echo '</div></div>';

                $patientID = $row['PatientID'];


                $pastVaccineStmt = $conn->prepare("SELECT Vaccinationrecord.*, vaccinationslot.Date, CONCAT(Nurse.FirstName, ' ', Nurse.LastName) AS NurseName, Vaccine.Name AS VaccineName FROM Vaccinationrecord INNER JOIN vaccinationslot ON Vaccinationrecord.SlotID = vaccinationslot.SlotID LEFT JOIN Nurse ON Vaccinationrecord.NurseID = Nurse.NurseID INNER JOIN Vaccine ON vaccinationslot.VaccineID = Vaccine.VaccineID WHERE PatientID = ? AND vaccinationslot.Date < ?");
                $pastVaccineStmt->bind_param("ss", $patientID, $currentDate);
                $pastVaccineStmt->execute();
                $pastVaccineResult = $pastVaccineStmt->get_result();

                if ($pastVaccineResult && $pastVaccineResult->num_rows > 0) {
                    echo '<div class="card mb-3"><div class="card-body">';
                    echo "<h5 class='card-title'>Past Vaccination Records</h5>";
                    while ($vaccineRow = $pastVaccineResult->fetch_assoc()) {
                        echo "<p> Dose Number: " . htmlspecialchars($vaccineRow["DoseNumber"]) ."</p> " ;
                        echo "<p> Vaccine: " . htmlspecialchars($vaccineRow["VaccineName"]) ."</p> ";
                        echo "<p> Date: " . htmlspecialchars($vaccineRow["Date"]) ."</p> ";
                        echo "<p> Nurse: " . htmlspecialchars($vaccineRow["NurseName"]) . "</p>";
                    }
                    echo '</div></div>';
                } else {
                    echo "<p>No past vaccination records found.</p>";
                }



                $upcomingVaccineStmt = $conn->prepare("SELECT Vaccinationrecord.*, vaccinationslot.*, CONCAT(Nurse.FirstName, ' ', Nurse.LastName) AS NurseName, Vaccine.Name AS VaccineName FROM Vaccinationrecord INNER JOIN vaccinationslot ON Vaccinationrecord.SlotID = vaccinationslot.SlotID LEFT JOIN Nurse ON Vaccinationrecord.NurseID = Nurse.NurseID INNER JOIN Vaccine ON vaccinationslot.VaccineID = Vaccine.VaccineID WHERE PatientID = ? AND vaccinationslot.Date > ?");                
                $upcomingVaccineStmt->bind_param("ss", $patientID, $currentDate);
                $upcomingVaccineStmt->execute();
                $upcomingVaccineResult = $upcomingVaccineStmt->get_result();

                if ($upcomingVaccineResult && $upcomingVaccineResult->num_rows > 0) {
                    echo '<div class="card mb-3"><div class="card-body">';
                    echo "<h5 class='card-title'>Upcoming Vaccine Schedules</h5>";
                    while ($scheduleRow = $upcomingVaccineResult->fetch_assoc()) {
                        $doseNumber = isset($scheduleRow["DoseNumber"]) ? htmlspecialchars($scheduleRow["DoseNumber"]) : 1;
                        $nurseName = isset($scheduleRow["NurseName"]) ? htmlspecialchars($scheduleRow["NurseName"]) : "Not assigned yet";
                        echo "<p> Dose Number: " . $doseNumber . "</p> " ;
                        echo "<p> Vaccine: " . htmlspecialchars($scheduleRow["VaccineName"]) ."</p> ";
                        echo "<p> Date: " . htmlspecialchars($scheduleRow["Date"]) ." </p>";
                        echo "<p> Start Time: " . htmlspecialchars($scheduleRow["StartTime"]) ." </p> ";
                        echo "<p> End Time: " . htmlspecialchars($scheduleRow["EndTime"]) . "</p>";
                        echo "<p> Nurse: ". $nurseName ."</p>";
                    }
                    echo '</div></div>';
                } else {
                    echo "<p>No upcoming vaccine schedules found.</p>";
                }


                $stmt->close();
            } else {
                echo "<p class='alert alert-danger'>No patient found with the provided information.</p>";
            }
            $conn->close();
        }
        ?>
    </div>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

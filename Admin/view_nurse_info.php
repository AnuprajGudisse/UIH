<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Nurse Information</title>
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
        <h2 class="mt-4">Search Nurse Information</h2>
        <form action="view_nurse_info.php" method="post" class="mb-4">
            <div class="form-group">
                <label for="searchTerm">Search by Nurse ID or Full Name:</label>
                <input type="text" class="form-control" id="searchTerm" name="searchTerm" required>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            include '../connection.php'; // Adjust the path as necessary

            if(isset($_POST["delete"])) {
                $id = $_POST["id"];
        
                // Delete the record from the database
                $sql = "DELETE FROM nurse WHERE NurseID = $id";
        
                if ($conn->query($sql) === TRUE) {
                    echo "Record deleted successfully";
                } else {
                    echo "Error deleting record: " . $conn->error;
                }
            }

            

            $searchTerm = $_POST["searchTerm"];
            $currentDate = date("Y-m-d");

            $stmt = $conn->prepare("SELECT * FROM Nurse WHERE NurseID = ? OR CONCAT(FirstName, ' ', LastName) = ?");
            $stmt->bind_param("ss", $searchTerm, $searchTerm);
            $stmt->execute();
            $nurseResult = $stmt->get_result();

            if ($nurseResult && $nurseResult->num_rows > 0) {
                $row = $nurseResult->fetch_assoc();
                echo '<div class="card mb-3"><div class="card-body">';
                echo '<div class="row justify-content-between">';
                echo "<h5 class='ml-3 card-title'>Nurse Information</h5>";
                echo "<div class='row row-end'>";
                // echo "<a class='mr-2 border p-1 px-2 border-primary' href='./edit_nurse.php'>edit</a>";
                echo "<button class='mr-2 text-primary border p-1 px-2 border-primary' onclick='onClickEdit()'>Edit</button>";
                echo "<form method='post' action='view_nurse_info.php'>";
                echo "<input type='hidden' name='id' value='".$row['NurseID']."'>";
                echo "<input class='mr-4 text-danger border p-1 border-danger' type='submit' name='delete' value='delete'>";
                echo "</form>";
                // echo "<a class='mr-4 text-danger border p-1 border-danger' href='./edit_nurse.php'>delete</a>";
                echo '</div></div>';
                // echo "<h5 class='ml-3 card-title'>Nurse Information</h5>";
                echo "<p>Name: " . htmlspecialchars($row["FirstName"]) . " " . htmlspecialchars($row["MiddleInitial"]) . " " . htmlspecialchars($row["LastName"]) . "</p>";
                echo "<p>NurseID: " . htmlspecialchars($row["NurseID"]) . "</p>";
                echo "<p>Age: " . htmlspecialchars($row["Age"]) . "</p>";
                echo "<p>Gender: " . htmlspecialchars($row["Gender"]) . "</p>";
                echo "<p>Phone Number: " . htmlspecialchars($row["PhoneNumber"]) . "</p>";
                echo "<p>Address: " . htmlspecialchars($row["Address"]) . "</p>";
                echo "</div></div>";

                $nurseID = $row['NurseID'];


                // $timeSlotStmt = $conn->prepare("SELECT Vaccinationrecord.*, vaccinationslot.Date, CONCAT(Nurse.FirstName, ' ', Nurse.LastName) AS NurseName, Vaccine.Name AS VaccineName FROM Vaccinationrecord INNER JOIN vaccinationslot ON Vaccinationrecord.SlotID = vaccinationslot.SlotID LEFT JOIN Nurse ON Vaccinationrecord.NurseID = Nurse.NurseID INNER JOIN Vaccine ON vaccinationslot.VaccineID = Vaccine.VaccineID WHERE nurseID = ? AND vaccinationslot.Date < ?");
                $timeSlotStmt = $conn->prepare("Select v.* from vaccinationslot v, nurseschedule ns where ns.NurseID = ? and ns.SlotID = v.SlotID");
                $timeSlotStmt->bind_param("s", $nurseID);
                $timeSlotStmt->execute();
                $timeSlotResult = $timeSlotStmt->get_result();

                if ($timeSlotResult && $timeSlotResult->num_rows > 0) {
                    echo '<div class="card mb-3"><div class="card-body">';
                    echo "<h5 class='card-title'>Time Slots Scheduled</h5>";
                    while ($slotRow = $timeSlotResult->fetch_assoc()) {
                        echo "<p> SlotID: " . htmlspecialchars($slotRow["SlotID"]) ."</p> ";
                        echo "<p> Date: " . htmlspecialchars($slotRow["Date"]) ."</p> ";
                        echo "<p> Start Time: " . htmlspecialchars($slotRow["StartTime"]) ."</p> ";
                        echo "<p> End Time: " . htmlspecialchars($slotRow["EndTime"]) ."</p> ";
                    }
                    echo '</div></div>';
                } else {
                    echo "<p>No Time slots scheduled found.</p>";
                }


                // $upcomingVaccineStmt = $conn->prepare("SELECT Vaccinationrecord.*, vaccinationslot.*, CONCAT(Nurse.FirstName, ' ', Nurse.LastName) AS NurseName, Vaccine.Name AS VaccineName FROM Vaccinationrecord INNER JOIN vaccinationslot ON Vaccinationrecord.SlotID = vaccinationslot.SlotID LEFT JOIN Nurse ON Vaccinationrecord.NurseID = Nurse.NurseID INNER JOIN Vaccine ON vaccinationslot.VaccineID = Vaccine.VaccineID WHERE nurseID = ? AND vaccinationslot.Date > ?");
                // $upcomingVaccineStmt->bind_param("ss", $nurseID, $currentDate);
                // $upcomingVaccineStmt->execute();
                // $upcomingVaccineResult = $upcomingVaccineStmt->get_result();

                // if ($upcomingVaccineResult && $upcomingVaccineResult->num_rows > 0) {
                //     echo '<div class="card mb-3"><div class="card-body">';
                //     echo "<h5 class='card-title'>Upcoming Vaccine Schedules</h5>";
                //     while ($scheduleRow = $upcomingVaccineResult->fetch_assoc()) {
                //         $doseNumber = isset($scheduleRow["DoseNumber"]) ? htmlspecialchars($scheduleRow["DoseNumber"]) : 1;
                //         $nurseName = isset($scheduleRow["NurseName"]) ? htmlspecialchars($scheduleRow["NurseName"]) : "Not assigned yet";
                //         echo "<p> Dose Number: " . $doseNumber . "</p> " ;
                //         echo "<p> Vaccine: " . htmlspecialchars($scheduleRow["VaccineName"]) ."</p> ";
                //         echo "<p> Date: " . htmlspecialchars($scheduleRow["Date"]) ." </p>";
                //         echo "<p> Start Time: " . htmlspecialchars($scheduleRow["StartTime"]) ." </p> ";
                //         echo "<p> End Time: " . htmlspecialchars($scheduleRow["EndTime"]) . "</p>";
                //         echo "<p> Nurse: ". $nurseName ."</p>";
                //     }
                //     echo '</div></div>';
                // } else {
                //     echo "<p>No upcoming vaccine schedules found.</p>";
                // }


                $stmt->close();
            } else {
                echo "<p class='alert alert-danger'>No Nurse found with the provided information.</p>";
            }
            $conn->close();
        }
        ?>
 <div class="container mt-4" id="editForm" style="display: none;">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
        <div class="card-body">
            <form action="update_nurse.php" method="post" class="form-group">

                <div class="mb-3">
                    <label for="FirstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" name="FirstName" id="FirstName" value="<?php echo $row['FirstName']; ?>">
                </div>

                <div class="mb-3">
                    <label for="mi" class="form-label">Middle Initial</label>
                    <input type="text" class="form-control" name="mi" id="mi" value="<?php echo $row['MiddleInitial']; ?>">
                </div>

                <!-- Repeat for each field -->
                <div class="mb-3">
                    <label for="LastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="LastName" id="LastName" value="<?php echo $row['LastName']; ?>">
                </div>

                <div class="mb-3">
                    <label for="age" class="form-label">Age</label>
                    <input type="text" class="form-control" name="age" id="age" value="<?php echo $row['Age']; ?>">
                </div>

                <div class="mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <input type="text" class="form-control" name="gender" id="gender" value="<?php echo $row['Gender']; ?>">
                </div>
                <?php echo "<input type='hidden' name='nurseID' value='".$nurseID."'/>"; ?>

                <div class='text-center'><button type="submit" class="btn btn-success ">Update</button></div>
            </form>
    </div>
    </div></div>
    </div></div>

    </div>

    <script>
        function onClickEdit(){
            var form = document.getElementById("editForm");
            form.style.display = form.style.display === "none" ? "block" : "none";
        }
    </script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

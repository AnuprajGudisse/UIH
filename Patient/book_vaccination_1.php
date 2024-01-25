<?php
include '../connection.php'; // Include your connection file
session_start();

function hasExistingVaccinationRecord($conn, $patientID, $slotID) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM vaccinationrecord WHERE PatientID = ? AND SlotID = ?");
    $stmt->bind_param("ii", $patientID, $slotID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

function checkCapacity($conn, $date, $startTime) {
    $formattedStartTime = $startTime->format('H:i');

    $stmt = $conn->prepare("SELECT MIN(Capacity) AS MinCapacity  FROM vaccinationslot WHERE Date = ? AND StartTime = ?");
    $stmt->bind_param("ss", $date, $formattedStartTime);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $slot = $result->fetch_assoc();
        return $slot['MinCapacity'];
    }

    return 99; 
}

function updateVaccineID($conn, $date, $startTime, $patientID, $vaccineID) {
    $formattedStartTime = $startTime->format('H:i');

    $stmt = $conn->prepare("UPDATE vaccinationslot SET VaccineID = ? WHERE Date = ? AND StartTime = ? AND VaccineID IS NULL");
    $stmt->bind_param("iss", $vaccineID, $date, $formattedStartTime);
    $stmt->execute();

    $affectedRows = $stmt->affected_rows;
    $stmt->close();

    return $affectedRows > 0;
}


function scheduleAppointment($conn, $vaccineID, $date, $timeSlot) {
    // Retrieve PatientID from session
    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT PatientID FROM patient WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();
    $patientID = $patient['PatientID'];

    // Check availability and on-hold status of the vaccine
    $stmt = $conn->prepare("SELECT Availability, OnHold FROM vaccine WHERE VaccineID = ?");
    $stmt->bind_param("s", $vaccineID);
    $stmt->execute();
    $result = $stmt->get_result();
    $vaccine = $result->fetch_assoc();

    if ($vaccine && ($vaccine['Availability'] - $vaccine['OnHold'] > 0)) {
        // Format the time slot
        $startTime = DateTime::createFromFormat('H:i', $timeSlot);
        $endTime = clone $startTime;
        $endTime->modify('+1 hour');
        $formattedStartTime = $startTime->format('H:i');
        $formattedEndTime = $endTime->format('H:i');

        // Check if the date and time slot already exists
        $stmt = $conn->prepare("SELECT SlotID, Capacity,VaccineID FROM vaccinationslot WHERE Date = ? AND StartTime = ?");
        $stmt->bind_param("ss", $date, $formattedStartTime);
        $stmt->execute();
        $result = $stmt->get_result();

        $slotID = null;

        if ($result->num_rows > 0) {
            // Slot exists, check capacity and if the patient already has a record
            $slot = $result->fetch_assoc();
            if ($slot['Capacity'] > 0 && !hasExistingVaccinationRecord($conn, $patientID, $slot['SlotID'])) {
                // Update VaccineID if it's null
                if ($slot['VaccineID'] === null) {
                    $updateSuccess = updateVaccineID($conn, $date, $startTime, $patientID, $vaccineID);                    
                    $newCapacity = checkCapacity($conn, $date, $startTime);
                    $stmt = $conn->prepare("UPDATE vaccinationslot SET Capacity = ? - 1 WHERE Date = ? AND StartTime = ?");
                    $stmt->bind_param("iss", $newCapacity, $date, $formattedStartTime);
                    $stmt->execute();

                    $stmt = $conn->prepare("UPDATE vaccine SET OnHold = OnHold + 1 WHERE VaccineID = ?");
                    $stmt->bind_param("s", $vaccineID);
                    $stmt->execute();

                    $slotID = $slot['SlotID'];
                    if ($updateSuccess) {
                        echo "Vaccine selection updated successfully.";
                    } else {
                        echo "Failed to update vaccine selection.";
                    }
                } else {
                    // Decrement capacity by 1
                    $newCapacity = checkCapacity($conn, $date, $startTime);
                    $stmt = $conn->prepare("UPDATE vaccinationslot SET Capacity = ? - 1 WHERE Date = ? AND StartTime = ?");
                    $stmt->bind_param("iss", $newCapacity, $date, $formattedStartTime);
                    $stmt->execute();

                    $stmt = $conn->prepare("UPDATE vaccine SET OnHold = OnHold + 1 WHERE VaccineID = ?");
                    $stmt->bind_param("s", $vaccineID);
                    $stmt->execute();

                    $slotID = $slot['SlotID'];
                    echo "Appointment scheduled successfully.";
                }
            } else {
                echo "This time slot is fully booked or you have already booked.";
                return;
            }
        }  else {
            // Slot doesn't exist, create a new one
            $capacity = 99;
            $stmt = $conn->prepare("INSERT INTO vaccinationslot (Date, StartTime, EndTime, Capacity, VaccineID) VALUES (?, ?, ?, ?,?)");
            $stmt->bind_param("sssii", $date, $formattedStartTime, $formattedEndTime, $capacity, $vaccineID);
            $stmt->execute();

            $slotID = $conn->insert_id;
        }

        // Insert into vaccinationrecord
        if ($slotID !== null) {
            $stmt = $conn->prepare("INSERT INTO vaccinationrecord (PatientID, SlotID) VALUES (?, ?)");
            $stmt->bind_param("ii", $patientID, $slotID);
            $stmt->execute();

            $stmt = $conn->prepare("UPDATE vaccine SET OnHold = OnHold + 1 WHERE VaccineID = ?");
            $stmt->bind_param("s", $vaccineID);
            $stmt->execute();

            echo "Appointment scheduled successfully.";
        }
    } else {
        echo "No available doses for this vaccine.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['vaccineID']) && isset($_POST['date']) && isset($_POST['timeSlot'])) {
        $vaccineID = $_POST['vaccineID'];
        $date = $_POST['date'];
        $timeSlot = $_POST['timeSlot'];

        scheduleAppointment($conn, $vaccineID, $date, $timeSlot);
    } else {
        echo "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Vaccination</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../style.css" rel="stylesheet">
    <style>
        .schedule-form {
            max-width: 600px;
            margin: 2rem auto;
            padding: 1rem;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-control, .btn {
            min-height: 38px;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <!-- Logo and website name on the left -->
        <a class="navbar-brand" href="index.php">
            <img src="../logo.png" width="100" height="100" class="d-inline-block align-top" alt="">
        </a>
        <a class="navbar-brand" href="index.php">
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
                    <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="register.php">Register <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#">About Us <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="logout.php">Log Out<span class="sr-only">(current)</span></a>
                </li>
                <!-- Additional nav items here -->
            </ul>
        </div>
    </div>
    </nav>

    <!-- Vaccine Booking Form -->
    <div class="schedule-form">
        <h3 class="text-center mb-3">Schedule Your Vaccination</h3>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="p-3">
            <div class="form-group">
                <label for="vaccineID">Select Vaccine:</label>
                <select name="vaccineID" id="vaccine" class="form-control">
                    <?php
            $vaccineQuery = $conn->query("SELECT * FROM vaccine WHERE Availability > OnHold");
            while ($row = $vaccineQuery->fetch_assoc()) {
                echo "<option value='{$row['VaccineID']}'>{$row['Name']}</option>";
            }
            ?>
                </select>
            </div>
            <div class="form-group">
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" class="form-control">
            </div>
            <div class="form-group">
                <label for="timeSlot">Select Time Slot:</label>
                <select name="timeSlot" id="timeSlot" class="form-control">
            <option value="10:00">10:00 - 11:00</option>
            <option value="11:00">11:00 - 12:00</option>
            <option value="12:00">12:00 - 01:00</option>
            <option value="13:00">01:00 - 02:00</option>
            <option value="14:00">02:00 - 03:00</option>
            <option value="15:00">03:00 - 04:00</option>
                </select>
            </div>
            <div class="text-center">
                <input type="submit" value="Schedule Appointment" class="btn btn-primary">
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>



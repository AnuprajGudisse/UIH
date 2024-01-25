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
            </ul>
        </div>
    </div>
    </nav>

    <!-- Vaccine Booking Form -->
    <div class="schedule-form">
        <h3 class="text-center mb-3">Schedule Your Time Slot</h3>

<?php
include '../connection.php'; // Include your connection file
session_start();

function hasExistingTimeSlot($conn, $nurseID, $slotID) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM nurseschedule WHERE NurseID = ? AND SlotID = ?");
    $stmt->bind_param("ii", $nurseID, $slotID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

function hasReachedLimit($conn, $slotID) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count from nurseschedule where SlotID = ?");
    $stmt->bind_param("i", $slotID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 11;
}

function checkTimeSlot($conn, $date, $startTime) {
    $stmt = $conn->prepare("SELECT SlotID, Capacity FROM vaccinationslot WHERE Date = ? AND StartTime = ?");
    $stmt->bind_param("ss", $date, $startTime);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function scheduleTimeSlot($conn, $date, $startTime, $endTime) {
    
    $nurseID = $_SESSION['nurseID'];
    
    // Check if the date and time slot already exists
    $result = checkTimeSlot($conn, $date, $startTime);

    $slotID = null;

    if ($result->num_rows > 0) {
        // Slot exists, check if that slot is already scheduled by him/her
        $slot = $result->fetch_assoc();
        $slotID = $slot['SlotID'];
        if (hasExistingTimeSlot($conn, $nurseID, $slot['SlotID'])) {
            echo "You have already scheduled this time slot. Please choose another time slot.";
            return;
        } else if(hasReachedLimit($conn, $slot['SlotID'])){
            echo "The maximum limit of 12 nurses per time slot has reached. Please select another slot.";
            return;
        }
    } else {
        // Slot doesn't exist, create a new one
        $capacity = 99;
        $stmt = $conn->prepare("INSERT INTO vaccinationslot (Date, StartTime, EndTime, Capacity) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $date, $startTime, $endTime, $capacity);
        $stmt->execute();

        $result = checkTimeSlot($conn, $date, $startTime);
        $slot = $result->fetch_assoc();
        $slotID = $slot['SlotID'];
    }

    $stmt = $conn->prepare("INSERT into nurseschedule(NurseID, SlotID) values (?, ?)");
    $stmt->bind_param("ii", $nurseID, $slotID);
    $stmt->execute();

    echo "Time slot scheduled successfully.";
    return;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['date']) && isset($_POST['timeSlot'])) {
        $date = $_POST['date'];
        $timeSlot = explode('-', $_POST['timeSlot']);
        $startTime = $timeSlot[0];
        $endTime = $timeSlot[1];


        scheduleTimeSlot($conn, $date, $startTime, $endTime);
    } else {
        echo "All fields are required.";
    }
}
?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="p-3">

            <div class="form-group">
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" class="form-control">
            </div>
            <div class="form-group">
                <label for="timeSlot">Select Time Slot:</label>
                <select name="timeSlot" id="timeSlot" class="form-control">
            <option value="10:00:00-11:00:00">10:00 - 11:00</option>
            <option value="11:00:00-12:00:00">11:00 - 12:00</option>
            <option value="12:00:00-01:00:00">12:00 - 01:00</option>
            <option value="01:00:00-02:00:00">01:00 - 02:00</option>
            <option value="02:00:00-03:00:00">03:00 - 04:00</option>
            <option value="03:00:00-04:00:00">02:00 - 03:00</option>
                </select>
            </div>
            <div class="text-center">
                <input type="submit" value="Schedule Slot" class="btn btn-primary">
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>



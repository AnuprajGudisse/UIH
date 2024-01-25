<?php
include '../connection.php'; // Include your connection file
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$patientID = null;

// Fetch PatientID based on username
$stmt = $conn->prepare("SELECT PatientID FROM patient WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($patient = $result->fetch_assoc()) {
    $patientID = $patient['PatientID'];
} else {
    echo "Patient not found.";
    exit;
}

// Function to calculate dose number
function calculateDoseNumber($conn, $patientID, $vaccineID, $recordDate) {
    $currentDate = date("Y-m-d");
    if ($recordDate > $currentDate) {
        // Count future bookings for the same vaccine
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM vaccinationrecord INNER JOIN vaccinationslot ON vaccinationrecord.SlotID = vaccinationslot.SlotID WHERE PatientID = ? AND VaccineID = ? ");
        $stmt->bind_param("ii", $patientID, $vaccineID);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['count'];
    }
    return 1; // Default to 1 if the date is not in the future
}

// Function to fetch booking details
function fetchBookings($conn, $patientID) {
    $stmt = $conn->prepare("SELECT vaccinationrecord.RecordID, vaccine.Name, vaccinationslot.Date, vaccinationslot.VaccineID, vaccinationslot.StartTime, vaccinationslot.EndTime,vaccinationrecord.DoseNumber FROM vaccinationrecord INNER JOIN vaccinationslot ON vaccinationrecord.SlotID = vaccinationslot.SlotID INNER JOIN vaccine ON vaccinationslot.VaccineID = vaccine.VaccineID WHERE vaccinationrecord.PatientID = ?");
    $stmt->bind_param("i", $patientID);
    $stmt->execute();    
    $result = $stmt->get_result();
    $bookings = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($bookings as $key => $booking) {
        $doseNumber = calculateDoseNumber($conn, $patientID, $booking['VaccineID'], $booking['Date']);
        $bookings[$key]['DoseNumber'] = $doseNumber;
    }
    return $bookings;}


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



// Handling form submission for updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $recordID = $_POST['recordID'];
    $newDate = $_POST['newDate'];
    $newTimeSlot = $_POST['newTimeSlot'];

    // Get the old SlotID and VaccineID before updating
    $stmt = $conn->prepare("SELECT vaccinationrecord.SlotID, vaccinationslot.VaccineID FROM vaccinationrecord INNER JOIN vaccinationslot ON vaccinationrecord.SlotID = vaccinationslot.SlotID WHERE RecordID = ?");
    $stmt->bind_param("i", $recordID);
    $stmt->execute();
    $oldBookingResult = $stmt->get_result();
    $oldBooking = $oldBookingResult->fetch_assoc();
    $oldSlotID = $oldBooking['SlotID'];
    $vaccineID = $oldBooking['VaccineID']; // Retain the same vaccine ID

    // Format the new time slot
    $startTime = DateTime::createFromFormat('H:i', $newTimeSlot);
    $endTime = clone $startTime;
    $endTime->modify('+1 hour');
    $formattedStartTime = $startTime->format('H:i');
    $formattedEndTime = $endTime->format('H:i');

    // Check if a slot with the new date and time already exists
    $stmt = $conn->prepare("SELECT SlotID FROM vaccinationslot WHERE Date = ? AND StartTime = ? LIMIT 1");
    $stmt->bind_param("ss", $newDate, $formattedStartTime);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Slot already exists, use its SlotID
        $row = $result->fetch_assoc();
        $newSlotID = $row['SlotID'];
    } else {
        // Slot does not exist, create a new one
        $capacity = 99; // Assuming a default capacity
        $stmt = $conn->prepare("INSERT INTO vaccinationslot (Date, StartTime, EndTime, Capacity, VaccineID) VALUES (?, ?, ?, ?,?)");
        $stmt->bind_param("sssii", $newDate, $formattedStartTime, $formattedEndTime, $capacity,$vaccineID);
        $stmt->execute();
        $newSlotID = $conn->insert_id;
    }

    if ($newSlotID != $oldSlotID) {
        // Increase the capacity of the old slot
        $newCapacity=checkCapacity($conn, $newDate, $startTime);
        $stmt = $conn->prepare("UPDATE vaccinationslot SET Capacity = ? - 1 WHERE Date = ? AND StartTime = ?");
        $stmt->bind_param("iss",$newCapacity, $newDate, $formattedStartTime);
        $stmt->execute();

        // If creating a new slot, ensure it has the correct VaccineID
        if (!isset($row)) {
            $stmt = $conn->prepare("UPDATE vaccinationslot SET VaccineID = ? WHERE SlotID = ?");
            $stmt->bind_param("ii", $vaccineID, $newSlotID);
            $stmt->execute();
        }

        // Update the vaccinationrecord table to the new slot
        $stmt = $conn->prepare("UPDATE vaccinationrecord SET SlotID = ? WHERE RecordID = ?");
        $stmt->bind_param("ii", $newSlotID, $recordID);
        $stmt->execute();

        echo "Booking updated successfully.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel'])) {

        $recordID = $_POST['recordID'];
        // Get the SlotID of the booking
        $stmt = $conn->prepare("SELECT vaccinationrecord.SlotID,vaccinationslot.VaccineID FROM vaccinationrecord INNER JOIN vaccinationslot ON vaccinationrecord.SlotID = vaccinationslot.SlotID WHERE RecordID = ?");
        $stmt->bind_param("i", $recordID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $slotID = $row['SlotID'];
            $vaccineID= $row['VaccineID'];

            // Update the vaccinationslot capacity and delete the booking
            $stmt = $conn->prepare("UPDATE vaccinationslot SET Capacity = Capacity + 1 WHERE SlotID = ?");
            $stmt->bind_param("i", $slotID);
            $stmt->execute();

            $stmt = $conn->prepare("UPDATE vaccine SET OnHold = OnHold + 1 WHERE VaccineID = ?");
            $stmt->bind_param("i", $vaccineID);
            $stmt->execute();

            $stmt = $conn->prepare("DELETE FROM vaccinationrecord WHERE RecordID = ?");
            $stmt->bind_param("i", $recordID);
            $stmt->execute();

            echo "Booking canceled successfully.";
        } else {
            echo "Booking not found.";
        }

}

$bookings = fetchBookings($conn, $patientID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Your Bookings</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4; /* Light grey background */
            font-family: 'Arial', sans-serif;
        }
        .booking-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth transition for hover effects */
            margin-bottom: 20px; /* Spacing between cards */
        }
        .booking-card:hover {
            transform: translateY(-10px); /* Slightly raise the card on hover */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect for depth */
        }
        .booking-card .card-body {
            text-align: left; /* Left align text */
        }
        .booking-card .btn-primary {
            background-color: #0056b3; /* Custom button color */
            border: none;
        }
        .booking-card .btn-primary:hover {
            background-color: #004494; /* Darken button on hover */
        }
        .card-title {
            color: #333; /* Dark text for title */
            font-size: 1.2rem; /* Larger font size for title */
        }
        .update-form {
            display: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>
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

    <div class="container my-5">
        <h2 class="text-center">Manage Your Bookings</h2>
        <div class="row">
            <!-- PHP code to loop through bookings and create cards -->
            <?php foreach ($bookings as $booking): ?>
                <div class="col-md-6">
                    <div class="card booking-card">
                        <div class="card-body">
                            <h5 class="card-title">Vaccine: <?php echo htmlspecialchars($booking['Name']); ?></h5>
                            <p>Date: <?php echo htmlspecialchars($booking['Date']); ?></p>
                            <p>Time: <?php echo htmlspecialchars($booking['StartTime'] . ' - ' . $booking['EndTime']); ?></p>
                            <p>Dose Number: <?php echo $booking['DoseNumber']; ?></p>
                            <?php
                            // Check if the booking date is in the future
                            if (new DateTime($booking['Date']) > new DateTime()) {
                                echo '<button onclick="document.getElementById(\'updateForm' . $booking['RecordID'] . '\').style.display=\'block\'"             class="btn btn-primary">Update</button>';
                                echo '<form method="post" style="display: inline-block;">
                                          <input type="hidden" name="recordID" value="' . $booking['RecordID'] . '">
                                          <input type="submit" name="cancel" value="Cancel" class="btn btn-danger">
                                      </form>';
                            } else {
                                echo '<button class="btn btn-secondary" disabled>Past Booking</button>';
                            }
                            ?>

                            <div id="updateForm<?php echo $booking['RecordID']; ?>" class="update-form">
                                <form method="post">
                                    <input type="hidden" name="recordID" value="<?php echo $booking['RecordID']; ?>">
                                    <label for="newDate">New Date:</label>
                                    <input type="date" id="newDate" name="newDate" required><br>
                                    <div class="form-group">
                                        <label for="newTimeSlot">Select Time Slot:</label>
                                            <select name="newTimeSlot" id="newTimeSlot" class="form-control">
                                                <option value="10:00">10:00 - 11:00</option>
                                                <option value="11:00">11:00 - 12:00</option>
                                                <option value="12:00">12:00 - 01:00</option>
                                                <option value="13:00">01:00 - 02:00</option>
                                                <option value="14:00">02:00 - 03:00</option>
                                                <option value="15:00">03:00 - 04:00</option>
                                            </select>
                                    </div><br>
                                    <input type="submit" name="update" value="Update Booking" class="btn btn-primary">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

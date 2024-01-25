<?php
    session_start();
    include('../connection.php');
    // echo 'hello';
    $sql = "SELECT  * FROM `nurse` WHERE `NurseID`=".$_SESSION['nurseID'];
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);

?>
<!DOCTYPE html>
    <head>
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <!-- Bootstrap CSS -->
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="../style.css" rel="stylesheet">
        <title>Nurse</title>
    </head>
<body>
    <style>
        body{
            margin: 0%;
            padding: 0%;
        }
        li{
            list-style: none;
            padding: 10px;
            text-transform: uppercase;
        }
        li:nth-child(3){
            float: right;
        }
        a{
  
            text-decoration: none; 
            color: white;
        }
        
        button{
            height: 40px;
             width: 150px;
             border-radius: 5px;
             color: #fff;
             background-color: #4eafcb;
             cursor: pointer;
             border: none;
        }
        .custom{
            height: 40px;
            width: 150px;
            border-radius: 5px;
            color: #fff;
            background-color: #4eafcb;
            cursor: pointer;
            border: none;
        }
        
        .fa-check{
            padding-right: 20px;
        }
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


        <div class="collapse navbar-collapse d-flex flex-row-reverse" style=" height: 20%; padding: 10px;">
            <div class="info">
                <h3><?php echo $row['FirstName'].' '.$row['MiddleInitial'].' '.$row['LastName']  ?></h3>
                <p class='mb-1'>Nurse ID : <?php echo $row['NurseID']  ?></p>
                <p class='mb-1'>Phone : <?php echo $row['PhoneNumber']  ?> </p>
            </div>
            
            <div class="image" >
                <img src="../profile.jpeg" style="height: 100px; width: 75px; margin-right: 20px;">
            </div>
        </div>

    </div>
    </nav>

    

    <div class="navbar" style="background-color: #000; height: 50px;">
        <ul style="margin: 0%; padding: 0%; display: inline-flex;">
            <li><a href="./profile.php">profile</a></li>
            <li><a href="./index.php">slot availability</a></li>
            <li><a href="./record_vaccine.php">record vaccine</a></li>
            <li><a href="./logout.php">logout</a></li>
        </ul>
    </div>



    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assigning form data to variables
    $slotID = $_POST["slotID"];
    $patientID = $_POST["patientID"];
    $dose = $_POST["dose"];


    $sqlDoseUpdate = "UPDATE vaccinationrecord SET DoseNumber = ? WHERE PatientID = ? AND SlotID = ?";
    $stmtDoseUpdate = $conn->prepare($sqlDoseUpdate);
    $stmtDoseUpdate->bind_param("iii", $dose, $patientID, $slotID);

    if ($stmtDoseUpdate->execute()) {
        echo "<p class='text-center'>Dose number updated successfully!</p>";
    } else {
        echo "Error updating DoseNumber: " . $stmtDoseUpdate->error;
    }

    $sql = "UPDATE vaccinationrecord SET NurseID= ? WHERE PatientID = ? AND SlotID = ? ";
    // Prepare statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['nurseID'], $patientID, $slotID);

    if ($stmt->execute()) {
        echo "<p class='text-center'>Recorded successfully!</p>";
        // Redirect to login page or another appropriate page  
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt = $conn->prepare("SELECT VaccineID FROM vaccinationslot WHERE SlotID=? ");
    $stmt->bind_param("i", $slotID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $vaccineID=$row['VaccineID'];

    $stmt = $conn->prepare("UPDATE vaccine SET Availability = Availability - 1 WHERE VaccineID = ?");
    $stmt->bind_param("i", $vaccineID);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE vaccine SET OnHold = OnHold - 1 WHERE VaccineID = ?");
    $stmt->bind_param("i", $vaccineID);
    $stmt->execute();

    $stmt->close();
    $conn->close();
}
?>
                <div class="login-form bg-light mt-4 p-4">
                    <form action="./record_vaccine.php" method="post" class="row g-3">
                        <h4 class="col-12 text-center">Record the vaccine delivered</h4>
                        <div class="col-12">
                            <label>Slot ID</label>
                            <input type="text" name="slotID" class="form-control" placeholder="Enter the slot ID">
                        </div>
                        <div class="col-12">
                            <label>Patient ID</label>
                            <input type="text" name="patientID" class="form-control" placeholder="Enter the patient ID">
                        </div>
                        <div class="col-12">
                            <label>Dose Number</label>
                            <input type="number" name="dose" class="form-control" placeholder="Enter the dose number">
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-dark float-end" style="margin-top: 10px;" >Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

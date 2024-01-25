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

    <div>

        <div style="margin-left: 40px;">
            <h3 class='mt-4' style="position: relative;">Upcoming Time slots</h3>
            <button  id="sidebutton" style="display:none; position: relative; left:80%; top:-40%; cursor: pointer;" onclick="addTable()" >+ Add Slot</button>
        </div>

        <?php
            $currentDate = date("Y-m-d");
            // echo 'date: '.$currentDate;
            $stmt = $conn->prepare("SELECT v.* FROM vaccinationslot v JOIN nurseschedule ns ON ns.SlotID = v.SlotID Where ns.NurseID = ?");
            $stmt->bind_param("i", $_SESSION['nurseID']);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                echo "<div class='border rounded d-flex flex-row ' style='margin-left: 40px; margin-top: 10px; width: fit-content;'>";
                echo "<div class='m-1 p-1 d-flex flex-column'>";
                echo "<p class='mb-1'>Slot ID: ".$row['SlotID']."</p>";
                echo "<p class='mb-1'>Date: ".$row['Date']."</p>";
                echo "<p class='mb-1'>Start Time: ".$row['StartTime']."</p>";
                echo "<p class='mb-1'>End Time: ".$row['EndTime']."</p>";
                echo "</div>";
                echo "<form method='post' action='delete_slot.php'>";
                echo "<input type='hidden' name='nurseID' value='".$_SESSION['nurseID']."'>";
                echo "<input type='hidden' name='slotID' value='".$row['SlotID']."'>";
                echo "<input class='ml-4 text-danger border p-1 border-danger' type='submit' name='delete' value='delete'>";
                echo "</form>";
                echo "</div>";
            }
        ?>
        
        

        <div id="tablediv" style="height: 200px;position: relative; margin-left: 40px;">
            <div  id="centerbutton"  style="position: absolute; margin: 0%; top: 30%; left: 35%; display: block; text-align: center;">
                <!-- <h5>You have not shared your availability yet</h5> -->
                <!-- <button onclick="hidedisplay(),addTable()">+ Schedule Slot</button> -->
                <a class='px-4 py-2 bg-primary rounded' href='./schedule_slot.php'>+ Schedule Slot</a>
            </div>
        </div>

    </div>
</body>
<?php
    session_start();
    include('../connection.php');
    // echo 'hello';
    $sql = "SELECT  * FROM `nurse` WHERE `NurseID`=".$_SESSION['nurseID'];
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $id = $_POST["id"];
        $newContent = $_POST["newContent"];
        $nurseID = $_SESSION['nurseID'];
    
        // Update the database based on the provided ID and new content
        $sql = "UPDATE nurse SET " . $id . " = ? WHERE NurseID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $newContent, $nurseID);
        $stmt->execute();
    
        if ($stmt->affected_rows > 0) {
            echo "Update successful";
        } else {
            echo "Update failed";
        }
    
        $stmt->close();
        $conn->close();
    }
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
            height: 30px;
             width: 70px;
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


    <script>
        function editContent(id) {
            var para = document.getElementById(id);
            var newContent = prompt('Enter new ' + id + ' :', para.innerText);
            if (newContent !== null) {
                // Send the newContent value to a PHP script using AJAX
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "profile.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        console.log(xhr.responseText); // Log the response from the PHP script
                        location.reload();
                    }
                };
                xhr.send("id=" + id + "&newContent=" + encodeURIComponent(newContent));
            }
        }
    </script>

    <div class='container mt-4'>
    <?php
    echo '<div class="card mb-3"><div class="card-body">';
    echo '<div class="row justify-content-between">';
    echo "<h4 class='ml-3 card-title'>Your Information</h4>";
    echo "<div class='row row-end'>";
    echo '</div></div>';
    echo "<p>Name: " . htmlspecialchars($row["FirstName"]) . " " . htmlspecialchars($row["MiddleInitial"]) . " " . htmlspecialchars($row["LastName"]) . "</p>";
    echo "<p>NurseID: " . htmlspecialchars($row["NurseID"]) . "</p>";
    echo "<p>Age: " . htmlspecialchars($row["Age"]) . "</p>";
    echo "<p>Gender: " . htmlspecialchars($row["Gender"]) . "</p>";
    echo "<div class='d-flex justify-content-between'>";
    echo "<div class='d-flex'><p>Phone Number: </p> <p id='PhoneNumber' class='ml-2'>". htmlspecialchars($row["PhoneNumber"]) . "</p></div>";
    ?>
    <button type='button' onclick="editContent('PhoneNumber')">edit</button>
    </div>
    <div class='d-flex justify-content-between'>
    <div class='d-flex'><p>Address: </p><?php echo "<p id='Address' class='ml-2'>". htmlspecialchars($row["Address"]) . "</p>"; ?> </div>
    <button type='button' onclick="editContent('Address')">edit</button>
    
    </div>
</body>
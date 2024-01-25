<?php
session_start();
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $firstName = $_POST['FirstName'];
    $mi = $_POST["mi"];
    $lastname = $_POST["LastName"];
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    $nurseID = $_POST['nurseID'];
    

    // print_r($_POST);

    // Update query
    $stmt = $conn->prepare("UPDATE nurse SET FirstName = ?, MiddleInitial= ?, LastName = ?, Age = ?, Gender = ? WHERE NurseID = ?");
    $stmt->bind_param("sssiss", $firstName, $mi, $lastname, $age, $gender, $nurseID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Update successful
        header("Location: view_nurse_info.php"); // Redirect back to the profile page
    } 
    else {      
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>

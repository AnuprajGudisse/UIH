<?php
session_start();
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //$username = $_POST['username']; // Hidden field in the form
    $firstName = $_POST['FirstName'];
    $mi = $_POST["mi"];
    $lastname = $_POST["LastName"];
    $ssn = $_POST["ssn"];
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    $race = $_POST["race"];
    $occupation_class = $_POST["occupation_class"];
    $med_history = $_POST["med_history"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $password = $_POST["password"];

    print_r($_POST);

    // Update query
    $stmt = $conn->prepare("UPDATE patient SET FirstName = ?, MiddleInitial= ?, LastName = ?, SSN = ?, Age = ?, Gender = ?, Race = ?, OccupationClass = ?, MedicalHistory = ?, PhoneNumber = ?, Address = ?, Password = ? WHERE Username = ?");
    $stmt->bind_param("sssisssssssss", $firstName, $mi, $lastname, $ssn, $age, $gender, $race, $occupation_class, $med_history, $phone, $address,$password,$_SESSION['username']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Update successful
        header("Location: patient_details.php"); // Redirect back to the profile page
    } 
    else {      
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>

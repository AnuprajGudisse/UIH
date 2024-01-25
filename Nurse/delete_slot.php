<?php
session_start();
include('../connection.php');

// Collect data from the form
$nurseID = $_POST['nurseID'];
$slotID = $_POST['slotID'];


// Perform the database insertion
$sql = "DELETE from nurseschedule where NurseID = ? and SlotID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $nurseID, $slotID);

if ($stmt->execute()) {
    header('Location: ./index.php');
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

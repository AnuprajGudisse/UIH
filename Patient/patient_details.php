<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include '../connection.php';

$username = $_SESSION['username'];

// Prepare and bind
$stmt = $conn->prepare("SELECT FirstName, MiddleInitial, LastName, SSN, Age, Gender, Race, OccupationClass, MedicalHistory, PhoneNumber, Address, Password FROM patient WHERE Username = ?");
$stmt->bind_param("s", $username);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    $error = "No user details found.";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../style.css" rel="stylesheet">
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

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <?php if (isset($error)): ?>
                    <p class="alert alert-danger"><?php echo $error; ?></p>
                <?php else: ?>
                    <div class="card user-details-card">
                        <div class="card-header text-center">
                            <h3>User Details</h3>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tbody>
                                    <tr><th>First Name</th><td><?php echo $row['FirstName']; ?></td></tr>
                                    <tr><th>Middle Initial</th><td><?php echo $row['MiddleInitial']; ?></td></tr>
                                    <tr><th>Last Name</th><td><?php echo $row['LastName']; ?></td></tr>
                                    <tr><th>SSN</th><td><?php echo $row['SSN']; ?></td></tr>
                                    <tr><th>Age</th><td><?php echo $row['Age']; ?></td></tr>
                                    <tr><th>Gender</th><td><?php echo $row['Gender']; ?></td></tr>
                                    <tr><th>Race</th><td><?php echo $row['Race']; ?></td></tr>
                                    <tr><th>Occupation Class</th><td><?php echo $row['OccupationClass']; ?></td></tr>
                                    <tr><th>Medical History</th><td><?php echo $row['MedicalHistory']; ?></td></tr>
                                    <tr><th>Phone Number</th><td><?php echo $row['PhoneNumber']; ?></td></tr>
                                    <tr><th>Address</th><td><?php echo $row['Address']; ?></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
        <div class="card-footer text-center">
            <button class="btn btn-primary" onclick="toggleEditForm()">Edit Details</button>
        </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">

    <div class="card-body" id="editForm" style="display: none;">
    <form action="update_user.php" method="post" class="form-group">

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
            <label for="ssn" class="form-label">SSN</label>
            <input type="text" class="form-control" name="ssn" id="ssn" value="<?php echo $row['SSN']; ?>">
        </div>

        <div class="mb-3">
            <label for="age" class="form-label">Age</label>
            <input type="text" class="form-control" name="age" id="age" value="<?php echo $row['Age']; ?>">
        </div>

        <div class="mb-3">
            <label for="gender" class="form-label">Gender</label>
            <input type="text" class="form-control" name="gender" id="gender" value="<?php echo $row['Gender']; ?>">
        </div>

        <div class="mb-3">
            <label for="race" class="form-label">Race</label>
            <input type="text" class="form-control" name="race" id="race" value="<?php echo $row['Race']; ?>">
        </div>

        <div class="mb-3">
            <label for="occupation_class" class="form-label">Occupation Class</label>
            <input type="text" class="form-control" name="occupation_class" id="occupation_class" value="<?php echo $row['OccupationClass']; ?>">
        </div>

        <div class="mb-3">
            <label for="med_history" class="form-label">Medical History</label>
            <input type="text" class="form-control" name="med_history" id="med_history" value="<?php echo $row['MedicalHistory']; ?>">
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" class="form-control" name="phone" id="phone" value="<?php echo $row['PhoneNumber']; ?>">
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" name="address" id="address" value="<?php echo $row['Address']; ?>">
        </div>

        <!-- ...other fields... -->

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" value="<?php echo $row['Password']; ?>">
        </div>

        <button type="submit" class="btn btn-success">Update</button>
    </form>
    </div>
            </div>
        </div>
    </div>
</div>


    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    function toggleEditForm() {
        var form = document.getElementById("editForm");
        form.style.display = form.style.display === "none" ? "block" : "none";
    }
</script>

</body>
</html>

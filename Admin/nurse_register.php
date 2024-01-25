<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <!-- Logo and website name on the left -->
        <a class="navbar-brand" href="dashboard.php">
            <img src="../logo.png" width="100" height="100" class="d-inline-block align-top" alt="">
        </a>
        <a class="navbar-brand" href="dashboard.php">
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
                    <a class="nav-link" href="../index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="login.html">Login <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#">About Us <span class="sr-only">(current)</span></a>
                </li>
                <!-- Additional nav items here -->
            </ul>
        </div>
    </div>
	</nav>



    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <!-- Display Image -->
                <div class="text-center mt-4">
                    <img src="../login_avatar.png" class="img-fluid" alt="Display Image" style="max-height: 150px;">
                </div>
                
<?php
// Include database connection file
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assigning form data to variables
    $firstname = $_POST["firstname"];
    $mi = $_POST["mi"];
    $lastname = $_POST["lastname"];
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Insert data into database
    $sql = "INSERT INTO nurse (FirstName, MiddleInitial, LastName, Age, Gender, PhoneNumber, Address, Username, Password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $firstname, $mi, $lastname, $age, $gender, $phone, $address, $username, $password);

    if ($stmt->execute()) {
        echo "Registration successful!";
        // Redirect to login page or another appropriate page
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
                <div class="login-form bg-light mt-4 p-4">
                    <form action="./nurse_register.php" method="post" class="row g-3">
                        <h4 class="col-12 text-center">Register a Nurse</h4>
                        <div class="col-12">
                            <label>First Name</label>
                            <input type="text" name="firstname" class="form-control" placeholder="First Name">
                        </div>
                        <div class="col-12">
                            <label>Middle Initial</label>
                            <input type="text" name="mi" class="form-control" placeholder="Middle Initial">
                        </div>
                        <div class="col-12">
                            <label>Last Name</label>
                            <input type="text" name="lastname" class="form-control" placeholder="Last Name">
                        </div>
                        <div class="col-12">
                            <label>Age</label>
                            <input type="text" name="age" class="form-control" placeholder="Age">
                        </div>
                        <div class="col-12">
                            <label>Gender</label>
                            <input type="text" name="gender" class="form-control" placeholder="Gender">
                        </div>
                        <div class="col-12">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="Phone Number">
                        </div>
                        <div class="col-12">
                            <label>Address</label>
                            <input type="text" name="address" class="form-control" placeholder="Address">
                        </div>
                        <div class="col-12">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Username">
                        </div>
                        <div class="col-12">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Password">
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-dark float-end" style="margin-top: 10px;" >Register</button>
                        </div>
                    </form>
                    <!-- <hr class="mt-4">
                    <div class="col-12">
                        <p class="text-center mb-0">Already registered! <a href="login.php">Login Here</a></p>
                    </div> -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

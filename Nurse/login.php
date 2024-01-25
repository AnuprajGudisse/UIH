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
        <a class="navbar-brand" href="../index.php">
            <img src="../logo.png" width="100" height="100" class="d-inline-block align-top" alt="">
        </a>
        <a class="navbar-brand" href="../index.php">
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
                    <a class="nav-link" href="register.html">Register <span class="sr-only">(current)</span></a>
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
                <div class="login-form bg-light mt-4 p-4">
                    <form action="login.php" method="post" class="row g-3">
                        <h4 class="col-12 text-center">Login as Nurse</h4>
                        <div class="col-12">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Username">
                        </div>
                        <div class="col-12">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Password">
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-dark float-end" style="margin-top: 10px;" >Login</button>
                        </div>
                    </form>
                    <!-- <hr class="mt-4"> -->
                    <!-- <div class="col-12">
                        <p class="text-center mb-0">Have not registered yet? <a href="register.php">Register Here</a></p>
                    </div> -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
session_start();
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Database query to check the credentials
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM nurse WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Credentials are correct
        // Start a new session and set session variables
        $row = mysqli_fetch_array($result);
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['nurseID'] = $row['NurseID'];

        // Redirect to a new page or show a success message
        header("Location: index.php"); // Redirect to a different page
        exit; // Important to prevent further script execution after a redirect
    } else {
        // Credentials are incorrect
        echo "<p>Login failed: Invalid username or password.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

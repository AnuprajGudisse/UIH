<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UIH</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="style.css" rel="stylesheet">

    <style>
        .navigation-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth transition for hover effects */
            cursor: pointer;
        }
        .navigation-card:hover {
            transform: translateY(-10px); /* Slightly raise the card on hover */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect for depth */
        }
        .navigation-card .card-body {
            text-align: center; /* Center align text */
        }
        .navigation-card .btn-primary {
            background-color: #0056b3; /* Custom button color */
            border: none;
        }
        .navigation-card .btn-primary:hover {
            background-color: #004494; /* Darken button on hover */
        }
        .card-title {
            color: #333; /* Dark text for title */
            font-size: 1.2rem; /* Larger font size for title */
        }
    </style>
</head>
<body>    
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <!-- Logo and website name on the left -->
        <a class="navbar-brand" href="../index.php">
            <img src="logo.png" width="100" height="100" class="d-inline-block align-top" alt="">
        </a>
        <a class="navbar-brand" href="../index.php">
            <span class="website-name">UI Health</span>
        </a>


        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#">About Us<span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card navigation-card" style="height: 200px;">
                    <div class="card-body">
                        <h5 class="card-title">Patient</h5>
                        <p class="card-text">Login as Patient</p>
                        <a href="Patient/login.php" class="btn btn-primary">Login</a>
                    </div>
                </div>
            </div>

            <!-- Nurses Management Box -->
            <div class="col-md-4">
                <div class="card navigation-card" style="height: 200px;">
                    <div class="card-body">
                        <h5 class="card-title">Nurse</h5>
                        <p class="card-text">Login as Nurse</p>
                        <a href="Nurse/login.php" class="btn btn-primary">Login</a>
                    </div>
                </div>
            </div>
            
            <!-- Vaccines Management Box -->
            <div class="col-md-4">
                <div class="card navigation-card" style="height: 200px;">
                    <div class="card-body">
                        <h5 class="card-title">Admin</h5>
                        <p class="card-text">Login as Admin</p>
                        <a href="Admin/login.php" class="btn btn-primary">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

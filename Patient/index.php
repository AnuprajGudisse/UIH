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

    <style>
        body {
            background-color: #f4f4f4; /* Light grey background */
            font-family: 'Arial', sans-serif;
        }
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
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <!-- Logo and website name on the left -->
        <a class="navbar-brand" href="">
            <img src="../logo.png" width="100" height="100" class="d-inline-block align-top" alt="">
        </a>
        <a class="navbar-brand" href="">
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
                    <a class="nav-link" href="patient_details.php">My Profile<span class="sr-only">(current)</span></a>
                </li>
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
    <div class="text-center mt-4">
        <img src="index_slide.png" width="500px" height="400px"  class="img-fluid" alt="Descriptive Alt Text">
    </div>

     <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <!-- Box 1 -->
                <div class="card navigation-card" style="height: 200px;">
                    <div class="card-body">
                        <h5 class="card-title">Book your Slot</h5>
                        <p class="card-text">check available slots and Schedule your vaccination slot here</p>
                        <a href="book_vaccination.php" class="btn btn-primary">Click Here</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Box 2 -->
                <div class="card navigation-card" style="height: 200px;">
                    <div class="card-body">
                        <h5 class="card-title">View Booking</h5>
                        <p class="card-text">View details of your slot, update or cancel your booking. </p>
                        <a href="view_booking.php" class="btn btn-primary">Click Here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
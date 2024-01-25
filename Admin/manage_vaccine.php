<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Vaccine Management</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
                    <a class="nav-link" href="dashboard.php">Home <span class="sr-only">(current)</span></a>
                </li>                
                <li class="nav-item active">
                    <a class="nav-link" href="#">About Us <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </div>
    </nav>

    <div class="container">
        <h2 class="mt-4">Admin Vaccine Management</h2>

        <!-- Add Vaccine Form -->
        <h3>Add Vaccine</h3>
        <form action="manage_vaccine.php" method="post">
            <div class="form-group">
                <label for="name">Vaccine Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="companyName">Company Name:</label>
                <input type="text" class="form-control" id="companyName" name="companyName" required>
            </div>
            <div class="form-group">
                <label for="dosesRequired">Doses Required:</label>
                <input type="number" class="form-control" id="dosesRequired" name="dosesRequired" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="availability">Availability:</label>
                <input type="number" class="form-control" id="availability" name="availability" required>
            </div>
            <button type="submit" class="btn btn-primary" name="addVaccine">Add Vaccine</button>
        </form>

        <!-- Update Vaccine Form -->
        <h3 class="mt-4">Update Vaccine</h3>
        <form action="manage_vaccine.php" method="post">
            <div class="form-group">
                <label for="vaccineIdToUpdate">Vaccine:</label>                
                <select class="form-control" id="vaccineIdToUpdate" name="vaccineIdToUpdate">
                    <?php
                    include '../connection.php'; // Database connection
                    $stmt = $conn->prepare("SELECT VaccineID, Name, Availability FROM Vaccine WHERE OnHold = 0");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['VaccineID'] . "'>" . $row['Name'] . " - Current Availability: " . $row['Availability'] . "</option>";
                    }
                    $stmt->close();
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="newAvailability">New Availability:</label>
                <input type="number" class="form-control" id="newAvailability" name="newAvailability" required>
            </div>
            <button type="submit" class="btn btn-primary" name="updateVaccine">Update Vaccine</button>
        </form>

        <?php
        include '../connection.php'; // Database connection

        // Function to add vaccine
        function addVaccine($conn) {
            $stmt = $conn->prepare("INSERT INTO Vaccine (Name, CompanyName, DosesRequired, Description, Availability) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisi", $_POST['name'], $_POST['companyName'], $_POST['dosesRequired'], $_POST['description'], $_POST['availability']);
            $stmt->execute();

            if($stmt->affected_rows > 0) {
                echo "<p class='alert alert-success'>Vaccine added successfully!</p>";
            } else {
                echo "<p class='alert alert-danger'>Failed to add vaccine.</p>";
            }
            $stmt->close();
        }

        // Function to update vaccine
        function updateVaccine($conn) {
            $stmt = $conn->prepare("UPDATE Vaccine SET Availability = ? WHERE VaccineID = ?");
            $stmt->bind_param("ii", $_POST['newAvailability'], $_POST['vaccineIdToUpdate']);
            $stmt->execute();

            if($stmt->affected_rows > 0) {
                echo "<p class='alert alert-success'>Vaccine updated successfully!</p>";
            } else {
                echo "<p class='alert alert-danger'>Failed to update vaccine.</p>";
            }
            $stmt->close();
        }

        // Check if Add Vaccine button was clicked
        if(isset($_POST['addVaccine'])) {
            addVaccine($conn);
        }

        // Check if Update Vaccine button was clicked
        if(isset($_POST['updateVaccine'])) {
            updateVaccine($conn);
        }

        $conn->close();
        ?>
    </div>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

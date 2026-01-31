<?php
session_start();
include('config.php');
include("./lock.php");

$alertMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add functionality
    if (isset($_POST['confirmAdd'])) {
        $userID = $_SESSION['user']['UserID'];
        $pickup = filter_var($_POST['pickup'], FILTER_SANITIZE_STRING);
        $dropoff = filter_var($_POST['dropoff'], FILTER_SANITIZE_STRING);
        $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
        $carType = filter_var($_POST['carType'], FILTER_SANITIZE_STRING);

        $sql = "INSERT INTO tblcars (UserID, Pickup, Dropoff, Date, CarType) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $userID, $pickup, $dropoff, $date, $carType);

        if ($stmt->execute()) {
            $alertMessage = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                Booking added successfully!
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        } else {
            $alertMessage = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                Error adding booking: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8') . "
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        }
        $stmt->close();
    }

    // Edit functionality
    if (isset($_POST['edit_booking_id'])) {
        $editBookingID = filter_var($_POST['edit_booking_id'], FILTER_SANITIZE_NUMBER_INT);
        $editPickup = filter_var($_POST['edit_pickup'], FILTER_SANITIZE_STRING);
        $editDropoff = filter_var($_POST['edit_dropoff'], FILTER_SANITIZE_STRING);
        $editDate = filter_var($_POST['edit_date'], FILTER_SANITIZE_STRING);
        $editCarType = filter_var($_POST['edit_car_type'], FILTER_SANITIZE_STRING);

        $sql = "UPDATE tblcars SET Pickup=?, Dropoff=?, Date=?, CarType=? WHERE CarID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $editPickup, $editDropoff, $editDate, $editCarType, $editBookingID);

        if ($stmt->execute()) {
            $alertMessage = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                Booking updated successfully!
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        } else {
            $alertMessage = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                Error updating booking: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8') . "
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        }
        $stmt->close();
    }

    // Delete functionality
    if (isset($_POST['delete_id'])) {
        $deleteBookingID = filter_var($_POST['delete_id'], FILTER_SANITIZE_NUMBER_INT);

        $sql = "DELETE FROM tblcars WHERE CarID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $deleteBookingID);

        if ($stmt->execute()) {
            $alertMessage = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                Booking deleted successfully!
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        } else {
            $alertMessage = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                Error deleting booking: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8') . "
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        }
        $stmt->close();
    }
}

// Search funtionality
if (isset($_GET["keyword"]) && $_GET["keyword"]) {
    $keyword = "%" . filter_var($_GET["keyword"], FILTER_SANITIZE_SPECIAL_CHARS) . "%";
    $sql_select = "SELECT * FROM tblcars WHERE Pickup LIKE ? OR Dropoff LIKE ? OR CarType LIKE ?";
    $stmt = $conn->prepare($sql_select);
    $stmt->bind_param("sss", $keyword, $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql_select = "SELECT * FROM tblcars";
    $result = $conn->query($sql_select);
};

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Car Rentals</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="./style.css?<?php echo time(); ?>">

    <!-- BOOTSTRAP CSS -->
    <link rel="stylesheet" href="../assets/bootstrap-5.3.0-alpha3-dist/css/bootstrap.css">

    <!-- SCRIPTS -->
    <script src="../assets/bootstrap-5.3.0-alpha3-dist/js/bootstrap.js"></script>
    <script src="../assets/JQuery3.7.1.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .scroll-top-btn {
            background-color: rgb(0, 206, 209);
            display: none;
            color: white;
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 20;
            height: 45px;
            width: 45px;
            border-radius: 100%;
            transition: 0.3s;
            display: flex;
            /* Ensure alignment properties work */
            align-items: center;
            justify-content: center;
            border: none;
            /* Remove default border */
            cursor: pointer;
            /* Change cursor on hover */
        }

        .scroll-top-btn:hover {
            transform: scale(1.2);
        }

        .scroll-top-btn:active {
            transform: scale(0.8);
        }

        /* Optional: Adding box shadow for better visibility */
        .scroll-top-btn {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Font Awesome Icon specific styles */
        .scroll-top-btn i {
            font-size: 17px;
        }
    </style>
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color:#022E3B; padding: 0;">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse gap" id="navbarTogglerDemo01">
                <a class="navbar-brand" style="padding: 0;" href="index.php">
                    <img src="../public/taralogo.png" alt="tara-logo" class="logo">
                </a>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="tours.php">Tours and Tickets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="hotels.php">Hotels</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cars.php">Car Rentals</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ratings.php">Ratings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php" target="_blank">TARA</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
                <?php if (isset($_SESSION['user'])) : ?>
                    <span class="navbar-text" style="margin-right:2rem">
                        Welcome, <?php echo htmlspecialchars($_SESSION['user']['FirstName']); ?>
                    </span>
                <?php endif; ?>
                <form class="d-flex" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="GET">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="keyword">
                    <button class="btn btn-outline-info" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>
    <br><br>

    <!-- CARD -->
    <main class="container mt-5">
        <?php if ($alertMessage) : ?>
            <div class="alert-wrapper">
                <?= $alertMessage ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET["keyword"]) && $_GET["keyword"]) : ?>
            <h3>Search results for "<?php echo $_GET["keyword"] ?>"</h3>
            <br>
        <?php endif; ?>
        <div class="card">
            <div class="card-header">
                <h3>List of Car Rentals</h3>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    Add
                </button>
            </div>
            <div class="card-body table-responsive">
                <?php if (mysqli_num_rows($result) > 0) : ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Car ID</th>
                                <th>User ID</th>
                                <th>Pickup</th>
                                <th>Dropoff</th>
                                <th>Car Type</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($rows = mysqli_fetch_array($result)) { ?>
                                <tr>
                                    <td><?= $rows['CarID']; ?></td>
                                    <td><?= $rows['UserID']; ?></td>
                                    <td><?= $rows['Pickup']; ?></td>
                                    <td><?= $rows['Dropoff']; ?></td>
                                    <td><?= $rows['CarType']; ?></td>
                                    <td><?= $rows['Date']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning" onclick="editRecord(
                                            '<?= $rows['CarID']; ?>',
                                            '<?= $rows['Pickup']; ?>',
                                            '<?= $rows['CarType']; ?>',
                                            '<?= $rows['Date']; ?>'
                                        )">Edit</button>
                                        <button type="button" class="btn btn-danger" onclick="deleteRecordConfirmation(
                                            <?= $rows['CarID']; ?>
                                        )">Delete</button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div class="alert alert-danger">
                        No results found.
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <?php echo mysqli_num_rows($result) . " record/s found"; ?>
            </div>
        </div>
        <!-- Scroll to top button -->
        <button class="scroll-top-btn">
            <i class="fas fa-arrow-up"></i>
        </button>
        <br>
    </main>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <input type="hidden" name="confirmAdd" value="1">
                        <div class="mb-3">
                            <label for="pickup" class="form-label">Pickup</label>
                            <select class="form-select" id="pickup" name="pickup" required>
                                <option value="" disabled selected>Select Pickup Location</option>
                                <option value="Puerto Prinsesa">Puerto Prinsesa - Palawan</option>
                                <option value="Coron">Coron - Palawan</option>
                                <option value="El Nido">El Nido - Palawan</option>
                                <option value="Mayon Volcano">Mayon Volcano - Albay</option>
                                <option value="Cagsawa Ruins">Cagsawa Ruins - Albay</option>
                                <option value="Daraga Church">Daraga Church - Albay</option>
                                <option value="Baluarte">Baluarte - Vigan</option>
                                <option value="Calle Crisologo">Calle Crisologo - Vigan</option>
                                <option value="Plaza Salcedo">Plaza Salcedo - Vigan</option>
                                <option value="Chocolate Hills">Chocolate Hills - Bohol</option>
                                <option value="Philippine Tarsier Sanctuary">Philippine Tarsier Sanctuary - Bohol
                                </option>
                                <option value="Hinagdanan Cave">Hinagdanan Cave - Bohol</option>
                                <option value="Cloud 9">Cloud 9 - Siargao</option>
                                <option value="Nikka's Bridge">Nikka's Bridge - Siargao</option>
                                <option value="La Prinsesa Strawberry Farm">La Prinsesa Strawberry Farm - Siargao
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dropoff" class="form-label">Dropoff</label>
                            <select class="form-select" id="dropoff" name="dropoff" required disabled>
                                <option value="" disabled selected>Select Pickup Location First</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="car-type" class="form-label">Type of Car</label>
                            <select class="form-control" id="car-type" name="carType" required>
                                <option value="" disabled selected>Select Car Type</option>
                                <option value="4-seater">4 Seater</option>
                                <option value="6-seater">6 Seater</option>
                                <option value="8-seater">8 Seater</option>
                            </select>
                        </div>


                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const regions = {
                'Palawan': ['Puerto Prinsesa', 'Coron', 'El Nido'],
                'Albay': ['Mayon Volcano', 'Cagsawa Ruins', 'Daraga Church'],
                'Vigan': ['Baluarte', 'Calle Crisologo', 'Plaza Salcedo'],
                'Bohol': ['Chocolate Hills', 'Philippine Tarsier Sanctuary', 'Hinagdanan Cave'],
                'Siargao': ['Cloud 9', 'Nikka\'s Bridge', 'La Prinsesa Strawberry Farm']
            };

            const pickupSelect = document.getElementById('pickup');
            const dropoffSelect = document.getElementById('dropoff');

            // Function to populate dropoff options based on selected pickup
            function updateDropoffOptions() {
                const selectedPickup = pickupSelect.value;
                dropoffSelect.disabled = false;
                dropoffSelect.innerHTML = '';

                if (selectedPickup) {
                    const pickupRegion = findRegion(selectedPickup);
                    const locations = regions[pickupRegion];

                    locations.forEach(location => {
                        if (location !== selectedPickup) {
                            const option = document.createElement('option');
                            option.value = location;
                            option.textContent = `${location} - ${pickupRegion}`;
                            dropoffSelect.appendChild(option);
                        }
                    });
                }
            }

            // Function to find region based on location
            function findRegion(location) {
                for (const region in regions) {
                    if (regions[region].includes(location)) {
                        return region;
                    }
                }
                return null;
            }

            // Event listener for pickup select
            pickupSelect.addEventListener('change', updateDropoffOptions);

        });
    </script>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="modal-body" id="editForm" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <input type="hidden" id="edit-booking-id" name="edit_booking_id">
                        <div class="mb-3">
                            <label for="edit-pickup" class="form-label">Pickup</label>
                            <select class="form-select" id="edit-pickup" name="edit_pickup" required>
                                <option value="" disabled selected>Select Pickup Location</option>
                                <option value="Puerto Prinsesa">Puerto Prinsesa - Palawan</option>
                                <option value="Coron">Coron - Palawan</option>
                                <option value="El Nido">El Nido - Palawan</option>
                                <option value="Mayon Volcano">Mayon Volcano - Albay</option>
                                <option value="Cagsawa Ruins">Cagsawa Ruins - Albay</option>
                                <option value="Daraga Church">Daraga Church - Albay</option>
                                <option value="Baluarte">Baluarte - Vigan</option>
                                <option value="Calle Crisologo">Calle Crisologo - Vigan</option>
                                <option value="Plaza Salcedo">Plaza Salcedo - Vigan</option>
                                <option value="Chocolate Hills">Chocolate Hills - Bohol</option>
                                <option value="Philippine Tarsier Sanctuary">Philippine Tarsier Sanctuary - Bohol
                                </option>
                                <option value="Hinagdanan Cave">Hinagdanan Cave - Bohol</option>
                                <option value="Cloud 9">Cloud 9 - Siargao</option>
                                <option value="Nikka's Bridge">Nikka's Bridge - Siargao</option>
                                <option value="La Prinsesa Strawberry Farm">La Prinsesa Strawberry Farm - Siargao
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit-dropoff" class="form-label">Dropoff</label>
                            <select class="form-select" id="edit-dropoff" name="edit_dropoff" required disabled>
                                <option value="" disabled selected>Edit Pickup Location First</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit-car-type" class="form-label">Type of Car</label>
                            <select class="form-select" id="edit-car-type" name="edit_car_type" required>
                                <option value="" disabled selected>Select Car Type</option>
                                <option value="4-seater">4 Seater</option>
                                <option value="6-seater">6 Seater</option>
                                <option value="8-seater">8 Seater</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit-date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="edit-date" name="edit_date" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const regions = {
                'Palawan': ['Puerto Prinsesa', 'Coron', 'El Nido'],
                'Albay': ['Mayon Volcano', 'Cagsawa Ruins', 'Daraga Church'],
                'Vigan': ['Baluarte', 'Calle Crisologo', 'Plaza Salcedo'],
                'Bohol': ['Chocolate Hills', 'Philippine Tarsier Sanctuary', 'Hinagdanan Cave'],
                'Siargao': ['Cloud 9', 'Nikka\'s Bridge', 'La Prinsesa Strawberry Farm']
            };

            const editPickupSelect = document.getElementById('edit-pickup');
            const editDropoffSelect = document.getElementById('edit-dropoff');

            // Function to populate dropoff options based on selected pickup
            function updateEditDropoffOptions() {
                const selectedPickup = editPickupSelect.value;
                editDropoffSelect.innerHTML = '';

                if (selectedPickup) {
                    const pickupRegion = findRegion(selectedPickup);
                    const locations = regions[pickupRegion];

                    locations.forEach(location => {
                        if (location !== selectedPickup) {
                            const option = document.createElement('option');
                            option.value = location;
                            option.textContent = `${location} - ${pickupRegion}`;
                            editDropoffSelect.appendChild(option);
                        }
                    });

                    // Enable and select the first option if there are options available
                    editDropoffSelect.disabled = false;
                    if (locations.length > 0) {
                        editDropoffSelect.selectedIndex = 0;
                    }
                } else {
                    // If no pickup is selected, disable dropoff and show default message
                    editDropoffSelect.disabled = true;
                    const defaultOption = document.createElement('option');
                    defaultOption.textContent = 'Edit Pickup Location First';
                    editDropoffSelect.appendChild(defaultOption);
                }
            }

            // Function to find region based on location
            function findRegion(location) {
                for (const region in regions) {
                    if (regions[region].includes(location)) {
                        return region;
                    }
                }
                return null;
            }

            // Event listener for pickup select
            editPickupSelect.addEventListener('change', updateEditDropoffOptions);

            // Initialize dropoff options when editing (if preselected pickup exists)
            const preselectedPickup = editPickupSelect.value;
            if (preselectedPickup) {
                updateEditDropoffOptions();
            }
        });
    </script>



    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this record?
                </div>
                <div class="modal-footer">
                    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <input type="hidden" id="delete-id" name="delete_id">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-3">
        <div class="row justify-content-end">
            <div class="col-auto">
                <form action="generate_report.php" method="POST">
                    <button type="submit" class="btn btn-success" name="generateCars    ">Generate Report</button>
                </form>
            </div>
        </div>
    </div>
    <br><br>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
    <script>
        function editRecord(carID, pickup, carType, date) {
            document.getElementById('edit-booking-id').value = carID;
            document.getElementById('edit-pickup').value = pickup;
            document.getElementById('edit-car-type').value = carType;
            document.getElementById('edit-date').value = date;

            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }

        function deleteRecordConfirmation(carID) {
            document.getElementById('delete-id').value = carID;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // Auto-close alert messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alertWrapper = document.querySelector('.alert-wrapper');
                if (alertWrapper) {
                    alertWrapper.style.display = 'none';
                }
            }, 5000);
        });
    </script>


    <script>
        const scrollToTopButton = document.querySelector('.scroll-top-btn');

        window.addEventListener('scroll', () => {
            if (window.scrollY > -1) {
                scrollToTopButton.style.display = 'flex';
            } else {
                scrollToTopButton.style.display = 'none';
            }
        });

        // Scroll to the top when the user clicks the button
        scrollToTopButton.addEventListener('click', () => {
            document.body.scrollTop = 0; // For Safari
            document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE, and Opera
        });
    </script>
</body>

</html>
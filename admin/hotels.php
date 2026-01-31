<?php
session_start();
include('config.php');
include('lock.php');

$alertMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Add functionality
    if (isset($_POST['confirmAdd'])) {
        // Ensure userID is retrieved from session
        $userID = $_SESSION['user']['UserID'];

        // Retrieve and sanitize form data
        $destination = filter_var($_POST['destination'], FILTER_SANITIZE_STRING);
        $checkin = filter_var($_POST['checkin'], FILTER_SANITIZE_STRING);
        $checkout = filter_var($_POST['checkout'], FILTER_SANITIZE_STRING);
        $roomType = filter_var($_POST['roomType'], FILTER_SANITIZE_STRING);
        $totalPrice = filter_var($_POST['actualHotelTotalPrice'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $paymentMethod = filter_var($_POST['addpaymentMethod'], FILTER_SANITIZE_STRING); // Sanitize payment method

        // Prepare SQL statement for inserting a new booking
        $sql = "INSERT INTO tblhotels (UserID, Destination, `Check-in`, `Check-out`, RoomType, TotalPrice, Payment) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bind_param("issssds", $userID, $destination, $checkin, $checkout, $roomType, $totalPrice, $paymentMethod);

        // Execute the statement and handle success or failure
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

        // Close the statement
        $stmt->close();
    }

    // Update functionality
    if (isset($_POST['edit_booking_id'])) {
        $editBookingID = filter_var($_POST['edit_booking_id'], FILTER_SANITIZE_NUMBER_INT);
        $editDestination = filter_var($_POST['edit_destination'], FILTER_SANITIZE_STRING);
        $editCheckin = filter_var($_POST['edit_checkin'], FILTER_SANITIZE_STRING);
        $editCheckout = filter_var($_POST['edit_checkout'], FILTER_SANITIZE_STRING);
        $editRoomType = filter_var($_POST['edit_roomType'], FILTER_SANITIZE_STRING);
        $editTotalPrice = filter_var($_POST['edit_hotelTotalPrice'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $editpaymentMethod = filter_var($_POST['editpaymentMethod'], FILTER_SANITIZE_STRING);

        // Prepare SQL statement for updating a booking
        $sql = "UPDATE tblhotels SET Destination=?, `Check-in`=?, `Check-out`=?, RoomType=?, TotalPrice=? ,Payment=? WHERE HotelID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssdsi", $editDestination, $editCheckin, $editCheckout, $editRoomType, $editTotalPrice, $editpaymentMethod, $editBookingID);

        // Execute the statement and handle success or failure
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

        // Close the statement
        $stmt->close();
    }

    // Delete functionality
    if (isset($_POST['delete_id'])) {
        $deleteBookingID = filter_var($_POST['delete_id'], FILTER_SANITIZE_NUMBER_INT);

        // Prepare SQL statement for deleting a booking
        $sql = "DELETE FROM tblhotels WHERE HotelID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $deleteBookingID);

        // Execute the statement and handle success or failure
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

        // Close the statement
        $stmt->close();
    }
}

// Handling search functionality
if (isset($_GET["keyword"]) && $_GET["keyword"]) {
    $keyword = "%" . filter_var($_GET["keyword"], FILTER_SANITIZE_SPECIAL_CHARS) . "%";
    $sql_select = "SELECT * FROM tblhotels WHERE `Check-in` LIKE ? OR `Check-out` LIKE ? OR RoomType LIKE ? OR Destination LIKE ?";
    $stmt = $conn->prepare($sql_select);
    $stmt->bind_param("ssss", $keyword, $keyword, $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql_select = "SELECT * FROM tblhotels";
    $result = $conn->query($sql_select);
}


// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Hotel Bookings</title>
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
                        <a class="nav-link " href="tours.php">Tours and Tickets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="hotels.php">Hotels</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cars.php">Car Rentals</a>
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
                <h3>List of Hotel Bookings</h3>
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
                                <th>Hotel ID</th>
                                <th>User ID</th>
                                <th>Destination</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Room Type</th>
                                <th>Payment</th>
                                <th>Total Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($rows = mysqli_fetch_array($result)) { ?>
                                <tr>
                                    <td><?= $rows['HotelID']; ?></td>
                                    <td><?= $rows['UserID']; ?></td>
                                    <td><?= $rows['Destination']; ?></td>
                                    <td><?= $rows['Check-in']; ?></td>
                                    <td><?= $rows['Check-out']; ?></td>
                                    <td><?= $rows['RoomType']; ?></td>
                                    <td><?= $rows['Payment']; ?></td>
                                    <td><?= $rows['TotalPrice']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning" onclick="editRecord(
                                                '<?= $rows['HotelID']; ?>',
                                                '<?= $rows['Destination']; ?>',
                                                '<?= $rows['Check-in']; ?>',
                                                '<?= $rows['Check-out']; ?>',
                                                '<?= $rows['RoomType']; ?>',
                                                 '<?= $rows['Payment']; ?>'
    
                                            )">Edit</button>
                                        <button type="button" class="btn btn-danger" onclick="deleteRecordConfirmation(
                                                <?= $rows['HotelID']; ?>
                                            )">Delete</button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div class="alert alert-danger  ">
                        No results found.
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <?php echo mysqli_num_rows($result) . " record/s found"; ?>
            </div>
        </div>
        <br>
        <!-- Scroll to top button -->
        <button class="scroll-top-btn">
            <i class="fas fa-arrow-up"></i>
        </button>
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
                            <label for="destination" class="form-label">Destination</label>
                            <select class="form-select" id="destination" name="destination" required>
                                <option value="" disabled selected>Select a destination</option>
                                <option value="Luxury Beachfront Hotel">Luxury Beachfront Hotel</option>
                                <option value="City Center Hotel with Rooftop Pool">City Center Hotel with Rooftop Pool
                                </option>
                                <option value="Beach Resort with Private Villas">Beach Resort with Private Villas
                                </option>
                                <option value="Historical Hotel in the Heart of Manila">Historical Hotel in the Heart of
                                    Manila</option>
                                <option value="Boutique Hotel with Garden View">Boutique Hotel with Garden View</option>
                                <option value="Mountain Resort with Scenic Views">Mountain Resort with Scenic Views
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="checkin" class="form-label">Check-in</label>
                            <input type="date" class="form-control" id="checkin" name="checkin" required>
                        </div>

                        <div class="mb-3">
                            <label for="checkout" class="form-label">Check-out</label>
                            <input type="date" class="form-control" id="checkout" name="checkout" required>
                        </div>

                        <div class="mb-3">
                            <label for="roomType" class="form-label">Room Type</label>
                            <select name="roomType" class="form-select" id="roomType" required>
                                <option value="" disabled selected>Select Room Type</option>
                                <option value="Single">Single</option>
                                <option value="Double">Double (Additional 100 per Night)</option>
                                <option value="Suite">Suite (Additional 300 per Night)</option>
                                <option value="Family">Family (Additional 500 per Night)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="addpaymentMethod" class="form-label check-mo">Payment</label>
                            <select class="form-select" id="addpaymentMethod" name="addpaymentMethod" required>
                                <option value="" disabled selected>Select Payment Method</option>
                                <option value="GCash">GCash</option>
                                <option value="PayMaya">PayMaya</option>
                                <option value="Banko de Oro">Banko de Oro</option>
                                <option value="LandBank">LandBank</option>
                            </select>
                        </div>

                        <!-- Hidden fields for hotel booking calculation -->
                        <input type="hidden" id="hotelName" name="hotelName">
                        <input type="hidden" id="actualHotelTotalPrice" name="actualHotelTotalPrice">

                        <!-- Hotel booking error message -->
                        <div class="alert alert-danger d-none" role="alert" id="hotelError">
                        </div>

                        <div id="add-alertContainer" class="alert alert-warning d-none" role="alert">
                            Check-out date cannot be the same as check-in date.
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <input type="hidden" id="edit-booking-id" name="edit_booking_id">

                        <div class="mb-3">
                            <label for="edit-destination" class="form-label">Destination</label>
                            <select class="form-select" id="edit-destination" name="edit_destination" required>
                                <option value="" disabled>Select a destination</option>
                                <option value="Luxury Beachfront Hotel">Luxury Beachfront Hotel</option>
                                <option value="City Center Hotel with Rooftop Pool">City Center Hotel with Rooftop Pool
                                </option>
                                <option value="Beach Resort with Private Villas">Beach Resort with Private Villas
                                </option>
                                <option value="Historical Hotel in the Heart of Manila">Historical Hotel in the Heart of
                                    Manila</option>
                                <option value="Boutique Hotel with Garden View">Boutique Hotel with Garden View</option>
                                <option value="Mountain Resort with Scenic Views">Mountain Resort with Scenic Views
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit-checkin" class="form-label">Check-in</label>
                            <input type="date" class="form-control" id="edit-checkin" name="edit_checkin" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit-checkout" class="form-label">Check-out</label>
                            <input type="date" class="form-control" id="edit-checkout" name="edit_checkout" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit-roomType" class="form-label">Room Type</label>
                            <select name="edit_roomType" class="form-select" id="edit-roomType" required>
                                <option value="" disabled selected>Select Room Type</option>
                                <option value="Single">Single</option>
                                <option value="Double">Double (Additional 100 per Night)</option>
                                <option value="Suite">Suite (Additional 300 per Night)</option>
                                <option value="Family">Family (Additional 500 per Night)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="editpaymentMethod" class="form-label check-mo">Payment</label>
                            <select class="form-select" id="editpaymentMethod" name="editpaymentMethod" required>
                                <option value="" disabled selected>Select Payment Method</option>
                                <option value="GCash">GCash</option>
                                <option value="PayMaya">PayMaya</option>
                                <option value="Banko de Oro">Banko de Oro</option>
                                <option value="LandBank">LandBank</option>
                            </select>
                        </div>
                        <!-- Hidden field for Total Price -->
                        <input type="hidden" name="edit_hotelTotalPrice" id="edit-hotelTotalPrice">

                        <!-- Hotel booking error message -->
                        <div class="alert alert-danger d-none" role="alert" id="edit-hotelError">
                        </div>

                        <div id="edit-alertContainer" class="alert alert-warning d-none" role="alert">
                            Check-out date cannot be the same as check-in date.
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

    <div class="container mt-3">
        <div class="row justify-content-end">
            <div class="col-auto">
                <form action="generate_report.php" method="POST">
                    <button type="submit" class="btn btn-success" name="generateHotel">Generate Report</button>
                </form>
            </div>
        </div>
    </div>
    <br><br>

    <!-- // JavaScript for Add Modal -->
    <script>
        // Event listeners for Add Modal
        document.getElementById('checkin').addEventListener('change', function() {
            updateTotalPrice('checkin', 'checkout', 'destination', 'hotelTotalPrice', 'actualHotelTotalPrice', 'hotelError', 'roomType');
        });

        document.getElementById('checkout').addEventListener('change', function() {
            updateTotalPrice('checkin', 'checkout', 'destination', 'hotelTotalPrice', 'actualHotelTotalPrice', 'hotelError', 'roomType');

            // Additional check for same check-in and check-out dates
            const checkInDate = document.getElementById('checkin').value;
            const checkOutDate = document.getElementById('checkout').value;

            const alertContainer = document.getElementById('add-alertContainer');
            if (checkInDate === checkOutDate) {
                if (alertContainer) {
                    alertContainer.classList.remove('d-none');
                }
                // Clear check-out date
                document.getElementById('checkout').value = '';
            } else {
                if (alertContainer) {
                    alertContainer.classList.add('d-none');
                }
            }
        });

        document.getElementById('roomType').addEventListener('change', function() {
            updateTotalPrice('checkin', 'checkout', 'destination', 'hotelTotalPrice', 'actualHotelTotalPrice', 'hotelError', 'roomType');
        });

        // Function to calculate and update total price for hotel booking in modal
        function updateTotalPrice(checkInId, checkOutId, hotelNameId, displayTotalPriceId, hotelTotalPriceId, hotelErrorId, roomTypeId) {
            const checkInDate = document.getElementById(checkInId).value;
            const checkOutDate = document.getElementById(checkOutId).value;
            const hotelName = document.getElementById(hotelNameId).value;
            const displayTotalPrice = document.getElementById(displayTotalPriceId);
            const hotelTotalPrice = document.getElementById(hotelTotalPriceId);
            const hotelError = document.getElementById(hotelErrorId);
            const roomType = document.getElementById(roomTypeId).value;

            if (checkInDate && checkOutDate && hotelName) {
                if (new Date(checkInDate) <= new Date(checkOutDate)) {
                    let pricePerDay = getHotelPricePerDay(hotelName);
                    let totalPrice = pricePerDay * daysBetween(checkInDate, checkOutDate);

                    // Adjust the price based on room type
                    switch (roomType) {
                        case 'Double':
                            totalPrice += (100 * daysBetween(checkInDate, checkOutDate));
                            break;
                        case 'Suite':
                            totalPrice += (300 * daysBetween(checkInDate, checkOutDate));
                            break;
                        case 'Family':
                            totalPrice += (500 * daysBetween(checkInDate, checkOutDate));
                            break;
                        default:
                            break; // No extra charge for 'Single' or if no room type is selected
                    }

                    if (displayTotalPrice) {
                        displayTotalPrice.innerText = formatPriceWithCommas(totalPrice); // Update displayed total price
                    }

                    if (hotelTotalPrice) {
                        hotelTotalPrice.value = totalPrice; // Set the input value
                    }

                    if (hotelError) {
                        hotelError.classList.add('d-none'); // Hide error message
                    }
                } else {
                    if (displayTotalPrice) {
                        displayTotalPrice.innerText = formatPriceWithCommas(0);
                    }

                    if (hotelTotalPrice) {
                        hotelTotalPrice.value = 0;
                    }

                    if (hotelError) {
                        hotelError.classList.remove('d-none'); // Show error message
                        hotelError.innerText = 'Check-in date cannot be later than check-out date.';
                    }

                    // Clear invalid check-out date
                    document.getElementById(checkOutId).value = '';
                }
            }
        }

        // Function to format price with commas
        function formatPriceWithCommas(price) {
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(price);
        }

        // Function to retrieve price per day based on hotel name
        function getHotelPricePerDay(hotelName) {
            switch (hotelName) {
                case 'Luxury Beachfront Hotel':
                    return 10000;
                case 'City Center Hotel with Rooftop Pool':
                    return 7500;
                case 'Boutique Hotel with Garden View':
                    return 5000;
                case 'Beach Resort with Private Villas':
                    return 10000;
                case 'Historical Hotel in the Heart of Manila':
                    return 8500;
                case 'Mountain Resort with Scenic Views':
                    return 6000;
                default:
                    return 0; // Default price if hotel name not recognized
            }
        }

        // Function to calculate the difference in days between two dates
        function daysBetween(date1, date2) {
            const oneDay = 24 * 60 * 60 * 1000; // hours * minutes * seconds * milliseconds
            const firstDate = new Date(date1);
            const secondDate = new Date(date2);
            return Math.round(Math.abs((firstDate - secondDate) / oneDay));
        }
    </script>

    <!-- JavaScript for Edit Modal -->
    <script>
        // Variables to store initial values
        let initialCheckInDate = document.getElementById('edit-checkin').value;
        let initialCheckOutDate = document.getElementById('edit-checkout').value;
        let initialHotelName = document.getElementById('edit-destination').value;
        let initialRoomType = document.getElementById('edit-roomType').value;
        let initialTotalPrice = null;

        // Event listeners for Edit Modal
        document.getElementById('edit-checkin').addEventListener('change', function() {
            updateTotalPrice('edit-checkin', 'edit-checkout', 'edit-destination', 'edit-displayTotalPrice', 'edit-hotelTotalPrice', 'edit-hotelError', 'edit-roomType');
        });

        document.getElementById('edit-checkout').addEventListener('change', function() {
            updateTotalPrice('edit-checkin', 'edit-checkout', 'edit-destination', 'edit-displayTotalPrice', 'edit-hotelTotalPrice', 'edit-hotelError', 'edit-roomType');

            // Additional check for same check-in and check-out dates
            const checkInDate = document.getElementById('edit-checkin').value;
            const checkOutDate = document.getElementById('edit-checkout').value;

            const alertContainer = document.getElementById('edit-alertContainer');
            if (checkInDate === checkOutDate) {
                if (alertContainer) {
                    alertContainer.classList.remove('d-none');
                }
                // Clear check-out date
                document.getElementById('edit-checkout').value = initialCheckOutDate; // Restore initial check-out date
            } else {
                if (alertContainer) {
                    alertContainer.classList.add('d-none');
                }
            }
        });

        document.getElementById('edit-destination').addEventListener('change', function() {
            updateTotalPrice('edit-checkin', 'edit-checkout', 'edit-destination', 'edit-displayTotalPrice', 'edit-hotelTotalPrice', 'edit-hotelError', 'edit-roomType');
        });

        document.getElementById('edit-roomType').addEventListener('change', function() {
            updateTotalPrice('edit-checkin', 'edit-checkout', 'edit-destination', 'edit-displayTotalPrice', 'edit-hotelTotalPrice', 'edit-hotelError', 'edit-roomType');
        });

        // Initial price calculation on page load
        updateTotalPrice('edit-checkin', 'edit-checkout', 'edit-destination', 'edit-displayTotalPrice', 'edit-hotelTotalPrice', 'edit-hotelError', 'edit-roomType');

        // Function to calculate and update total price for hotel booking in modal
        function updateTotalPrice(checkInId, checkOutId, hotelNameId, displayTotalPriceId, hotelTotalPriceId, hotelErrorId, roomTypeId) {
            const checkInDate = document.getElementById(checkInId).value;
            const checkOutDate = document.getElementById(checkOutId).value;
            const hotelName = document.getElementById(hotelNameId).value;
            const displayTotalPrice = document.getElementById(displayTotalPriceId);
            const hotelTotalPrice = document.getElementById(hotelTotalPriceId);
            const hotelError = document.getElementById(hotelErrorId);
            const roomType = document.getElementById(roomTypeId).value;

            // Check if any relevant input has changed
            if (checkInDate !== initialCheckInDate || checkOutDate !== initialCheckOutDate || hotelName !== initialHotelName || roomType !== initialRoomType) {
                if (checkInDate && checkOutDate && hotelName) {
                    const checkIn = new Date(checkInDate);
                    const checkOut = new Date(checkOutDate);

                    if (checkIn <= checkOut) {
                        let pricePerDay = getHotelPricePerDay(hotelName);
                        let totalPrice = pricePerDay * daysBetween(checkInDate, checkOutDate);

                        switch (roomType) {
                            case 'Double':
                                totalPrice += (100 * daysBetween(checkInDate, checkOutDate));
                                break;
                            case 'Suite':
                                totalPrice += (300 * daysBetween(checkInDate, checkOutDate));
                                break;
                            case 'Family':
                                totalPrice += (500 * daysBetween(checkInDate, checkOutDate));
                                break;
                            default:
                                break; // No extra charge for 'Single' or if no room type is selected
                        }

                        if (displayTotalPrice) {
                            displayTotalPrice.innerText = formatPriceWithCommas(totalPrice); // Update displayed total price
                        }

                        if (hotelTotalPrice) {
                            hotelTotalPrice.value = totalPrice; // Set the input value
                        }

                        if (hotelError) {
                            hotelError.classList.add('d-none'); // Hide error message
                        }

                        initialTotalPrice = totalPrice; // Update initial total price
                    } else {
                        if (displayTotalPrice) {
                            displayTotalPrice.innerText = formatPriceWithCommas(0);
                        }

                        if (hotelTotalPrice) {
                            hotelTotalPrice.value = 0;
                        }

                        if (hotelError) {
                            hotelError.classList.remove('d-none'); // Show error message
                            hotelError.innerText = 'Check-in date cannot be later than check-out date.';
                        }

                        // Restore initial check-out date
                        document.getElementById(checkOutId).value = initialCheckOutDate;

                        initialTotalPrice = 0; // Update initial total price to 0
                    }
                }
            } else {
                // Restore initial total price if no changes were made
                if (initialTotalPrice !== null && displayTotalPrice) {
                    displayTotalPrice.innerText = formatPriceWithCommas(initialTotalPrice);
                }
            }
        }

        // Function to format price with commas
        function formatPriceWithCommas(price) {
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(price);
        }

        // Function to retrieve price per day based on hotel name
        function getHotelPricePerDay(hotelName) {
            switch (hotelName) {
                case 'Luxury Beachfront Hotel':
                    return 10000;
                case 'City Center Hotel with Rooftop Pool':
                    return 7500;
                case 'Boutique Hotel with Garden View':
                    return 5000;
                case 'Beach Resort with Private Villas':
                    return 10000;
                case 'Historical Hotel in the Heart of Manila':
                    return 8500;
                case 'Mountain Resort with Scenic Views':
                    return 6000;
                default:
                    return 0; // Default price if hotel name not recognized
            }
        }

        // Function to calculate the difference in days between two dates
        function daysBetween(date1, date2) {
            const oneDay = 24 * 60 * 60 * 1000; // hours * minutes * seconds * milliseconds
            const firstDate = new Date(date1);
            const secondDate = new Date(date2);
            return Math.round(Math.abs((secondDate - firstDate) / oneDay));
        }
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

    <script>
        // Initialize initialTotalPrice when the edit modal is opened
        function editRecord(hotelID, destination, checkin, checkout, roomType, payment) {
            document.getElementById('edit-booking-id').value = hotelID;
            document.getElementById('edit-destination').value = destination;
            document.getElementById('edit-checkin').value = checkin;
            document.getElementById('edit-checkout').value = checkout;
            document.getElementById('edit-roomType').value = roomType;
            document.getElementById('editpaymentMethod').value = payment;

            // Calculate and set initial total price
            updateTotalPrice('edit-checkin', 'edit-checkout', 'edit-destination', 'edit-displayTotalPrice', 'edit-hotelTotalPrice', 'edit-hotelError', 'edit-roomType');

            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }


        function deleteRecordConfirmation(hotelID) {
            document.getElementById('delete-id').value = hotelID;
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
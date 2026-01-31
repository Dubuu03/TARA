<?php
session_start();
include('config.php');
include("./lock.php");

$alertMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add functionality
    if (isset($_POST['confirmAdd'])) {
        $userID = $_SESSION['user']['UserID'];
        $place = filter_var($_POST['place'], FILTER_SANITIZE_STRING);
        $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
        $adult = filter_var($_POST['adult'], FILTER_SANITIZE_NUMBER_INT);
        $children = filter_var($_POST['children'], FILTER_SANITIZE_NUMBER_INT);
        $payment = filter_var($_POST['payment'], FILTER_SANITIZE_STRING);

        // Validate if both adult and children are zero
        if ($adult == 0 && $children == 0) {
            $alertMessage = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                Both adults and children cannot be zero. Please select at least one adult or child.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        } else {
            // Calculate total price based on selected place and number of adults/children
            $adultPrice = 0;
            switch ($place) {
                case 'Boracay':
                    $adultPrice = 1079;
                    break;
                case 'Cebu':
                    $adultPrice = 2938;
                    break;
                case 'Siargao':
                    $adultPrice = 2600;
                    break;
                case 'Bohol':
                    $adultPrice = 7332;
                    break;
                case 'Fort Santiago':
                    $adultPrice = 3156;
                    break;
                case 'Albay':
                    $adultPrice = 3366;
                    break;
                default:
                    $adultPrice = 0; // Default price if place not recognized
            }

            // Calculate child price as 90% of adult price
            $childPrice = $adultPrice * 0.9;

            // Calculate total price
            $totalPrice = ($adultPrice * $adult) + ($childPrice * $children);

            // Prepare and execute the SQL insertion
            $sql = "INSERT INTO tblbookings (UserID, Place, Date, Adult, Children, Payment, TotalPrice) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssisd", $userID, $place, $date, $adult, $children, $payment, $totalPrice);
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
    }

    // Update functionality
    if (isset($_POST['edit_booking_id'])) {
        $editBookingID = filter_var($_POST['edit_booking_id'], FILTER_SANITIZE_NUMBER_INT);
        $editPlace = filter_var($_POST['edit_place'], FILTER_SANITIZE_STRING);
        $editDate = filter_var($_POST['edit_date'], FILTER_SANITIZE_STRING);
        $editAdult = filter_var($_POST['edit_adult'], FILTER_SANITIZE_NUMBER_INT);
        $editChildren = filter_var($_POST['edit_children'], FILTER_SANITIZE_NUMBER_INT);
        $editPayment = filter_var($_POST['edit_payment'], FILTER_SANITIZE_STRING);

        // Validate if both adult and children are zero
        if ($editAdult == 0 && $editChildren == 0) {
            $alertMessage = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                Both adults and children cannot be zero. Please select at least one adult or child.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        } else {
            // Calculate total price based on selected place and number of adults/children
            $editAdultPrice = 0;
            switch ($editPlace) {
                case 'Boracay':
                    $editAdultPrice = 1079;
                    break;
                case 'Cebu':
                    $editAdultPrice = 2938;
                    break;
                case 'Siargao':
                    $editAdultPrice = 2600;
                    break;
                case 'Bohol':
                    $editAdultPrice = 7332;
                    break;
                case 'Fort Santiago':
                    $editAdultPrice = 3156;
                    break;
                case 'Albay':
                    $editAdultPrice = 3366;
                    break;
                default:
                    $editAdultPrice = 0; // Default price if place not recognized
            }

            // Calculate child price as 90% of adult price
            $editChildPrice = $editAdultPrice * 0.9;

            // Calculate total price
            $editTotalPrice = ($editAdultPrice * $editAdult) + ($editChildPrice * $editChildren);

            // Prepare and execute the SQL update
            $sql = "UPDATE tblbookings SET Place=?, Date=?, Adult=?, Children=?, Payment=?, TotalPrice=? WHERE BookingID=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiisdi", $editPlace, $editDate, $editAdult, $editChildren, $editPayment, $editTotalPrice, $editBookingID);

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
    }

    // Delete functionality
    if (isset($_POST['delete_id'])) {
        $deleteBookingID = filter_var($_POST['delete_id'], FILTER_SANITIZE_NUMBER_INT);

        $sql = "DELETE FROM tblbookings WHERE BookingID=?";
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


if (isset($_GET["keyword"]) && $_GET["keyword"]) {
    $keyword = "%" . filter_var($_GET["keyword"], FILTER_SANITIZE_SPECIAL_CHARS) . "%";
    $sql_select = "SELECT * FROM tblbookings WHERE Place LIKE ? OR Date LIKE ? OR Payment LIKE ?";
    $stmt = $conn->prepare($sql_select);
    $stmt->bind_param("sss", $keyword, $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql_select = "SELECT * FROM tblbookings";
    $result = $conn->query($sql_select);
};



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tours and Tickets</title>
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
                        <a class="nav-link active" href="tours.php">Tours and Tickets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="hotels.php">Hotels</a>
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

    <!-- Main Content -->
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
                <h3>List of Tours and Tickets</h3>
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
                                <th>Booking ID</th>
                                <th>User ID</th>
                                <th>Place</th>
                                <th>Date</th>
                                <th>Adult</th>
                                <th>Children</th>
                                <th>Payment</th>
                                <th>Total Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($rows = mysqli_fetch_array($result)) { ?>
                                <tr>
                                    <td><?= $rows['BookingID']; ?></td>
                                    <td><?= $rows['UserID']; ?></td>
                                    <td><?= $rows['Place']; ?></td>
                                    <td><?= $rows['Date']; ?></td>
                                    <td><?= $rows['Adult']; ?></td>
                                    <td><?= $rows['Children']; ?></td>
                                    <td><?= $rows['Payment']; ?></td>
                                    <td><?= $rows['TotalPrice']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning" onclick="editRecord(
                                            '<?= $rows['BookingID']; ?>',
                                            '<?= $rows['Place']; ?>',
                                            '<?= $rows['Date']; ?>',
                                            '<?= $rows['Adult']; ?>',
                                            '<?= $rows['Children']; ?>',
                                            '<?= $rows['Payment']; ?>'
                                        )">Edit</button>
                                        <button type="button" class="btn btn-danger" onclick="deleteRecordConfirmation(
                                            <?= $rows['BookingID']; ?>
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
                            <label for="place" class="form-label">Place</label>
                            <select class="form-select" id="place" name="place" required>
                                <option value="" disabled selected>Select a Place</option>
                                <option value="Boracay">Boracay</option>
                                <option value="Cebu">Cebu</option>
                                <option value="Siargao">Siargao</option>
                                <option value="Bohol">Bohol</option>
                                <option value="Fort Santiago">Fort Santiago</option>
                                <option value="Albay">Albay</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="adult" class="form-label">Adult</label>
                            <input type="number" class="form-control" id="adult" name="adult" required min="0">
                        </div>
                        <div class="mb-3">
                            <label for="children" class="form-label">Children</label>
                            <input type="number" class="form-control" id="children" name="children" required min="0">
                        </div>

                        <div class="mb-3">
                            <label for="payment" class="form-label">Payment</label>
                            <select name="payment" class="form-select custom-select" required>
                                <option value="" disabled selected>Select Payment Method</option>
                                <option value="Gcash">Gcash</option>
                                <option value="Paymaya">Paymaya</option>
                                <option value="Banko de Oro">Banko de Oro</option>
                                <option value="Landbank">Landbank</option>
                            </select>
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
                    <form class="modal-body" id="editForm" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <input type="hidden" id="edit-booking-id" name="edit_booking_id">
                        <div class="mb-3">
                            <label for="edit-place" class="form-label">Place</label>
                            <select class="form-select" id="edit-place" name="edit_place" required>
                                <option value="Boracay">Boracay</option>
                                <option value="Cebu">Cebu</option>
                                <option value="Siargao">Siargao</option>
                                <option value="Bohol">Bohol</option>
                                <option value="Fort Santiago">Fort Santiago</option>
                                <option value="Albay">Albay</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit-date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="edit-date" name="edit_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-adult" class="form-label">Adult</label>
                            <input type="number" class="form-control" id="edit-adult" name="edit_adult" required min="0" placeholder="Enter number of adults">
                        </div>
                        <div class="mb-3">
                            <label for="edit-children" class="form-label">Children</label>
                            <input type="number" class="form-control" id="edit-children" name="edit_children" required min="0" placeholder="Enter number of children">
                        </div>

                        <div class="mb-3">
                            <label for="edit-payment" class="form-label">Payment</label>
                            <select name="edit_payment" class="form-select custom-select" id="edit-payment" required>
                                <option value="Gcash">Gcash</option>
                                <option value="Paymaya">Paymaya</option>
                                <option value="Banko de Oro">Banko de Oro</option>
                                <option value="Landbank">Landbank</option>
                            </select>
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
                    <button type="submit" class="btn btn-success" name="generateTours">Generate Report</button>
                </form>
            </div>
        </div>
    </div>
    <br><br>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>

    <!-- JavaScript for Modals -->
    <script>
        function editRecord(bookingID, place, date, adult, children, payment) {
            document.getElementById('edit-booking-id').value = bookingID;
            document.getElementById('edit-place').value = place;
            document.getElementById('edit-date').value = date;
            document.getElementById('edit-adult').value = adult;
            document.getElementById('edit-children').value = children;
            document.getElementById('edit-payment').value = payment;
            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }

        function deleteRecordConfirmation(bookingID) {
            document.getElementById('delete-id').value = bookingID;
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
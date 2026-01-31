<?php
session_start();
include('config.php');
include("./lock.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <title>TARA Admin</title>
</head>

<body style="padding-top: 50px;">
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color:#022E3B; padding: 0;">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse gap" id="navbarTogglerDemo01">

                <a class="navbar-brand" style="padding: 0;" href="#">
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
                <form class="d-flex" method="GET" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="keyword">
                    <button class="btn btn-outline-info" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>
    <br><br>


    <!-- CARDS -->
    <main class="container">
        <?php if (isset($_GET["keyword"]) && $_GET["keyword"]) : ?>
            <h3>Search results for "<?php echo $_GET["keyword"] ?>"</h3>
            <br>
        <?php endif; ?>

        <!-- TOURS AND TICKET CARD -->
        <div class="card">
            <div class="card-header">
                <h3>List of Tours and Tickets</h3>
            </div>
            <div class="card-body table-responsive">
                <?php
                //show table records
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
                }

                // Display table only if there are records
                if (mysqli_num_rows($result) > 0) {
                ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>User ID</th>
                                <th>Place</th>
                                <th>Date</th>
                                <th>Number of Children</th>
                                <th>Number of Adult</th>
                                <th>Payment Method</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($rows = mysqli_fetch_array($result)) {
                            ?>
                                <tr>
                                    <td><?= $rows["BookingID"]; ?></td>
                                    <td><?= $rows["UserID"]; ?></td>
                                    <td><?= $rows["Place"]; ?></td>
                                    <td><?= $rows["Date"]; ?></td>
                                    <td><?= $rows["Children"]; ?></td>
                                    <td><?= $rows["Adult"]; ?></td>
                                    <td><?= $rows["Payment"]; ?></td>
                                    <td><?= $rows["TotalPrice"]; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php
                } else {
                    // Display a message if no records found
                    echo '<div class="alert alert-danger" role="alert">No records found.</div>';
                }
                ?>
            </div>
            <div class="card-footer">
                <?php echo mysqli_num_rows($result) . " record/s found"; ?>
            </div>
        </div><br>

        <!-- HOTEL CARD -->
        <div class="card">
            <div class="card-header">
                <h3>List of Hotel Bookings</h3>
            </div>
            <div class="card-body table-responsive">
                <?php
                //show table records
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

                // Display table only if there are records
                if (mysqli_num_rows($result) > 0) {
                ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Hotel ID</th>
                                <th>User ID</th>
                                <th>Destination</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Payment</th>
                                <th>Room Type</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($rows = mysqli_fetch_array($result)) {
                            ?>
                                <tr>
                                    <td><?= $rows["HotelID"]; ?></td>
                                    <td><?= $rows["UserID"]; ?></td>
                                    <td><?= $rows["Destination"]; ?></td>
                                    <td><?= $rows["Check-in"]; ?></td>
                                    <td><?= $rows["Check-out"]; ?></td>
                                    <td><?= $rows["Payment"]; ?></td>
                                    <td><?= $rows["RoomType"]; ?></td>
                                    <td><?= $rows["TotalPrice"]; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php
                } else {
                    // Display a message if no records found
                    echo '<div class="alert alert-danger" role="alert">No records found.</div>';
                }
                ?>
            </div>
            <div class="card-footer">
                <?php echo mysqli_num_rows($result) . " record/s found"; ?>
            </div>
        </div> <br>

        <!-- CAR RENTAL CARD -->
        <div class="card">
            <div class="card-header">
                <h3>List of Car Rental</h3>
            </div>
            <div class="card-body table-responsive">
                <?php
                //show table records
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
                }


                // Display table only if there are records
                if (mysqli_num_rows($result) > 0) {
                ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Car ID</th>
                                <th>User ID</th>
                                <th>Pick-up</th>
                                <th>Drop-off</th>
                                <th>Car Type</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($rows = mysqli_fetch_array($result)) {
                            ?>
                                <tr>
                                    <td><?= $rows["CarID"]; ?></td>
                                    <td><?= $rows["UserID"]; ?></td>
                                    <td><?= $rows["Pickup"]; ?></td>
                                    <td><?= $rows["Dropoff"]; ?></td>
                                    <td><?= $rows["CarType"]; ?></td>
                                    <td><?= $rows["Date"]; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php
                } else {
                    // Display a message if no records found
                    echo '<div class="alert alert-danger" role="alert">No records found.</div>';
                }
                ?>
            </div>
            <div class="card-footer">
                <?php echo mysqli_num_rows($result) . " record/s found"; ?>
            </div>
        </div><br>

        <!-- USER CARD -->
        <div class="card">
            <div class="card-header">
                <h3>List of Users</h3>
            </div>
            <div class="card-body table-responsive">
                <?php

                if (isset($_GET["keyword"]) && $_GET["keyword"]) {
                    $keyword = "%" . filter_var($_GET["keyword"], FILTER_SANITIZE_SPECIAL_CHARS) . "%";
                    $sql_select = "SELECT * FROM tbluser WHERE (LastName LIKE ? OR FirstName LIKE ? OR Email LIKE ? OR AccessLevel LIKE ?) AND UserID != ?";
                    $stmt = $conn->prepare($sql_select);
                    $stmt->bind_param("ssssi", $keyword, $keyword, $keyword, $keyword, $_SESSION["user"]["UserID"]);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } else {
                    $sql_select = "SELECT * FROM tbluser WHERE UserID != ?";
                    $stmt = $conn->prepare($sql_select);
                    $stmt->bind_param("i", $_SESSION["user"]["UserID"]);
                    $stmt->execute();
                    $result = $stmt->get_result();
                }


                // Display table only if there are records
                if (mysqli_num_rows($result) > 0) {
                ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Email</th>
                                <th>User Image</th>
                                <th>Password</th>
                                <th>Access Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($rows = mysqli_fetch_array($result)) {
                            ?>
                                <tr>
                                    <td><?= $rows['UserID']; ?></td>
                                    <td><?= $rows['LastName']; ?></td>
                                    <td><?= $rows['FirstName']; ?></td>
                                    <td><?= $rows['Email']; ?></td>
                                    <td class="image-cell"><img class="user-image" src="../<?= $rows['UserImage']; ?>" alt="<?= $rows['Email']; ?>"></td>
                                    <!-- <td><span class="password-cell"><?= $rows['Password']; ?></span></td> -->
                                    <td><span class="password-cell"><?= str_repeat('*', strlen($rows['Password'])); ?></span>
                                    </td>
                                    <td><?= $rows['AccessLevel']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php
                } else {
                    // Display a message if no records found
                    echo '<div class="alert alert-danger" role="alert">No records found.</div>';
                }
                ?>
            </div>
            <div class="card-footer">
                <?php echo mysqli_num_rows($result) . " record/s found"; ?>
            </div>
        </div><br>

        <!-- RATINGS CARD -->
        <div class="card">
            <div class="card-header">
                <h3>List of Tours and Tickets</h3>
            </div>
            <div class="card-body table-responsive">
                <?php
                //show table records
                if (isset($_GET["keyword"]) && $_GET["keyword"]) {
                    $keyword = "%" . filter_var($_GET["keyword"], FILTER_SANITIZE_SPECIAL_CHARS) . "%";
                    $sql_select = "SELECT * FROM tblratings WHERE HotelName LIKE ? OR Stars LIKE ?";
                    $stmt = $conn->prepare($sql_select);
                    $stmt->bind_param("si", $keyword, $keyword);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } else {
                    $sql_select = "SELECT * FROM tblratings";
                    $result = $conn->query($sql_select);
                }

                // Display table only if there are records
                if (mysqli_num_rows($result) > 0) {
                ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Rating ID</th>
                                <th>User ID</th>
                                <th>Hotel Name</th>
                                <th>Stars</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($rows = mysqli_fetch_array($result)) {
                            ?>
                                <tr>
                                    <td><?= $rows["RatingID"]; ?></td>
                                    <td><?= $rows["UserID"]; ?></td>
                                    <td><?= $rows["HotelName"]; ?></td>
                                    <td><?= $rows["Stars"]; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php
                } else {
                    // Display a message if no records found
                    echo '<div class="alert alert-danger" role="alert">No records found.</div>';
                }
                ?>
            </div>
            <div class="card-footer">
                <?php echo mysqli_num_rows($result) . " record/s found"; ?>
            </div>
        </div><br>

        <!-- Scroll to top button -->
        <button class="scroll-top-btn">
            <i class="fas fa-arrow-up"></i>
        </button>
    </main>


    <!-- Modal Employee -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="modal-body" action="submit_form.php" method="POST">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" required>

                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" required>

                        <label for="birthdate" class="form-label">Birthdate</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate" required>

                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" required>

                        <label for="positionID" class="form-label">Position ID</label>
                        <input type="text" class="form-control" id="positionID" name="positionID" required>

                        <label for="departmentID" class="form-label">Department ID</label>
                        <input type="text" class="form-control" id="departmentID" name="departmentID" required><br>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                </div>

                </form>

            </div>
        </div>
    </div>

    <!-- Modal Department -->
    <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="modal-body" action="submit_form.php" method="POST">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" class="form-control" id="department" name="department" required><br>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                </div>

                </form>

            </div>
        </div>
    </div>

    <!-- Modal Position -->
    <div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Position</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="modal-body" action="submit_form.php" method="POST">
                        <label for="position" class="form-label">Position</label>
                        <input type="text" class="form-control" id="position" name="position" required><br>

                        <label for="salary" class="form-label">Salary</label>
                        <input type="text" class="form-control" id="salary" name="salary" required><br>

                        <label for="department" class="form-label">Department ID</label>
                        <input type="text" class="form-control" id="department" name="department" required><br>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                </div>

                </form>

            </div>
        </div>
    </div>

    <!-- Modal User -->
    <div class="modal fade" id="exampleModal4" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="modal-body" action="submit_form.php" method="POST">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required><br>

                        <label for="password" class="form-label">Password</label>
                        <input type="text" class="form-control" id="password" name="password" required><br>

                        <label for="accesslevel" class="form-label">Access Level</label>
                        <input type="text" class="form-control" id="accesslevel" name="accesslevel" required><br>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                </div>

                </form>

            </div>
        </div>
    </div>

    <div class="container mt-3">
        <div class="row justify-content-end">
            <div class="col-auto">
                <form action="generate_report.php" method="POST">
                    <button type="submit" class="btn btn-success" name="generateIndex">Generate Report</button>
                </form>
            </div>
        </div>
    </div>
    <br><br>


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
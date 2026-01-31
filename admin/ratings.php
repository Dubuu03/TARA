<?php
session_start();
include('../config.php');

// Check if $pdo is defined
if (!isset($pdo)) {
    die('Database connection not established.');
}

// Retrieve UserID from the session
$currentUserID = isset($_SESSION['user']['UserID']) ? $_SESSION['user']['UserID'] : '';

// Handling search functionality
if (isset($_GET["keyword"]) && $_GET["keyword"]) {
    $keyword = "%" . filter_var($_GET["keyword"], FILTER_SANITIZE_SPECIAL_CHARS) . "%";
    $sql_select = "SELECT * FROM tblratings WHERE HotelName LIKE :keyword OR UserID LIKE :keyword";
    $stmt = $pdo->prepare($sql_select);
    $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $sql_select = "SELECT * FROM tblratings";
    $stmt = $pdo->prepare($sql_select);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handling form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirmAdd'])) {
        $userID = $currentUserID;
        $hotelName = $_POST['hotelName'];
        $stars = $_POST['stars'];

        $sql_insert = "INSERT INTO tblratings (UserID, HotelName, Stars) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql_insert);
        $stmt->bindParam(1, $userID, PDO::PARAM_STR);
        $stmt->bindParam(2, $hotelName, PDO::PARAM_STR);
        $stmt->bindParam(3, $stars, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['alert-message'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                        Record added successfully!
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                      </div>';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['edit_rating_id'])) {
        $ratingID = $_POST['edit_rating_id'];
        $userID = $_POST['edit_userID'];
        $hotelName = $_POST['edit_hotelName'];
        $stars = $_POST['edit_stars'];

        $sql_update = "UPDATE tblratings SET UserID = ?, HotelName = ?, Stars = ? WHERE RatingID = ?";
        $stmt = $pdo->prepare($sql_update);
        $stmt->bindParam(1, $userID, PDO::PARAM_STR);
        $stmt->bindParam(2, $hotelName, PDO::PARAM_STR);
        $stmt->bindParam(3, $stars, PDO::PARAM_INT);
        $stmt->bindParam(4, $ratingID, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['alert-message'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        Record updated successfully!
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                      </div>';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['delete_rating_id'])) {
        $ratingID = $_POST['delete_rating_id'];

        $sql_delete = "DELETE FROM tblratings WHERE RatingID = ?";
        $stmt = $pdo->prepare($sql_delete);
        $stmt->bindParam(1, $ratingID, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['alert-message'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        Record deleted successfully!
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                      </div>';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ratings</title>
    <link rel="stylesheet" href="./style.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/bootstrap-5.3.0-alpha3-dist/css/bootstrap.css">
    <script src="../assets/bootstrap-5.3.0-alpha3-dist/js/bootstrap.bundle.min.js"></script>
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
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
        }

        .scroll-top-btn:hover {
            transform: scale(1.2);
        }

        .scroll-top-btn:active {
            transform: scale(0.8);
        }

        .scroll-top-btn {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

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
                        <a class="nav-link" href="cars.php">Car Rentals</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="user.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="ratings.php">Ratings</a>
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

                <form class="d-flex" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="keyword">
                    <button class="btn btn-outline-info" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>
    <br><br>

    <!-- CARD -->
    <main class="container mt-5">
        <?php if (isset($_SESSION["alert-message"])) : ?>
            <div class="alert-wrapper">
                <?= $_SESSION["alert-message"] ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET["keyword"]) && $_GET["keyword"]) : ?>
            <h3>Search results for "<?php echo htmlspecialchars($_GET["keyword"]); ?>"</h3>
            <br>
        <?php endif; ?>
        <div class="card">
            <div class="card-header">
                <h3>List of Ratings</h3>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    Add
                </button>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">User ID</th>
                            <th scope="col">Hotel Name</th>
                            <th scope="col">Stars</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result as $row) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['UserID']); ?></td>
                                <td><?php echo htmlspecialchars($row['HotelName']); ?></td>
                                <td><?php echo htmlspecialchars($row['Stars']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" onclick="setEditData(<?php echo $row['RatingID']; ?>, '<?php echo htmlspecialchars($row['UserID']); ?>', '<?php echo htmlspecialchars($row['HotelName']); ?>', <?php echo $row['Stars']; ?>)">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="setDeleteData(<?php echo $row['RatingID']; ?>)">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Add Rating Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add New Rating</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="hotelName" class="form-label">Hotel Name</label>
                            <select class="form-select" id="hotelName" name="hotelName" required>
                                <option value="Luxury Beachfront Hotel with Pool">Luxury Beachfront Hotel with Pool</option>
                                <option value="City Center Hotel with Rooftop Pool">City Center Hotel with Rooftop Pool</option>
                                <option value="Boutique Hotel with Garden View">Boutique Hotel with Garden View</option>
                                <option value="Beach Resort with Private Villas">Beach Resort with Private Villas</option>
                                <option value="Historical Hotel in the Heart of Manila">Historical Hotel in the Heart of Manila</option>
                                <option value="Mountain Resort with Scenic Views">Mountain Resort with Scenic Views</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="stars" class="form-label">Stars</label>
                            <input type="number" class="form-control" id="stars" name="stars" min="1" max="5" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="confirmAdd" class="btn btn-primary">Add Rating</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Rating</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <input type="hidden" id="edit-rating-id" name="edit_rating_id">
                        <input type="hidden" id="edit-userID" name="edit_userID" value="<?= $currentUserID; ?>" readonly>
                        <div class="mb-3">
                            <label for="edit-hotelName" class="form-label">Hotel Name</label>
                            <select class="form-select" id="edit-hotelName" name="edit_hotelName" required>
                                <option value="">Select a Hotel</option>
                                <option value="Luxury Beachfront Hotel with Pool">Luxury Beachfront Hotel with Pool</option>
                                <option value="City Center Hotel with Rooftop Pool">City Center Hotel with Rooftop Pool</option>
                                <option value="Boutique Hotel with Garden View">Boutique Hotel with Garden View</option>
                                <option value="Beach Resort with Private Villas">Beach Resort with Private Villas</option>
                                <option value="Historical Hotel in the Heart of Manila">Historical Hotel in the Heart of Manila</option>
                                <option value="Mountain Resort with Scenic Views">Mountain Resort with Scenic Views</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit-stars" class="form-label">Stars</label>
                            <input type="number" class="form-control" id="edit-stars" name="edit_stars" required min="1" max="5">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Delete Rating Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Rating</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post">
                    <input type="hidden" id="delete_rating_id" name="delete_rating_id">
                    <div class="modal-body">
                        <p>Are you sure you want to delete this rating?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Rating</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container mt-3">
        <div class="row justify-content-end">
            <div class="col-auto">
                <form action="generate_report.php" method="POST">
                    <button type="submit" class="btn btn-success" name="generateRating">Generate Report</button>
                </form>
            </div>
        </div>
    </div>
    <br><br>
    <!-- Scroll to Top Button -->
    <button class="scroll-top-btn" id="scrollTopBtn">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // Handle scroll to top button visibility
        window.addEventListener('scroll', () => {
            const scrollTopBtn = document.getElementById('scrollTopBtn');
            if (window.scrollY > 200) {
                scrollTopBtn.style.display = 'block';
            } else {
                scrollTopBtn.style.display = 'none';
            }
        });

        // Scroll to top functionality
        document.getElementById('scrollTopBtn').addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Handle Edit Modal data
        function setEditData(ratingID, userID, hotelName, stars) {
            document.getElementById('edit_rating_id').value = ratingID;
            document.getElementById('edit_hotelName').value = hotelName;
            document.getElementById('edit_stars').value = stars;
        }

        // Handle Delete Modal data
        function setDeleteData(ratingID) {
            document.getElementById('delete_rating_id').value = ratingID;
        }

        // Auto-close alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.classList.remove('show');
                alert.classList.add('fade');
            });
        }, 5000);

        function editRecord(ratingID, userID, hotelName, stars) {
            document.getElementById('edit-rating-id').value = ratingID;
            document.getElementById('edit-userID').value = userID;
            document.getElementById('edit-hotelName').value = hotelName;
            document.getElementById('edit-stars').value = stars;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }

        // Handle Edit Modal data
        function setEditData(ratingID, userID, hotelName, stars) {
            document.getElementById('edit-rating-id').value = ratingID;
            document.getElementById('edit-userID').value = userID;

            // Set the selected hotel name in the dropdown
            const editHotelName = document.getElementById('edit-hotelName');
            editHotelName.value = hotelName;

            // Set the stars input value
            document.getElementById('edit-stars').value = stars;
        }
    </script>
</body>

</html>
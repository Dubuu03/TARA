<?php
session_start();
include('config.php');

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
};

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
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
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color:#022E3B; padding: 0;     ">
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
                        <a class="nav-link " href="cars.php">Car Rentals</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="user.php">Users</a>
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
            <h3>Search results for "<?php echo $_GET["keyword"] ?>"</h3>
            <br>
        <?php endif; ?>
        <div class="card">
            <div class="card-header">
                <h3>List of Users</h3>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    Add
                </button>
            </div>
            <div class="card-body table-responsive">
                <?php if (mysqli_num_rows($result) > 0) : ?>
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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($rows = mysqli_fetch_array($result)) { ?>
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
                                    <td>
                                        <button type="button" class="btn btn-warning" onclick="editRecord('<?= $rows['UserID']; ?>', '<?= $rows['LastName']; ?>',
                                        '<?= $rows['FirstName']; ?>', '<?= $rows['Email']; ?>',
                                        '<?= $rows['AccessLevel']; ?>')">Edit</button>
                                        <button type="button" class="btn btn-danger" onclick="deleteRecordConfirmation(<?= $rows['UserID']; ?>)">Delete</button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="./userActions.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="confirmAdd" value="1">
                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" required placeholder="Enter first name">
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required placeholder="Enter last name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="text" class="form-control" id="email" name="email" required placeholder="Enter email address">
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">User image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        </div>
                        <div>
                            <label for="add-password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="add-password" name="password" required placeholder="Enter password">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="show-add-password">
                            <label class="form-check-label" for="show-add-password" style="font-size: 13px;">Show
                                Password</label>
                        </div>
                        <div class="mb-3">
                            <label for="accessLevel" class="form-label">Access Level</label>
                            <select class="form-select" id="accessLevel" name="accessLevel" required>
                                <option value="Admin">Admin</option>
                                <option value="User">User</option>
                            </select>
                        </div>
                        <?php if (isset($_SESSION["add-user-password-error"])) : ?>
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Error:">
                                    <use xlink:href="#exclamation-triangle-fill" />
                                </svg>
                                <div>
                                    <?php echo $_SESSION["add-user-password-error"]; ?>
                                </div>
                            </div>
                            <script>
                                $(document).ready(function() {
                                    $('#addModal').modal('show');
                                });
                            </script>
                        <?php endif; ?>
                        <?php if (isset($_SESSION["add_user_upload_image_error"])) : ?>
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Error:">
                                    <use xlink:href="#exclamation-triangle-fill" />
                                </svg>
                                <div>
                                    <?php echo $_SESSION["add_user_upload_image_error"]; ?>
                                </div>
                            </div>
                            <script>
                                $(document).ready(function() {
                                    $('#addModal').modal('show');
                                });
                            </script>
                        <?php endif; ?>
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
                    <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="modal-body" id="editForm" action="./userActions.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="edit-user-id" name="edit_user_id">
                        <div class="mb-3">
                            <label for="edit-firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit-firstName" name="edit_firstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit-lastName" name="edit_lastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-email" class="form-label">Email address</label>
                            <input type="text" class="form-control" id="edit-email" name="edit_email" required>
                        </div>
                        <div class="mb-3">
                            <label for="new-image" class="form-label">User image</label>
                            <input type="file" class="form-control" id="new-image" name="new-image" accept="image/*" required>
                        </div>
                        <div>
                            <label for="edit-password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="edit-password" name="edit_password" required placeholder="Enter new password...">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="show-edit-password">
                            <label class="form-check-label" for="show-edit-password" style="font-size: 13px;">Show
                                Password</label>
                        </div>
                        <div class="mb-3"><br>
                            <label for="edit-accessLevel" class="form-label">Access Level</label>
                            <select class="form-select" id="edit-accessLevel" name="edit_accessLevel" required>
                                <option value="Admin">Admin</option>
                                <option value="User">User</option>
                            </select>
                        </div>
                        <?php if (isset($_SESSION["edit-user-password-error"])) : ?>
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Error:">
                                    <use xlink:href="#exclamation-triangle-fill" />
                                </svg>
                                <div>
                                    <?php echo $_SESSION["edit-user-password-error"]; ?>
                                </div>
                            </div>
                            <script>
                                $(document).ready(function() {
                                    $('#editModal').modal('show');
                                });
                            </script>
                        <?php endif; ?>
                        <?php if (isset($_SESSION["edit_user_upload_image_error"])) : ?>
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Error:">
                                    <use xlink:href="#exclamation-triangle-fill" />
                                </svg>
                                <div>
                                    <?php echo $_SESSION["edit_user_upload_image_error"]; ?>
                                </div>
                            </div>
                            <script>
                                $(document).ready(function() {
                                    $('#editModal').modal('show');
                                });
                            </script>
                        <?php endif; ?>
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
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <form action="./userActions.php" method="POST" id="deleteForm">
                        <input type="hidden" id="delete-user-id" name="delete_id">
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
                    <button type="submit" class="btn btn-success" name="generateUser">Generate Report</button>
                </form>
            </div>
        </div>
    </div>
    <br><br>


    <!-- SVG Icons for Alerts -->
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
        </symbol>
        <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
        </symbol>
        <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
        </symbol>
    </svg>

    <!-- JavaScript -->
    <script>
        // function editRecord(userID, lastName, firstName, email, password, accessLevel) {
        function editRecord(userID, lastName, firstName, email, accessLevel) {
            document.getElementById('edit-user-id').value = userID;
            document.getElementById('edit-lastName').value = lastName;
            document.getElementById('edit-firstName').value = firstName;
            document.getElementById('edit-email').value = email;
            // document.getElementById('edit-password').value = password;
            document.getElementById('edit-accessLevel').value = accessLevel;
            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }

        function deleteRecordConfirmation(userID) {
            document.getElementById('delete-user-id').value = userID;
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

        // Function to toggle password visibility
        $(document).ready(function() {
            $('#show-add-password').click(function() {
                var passwordField = $('#add-password');
                var fieldType = passwordField.attr('type');
                if (fieldType === 'password') {
                    passwordField.attr('type', 'text');
                } else {
                    passwordField.attr('type', 'password');
                }
            });

            $('#show-edit-password').click(function() {
                var passwordField = $('#edit-password');
                var fieldType = passwordField.attr('type');
                if (fieldType === 'password') {
                    passwordField.attr('type', 'text');
                } else {
                    passwordField.attr('type', 'password');
                }
            });
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

<?php
unset($_SESSION["alert-message"]);
unset($_SESSION["add-user-password-error"]);
unset($_SESSION["add_user_upload_image_error"]);
unset($_SESSION["edit-user-password-error"]);
unset($_SESSION["edit_user_upload_image_error"]);
?>

</html>
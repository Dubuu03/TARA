<?php
session_start();
include("../config.php");


if ($_SERVER["REQUEST_METHOD"] === "POST") {

   // Sign Up
   if (isset($_POST["confirmSignup"])) {
      try {

         $firstName = filter_var($_POST["firstName"], FILTER_SANITIZE_SPECIAL_CHARS);
         $lastName = filter_var($_POST["lastName"], FILTER_SANITIZE_SPECIAL_CHARS);
         $mobileOrEmail = filter_var($_POST["mobileOrEmail"], FILTER_SANITIZE_SPECIAL_CHARS);
         $password = filter_var($_POST["password"], FILTER_SANITIZE_SPECIAL_CHARS);

         // Separate checks for email format
         if (strpos($mobileOrEmail, '@') === 0) {
            $_SESSION["email_custom_error"] = true;
            header("Location: index.php#signupModal");
            exit();
         }

         if (!preg_match('/\.com$/', $mobileOrEmail)) {
            $_SESSION["email_format_error"] = true;
            header("Location: index.php#signupModal");
            exit();
         }

         // Check domain after @
         $allowed_domains = ['gmail.com', 'email.com', 'yahoo.com'];
         $email_parts = explode('@', $mobileOrEmail);
         if (count($email_parts) !== 2 || !in_array($email_parts[1], $allowed_domains)) {
            $_SESSION["email_domain_error"] = true;
            header("Location: index.php#signupModal");
            exit();
         }

         // Password length check
         if (strlen($password) < 8) {
            $_SESSION["password_error"] = true;
            header("Location: index.php#signupModal");
            exit();
         }

         // Check if the account already exists in the database
         $query = "SELECT * FROM tbluser WHERE EMAIL = :email";
         $stmt = $pdo->prepare($query);
         $stmt->execute([
            "email" => $mobileOrEmail
         ]);

         if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['account_exist'] = true;
         } else {
            // If account doesn't exist, proceed with sign up
            // Insert the data into the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO tbluser (LastName, FirstName, Email, Password, AccessLevel, UserImage) VALUES (:lastName, :firstName, :email, :password, :accessLevel, :userImage)";

            $stmt = $pdo->prepare($query);
            $stmt->execute([
               "lastName" => $lastName,
               "firstName" => $firstName,
               "email" => $mobileOrEmail,
               "password" => $hashedPassword,
               "accessLevel" => "User",
               "userImage" => "user-images/default.png"
            ]);

            // Get the last inserted ID
            $lastInsertId = $pdo->lastInsertId();

            // Fetch the newly inserted user data
            $query = "SELECT * FROM tbluser WHERE UserID = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
               "id" => $lastInsertId,
            ]);
            $newUser = $stmt->fetch(PDO::FETCH_ASSOC);

            // Set session variables for successful sign up
            $_SESSION["changes_success"] = true;
            $_SESSION["user"] = $newUser;
         }

         $pdo = null;
         header("Location: index.php#signupModal");
         exit();
      } catch (PDOException $e) {
         die($e->getMessage());
      }
   }



   // Login
   if (isset($_POST['confirmLogin'])) {
      try {
         $mobileOrEmail = filter_var($_POST["mobileOrEmail"], FILTER_SANITIZE_SPECIAL_CHARS);
         $password = filter_var($_POST["password"], FILTER_SANITIZE_SPECIAL_CHARS);

         $query = "SELECT * FROM tbluser WHERE Email = :email";
         $stmt = $pdo->prepare($query);
         $stmt->execute(["email" => $mobileOrEmail]);

         $result = $stmt->fetch(PDO::FETCH_ASSOC);

         if ($result) {

            // Check if the input password match the hashed password from the database
            if (password_verify($password, $result["Password"])) {
               $_SESSION['user'] = $result;
               $_SESSION["changes_success"] = true;

               // Check user AccessLevel
               if ($result['AccessLevel'] === 'Admin') {
                  // Redirect to Admin page
                  header("Location: ../admin");
                  exit();
               } else {
                  // Redirect to index page for regular users
                  header("Location: index.php");
                  exit();
               }
            } else {
               $_SESSION['error_message'] = "Invalid email or password.";
               header("Location: index.php#loginModal");
               exit();
            }

            $pdo = null;
         } else {
            // Invalid email or password, set error message and keep login modal open
            $_SESSION['error_message'] = "Invalid email or password.";
            header("Location: index.php#loginModal");
            exit();
         }
      } catch (PDOException $e) {
         die($e->getMessage());
      }
   }

   // Edit Profile
   if (isset($_POST["editProfile"])) {
      try {
         $firstName = filter_var($_POST["firstName"], FILTER_SANITIZE_SPECIAL_CHARS);
         $lastName = filter_var($_POST["lastName"], FILTER_SANITIZE_SPECIAL_CHARS);
         $email = filter_var($_POST["email"], FILTER_SANITIZE_SPECIAL_CHARS);
         $password = filter_var($_POST["password"], FILTER_SANITIZE_SPECIAL_CHARS);

         if (strlen($password) < 8) {
            $_SESSION["edit_password_error"] = true;
            header("Location: index.php");
            exit();
         }

         // Check if the new email already exists
         $query = "SELECT * FROM tbluser WHERE Email = :email AND UserID != :currentUserId";
         $stmt = $pdo->prepare($query);
         $stmt->execute([
            "email" => $email,
            "currentUserId" => $_SESSION["user"]["UserID"]
         ]);

         $result = $stmt->fetch(PDO::FETCH_ASSOC);

         if (!$result) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            //upload imagefile
            $target_dir = "../user-images/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $allowed_types = ["jpg", "jpeg", "png", "gif"];

            if (!in_array($imageFileType, $allowed_types)) {
               $_SESSION["invalid_file"] = "Only files of image type are allowed";
               header("Location: index.php");
            } else {
               if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {

                  $query = "UPDATE tbluser SET LastName = :lastName, FirstName = :firstName, Email = :email, Password = :password, UserImage = :userImage WHERE UserID = :currentUserId";
                  $stmt = $pdo->prepare($query);
                  $stmt->execute([
                     "lastName" => $lastName,
                     "firstName" => $firstName,
                     "email" => $email,
                     "password" => $hashedPassword,
                     "currentUserId" => $_SESSION["user"]["UserID"],
                     "userImage" => "user-images/" . $_FILES["image"]["name"]
                  ]);

                  $_SESSION["user"]["FirstName"] = $firstName;
                  $_SESSION["user"]["LastName"] = $lastName;
                  $_SESSION["user"]["Email"] = $email;
                  $_SESSION["user"]["Password"] = $hashedPassword;
                  $_SESSION["user"]["UserImage"] = "user-images/" . $_FILES["image"]["name"];

                  $_SESSION["changes_success"] = true;
                  $pdo = null;
                  header("Location: index.php");
               } else {
                  $_SESSION["invalid_file"] = "Sorry, there was an error uploading your file.";
                  header("Location: index.php");
               }
            }
         } else {
            // Redirect to index.php if exists
            $_SESSION["editProfileErrorMessage"] = "Email already in use.";
            header("Location: index.php");
         }
      } catch (PDOException $e) {
         die($e->getMessage());
      }
   }

   // Add Booking 
   if (isset($_POST["confirmBooking"])) {
      $place = filter_var($_POST["tourName"], FILTER_SANITIZE_EMAIL);
      $totalPrice = filter_var($_POST["totalPrice"]);
      $bookingDate = filter_var($_POST["bookingDate"], FILTER_SANITIZE_SPECIAL_CHARS);
      $paymentMethod = filter_var($_POST["paymentMethod"], FILTER_SANITIZE_SPECIAL_CHARS);
      $adultCount = filter_var($_POST["adultCount"], FILTER_SANITIZE_NUMBER_INT);
      $childrenCount = filter_var($_POST["childrenCount"], FILTER_SANITIZE_NUMBER_INT);

      $query = "INSERT INTO tblbookings(UserID, Place, Date, Adult, Children, Payment, TotalPrice) VALUES(:userID, :place, :date, :adult, :children, :payment, :totalPrice)";

      $stmt = $pdo->prepare($query);
      $stmt->execute([
         "userID" => $_SESSION["user"]["UserID"],
         "place" => $place,
         "date" => $bookingDate,
         "adult" => $adultCount,
         "children" => $childrenCount,
         "payment" => $paymentMethod,
         "totalPrice" => $totalPrice
      ]);

      $_SESSION["changes_success"] = true;
      $pdo = null;
      header("Location: index.php");
   }


   // Delete Booking
   if (isset($_POST["confirmTourCancel"])) {
      try {
         $bookingID = filter_var($_POST["bookingID"], FILTER_SANITIZE_NUMBER_INT);
         $query = "DELETE FROM tblbookings WHERE BookingID = :bookingID AND UserID = :userID";

         $stmt = $pdo->prepare($query);
         $stmt->bindParam(":bookingID", $bookingID, PDO::PARAM_INT);
         $stmt->bindParam(":userID", $_SESSION["user"]["UserID"], PDO::PARAM_INT);
         $stmt->execute();

         header("Location: index.php");
         $_SESSION["changes_success"] = true;

         $pdo = null;
      } catch (PDOException $e) {
         die($e->getMessage());
      }
   }

   // Add Hotel Booking
   if (isset($_POST["confirmHotelBooking"])) {
      try {
         // Sanitize input data
         $hotelName = filter_var($_POST["hotelName"], FILTER_SANITIZE_SPECIAL_CHARS);
         $checkInDate = filter_var($_POST["checkInDate"], FILTER_SANITIZE_SPECIAL_CHARS);
         $checkOutDate = filter_var($_POST["checkOutDate"], FILTER_SANITIZE_SPECIAL_CHARS);
         $roomType = filter_var($_POST["roomType"], FILTER_SANITIZE_SPECIAL_CHARS);
         $totalPrice = filter_var($_POST["actualHotelTotalPrice"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
         $paymentMethod = filter_var($_POST["hotelpaymentMethod"], FILTER_SANITIZE_SPECIAL_CHARS); // Updated parameter name

         // Prepare SQL query
         $query = "INSERT INTO tblhotels (UserID, `Check-in`, `Check-out`, RoomType, Destination, TotalPrice, Payment) 
                  VALUES (:userID, :checkIn, :checkOut, :roomType, :destination, :totalPrice, :paymentMethod)";

         $stmt = $pdo->prepare($query);

         // Bind parameters and execute query
         $stmt->execute([
            "userID" => $_SESSION["user"]["UserID"],
            "checkIn" => $checkInDate,
            "checkOut" => $checkOutDate,
            "destination" => $hotelName,
            "roomType" => $roomType,
            "totalPrice" => $totalPrice,
            "paymentMethod" => $paymentMethod,
         ]);

         // Redirect after successful insertion
         header("Location: index.php");
         $_SESSION["changes_success"] = true;

         // Close connection
         $pdo = null;
         exit; // Ensure script stops execution after redirect
      } catch (PDOException $e) {
         die($e->getMessage()); // Handle database errors
      }
   }


   // Delete Hotel Booking
   if (isset($_POST["confirmHotelCancel"])) {
      try {
         $hotelId = filter_var($_POST["hotelID"], FILTER_SANITIZE_NUMBER_INT);
         $query = "DELETE FROM tblhotels WHERE HotelID = :hotelID AND UserID = :userID";

         $stmt = $pdo->prepare($query);
         $stmt->bindParam(":hotelID", $hotelId, PDO::PARAM_INT);
         $stmt->bindParam(":userID", $_SESSION["user"]["UserID"], PDO::PARAM_INT);
         $stmt->execute();

         header("Location: index.php");
         $_SESSION["changes_success"] = true;

         $pdo = null;
      } catch (PDOException $e) {
         die($e->getMessage());
      }
   }

   // Book Car Rental
   if (isset($_POST["book-car-rental"])) {
      try {
         $pickupLocation = filter_var($_POST["pickup-location"], FILTER_SANITIZE_SPECIAL_CHARS);
         $dropoffLocation = filter_var($_POST["dropoff"], FILTER_SANITIZE_SPECIAL_CHARS);
         $date = filter_var($_POST["date"], FILTER_SANITIZE_SPECIAL_CHARS);
         $carType = filter_var($_POST["car-type"], FILTER_SANITIZE_SPECIAL_CHARS);

         // Assuming $_SESSION["user"]["UserID"] contains the logged-in user's ID
         $query = "INSERT INTO tblcars (UserID, Pickup, Dropoff, Date, CarType) VALUES(:userID, :pickup, :dropoff, :date, :carType)";

         $stmt = $pdo->prepare($query);
         $stmt->execute([
            "userID" => $_SESSION["user"]["UserID"],
            "pickup" => $pickupLocation,
            "dropoff" => $dropoffLocation,
            "date" => $date,
            "carType" => $carType // Binding carType parameter
         ]);

         $_SESSION["changes_success"] = true;
         $pdo = null;
         header("Location: index.php");
         exit(); // Exit after redirect
      } catch (PDOException $e) {
         die($e->getMessage());
      }
   }




   // Delete Car Rental Booking
   if (isset($_POST["confirmCarCancel"])) {
      try {
         $carRentalId = filter_var($_POST["carID"]);
         $query = "DELETE FROM tblcars WHERE CarID = :carID AND UserID = :userID";

         $stmt = $pdo->prepare($query);
         $stmt->bindParam(":carID", $carRentalId, PDO::PARAM_INT);
         $stmt->bindParam(":userID", $_SESSION["user"]["UserID"], PDO::PARAM_INT);
         $stmt->execute();

         header("Location: index.php");
         $_SESSION["changes_success"] = true;

         $pdo = null;
      } catch (PDOException $e) {
         die($e->getMessage());
      }
   }
   // Forgot Password
   if (isset($_POST['confirmForgotPassword'])) {
      $email = $_POST['email'];

      // Check if the email exists
      $query = "SELECT * FROM tbluser WHERE Email = :email";
      $stmt = $pdo->prepare($query);
      $stmt->execute(['email' => $email]);

      if ($stmt->rowCount() > 0) {
         // Email exists, set session and redirect to index.php
         $_SESSION['reset_email'] = $email;
         $_SESSION['show_change_password_modal'] = true; // Flag to show change password modal

         // Redirect to index.php
         header('Location: index.php');
         exit();
      } else {
         // Email does not exist, set session and redirect back to forgot password modal
         $_SESSION['forgot_password_error'] = true; // Flag to show error alert

         // Redirect back to index.php
         header('Location: index.php');
         exit();
      }
   }

   // Change Password
   if (isset($_POST['changePassword'])) {
      // Validate and sanitize input
      $newPassword = $_POST['newPassword'];
      $confirmPassword = $_POST['confirmPassword'];
      $resetEmail = $_SESSION['reset_email']; // Assuming you set this session variable

      // Validate if passwords match
      if ($newPassword === $confirmPassword) {
         // Check password length
         if (strlen($newPassword) >= 8) {

            $query = "SELECT Password FROM tbluser WHERE Email = :email";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['email' => $resetEmail]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
               $_SESSION['password_change_error'] = 'User not found or email not valid.';
               header('Location: index.php'); // Redirect back to index.php or appropriate page
               exit();
            }

            $currentPasswordHash = $row['Password'];

            // Check if the new password is the same as the current password
            if (password_verify($newPassword, $currentPasswordHash)) {
               $_SESSION['password_same_as_current_error'] = true;
               header('Location: index.php'); // Redirect back to index.php or appropriate page
               exit();
            }

            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the password in the database
            $updateQuery = "UPDATE tbluser SET Password = :password WHERE Email = :email";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([
               'password' => $hashedPassword,
               'email' => $resetEmail
            ]);

            // Set session variable for successful password change
            $_SESSION['password_change_success'] = true;

            // Redirect after successful password change
            header('Location: index.php');
            exit();
         } else {
            $_SESSION['password_length_error'] = true; // Set error flag for password length
         }
      } else {
         $_SESSION['password_mismatch_error'] = true; // Set error flag for password mismatch
      }

      // Redirect back to index.php or wherever appropriate after processing
      header('Location: index.php');
      exit();
   }
} else {
   header("Location: index.php");
   die();
}

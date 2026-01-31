<?php
session_start();
include ('config.php');


if ($_SERVER["REQUEST_METHOD"] === "POST") {
   // Add functionality
   if (isset($_POST['confirmAdd'])) {
      $lastName = $_POST['lastName'];
      $firstName = $_POST['firstName'];
      $email = $_POST['email'];
      $password = $_POST['password'];
      $accessLevel = $_POST['accessLevel'];

      if (strlen($password) < 8) {
         $_SESSION["add-user-password-error"] = "Password should be at least 8 characters.";
         header("Location: user.php");
         exit();
      } else {
         $allowed_types = ["jpg", "jpeg", "png", "gif"];
         $target_dir = "../user-images/";

         //upload imagefile
         $target_file = $target_dir . basename($_FILES["image"]["name"]);
         $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

         if (!in_array($imageFileType, $allowed_types)) {
            $_SESSION["add_user_upload_image_error"] = "Only files of type image are allowed";
            header("Location: user.php");
            exit();
         } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {

               $imagePath = "user-images/" . $_FILES["image"]["name"];
               $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
               $sql = "INSERT INTO tbluser (FirstName,LastName, Email, Password, AccessLevel, UserImage) VALUES (?, ?, ?, ?, ?, ?)";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("ssssss", $firstName, $lastName, $email, $hashedPassword, $accessLevel, $imagePath);

               if ($stmt->execute()) {
                  $_SESSION["alert-message"] = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                          User added successfully!
                          <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
               } else {
                  $_SESSION["alert-message"] = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                          Error adding user: " . $stmt->error . "
                          <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
               }

               header("Location: user.php");
               exit();
            } else {
               $_SESSION["add_user_upload_image_error"] = "Sorry, there was an error uploading your file.";
               header("Location: user.php");
               exit();
            }
         }
      }
   }

   // Update functionality
   if (isset($_POST['edit_user_id'])) {
      $editUserID = $_POST['edit_user_id'];
      $editLastName = $_POST['edit_lastName'];
      $editFirstName = $_POST['edit_firstName'];
      $editEmail = $_POST['edit_email'];
      $editPassword = $_POST['edit_password'];
      $editAccessLevel = $_POST['edit_accessLevel'];

      if (strlen($editPassword) < 8) {
         $_SESSION["edit-user-password-error"] = "Password should be at least 8 characters.";
         header("Location: user.php");
         exit();
      } else {
         $allowed_types = ["jpg", "jpeg", "png", "gif",];
         $target_dir = "../user-images/";

         //upload imagefile
         $target_file = $target_dir . basename($_FILES["new-image"]["name"]);
         $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

         if (!in_array($imageFileType, $allowed_types)) {
            $_SESSION["edit_user_upload_image_error"] = "Only files of type image are allowed";
            header("Location: user.php");
            exit();
         } else {
            if (move_uploaded_file($_FILES["new-image"]["tmp_name"], $target_file)) {
               $imagePath = "user-images/" . $_FILES["new-image"]["name"];
               $hashedPassword = password_hash($editPassword, PASSWORD_DEFAULT);

               $sql = "UPDATE tbluser SET  FirstName=?,LastName=?, Email=?, Password=?, AccessLevel=?, UserImage=? WHERE UserID=?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("ssssssi", $editFirstName, $editLastName, $editEmail, $hashedPassword, $editAccessLevel, $imagePath, $editUserID);

               if ($stmt->execute()) {
                  $_SESSION["alert-message"] = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                           User updated successfully!
                           <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                       </div>";
               } else {
                  $_SESSION["alert-message"] = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                           Error updating user: " . $stmt->error . "
                           <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                       </div>";
               }

               header("Location: user.php");
               exit();
            } else {
               $_SESSION["edit_user_upload_image_error"] = "Sorry, there was an error uploading your file.";
               header("Location: user.php");
               exit();
            }
         }
      }
   }

   // Delete functionality
   if (isset($_POST['delete_id'])) {
      $deleteUserID = $_POST['delete_id'];

      $sql = "DELETE FROM tbluser WHERE UserID=?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $deleteUserID);

      if ($stmt->execute()) {
         $_SESSION["alert-message"] = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
               User deleted successfully!
               <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
           </div>";
      } else {
         $_SESSION["alert-message"] = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
               Error deleting user: " . $stmt->error . "
               <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
           </div>";
      }

      header("Location: user.php");
      exit();
   }
} else {
   header("Location: ../index.php");
   exit();
}

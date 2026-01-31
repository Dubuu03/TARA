<?php
session_start();
include("../config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = $_POST['userID'];
    $hotelName = $_POST['hotelName'];
    $stars = $_POST['stars'];

    try {
        $sql = "INSERT INTO tblratings (UserID, HotelName, Stars) VALUES (:userID, :hotelName, :stars)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':hotelName', $hotelName);
        $stmt->bindParam(':stars', $stars);
        $stmt->execute();

        // Set session variable for success
        $_SESSION['rating_success'] = true;
    } catch (PDOException $e) {
        $_SESSION['rating_success'] = false;
        $_SESSION['rating_error'] = $e->getMessage();
    }

    // Redirect to index.php
    header("Location: index.php");
    exit();
}

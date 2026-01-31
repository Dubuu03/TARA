<?php
session_start();
include("../config.php");

function getAverageRating($hotelName)
{
   global $pdo;

   if (!$pdo) {
      die("Database connection is not initialized.");
   }

   $stmt = $pdo->prepare("SELECT AVG(Stars) as averageRating FROM tblratings WHERE HotelName = :HotelName");
   $stmt->execute(['HotelName' => $hotelName]);
   $result = $stmt->fetch(PDO::FETCH_ASSOC);

   return $result ? round($result['averageRating'], 1) : 0;
}

$hotels = [
   "Luxury Beachfront Hotel with Pool",
   "City Center Hotel with Rooftop Pool",
   "Boutique Hotel with Garden View",
   "Beach Resort with Private Villas",
   "Historical Hotel in the Heart of Manila",
   "Mountain Resort with Scenic Views"
];

$ratings = [];
foreach ($hotels as $hotel) {
   $ratings[$hotel] = getAverageRating($hotel);
}


// Determine which modal to show based on user session
if (isset($_SESSION['user'])) {
   $user = $_SESSION['user'];
   $modalTarget = "#accountModal";
   $isLoggedIn = true;
} else {
   $modalTarget = "#loginModal";
   $isLoggedIn = false;
}

if (isset($_SESSION['user'])) {
   $userID = $_SESSION['user']['UserID'];

   // Get Bookings
   $query = "SELECT * FROM tblbookings WHERE UserID = :userID";
   $stmt = $pdo->prepare($query);
   $stmt->execute(["userID" => $userID]);

   $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

   // Get Car Rentals
   $query = "SELECT * FROM tblcars WHERE UserID = :userID";
   $stmt = $pdo->prepare($query);
   $stmt->execute(["userID" => $userID]);

   $carRentals = $stmt->fetchAll(PDO::FETCH_ASSOC);

   // Get Hotel Bookings
   $query = "SELECT * FROM tblhotels WHERE UserID = :userID";
   $stmt = $pdo->prepare($query);
   $stmt->execute(["userID" => $userID]);

   $hotelBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

   // Close connection
   $pdo = null;
}

$bookBtnTarget = isset($_SESSION["user"]) ? "#bookingModal" : "#loginModal";
$hotelBookBtnTarget = isset($_SESSION["user"]) ? "#hotelBookingModal" : "#loginModal";

function formatToPHP($number)
{
   // Format the number to 2 decimal places with comma as thousand separator and dot as decimal point
   $formatted_number = number_format($number, 2, '.', ',');

   // Append the Philippine Peso symbol (₱)
   return '₱ ' . $formatted_number;
}

// Fetch the alert message from the session
$alertMessage = isset($_SESSION['alert_message']) ? $_SESSION['alert_message'] : '';
// Clear the alert message from the session
unset($_SESSION['alert_message']);



?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link href="../global.css?<?php echo time(); ?>" rel="stylesheet" />
   <link href="../styles/home.css?<?php echo time(); ?>" rel="stylesheet" />
   <link href="../styles/services.css" rel="stylesheet" />

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

   <!-- BOOTSTRAP CSS -->
   <link rel="stylesheet" href="../assets/bootstrap-5.3.0-alpha3-dist/css/bootstrap.css">

   <!-- SCRIPTS -->
   <script src="../assets/bootstrap-5.3.0-alpha3-dist/js/bootstrap.js"></script>
   <script src="../assets/JQuery3.7.1.js"></script>

   <title>Travel Philippines</title>

   <style>
      .carousel-control-prev-icon {
         background-image: url(./public/left.png);
      }

      .carousel-control-next-icon {
         background-image: url(./public/right.png);
      }

      .card-body-container {
         display: flex;
         flex-direction: column;
         align-items: center;
      }

      .card-title-centered {
         margin-bottom: 20px;
      }

      .card-body-2col {
         column-count: 2;
         column-gap: 20px;
         padding-top: 5px;
      }

      .custom-footer {
         padding: .5rem 1rem;
         background-color: #022E3B;
         border-top: 1px solid rgba(0, 0, 0, 0.125);
         border-bottom: 12px;
         ;
      }

      .custom-footer a.btn {
         color: #fff;
         font-size: 14px;
      }

      .card-titles {
         font-size: 18px;
         color: #064358;
         padding: 0rem 1rem;
      }

      .card-text {
         font-size: 14px;
         color: #064358;
         margin-top: 5px;
         font-family: Arial, Helvetica, sans-serif;
      }

      .card-title {
         font-weight: bold;
         font-family: Arial, Helvetica, sans-serif;
      }

      .card {
         border-radius: 15px;
         box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
      }

      .card-slider .card-img-top {
         height: 250px;
         object-fit: cover;
         border-top-left-radius: 10px;
         border-top-right-radius: 10px;
      }

      .two-column-section {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 50px;
      }

      .column {
         width: 48%;
         /* Adjust width as needed */
         color: white;
         /* Set text color to white */
      }

      .column img {
         max-width: 100%;
      }

      .column form {
         max-width: 100%;
      }

      /* Set color of form inputs and labels to white */
      .column form input[type="text"],
      .column form input[type="date"],
      .column form label {
         color: white;
      }

      /* Set color of form button text to white */
      .column form button {
         color: white;
      }

      /* Set color of form button background to white */
      .column form button[type="submit"] {
         background-color: white;
      }

      .hero .card {
         flex: 1;
         margin-left: 40rem;
         width: 35rem;
         height: 28rem;
      }


      .card-subtitle {
         font-weight: normal;
         margin-top: 5px;
      }

      .form-label {
         font-family: Arial, Helvetica, sans-serif;
         font-weight: bold;
         text-align: center;
      }

      .edit-form-label {
         font-weight: 0%;
      }

      .form-label-date {
         font-family: Arial, Helvetica, sans-serif;
         text-align: left;
         font-weight: bold;
      }

      .form-select {
         margin-bottom: 15px;
      }

      .tprice {
         font-size: 15 px;
         color: black;
         text-align: center !important;
      }

      .check-mo {
         font-family: Arial;
         font-weight: 550 !important;
         font-size: 15px !important;
         margin: 0 350px 10px 0 !important;
      }

      .services-hero-text h1 {
         font-family: 'Impact', serif;
         font-size: clamp(40px, 10vw, 100px);
         font-weight: 50;
         letter-spacing: 22px;
         color: white;
      }

      .services-hero {
         background-attachment: fixed;
      }

      .services-hero-text p {
         font-family: 'Arial', sans-serif;
         font-size: 18px;
         margin-bottom: 20px;
         letter-spacing: 3px;
         color: white;
         line-height: 30px;
      }

      .form-label-s {
         font-family: Arial, Helvetica, sans-serif;
         font-weight: bold;
         text-align: center;
         margin-top: 10px;
         margin-bottom: 0%;
      }

      .btn.changecol:hover {
         background-color: rgb(1, 143, 1) !important;
         color: white !important;
      }

      .star-rating {
         position: absolute;
         top: 10px;
         right: 10px;
         z-index: 10;
      }

      .star {
         color: #ccc;
         cursor: pointer;
      }

      .star.selected {
         color: #f39c12;
      }

      .card {
         position: relative;
      }
   </style>
</head>

<body>
   <!-- HEADER -->
   <header>
      <a href="../index.php">
         <img src="public/taralogo.png" alt="tara-logo" class="logo">
      </a>
      <nav class="custom-nav">
         <ul>
            <li class="mobile-user-icon">
               <a href="#" data-bs-toggle="modal" data-bs-target="<?php echo $modalTarget; ?>">
                  <img src="./public/account.png" alt="account">
               </a>
            </li>
            <li>
               <a href="../index.php">
                  HOME
               </a>
            </li>
            <li class="dropdown">
               <button class="dropdown-trigger" data-dropdown-content="tourist-content">
                  <span>TOURIST ATTRACTIONS</span>
                  <span class="arrow">&#9662;</span>
               </button>
               <div id="tourist-content" class="dropdown-content">
                  <a href="../calle/index.php">Calle Crisologo</a>
                  <a href="../bohol/index.php">Chocolate Hills</a>
                  <a href="../siargao/index.php">Cloud 9</a>
                  <a href="../mayon/index.php">Mayon Volcano</a>
                  <a href="../tubbataha/index.php">Tubbataha Reef</a>
               </div>
            </li>
            <li class="dropdown">
               <button class="dropdown-trigger" data-dropdown-content="festivals-content">
                  <span>FESTIVALS</span>
                  <span class="arrow">&#9662;</span>
               </button>
               <div id="festivals-content" class="dropdown-content">
                  <a href="../ati-atihan/index.php">Ati-atihan</a>
                  <a href="../pahiyas/index.php">Pahiyas</a>
                  <a href="../panagbenga/index.php">Panagbenga</a>
                  <a href="../pavvurulun/index.php">Pavvulurun</a>
                  <a href="../sinulog/index.php">Sinulog</a>
               </div>
            </li>
            <li class="dropdown">
               <button class="dropdown-trigger" data-dropdown-content="foods-content">
                  <span>FOODS</span>
                  <span class="arrow">&#9662;</span>
               </button>
               <div id="foods-content" class="dropdown-content">
                  <a href="../adobo/index.php">Adobo</a>
                  <a href="../kare-kare/index.php">Kare-Kare</a>
                  <a href="../lechon/index.php">Lechon</a>
                  <a href="../pakbet/index.php">Pakbet</a>
                  <a href="../sisig/index.php">Sisig</a>
               </div>
            </li>
            <li>
               <a href="#">
                  SERVICES
               </a>
            </li>
            <li>
               <a href="../about/index.php">
                  ABOUT
               </a>
            </li>
            <li class="large-user-icon">
               <a href="#" data-bs-toggle="modal" data-bs-target="<?php echo $modalTarget; ?>">
                  <img src="./public/account.png" alt="account">
               </a>
            </li>
         </ul>
      </nav>
      <button class="menu">
         <i class="fa-solid fa-bars"></i>
      </button>
      <button class="close-nav">
         <i class="fa-solid fa-xmark"></i>
      </button>
   </header>

   <main>
      <!-- Alert Display -->
      <?php if ($alertMessage) : ?>
         <div class="alert-wrapper">
            <?= $alertMessage ?>
         </div>
      <?php endif; ?>

      <section class="services-hero" style="background-image: url('./public/carrentals1.png');">
         <div class="services-hero-container">
            <div class="services-hero-text">
               <h1>CAR RENTALS</h1>
               <p>
                  Unlock your adventure: Rent the perfect vehicle/ride for your dream itinerary. Explore hidden gems,
                  set your own pace, and experience freedom on the open road. Book your wheels today and turn travel
                  plans into unforgettable memories.
               </p>
            </div>
            <div class="card c-card">
               <h5 class="card-header custom-header" style="text-align: center; font-family: Arial; font-weight: bold;">
                  Welcome to
                  Philippines
                  <p class="card-subtitle mb-2 text-muted" style="font-size:15px">Discover the most
                     extensive array
                     of
                     travel services
                     available
                     in the
                     Philippines.</p>
               </h5>
               <div class="card-body">
                  <form class="modal-body" id="form" action="actions.php" method="POST">

                     <label for="pickup-location" class="form-label" style="white-space: nowrap;">Pickup
                        Location</label>
                     <select class="form-select" id="pickup-location" name="pickup-location" required>
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
                        <option value="Philippine Tarsier Sanctuary">Philippine Tarsier Sanctuary - Bohol</option>
                        <option value="Hinagdanan Cave">Hinagdanan Cave - Bohol</option>
                        <option value="Cloud 9">Cloud 9 - Siargao</option>
                        <option value="Nikka's Bridge">Nikka's Bridge - Siargao</option>
                        <option value="La Prinsesa Strawberry Farm">La Prinsesa Strawberry Farm - Siargao</option>
                     </select>

                     <label for="dropoff" class="form-label" style="white-space: nowrap;">Dropoff Location</label>
                     <select class="form-select" id="dropoff" name="dropoff" required disabled>
                        <option value="" disabled selected>Select Pickup Location First</option>
                     </select>

                     <label for="car-type" class="form-label">Type of Car</label>
                     <select class="form-control" id="car-type" name="car-type" required>
                        <option value="" disabled selected>Select Car Type</option>
                        <option value="4-seater">4 Seater</option>
                        <option value="6-seater">6 Seater</option>
                        <option value="8-seater">8 Seater</option>
                     </select>

                     <label for="date" class="form-label-date mt-3">Date</label>
                     <input type="date" class="form-control" id="date" name="date" required>



                     <br>
                     <div class="modal-footer">
                        <?php if (isset($_SESSION["user"])) : ?>
                           <button type="submit" class="btn btn-primary" name="book-car-rental">Book Now</button>
                        <?php else : ?>
                           <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Book Now</button>
                        <?php endif ?>
                     </div>
                  </form>
               </div>

            </div>
         </div>
      </section>

      <!--Tours and Tickets -->
      <section class="card-slider">
         <div class="container">
            <div class="row">
               <div class="col">
                  <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                     <h2 class="mb-4 mt-4" style="text-align: center;">
                        <i style="font-family: Times New Roman; font-weight: bold;">TOURS</i>
                        <span style="font-family: Cormorant Garamond; font-weight: 100;"> & TICKETS</span>
                     </h2>
                     <div class="carousel-inner">
                        <div class="carousel-item active">
                           <div class="row pb-5">
                              <div class="row">
                              </div>
                              <div class="col" style="max-height: 500px;">
                                 <div class="card" style="border-radius: 20px;">
                                    <img src="./public/tour1.png" class="card-img-top" alt="Tour 1">
                                    <div class="card-body-container">
                                       <h5 class="card-titles text-center mt-4" style="text-align: justify;">
                                          Boracay Island Hopping Shared Tour with Lunch, Hot Bath
                                          &
                                          Snorkeling Package</h5>
                                       </h5>

                                       <div class="card-body card-body-2col">
                                          <p class="card-text">Tour Starts: Boracay</p>
                                          <p class="card-text">Duration: 10 hours</p>
                                          <p class="card-text">Languages: English, Tagalog</p>
                                          <p class="card-text">Available: All year</p>
                                          <p class="card-text">Starting time: 6:00 AM</p>
                                          <p class="card-text">Ending place: Boracay</p>
                                          <p class="card-text">Difficulty: Easy</p>
                                          <p class="card-text">Minimum Age: None</p>
                                       </div>
                                    </div>
                                    <div class="card-footer custom-footer d-flex justify-content-between" style="border-bottom-right-radius: 20px; border-bottom-left-radius: 15px; background-color: rgb(2, 46, 59);">
                                       <div style="color: white;">
                                          <h4>From <span style="font-weight: bold;">1,079</span> PHP
                                          </h4>
                                       </div>
                                       <button class="btn btn-primary ml-auto book-btn" data-bs-toggle="modal" data-bs-target="<?php echo $bookBtnTarget; ?>" data-tour-name="Boracay">Book</button>
                                    </div>
                                 </div>
                              </div>
                              <div class="col">
                                 <div class="card" style="border-radius: 20px;">
                                    <img src="./public/tour2.png" class="card-img-top" alt="Tour 2">
                                    <div class="card-body-container">
                                       <h5 class="card-titles text-center mt-4" style="text-align: justify;">
                                          Cebu Kawasan Falls Canyoneering Tour with Lunch &
                                          Transfers from Cebu Citys
                                       </h5>
                                       <div class="card-body card-body-2col">
                                          <y class="card-text">Tour Starts: Cebu</p>
                                             <p class="card-text">Duration: 12 hours</p>
                                             <p class="card-text">Languages: English, Tagalog</p>
                                             <p class="card-text">Available: All year</p>
                                             <p class="card-text">Starting time: 6:00 AM</p>
                                             <p class="card-text">Ending place: Cebu </p>
                                             <p class="card-text">Difficulty: Easy</p>
                                             <p class="card-text">Minimum Age: None</p>
                                       </div>
                                    </div>
                                    <div class="card-footer custom-footer d-flex justify-content-between" style="border-bottom-right-radius: 15px; border-bottom-left-radius: 15px; background-color: rgb(2, 46, 59);">
                                       <div style="color: white;">
                                          <h4>From <span style="font-weight: bold;">2,983</span> PHP
                                          </h4>
                                       </div>
                                       <button class="btn btn-primary ml-auto book-btn" data-bs-toggle="modal" data-bs-target="<?php echo $bookBtnTarget; ?>" data-tour-name="Cebu">Book</button>
                                    </div>
                                 </div>
                              </div>
                              <div class="col">
                                 <div class="card" style="border-radius: 20px;">
                                    <img src="./public/tour3.png" class="card-img-top" alt="Tour 3">
                                    <div class="card-body-container">
                                       <h5 class="card-titles text-center mt-4" style="text-align: justify;">
                                          Siargao Joiner Tour to Sugba Lagoon, Secret Beach, & Maasin
                                          River wih Lunch
                                       </h5>
                                       <div class="card-body card-body-2col">
                                          <p class="card-text">Tour Starts: General Luna</p>
                                          <p class="card-text">Duration: 9.5 hours</p>
                                          <p class="card-text">Languages: English, Tagalog</p>
                                          <p class="card-text">Available: All year</p>
                                          <p class="card-text">Starting time: 7:30 AM</p>
                                          <p class="card-text">Ending place: General Luna </p>
                                          <p class="card-text">Difficulty: Easy</p>
                                          <p class="card-text">Minimum Age: None</p>
                                       </div>

                                    </div>
                                    <div class="card-footer custom-footer d-flex justify-content-between" style="border-bottom-right-radius: 15px; border-bottom-left-radius: 15px; background-color: rgb(2, 46, 59);">
                                       <div style="color: white;">
                                          <h4>From <span style="font-weight: bold;">2,600</span> PHP
                                          </h4>
                                       </div>
                                       <button class="btn btn-primary ml-auto book-btn" data-bs-toggle="modal" data-tour-name="Siargao" data-bs-target="<?php echo $bookBtnTarget; ?>">Book</button>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="carousel-item">
                           <div class="row pb-5">
                              <div class="col">
                                 <div class="col">
                                    <div class="card" style="border-radius: 20px;">
                                       <img src="./public/tour1.png" class="card-img-top" alt="Tour 4">
                                       <div class="card-body-container">
                                          <h5 class="card-titles text-center mt-4" style="text-align: justify;">
                                             Bohol Countryside & Chocolate Hills Adventure
                                             with Lunch & Transfers
                                          </h5>
                                          <div class="card-body card-body-2col">
                                             <p class="card-text">Tour Starts: Tagbiliran City</p>
                                             <p class="card-text">Duration: 10 hours</p>
                                             <p class="card-text">Languages: English, Tagalog</p>
                                             <p class="card-text">Available: All year</p>
                                             <p class="card-text">Starting time: 6:00 AM</p>
                                             <p class="card-text">Ending place: Tagbiliran </p>
                                             <p class="card-text">Difficulty: Easy</p>
                                             <p class="card-text">Minimum Age: None</p>
                                          </div>

                                       </div>
                                       <div class="card-footer custom-footer d-flex justify-content-between" style="border-bottom-right-radius: 15px; border-bottom-left-radius: 15px; background-color: rgb(2, 46, 59);">
                                          <div style="color: white;">
                                             <h4>From <span style="font-weight: bold;">7332</span>
                                                PHP
                                             </h4>
                                          </div>
                                          <button class="btn btn-primary ml-auto book-btn" data-bs-toggle="modal" data-bs-target="<?php echo $bookBtnTarget; ?>" data-tour-name="Bohol">Book</button>

                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col">
                                 <div class="card" style="border-radius: 20px;">
                                    <img src="./public/tour5.png" class="card-img-top" alt="Tour 5">
                                    <div class="card-body-container">
                                       <h5 class="card-titles text-center mt-4" style="text-align: justify;">
                                          Fort Santiago Historical Expess Tour in Eco-friendly Bamboo Bike
                                       </h5>
                                       <div class="card-body card-body-2col">
                                          <p class="card-text">TourTStarts: Bambike</p>
                                          <p class="card-text">Duration: 1.5 hours</p>
                                          <p class="card-text">Languages: English, Tagalog</p>
                                          <p class="card-text">Available: All year</p>
                                          <p class="card-text">Starting time: 9:30 AM</p>
                                          <p class="card-text">Ending place: Bambike</p>
                                          <p class="card-text">Difficulty: Easy</p>
                                          <p class="card-text">Minimum Age: None</p>
                                       </div>

                                    </div>
                                    <div class="card-footer custom-footer d-flex justify-content-between" style="border-bottom-right-radius: 15px; border-bottom-left-radius: 15px; background-color: rgb(2, 46, 59);">
                                       <div style="color: white;">
                                          <h4>From <span style="font-weight: bold;">3,156</span> PHP
                                          </h4>
                                       </div>
                                       <button class="btn btn-primary ml-auto book-btn" data-bs-toggle="modal" data-bs-target="<?php echo $bookBtnTarget; ?>" data-tour-name="Fort Santiago">Book</button>
                                    </div>
                                 </div>
                              </div>
                              <div class="col">
                                 <div class="card" style="border-radius: 20px;">
                                    <img src="./public/tour6.png" class="card-img-top" alt="Tour 6">
                                    <div class="card-body-container">
                                       <h5 class="card-titles text-center mt-4" style="text-align: justify;">
                                          Albay Best Views of Mayn Sighteeing & Tour with Snacks &
                                          Transfers from Legazpi
                                       </h5>
                                       <div class="card-body card-body-2col">
                                          <p class="card-text">Tour Starts: Albay</p>
                                          <p class="card-text">Duration: 10 hours</p>
                                          <p class="card-text">Languages: English, Tagalog</p>
                                          <p class="card-text">Available: All year</p>
                                          <p class="card-text">Starting time: 7:30 AM</p>
                                          <p class="card-text">Ending place: Albay </p>
                                          <p class="card-text">Difficulty: Easy</p>
                                          <p class="card-text">Minimum Age: None</p>
                                       </div>
                                    </div>
                                    <div class="card-footer custom-footer d-flex justify-content-between" style="border-bottom-right-radius: 15px; border-bottom-left-radius: 15px; background-color: rgb(2, 46, 59);">
                                       <div style="color: white;">
                                          <h4>From <span style="font-weight: bold;">3,366</span> PHP
                                          </h4>
                                       </div>
                                       <button class="btn btn-primary ml-auto book-btn" data-bs-toggle="modal" data-bs-target="<?php echo $bookBtnTarget; ?>" data-tour-name="Albay">Book</button>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <!-- Previous button -->
                     <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev" style="left: -50px; top: 44%; transform: translateY(-50%);">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                     </button>
                     <!-- Next button -->
                     <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next" style="right: -50px; top: 44%; transform: translateY(-50%); ">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                     </button>
                  </div>
               </div>
            </div>
         </div>
      </section>

      <!-- Hotels -->
      <section class="card-slider">
         <div class="container">
            <div class="row">
               <div class="col">
                  <div id="carouselExampleControlsHotels" class="carousel slide" data-bs-ride="carousel">
                     <h2 class="mb-4 mt-4" style="text-align: center;">
                        <i style="font-family: Times New Roman; font-weight: bold;">HOTELS</i>
                        <span style="font-family: Cormorant Garamond; font-weight: 100;"> & ACCOMMODATION</span>
                     </h2>
                     <div class="carousel-inner">
                        <div class="carousel-item active">
                           <div class="row pb-5">
                              <!-- Hotel 1 -->
                              <div class="col">
                                 <div class="card">
                                    <div class="star-rating">
                                       <i class="fas fa-star star" data-value="1"></i>
                                       <i class="fas fa-star star" data-value="2"></i>
                                       <i class="fas fa-star star" data-value="3"></i>
                                       <i class="fas fa-star star" data-value="4"></i>
                                       <i class="fas fa-star star" data-value="5"></i>
                                    </div>
                                    <img src="./public/boracayHotel.jpg" class="card-img-top" alt="Hotel 1">
                                    <div class="card-body-container">
                                       <h5 class="card-titles text-center mt-4">Luxury Beachfront Hotel with Pool</h5>
                                       <div class="card-body card-body-2col">
                                          <p class="card-text">Location: Boracay</p>
                                          <p class="card-text">Rating: <?php echo $ratings["Luxury Beachfront Hotel with Pool"]; ?> stars</p>
                                          <p class="card-text">Languages: English, Tagalog</p>
                                          <p class="card-text">Available: All year</p>
                                          <p class="card-text">Check-in: 2:00 PM</p>
                                          <p class="card-text">Check-out: 12:00 PM</p>
                                          <p class="card-text">Amenities: Free WiFi, Pool, Spa</p>
                                          <p class="card-text">Restrictions: Additional charges may apply for extra guests.</p>
                                       </div>
                                    </div>
                                    <div class="card-footer custom-footer d-flex justify-content-between" style="border-bottom-right-radius: 15px; border-bottom-left-radius: 15px;">
                                       <div style="color: white;">
                                          <h4>From <span style="font-weight: bold;">10,000</span>
                                             PHP/day</h4>
                                       </div>
                                       <button class="btn btn-primary ml-auto hotel-book-btn" data-bs-toggle="modal" data-bs-target="<?php echo $hotelBookBtnTarget; ?>" data-hotel-name="Luxury Beachfront Hotel">Book</button>
                                    </div>
                                 </div>
                              </div>
                              <!-- Hotel 2 -->
                              <div class="col">
                                 <div class="card">
                                    <div class="star-rating">
                                       <i class="fas fa-star star" data-value="1"></i>
                                       <i class="fas fa-star star" data-value="2"></i>
                                       <i class="fas fa-star star" data-value="3"></i>
                                       <i class="fas fa-star star" data-value="4"></i>
                                       <i class="fas fa-star star" data-value="5"></i>
                                    </div>
                                    <img src="./public/cebuHotel.jpg" class="card-img-top" alt="Hotel 2">
                                    <div class="card-body-container">
                                       <h5 class="card-titles text-center mt-4">City Center Hotel with Rooftop Pool</h5>
                                       <div class="card-body card-body-2col">
                                          <p class="card-text">Location: Cebu</p>
                                          <p class="card-text">Rating:<?php echo $ratings["City Center Hotel with Rooftop Pool"]; ?> stars</p>
                                          <p class="card-text">Languages: English, Tagalog</p>
                                          <p class="card-text">Available: All year</p>
                                          <p class="card-text">Check-in: 3:00 PM</p>
                                          <p class="card-text">Check-out: 11:00 AM</p>
                                          <p class="card-text">Amenities: Free WiFi, Rooftop Pool, Gym</p>
                                          <p class="card-text">Restrictions: No pets allowed on the property.</p>
                                       </div>
                                    </div>
                                    <div class="card-footer custom-footer d-flex justify-content-between" style="border-bottom-right-radius: 15px; border-bottom-left-radius: 15px;">
                                       <div style="color: white;">
                                          <h4>From <span style="font-weight: bold;">7,500</span>
                                             PHP/day</h4>
                                       </div>
                                       <button class="btn btn-primary ml-auto hotel-book-btn" data-bs-toggle="modal" data-bs-target="<?php echo $hotelBookBtnTarget; ?>" data-hotel-name="City Center Hotel with Rooftop Pool">Book</button>
                                    </div>
                                 </div>
                              </div>
                              <!-- Hotel 3 -->
                              <div class="col">
                                 <div class="card">
                                    <div class="star-rating">
                                       <i class="fas fa-star star" data-value="1"></i>
                                       <i class="fas fa-star star" data-value="2"></i>
                                       <i class="fas fa-star star" data-value="3"></i>
                                       <i class="fas fa-star star" data-value="4"></i>
                                       <i class="fas fa-star star" data-value="5"></i>
                                    </div>
                                    <img src="./public/siargaoHotel.jpg" class="card-img-top" alt="Hotel 3">
                                    <div class="card-body-container">
                                       <h5 class="card-titles text-center mt-4">Boutique Hotel with Garden View</h5>
                                       <div class="card-body card-body-2col">
                                          <p class="card-text">Location: Siargao</p>
                                          <p class="card-text">Rating: <?php echo $ratings["Boutique Hotel with Garden View"]; ?> stars</p>
                                          <p class="card-text">Languages: English, Tagalog</p>
                                          <p class="card-text">Available: All year</p>
                                          <p class="card-text">Check-in: 2:00 PM</p>
                                          <p class="card-text">Check-out: 12:00 PM</p>
                                          <p class="card-text">Amenities: Free WiFi, Garden, Breakfast
                                             included</p>
                                          <p class="card-text">Restrictions: Non-refundable deposit required for reservations.</p>
                                       </div>
                                    </div>
                                    <div class="card-footer custom-footer d-flex justify-content-between" style="border-bottom-right-radius: 15px; border-bottom-left-radius: 15px;">
                                       <div style="color: white;">
                                          <h4>From <span style="font-weight: bold;">5,000</span>
                                             PHP/day</h4>
                                       </div>
                                       <button class="btn btn-primary ml-auto hotel-book-btn" data-bs-toggle="modal" data-bs-target="<?php echo $hotelBookBtnTarget; ?>" data-hotel-name="Boutique Hotel with Garden View">Book</button>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="carousel-item">
                           <div class="row pb-5">
                              <!-- Hotel 4 -->
                              <div class="col">
                                 <div class="card">
                                    <div class="star-rating">
                                       <i class="fas fa-star star" data-value="1"></i>
                                       <i class="fas fa-star star" data-value="2"></i>
                                       <i class="fas fa-star star" data-value="3"></i>
                                       <i class="fas fa-star star" data-value="4"></i>
                                       <i class="fas fa-star star" data-value="5"></i>
                                    </div>
                                    <img src="./public/boholHotel.jpg" class="card-img-top" alt="Hotel 4">
                                    <div class="card-body-container">
                                       <h5 class="card-titles text-center mt-4">Beach Resort with Private Villas</h5>
                                       <div class="card-body card-body-2col">
                                          <p class="card-text">Location: Bohol</p>
                                          <p class="card-text">Rating: <?php echo $ratings["Beach Resort with Private Villas"]; ?> stars</p>
                                          <p class="card-text">Languages: English, Tagalog</p>
                                          <p class="card-text">Available: All year</p>
                                          <p class="card-text">Check-in: 2:00 PM</p>
                                          <p class="card-text">Check-out: 12:00 PM</p>
                                          <p class="card-text">Amenities: Free WiFi, Private Villas,
                                             Spa</p>
                                          <p class="card-text">Restrictions: Smoking is prohibited in all indoor areas of the hotel.</p>
                                       </div>
                                    </div>
                                    <div class="card-footer custom-footer d-flex justify-content-between" style="border-bottom-right-radius: 15px; border-bottom-left-radius: 15px;">
                                       <div style="color: white;">
                                          <h4>From <span style="font-weight: bold;">12,000</span>
                                             PHP/day</h4>
                                       </div>
                                       <button class="btn btn-primary ml-auto hotel-book-btn" data-bs-toggle="modal" data-bs-target="<?php echo $hotelBookBtnTarget; ?>" data-hotel-name="Beach Resort with Private Villas">Book</button>
                                    </div>
                                 </div>
                              </div>
                              <!-- Hotel 5 -->
                              <div class="col">
                                 <div class="card">
                                    <div class="star-rating">
                                       <i class="fas fa-star star" data-value="1"></i>
                                       <i class="fas fa-star star" data-value="2"></i>
                                       <i class="fas fa-star star" data-value="3"></i>
                                       <i class="fas fa-star star" data-value="4"></i>
                                       <i class="fas fa-star star" data-value="5"></i>
                                    </div>
                                    <img src="./public/manilaHotel.jpg" class="card-img-top" alt="Hotel 5">
                                    <div class="card-body-container">
                                       <h5 class="card-titles text-center mt-4">Historical Hotel in the Heart of Manila</h5>
                                       <div class="card-body card-body-2col">
                                          <p class="card-text">Location: Manila</p>
                                          <p class="card-text">Rating: <?php echo $ratings["Historical Hotel in the Heart of Manila"]; ?> stars</p>
                                          <p class="card-text">Languages: English, Tagalog</p>
                                          <p class="card-text">Available: All year</p>
                                          <p class="card-text">Check-in: 3:00 PM</p>
                                          <p class="card-text">Check-out: 11:00 AM</p>
                                          <p class="card-text">Amenities: Free WiFi, Gym, Restaurant
                                          </p>
                                          <p class="card-text">Restrictions: Must be at least 18 years old to check in.</p>
                                       </div>
                                    </div>
                                    <div class="card-footer custom-footer d-flex justify-content-between" style="border-bottom-right-radius: 15px; border-bottom-left-radius: 15px;">
                                       <div style="color: white;">
                                          <h4>From <span style="font-weight: bold;">8,500</span>
                                             PHP/day</h4>
                                       </div>
                                       <button class="btn btn-primary ml-auto hotel-book-btn" data-bs-toggle="modal" data-bs-target="<?php echo $hotelBookBtnTarget; ?>" data-hotel-name="Historical Hotel in the Heart of Manila">Book</button>
                                    </div>
                                 </div>
                              </div>
                              <!-- Hotel 6 -->
                              <div class="col">
                                 <div class="card">
                                    <div class="star-rating">
                                       <i class="fas fa-star star" data-value="1"></i>
                                       <i class="fas fa-star star" data-value="2"></i>
                                       <i class="fas fa-star star" data-value="3"></i>
                                       <i class="fas fa-star star" data-value="4"></i>
                                       <i class="fas fa-star star" data-value="5"></i>
                                    </div>
                                    <img src="./public/albayHotel.jpg" class="card-img-top" alt="Hotel 6">
                                    <div class="card-body-container">
                                       <h5 class="card-titles text-center mt-4">Mountain Resort with Scenic Views</h5>
                                       <div class="card-body card-body-2col">
                                          <p class="card-text">Location: Albay</p>
                                          <p class="card-text">Rating: <?php echo $ratings["Mountain Resort with Scenic Views"]; ?> stars</p>
                                          <p class="card-text">Languages: English, Tagalog</p>
                                          <p class="card-text">Available: All year</p>
                                          <p class="card-text">Check-in: 2:00 PM</p>
                                          <p class="card-text">Check-out: 12:00 PM</p>
                                          <p class="card-text">Amenities: Free WiFi, Scenic Views,
                                             Breakfast included</p>
                                          <p class="card-text">Restrictions: Valid ID and a credit card are required at check-in for incidentals.</p>
                                       </div>
                                    </div>
                                    <div class="card-footer custom-footer d-flex justify-content-between" style="border-bottom-right-radius: 15px; border-bottom-left-radius: 15px;">
                                       <div style="color: white;">
                                          <h4>From <span style="font-weight: bold;">6,000</span>
                                             PHP/day</h4>
                                       </div>
                                       <button class="btn btn-primary ml-auto hotel-book-btn" data-bs-toggle="modal" data-bs-target="<?php echo $hotelBookBtnTarget; ?>" data-hotel-name="Mountain Resort with Scenic Views">Book</button>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- Previous button -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControlsHotels" data-bs-slide="prev" style="left: -50px; top: 40%; transform: translateY(-50%);">
                           <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                           <span class="visually-hidden">Previous</span>
                        </button>
                        <!-- Next button -->
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControlsHotels" data-bs-slide="next" style="right: -50px; top: 40%; transform: translateY(-50%);">
                           <span class="carousel-control-next-icon" aria-hidden="true"></span>
                           <span class="visually-hidden">Next</span>
                        </button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>

      <!-- star functionality -->
      <script>
         document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            stars.forEach(star => {
               star.addEventListener('click', function() {
                  const value = this.getAttribute('data-value');
                  const starRating = this.parentElement;
                  const allStars = starRating.querySelectorAll('.star');
                  const hotelName = starRating.parentElement.querySelector('.card-titles').textContent;

                  allStars.forEach(s => s.classList.remove('selected'));
                  for (let i = 0; i < value; i++) {
                     allStars[i].classList.add('selected');
                  }

                  // Update modal content
                  document.getElementById('hotelName').textContent = hotelName;
                  document.getElementById('hotelNameInput').value = hotelName;
                  document.getElementById('starRating').textContent = value;
                  document.getElementById('starRatingInput').value = value;

                  // Show modal
                  const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
                  const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                  const ratingModal = new bootstrap.Modal(document.getElementById('ratingModal'));

                  // Show appropriate modal
                  if (isLoggedIn) {
                     ratingModal.show();
                  } else {
                     loginModal.show();
                  }
               });
            });
         });


         document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['rating_success']) && $_SESSION['rating_success'] === true) : ?>
               // Show success modal
               var successModal = new bootstrap.Modal(document.getElementById('successModal'));
               successModal.show();
               <?php unset($_SESSION['rating_success']); // Clear the session variable 
               ?>
            <?php endif; ?>
         });
      </script>
   </main>

   <!-- Rating Modal -->
   <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <form id="ratingForm" method="post" action="submit_rating.php">
               <div class="modal-header">
                  <h5 class="modal-title" id="ratingModalLabel">Rate this Hotel</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                  <p>Hotel: <span id="hotelName"></span></p>
                  <input type="hidden" name="hotelName" id="hotelNameInput">
                  <p>Rating: <span id="starRating"></span> stars</p>
                  <input type="hidden" name="stars" id="starRatingInput">
                  <input type="hidden" name="userID" value="<?php echo htmlspecialchars($userID); ?>">
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Submit Rating</button>
               </div>
            </form>
         </div>
      </div>
   </div>

   <!-- Success Modal -->
   <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="successModalLabel">Rating Submitted</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <p>Your rating has been successfully submitted. Thank you for your feedback!</p>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
         </div>
      </div>
   </div>



   <!-- Scroll to top button -->
   <button class="scroll-top-btn">
      <i class="fas fa-arrow-up"></i>
   </button>

   <!-- Footer -->
   <footer>
      <div class="footer-container">
         <div class="footer-column">
            <a href="../index.php"><img src="./public/footerlogo.png" alt="footer-logo"></a>
            <p class="footer-text"> © 2023 TARA. All Rights Reserved.</p>
         </div>
         <div class="footer-column">
            <h3 class="footer-title">Quick Links</h3>
            <ul class="footer-links">
               <li><a href="../index.php">Home</a></li>
               <li><a href="../calle/index.php">Tourist Attractions</a></li>
               <li><a href="../ati-atihan/index.php">Festivals</a></li>
               <li><a href="../adobo/index.php">Foods</a></li>
               <li><a href="../services/index.php">Services</a></li>
               <li><a href="../about/index.php">About</a></li>
               <li><a href="<?php echo $modalTarget; ?>" data-bs-toggle="modal">Account</a></li>
            </ul>
         </div>
         <div class="footer-column">
            <h3 class="footer-title">Contact Us</h3>
            <p class="footer-contact">Email: info@tara.com</p>
            <p class="footer-contact">Phone: +63 123 456 7890</p>
            <p class="footer-contact">Address: 123 Main Street, Manila, Philippines</p>
         </div>
         <div class="footer-column">
            <h3 class="footer-title">Follow Us</h3>
            <div class="footer-social">
               <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                  <img class="footer-social-icon" alt="Facebook" src="./public/facebook.png">
                  <span style="color: white; text-decoration: none; cursor: pointer;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Facebook</span>
               </a>
            </div>
            <div class="footer-social">
               <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                  <img class="footer-social-icon" alt="Instagram" src="./public/instagram.png">
                  <span style="color: white; text-decoration: none; cursor: pointer;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Instagram</span>
               </a>
            </div>
            <div class="footer-social">
               <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                  <img class="footer-social-icon" alt="Twitter" src="./public/twitter.png">
                  <span style="color: white; text-decoration: none; cursor: pointer;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Twitter</span>
               </a>
            </div>
            <div class="footer-social">
               <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                  <img class="footer-social-icon" alt="YouTube" src="./public/youtube.png">
                  <span style="color: white; text-decoration: none; cursor: pointer;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Youtube</span>
               </a>
            </div>
         </div>

      </div>
   </footer>

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





   <!-- Cancellation Modals -->

   <!-- Cancel Booking Confirmation Modal -->
   <div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="cancelBookingModalLabel">Confirm Cancellation</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <p>Are you sure you want to cancel this booking?</p>
               <form id="cancelBookingForm" action="actions.php" method="POST">
                  <input type="hidden" name="bookingID" id="cancelBookingID">
               </form>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#accountModal">No</button>
               <button type="submit" form="cancelBookingForm" class="btn btn-danger" name="confirmTourCancel">Yes,
                  Cancel</button>
            </div>
         </div>
      </div>
   </div>

   <!-- Cancel Hotel Booking Confirmation Modal -->
   <div class="modal fade" id="cancelhotelBookingModal" tabindex="-1" aria-labelledby="cancelhotelBookingModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="cancelhotelBookingModalLabel">Confirm Hotel Booking Cancellation</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <p>Are you sure you want to cancel this hotel booking?</p>
               <form id="cancelHotelBookingForm" action="actions.php" method="POST">
                  <input type="hidden" name="hotelID" id="cancelHotelBookingID">
               </form>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
               <button type="submit" form="cancelHotelBookingForm" class="btn btn-danger" name="confirmHotelCancel">Yes,
                  Cancel</button>
            </div>
         </div>
      </div>
   </div>

   <!-- Cancel Car Booking Confirmation Modal -->
   <div class="modal fade" id="cancelcarBookingModal" tabindex="-1" aria-labelledby="cancelcarBookingModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="cancelcarBookingModalLabel">Confirm Car Rental Booking Cancellation</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <p>Are you sure you want to cancel this car rental booking?</p>
               <form id="cancelCarBookingForm" action="actions.php" method="POST">
                  <input type="hidden" name="carID" id="cancelCarBookingID">
               </form>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#accountModal">No</button>
               <button type="submit" form="cancelCarBookingForm" class="btn btn-danger" name="confirmCarCancel">Yes,
                  Cancel</button>
            </div>
         </div>
      </div>
   </div>

   <!-- Cancellation Modals -->

   <!-- Sign Up Modal -->
   <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Sign Up</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form class="modal-body" action="actions.php" method="POST">
                  <div class="row">
                     <div class="col">
                        <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Last Name" required>
                     </div>
                     <div class="col">
                        <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First Name" required>
                     </div>
                  </div>
                  <br>
                  <input type="email" class="form-control" id="mobileOrEmail" name="mobileOrEmail" placeholder="Email Address" required><br>

                  <input type="password" name="password" class="form-control password-input" id="signupPassword" placeholder="Password" required>

                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" id="show-signup-password" data-checkbox-for="signupPassword">
                     <label class="form-check-label" for="show-password" style="font-size: 13px">Show Password</label>
                  </div><br>

                  <?php if (isset($_SESSION['account_exist'])) : ?>
                     <div class="alert alert-danger" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Error:">
                           <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        Account Already Exists
                     </div>
                     <script>
                        $(document).ready(function() {
                           $('#signupModal').modal('show');
                        });
                     </script>
                     <?php unset($_SESSION['account_exist']); ?>
                  <?php endif; ?>


                  <?php if (isset($_SESSION['password_error'])) : ?>
                     <div class="alert alert-danger" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Error:">
                           <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        Password should be at least 8 characters.
                     </div>
                     <script>
                        $(document).ready(function() {
                           $('#signupModal').modal('show');
                        });
                     </script>
                     <?php unset($_SESSION['password_error']); ?>
                  <?php endif; ?>

                  <?php if (isset($_SESSION['email_custom_error'])) : ?>
                     <div class="alert alert-danger" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Error:">
                           <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        Email cannot start with '@'.
                     </div>
                     <script>
                        $(document).ready(function() {
                           $('#signupModal').modal('show');
                        });
                     </script>
                     <?php unset($_SESSION['email_custom_error']); ?>
                  <?php endif; ?>

                  <?php if (isset($_SESSION['email_format_error'])) : ?>
                     <div class="alert alert-danger" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Error:">
                           <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        Email must end with '.com'.
                     </div>
                     <script>
                        $(document).ready(function() {
                           $('#signupModal').modal('show');
                        });
                     </script>
                     <?php unset($_SESSION['email_format_error']); ?>
                  <?php endif; ?>

                  <?php if (isset($_SESSION['email_domain_error'])) : ?>
                     <div class="alert alert-danger" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Error:">
                           <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        Email domain must be 'gmail.com', 'email.com', or 'yahoo.com'.
                     </div>
                     <script>
                        $(document).ready(function() {
                           $('#signupModal').modal('show');
                        });
                     </script>
                     <?php unset($_SESSION['email_domain_error']); ?>
                  <?php endif; ?>

                  <div class="text-muted" style="font-size: 12px">
                     By clicking Sign Up, you agree to our Terms, Privacy Policy and Cookie Policy. You may receive SMS
                     Notifications from us and can opt out any time.
                  </div><br>

                  <!-- Sign up -->
                  <div class="modal-footer justify-content-center">
                     <button type="submit" class="btn changecol" name="confirmSignup" style="width: 150px; background-color: #00A400; color: white;">Sign Up</button>
                  </div>

                  <!-- Already have an account? -->
                  <p class="text-center">
                     <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal" style="text-decoration: none; color: #385898;" onmouseover="this.style.textDecoration='underline';" onmouseout="this.style.textDecoration='none';">
                        Already have an account?
                     </a>
                  </p>

               </form>
            </div>
         </div>
      </div>
   </div>

   <!-- Login Modal -->
   <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="loginModalLabel">Log In</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form class="modal-body" action="actions.php" method="POST">
                  <input type="text" class="form-control" id="loginMobileOrEmail" name="mobileOrEmail" placeholder="Mobile Number or Email" required style="background-color: #f0f0f0;"><br>

                  <input type="password" name="password" minlength="8" class="form-control password-input" id="loginPassword" placeholder="Password" required style="background-color: #f0f0f0;">

                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" id="show-login-password" data-checkbox-for="loginPassword">
                     <label class="form-check-label" for="show-login-password" style="font-size: 13px">Show
                        Password</label>
                  </div><br>

                  <!-- Error message -->
                  <?php if (isset($_SESSION['error_message'])) : ?>
                     <div class="alert alert-danger d-flex align-items-center" role="alert" id="loginErrorMessage">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Error:">
                           <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        <div>
                           <?php echo $_SESSION['error_message']; ?>
                        </div>
                     </div>
                     <script>
                        $(document).ready(function() {
                           $('#loginModal').modal('show'); // Ensure the modal stays open
                        });

                        document.addEventListener('DOMContentLoaded', function() {
                           const loginInputs = document.querySelectorAll('#loginMobileOrEmail, #loginPassword');
                           loginInputs.forEach(function(input) {
                              input.addEventListener('input', function() {
                                 document.getElementById('loginErrorMessage').style.display = 'none';
                              });
                           });
                        });
                     </script>
                     <?php unset($_SESSION['error_message']); ?>
                  <?php endif; ?>

                  <!-- Password reset success alert -->
                  <?php if (isset($_SESSION['password_change_success']) && $_SESSION['password_change_success']) : ?>
                     <div class="alert alert-success d-flex align-items-center" role="alert" id="passwordResetSuccessMessage">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:">
                           <use xlink:href="#check-circle-fill" />
                        </svg>
                        <div>
                           Password reset successfully!
                        </div>
                     </div>
                     <script>
                        $(document).ready(function() {
                           $('#loginModal').modal('show'); // Ensure the modal stays open
                        });

                        // Function to hide success message on new input in login form
                        $(document).ready(function() {
                           $('#loginMobileOrEmail, #loginPassword').on('input', function() {
                              $('#passwordResetSuccessMessage').fadeOut(); // Fade out the success message
                              setTimeout(function() {
                                 $('#passwordResetSuccessMessage').remove(); // Remove the success message from DOM after fadeout
                              }, 300); // Adjust this timing as needed
                           });
                        });
                     </script>
                     <?php unset($_SESSION['password_change_success']); ?>
                  <?php endif; ?>

                  <p class="text-muted" style="font-size: 13px;">Welcome back to TARA! Log in
                     to explore the best
                     tourist spots in the Philippines and book your
                     next adventure.
                  </p>

                  <p class="text-center">
                     <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" style="text-decoration: none; color: #385898;" onmouseover="this.style.textDecoration='underline';" onmouseout="this.style.textDecoration='none';">
                        Forgot Password?
                     </a>
                  </p>


                  <div class="modal-footer justify-content-center">
                     <button type="submit" class="btn changecol" name="confirmLogin" style="width: 150px; background-color: #00A400; color: white;">Log In</button>
                  </div>

                  <p class="text-center">
                     <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#signupModal" style="text-decoration: none; color: #385898;" onmouseover="this.style.textDecoration='underline';" onmouseout="this.style.textDecoration='none';">
                        Don't have an account? Sign Up
                     </a>
                  </p>

               </form>
            </div>
         </div>
      </div>
   </div>

   <!-- Change Password Modal -->
   <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form id="changePasswordForm" action="actions.php" method="POST">
                  <div class="mb-3">
                     <label for="newPassword" class="form-label">New Password:</label>
                     <div class="input-group">
                        <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="Enter your new password" required>
                     </div>
                     <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="showNewPassword">
                        <label class="form-check-label" for="showNewPassword" style="font-size: 13px;">
                           Show Password
                        </label>
                     </div>
                  </div>
                  <div class="mb-3">
                     <label for="confirmPassword" class="form-label">Confirm Password:</label>
                     <div class="input-group">
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm your new password" required>
                     </div>
                     <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="showConfirmPassword">
                        <label class="form-check-label" for="showConfirmPassword" style="font-size: 13px;">
                           Show Password
                        </label>
                     </div>
                  </div>

                  <!-- Alert for password length error -->
                  <?php if (isset($_SESSION['password_length_error']) && $_SESSION['password_length_error']) : ?>
                     <div class="alert alert-danger mt-2" role="alert">
                        Password must be at least 8 characters long.
                     </div>
                     <script>
                        $(document).ready(function() {
                           $("#changePasswordModal").modal("show");
                        });
                     </script>
                     <?php unset($_SESSION['password_length_error']); ?>
                  <?php endif; ?>

                  <!-- Alert for password mismatch error -->
                  <?php if (isset($_SESSION['password_mismatch_error']) && $_SESSION['password_mismatch_error']) : ?>
                     <div class="alert alert-danger mt-2" role="alert">
                        Passwords do not match. Please try again.
                     </div>
                     <script>
                        $(document).ready(function() {
                           $("#changePasswordModal").modal("show");
                        });
                     </script>
                     <?php unset($_SESSION['password_mismatch_error']); ?>
                  <?php endif; ?>

                  <!-- Alert for password same as current password error -->
                  <?php if (isset($_SESSION['password_same_as_current_error']) && $_SESSION['password_same_as_current_error']) : ?>
                     <div class="alert alert-danger mt-2" role="alert">
                        New password must be different from the current password.
                     </div>
                     <script>
                        $(document).ready(function() {
                           $("#changePasswordModal").modal("show");
                        });
                     </script>
                     <?php unset($_SESSION['password_same_as_current_error']); ?>
                  <?php endif; ?>
            </div>
            <div class="modal-footer justify-content-center">
               <button type="submit" class="btn btn-primary w-100" name="changePassword" style="background-color: #00A400; border: none;">Submit</button>
            </div>
            </form>
         </div>
      </div>
   </div>

   <!-- Script for Show Password Toggle -->
   <script>
      $(document).ready(function() {
         // Toggle for New Password field
         $('#showNewPassword').change(function() {
            var isChecked = $(this).prop('checked');
            var input = $('#newPassword');

            if (isChecked) {
               input.attr('type', 'text');
            } else {
               input.attr('type', 'password');
            }
         });

         // Toggle for Confirm Password field
         $('#showConfirmPassword').change(function() {
            var isChecked = $(this).prop('checked');
            var input = $('#confirmPassword');

            if (isChecked) {
               input.attr('type', 'text');
            } else {
               input.attr('type', 'password');
            }
         });
      });
   </script>

   <!-- Forgot Password Modal -->
   <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="forgotPasswordModalLabel">Find your account</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form id="forgotPasswordForm" action="actions.php" method="POST">
                  <div class="mb-3">
                     <label for="email" class="form-label">
                        Please enter your email to search for your account.</label>
                     <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required style="background-color: #f0f0f0;">
                  </div>

                  <!-- Alert for email not found -->
                  <?php if (isset($_SESSION['forgot_password_error']) && $_SESSION['forgot_password_error']) : ?>
                     <div class="alert alert-danger" role="alert">
                        Email does not exist. Please try again.
                     </div>
                     <script>
                        $(document).ready(function() {
                           $("#forgotPasswordModal").modal("show");
                        });
                     </script>
                     <?php unset($_SESSION['forgot_password_error']); ?>
                  <?php endif; ?>

                  <!-- Submit Button -->
                  <div class="modal-footer justify-content-center">
                     <button type="submit" class="btn btn-primary w-100" name="confirmForgotPassword" style="background-color: #00A400; border: none;">Search</button>
                  </div>

                  <!-- Back to login -->
                  <p class="text-center">
                     <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal" style="text-decoration: none; color: #385898;" onmouseover="this.style.textDecoration='underline';" onmouseout="this.style.textDecoration='none';">
                        Back to Login
                     </a>
                  </p>

               </form>
            </div>
         </div>
      </div>
   </div>

   <!-- Change Password Scripts -->
   <script>
      $(document).ready(function() {
         // Show change password modal if flagged
         <?php if (isset($_SESSION['show_change_password_modal']) && $_SESSION['show_change_password_modal']) : ?>
            $("#changePasswordModal").modal("show");
            <?php unset($_SESSION['show_change_password_modal']); ?>
         <?php endif; ?>

         // Show forgot password modal if flagged
         <?php if (isset($_SESSION['forgot_password_error']) && $_SESSION['forgot_password_error']) : ?>
            $("#forgotPasswordModal").modal("show");
            <?php unset($_SESSION['forgot_password_error']); ?>
         <?php endif; ?>

         // Remove forgot password alert on new input
         $('#forgotPasswordForm').on('input', '#email', function() {
            $('#forgotPasswordForm .alert').alert('close'); // Close any existing alerts within forgotPasswordForm
         });

         // Remove change password alert on new input
         $('#changePasswordForm').on('input', '#newPassword, #confirmPassword', function() {
            $('#changePasswordForm .alert').alert('close'); // Close any existing alerts within changePasswordForm
         });

         // Show login modal after successful password change
         <?php if (isset($_SESSION['password_change_success']) && $_SESSION['password_change_success']) : ?>
            $("#loginModal").modal("show");
            <?php unset($_SESSION['password_change_success']); ?>
         <?php endif; ?>
      });
   </script>

   <?php if (isset($_SESSION["user"])) : ?>

      <!-- Edit Profile Modal -->
      <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                  <form id="editProfileForm" action="actions.php" method="POST" enctype="multipart/form-data">
                     <div class="mb-3">
                        <label for="e-firstName" class="edit-form-label">First Name</label>
                        <input type="text" class="form-control" id="e-firstName" name="firstName" value="<?php echo htmlspecialchars($_SESSION['user']['FirstName']); ?>" required>
                     </div>
                     <div class="mb-3">
                        <label for="e-lastName" class="edit-form-label">Last Name</label>
                        <input type="text" class="form-control" id="e-lastName" name="lastName" value="<?php echo htmlspecialchars($_SESSION['user']['LastName']); ?>" required>
                     </div>
                     <div class="mb-3">
                        <label for="e-email" class="edit-form-label">Email</label>
                        <input type="email" class="form-control" id="e-email" name="email" value="<?php echo htmlspecialchars($_SESSION['user']['Email']); ?>" required>
                     </div>
                     <div class="mb-3">
                        <label for="image" class="edit-form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" required>
                     </div>
                     <div>
                        <label for="e-password" class="edit-form-label">Password</label>
                        <input type="password" class="form-control" id="e-password" name="password" required placeholder="Enter new password...">
                     </div>
                     <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="show-add-password">
                        <label class="form-check-label" for="show-add-password" style="font-size: 13px;">Show
                           Password</label>
                     </div><br>

                     <script>
                        // Function to toggle password visibility
                        $(document).ready(function() {
                           $('#show-add-password').click(function() {
                              var passwordField = $('#e-password');
                              var fieldType = passwordField.attr('type');
                              if (fieldType === 'password') {
                                 passwordField.attr('type', 'text');
                              } else {
                                 passwordField.attr('type', 'password');
                              }
                           });
                        });
                     </script>
                     <input type="submit" id="editProfile" name="editProfile" hidden>
                  </form>

                  <?php if (isset($_SESSION['editProfileErrorMessage'])) : ?>
                     <div class="alert alert-danger d-flex align-items-center" role="alert" id="loginErrorMessage">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Error:">
                           <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        <div>
                           <?php echo $_SESSION['editProfileErrorMessage']; ?>
                        </div>
                     </div>
                     <?php unset($_SESSION['editProfileErrorMessage']); ?>
                     <script>
                        $(document).ready(function() {
                           $('#editProfileModal').modal('show');
                        });
                     </script>
                  <?php endif; ?>

                  <?php if (isset($_SESSION['invalid_file'])) : ?>
                     <div class="alert alert-danger d-flex align-items-center" role="alert" id="loginErrorMessage">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Error:">
                           <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        <div>
                           <?php echo $_SESSION['invalid_file']; ?>
                        </div>
                     </div>
                     <?php unset($_SESSION['invalid_file']); ?>
                     <script>
                        $(document).ready(function() {
                           $('#editProfileModal').modal('show');
                        });
                     </script>
                  <?php endif; ?>

                  <?php if (isset($_SESSION['edit_password_error'])) : ?>
                     <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Error:">
                           <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        <div>
                           Password should be at least 8 characters.
                        </div>
                     </div>
                     <script>
                        $(document).ready(function() {
                           $('#editProfileModal').modal('show');
                        });
                     </script>
                     <?php unset($_SESSION['edit_password_error']); ?>
                  <?php endif; ?>

               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" id="saveChangesBtn">Save changes</button>
               </div>
            </div>
         </div>
      </div>

      <!-- Account Modal -->
      <div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-dialog-scrollable  " style="max-width: 550px;">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="accountModalLabel">Welcome,
                     <?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['FirstName']) : 'Guest'; ?>!
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                  <ul class="nav nav-tabs" id="myTab" role="tablist">
                     <!-- Nav tabs for different sections -->
                     <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Profile</button>
                     </li>
                     <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tours-tickets-tab" data-bs-toggle="tab" data-bs-target="#tours-tickets" type="button" role="tab" aria-controls="tours-tickets" aria-selected="false">Tours and Tickets</button>
                     </li>
                     <li class="nav-item" role="presentation">
                        <button class="nav-link" id="hotels-tab" data-bs-toggle="tab" data-bs-target="#hotels" type="button" role="tab" aria-controls="hotels" aria-selected="false">Hotels</button>
                     </li>
                     <li class="nav-item" role="presentation">
                        <button class="nav-link" id="car-rentals-tab" data-bs-toggle="tab" data-bs-target="#car-rentals" type="button" role="tab" aria-controls="car-rentals" aria-selected="false">Car
                           Rentals</button>
                     </li>
                  </ul>
                  <div class="tab-content" id="myTabContent">

                     <!-- Profile Tab -->
                     <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <br>
                        <div class="card mb-3">
                           <div class="card-body">
                              <div class="profile-container">
                                 <img src="<?php echo "../" . htmlspecialchars($user["UserImage"]); ?>" alt="<?php echo htmlspecialchars($user["Email"]); ?>">
                                 <div class="user-info-container">
                                    <div class="user-info-fullName">
                                       <?php echo htmlspecialchars($user["FirstName"]) . " " . htmlspecialchars($user["LastName"]); ?>
                                    </div>
                                    <div class="user-email">
                                       Email: <?php echo htmlspecialchars($user["Email"]); ?>
                                    </div>
                                    <div class="bookings-container">
                                       <div class="booking-count-container">
                                          <h6 class="booking-label">Tour Bookings</h6>
                                          <div class="booking-count">
                                             <?php echo htmlspecialchars(count($tours)); ?>
                                          </div>
                                       </div>
                                       <div class="booking-count-container">
                                          <h6 class="booking-label">Hotel Bookings</h6>
                                          <div class="booking-count">
                                             <?php echo htmlspecialchars(count($hotelBookings)); ?>
                                          </div>
                                       </div>
                                       <div class="booking-count-container">
                                          <h6 class="booking-label">Car Rentals</h6>
                                          <div class="booking-count">
                                             <?php echo htmlspecialchars(count($carRentals)); ?>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-auto">
                                       <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit</button>
                                       <button id="logout-btn" type="button" class="btn btn-danger" onclick="logout()">Logout</button>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Tours and Tickets Tab -->
                     <div class="tab-pane fade" id="tours-tickets" role="tabpanel" aria-labelledby="tours-tickets-tab">
                        <br>
                        <?php if (count($tours) > 0) : ?>
                           <?php foreach ($tours as $row) : ?>
                              <div class="card mb-3">
                                 <div class="card-body">
                                    <div class="row">
                                       <div class="col">
                                          <h5 class="card-title">Booking Details</h5>
                                          <p class="card-text mt-3">Place: <?php echo htmlspecialchars($row['Place']); ?></p>
                                          <p class="card-text">Date: <?php echo htmlspecialchars($row['Date']); ?></p>
                                          <p class="card-text">Adult: <?php echo htmlspecialchars($row['Adult']); ?></p>
                                          <p class="card-text">Children: <?php echo htmlspecialchars($row['Children']); ?>
                                          </p>
                                          <p class="card-text">Payment Method:
                                             <?php echo htmlspecialchars($row['Payment']); ?>
                                          </p>
                                          <p class="card-text">Total Price:
                                             <?php echo formatToPHP(htmlspecialchars($row['TotalPrice'])); ?>
                                          </p>
                                       </div>
                                       <div class="col-auto">
                                          <button type="button" class="btn btn-primary cancel-booking-btn" name="confirmCancel" data-bs-toggle="modal" data-bs-target="#cancelBookingModal" data-booking-id="<?php echo htmlspecialchars($row['BookingID']); ?>">Cancel</button>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           <?php endforeach; ?>
                        <?php else : ?>
                           <p>No Bookings found.</p>
                        <?php endif; ?>
                     </div>

                     <!-- Hotel Bookings Tab -->
                     <div class="tab-pane fade" id="hotels" role="tabpanel" aria-labelledby="hotels-tab">
                        <br>
                        <?php if (count($hotelBookings) > 0) : ?>
                           <?php foreach ($hotelBookings as $row) : ?>
                              <div class="card mb-3">
                                 <div class="card-body">
                                    <div class="row">
                                       <div class="col">
                                          <h5 class="card-title">Hotel Booking Details</h5>
                                          <p class="card-text mt-3">Destination:
                                             <?php echo htmlspecialchars($row['Destination']); ?>
                                          </p>
                                          <p class="card-text">Check-in Date:
                                             <?php echo htmlspecialchars($row['Check-in']); ?>
                                          </p>
                                          <p class="card-text">Check-out Date:
                                             <?php echo htmlspecialchars($row['Check-out']); ?>
                                          </p>
                                          <p class="card-text">Room Type:
                                             <?php echo htmlspecialchars($row['RoomType']); ?>
                                          </p>
                                          <p class="card-text">Payment Method:
                                             <?php echo htmlspecialchars($row['Payment']); ?>
                                          </p>
                                          <p class="card-text">Total Price:
                                             <?php echo formatToPHP(htmlspecialchars($row['TotalPrice'])); ?>
                                          </p>
                                       </div>
                                       <div class="col-auto">
                                          <button type="button" class="btn btn-primary cancel-hotel-booking-btn" data-bs-toggle="modal" data-bs-target="#cancelhotelBookingModal" data-booking-id="<?php echo htmlspecialchars($row['HotelID']); ?>">Cancel</button>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           <?php endforeach ?>
                        <?php else : ?>
                           <p>No hotel bookings found.</p>
                        <?php endif; ?>
                     </div>

                     <!-- Car Rentals Tab -->
                     <div class="tab-pane fade" id="car-rentals" role="tabpanel" aria-labelledby="car-rentals-tab">
                        <br>
                        <?php if (count($carRentals) > 0) : ?>
                           <?php foreach ($carRentals as $row) : ?>
                              <div class="card mb-3">
                                 <div class="card-body">
                                    <div class="row">
                                       <div class="col">
                                          <h5 class="card-title">Car Rental Details</h5>
                                          <p class="card-text mt-3">Pick up location:
                                             <?php echo htmlspecialchars($row['Pickup']); ?>
                                          </p>
                                          <p class="card-text">Drop off location:
                                             <?php echo htmlspecialchars($row['Dropoff']); ?>
                                          </p>
                                          <p class="card-text">Type of Car: <?php echo htmlspecialchars($row['CarType']); ?></p>
                                          <p class="card-text">Date: <?php echo htmlspecialchars($row['Date']); ?></p>

                                       </div>
                                       <div class="col-auto">
                                          <button type="button" class="btn btn-primary cancel-car-rental-btn" data-bs-toggle="modal" data-bs-target="#cancelcarBookingModal" data-rental-id="<?php echo htmlspecialchars($row['CarID']); ?>">Cancel</button>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           <?php endforeach; ?>
                        <?php else : ?>
                           <p>No car rentals found.</p>
                        <?php endif; ?>
                     </div>

                     <!-- JavaScript for logout -->
                     <script>
                        function logout() {
                           fetch('logout.php', {
                                 method: 'POST', // or 'GET' based on your server-side implementation
                                 credentials: 'same-origin' // include cookies (if any)

                              })
                              .then(response => {
                                 if (response.ok) {
                                    // Redirect to index.php after logout
                                    window.location.href = 'index.php'; // Replace with your desired page after logout
                                 } else {
                                    // Handle error responses
                                    console.error('Logout failed');
                                 }
                              })
                              .catch(error => {
                                 console.error('Error during logout:', error);
                              });
                        }
                     </script>
                  </div>
               </div>
            </div>
         </div>
      </div>

   <?php endif; ?>

   <!-- Booking Modal -->
   <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="bookingModalLabel">Booking</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form id="bookingForm" action="actions.php" method="POST">
                  <input type="hidden" id="tourName" name="tourName">
                  <input type="hidden" id="actualTotalPrice" name="totalPrice">
                  <div style="border: 1px solid #dee2e6; border-radius: 5px; padding: 10px;">
                     <table class="table">
                        <tbody>
                           <!-- Date -->
                           <tr>
                              <td>
                                 <label for="bookingDate" class="form-label-s">Date</label>
                              </td>
                              <td colspan="3">
                                 <input type="date" class="form-control" id="bookingDate" name="bookingDate" required>
                              </td>
                           </tr>
                           <!-- Adults -->
                           <tr>
                              <td>
                                 <label class="form-label-s">Adults</label>
                              </td>
                              <td>
                                 <button type="button" class="btn btn-outline-secondary" id="adultMinus">-</button>
                              </td>
                              <td>
                                 <input type="text" class="form-control text-center" id="adultCount" name="adultCount" value="0" readonly>
                              </td>
                              <td>
                                 <button type="button" class="btn btn-outline-secondary" id="adultPlus">+</button>
                              </td>
                           </tr>
                           <!-- Children -->
                           <tr>
                              <td>
                                 <label class="form-label-s">Children</label>
                              </td>
                              <td>
                                 <button type="button" class="btn btn-outline-secondary" id="childrenMinus">-</button>
                              </td>
                              <td>
                                 <input type="text" class="form-control text-center" id="childrenCount" name="childrenCount" value="0" readonly>
                              </td>
                              <td>
                                 <button type="button" class="btn btn-outline-secondary" id="childrenPlus">+</button>
                              </td>
                           </tr>
                           <!-- Payment Method -->
                           <tr>
                              <td>
                                 <label for="paymentMethod" class="form-label-s">Payment</label>
                              </td>
                              <td colspan="3">
                                 <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                                    <option value="" disabled selected>Select Payment Method</option>
                                    <option value="GCash">GCash</option>
                                    <option value="PayMaya">PayMaya</option>
                                    <option value="Banko de Oro">Banko de Oro</option>
                                    <option value="LandBank">LandBank</option>
                                 </select>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </div>

                  <!-- Validation Alert -->
                  <div id="validationAlert" class="alert alert-danger d-none mt-3" role="alert">
                     Please select at least one adult or one child.
                  </div>

                  <div class="mt-3 text-center">
                     <label class="form-label">Total Price: <span id="totalPrice">0</span></label>
                  </div>


                  <div class="modal-footer justify-content-center">
                     <button type="submit" class="btn btn-success" name="confirmBooking" style="width: 150px;">Confirm</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>

   <script>
      document.addEventListener('DOMContentLoaded', function() {
         const bookingForm = document.getElementById('bookingForm');
         bookingForm.addEventListener('submit', function(event) {
            const adultCount = parseInt(document.getElementById('adultCount').value);
            const childrenCount = parseInt(document.getElementById('childrenCount').value);

            if (adultCount === 0 && childrenCount === 0) {
               event.preventDefault(); // Prevent form submission
               document.getElementById('validationAlert').classList.remove('d-none');
               // Optionally, you can focus on the alert or scroll to it for better visibility
               document.getElementById('validationAlert').scrollIntoView({
                  behavior: 'smooth',
                  block: 'center'
               });
            } else {
               document.getElementById('validationAlert').classList.add('d-none');
            }
         });
      });
   </script>



   <!-- Hotel Booking Modal -->
   <div class="modal fade" id="hotelBookingModal" tabindex="-1" aria-labelledby="hotelBookingModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="hotelBookingModalLabel">Hotel Booking</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <form action="actions.php" method="POST">
                  <input type="hidden" id="hotelPricePerDay" name="hotelPricePerDay">
                  <input type="hidden" id="hotelName" name="hotelName">
                  <!-- Check-in Date -->
                  <div class="mb-3">
                     <label for="checkInDate" class="form-label check-mo">Check-in Date</label>
                     <input type="date" class="form-control" id="checkInDate" name="checkInDate" required>
                  </div>
                  <!-- Check-out Date -->
                  <div class="mb-3">
                     <label for="checkOutDate" class="form-label check-mo">Check-out Date</label>
                     <input type="date" class="form-control" id="checkOutDate" name="checkOutDate" required>
                  </div>
                  <!-- Room Type -->
                  <div class="mb-3">
                     <label for="roomType" class="form-label check-mo">Room Type</label>
                     <select class="form-select" id="roomType" name="roomType" required>
                        <option value="" disabled selected>Select Room Type</option>
                        <option value="Single">Single</option>
                        <option value="Double">Double (Additional 100 per Night)</option>
                        <option value="Suite">Suite (Additional 300 per Night)</option>
                        <option value="Family">Family (Additional 500 per Night)</option>
                     </select>
                  </div>
                  <div class="mb-3">
                     <label for="hotelpaymentMethod" class="form-label check-mo">Payment</label>
                     <select class="form-select" id="hotelpaymentMethod" name="hotelpaymentMethod" required>
                        <option value="" disabled selected>Select Payment Method</option>
                        <option value="GCash">GCash</option>
                        <option value="PayMaya">PayMaya</option>
                        <option value="Banko de Oro">Banko de Oro</option>
                        <option value="LandBank">LandBank</option>
                     </select>
                  </div>

                  <div class="alert alert-danger d-flex align-items-center d-none" role="alert" id="hotelError">
                  </div>

                  <div id="alertContainer" class="alert alert-warning d-none" role="alert">
                     Check-out date cannot be the same as check-in date.
                  </div>

                  <!-- Total Price -->
                  <div class="mt-3 text-center">
                     <label class="tprice form-label d-block mx-auto">Total Price: <span name="price" id="hotelTotalPrice">0</span></label>
                  </div>
                  <div class="modal-footer justify-content-center">
                     <input type="hidden" id="actualHotelTotalPrice" name="actualHotelTotalPrice">
                     <button type="submit" class="btn btn-success" name="confirmHotelBooking" style="width: 150px;">Confirm</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>

   <!-- Show account modal if the changes was successful  -->
   <?php if (isset($_SESSION["changes_success"])) : ?>
      <script>
         $(document).ready(function() {
            $('#accountModal').modal('show');
         });
      </script>
   <?php endif; ?>
   <?php unset($_SESSION["changes_success"]); ?>

   <script>
      // SERVICES ONLY

      // Function to format price with commas
      function formatPriceWithCommas(price) {
         return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP'
         }).format(price);
      }

      // Function to calculate and update total price
      function updateTotalPrice() {
         const adultCount = parseInt(document.getElementById('adultCount').value);
         const childrenCount = parseInt(document.getElementById('childrenCount').value);
         const totalPrice = (adultCount * adultPrice) + (childrenCount * childPrice);
         const formattedTotalPrice = formatPriceWithCommas(totalPrice);
         document.getElementById('actualTotalPrice').value = totalPrice;
         document.getElementById('totalPrice').innerText = formattedTotalPrice;
      }

      // Event listeners for tour buttons
      document.querySelectorAll('.book-btn').forEach(button => {
         button.addEventListener('click', () => {
            document.querySelectorAll('.book-btn').forEach(btn => {
               btn.classList.remove('active');
            });
            button.classList.add('active');
            const tourName = button.getAttribute('data-tour-name');
            switch (tourName) {
               case 'Boracay':
                  adultPrice = 1079;
                  break;
               case 'Cebu':
                  adultPrice = 2938;
                  break;
               case 'Siargao':
                  adultPrice = 2600;
                  break;
               case 'Bohol':
                  adultPrice = 7332;
                  break;
               case 'Fort Santiago':
                  adultPrice = 3156;
                  break;
               case 'Albay':
                  adultPrice = 3366;
                  break;
               default:
                  adultPrice = 0; // Default price if tour name not recognized
            }
            childPrice = adultPrice - adultPrice * 0.1;
            updateTotalPrice();
            document.getElementById('tourName').value = tourName;
         });
      });

      // Event listeners for count buttons
      function updateCount(id, increment) {
         let count = parseInt(document.getElementById(id).value);
         count += increment;
         if (count < 0) {
            count = 0;
         }
         document.getElementById(id).value = count;
         updateTotalPrice();
      }

      document.getElementById('adultPlus').addEventListener('click', () => updateCount('adultCount', 1));
      document.getElementById('adultMinus').addEventListener('click', () => updateCount('adultCount', -1));
      document.getElementById('childrenPlus').addEventListener('click', () => updateCount('childrenCount', 1));
      document.getElementById('childrenMinus').addEventListener('click', () => updateCount('childrenCount', -1));

      // Event listeners for hotel buttons
      const checkInDate = document.getElementById("checkInDate");
      const checkOutDate = document.getElementById("checkOutDate");
      const hotelPricePerDay = document.getElementById("hotelPricePerDay");
      const hotelTotalPrice = document.getElementById("hotelTotalPrice");
      const actualHotelBookingTotalPrice = document.getElementById("actualHotelTotalPrice");
      const roomType = document.getElementById("roomType");
      const alertContainer = document.getElementById("alertContainer");

      document.querySelectorAll(".hotel-book-btn").forEach((button) => {
         const hotelName = button.dataset.hotelName;

         button.addEventListener("click", () => {
            actualHotelBookingTotalPrice.value = 0;
            checkInDate.value = "";
            checkOutDate.value = "";
            hotelTotalPrice.innerText = formatPriceWithCommas(0);
            hideAlert(); // Ensure alert is hidden initially

            let pricePerDay = 0;
            switch (hotelName) {
               case 'Luxury Beachfront Hotel':
                  pricePerDay = 10000;
                  break;
               case 'City Center Hotel with Rooftop Pool':
                  pricePerDay = 7500;
                  break;
               case 'Boutique Hotel with Garden View':
                  pricePerDay = 5000;
                  break;
               case 'Beach Resort with Private Villas':
                  pricePerDay = 10000;
                  break;
               case 'Historical Hotel in the Heart of Manila':
                  pricePerDay = 8500;
                  break;
               case 'Mountain Resort with Scenic Views':
                  pricePerDay = 6000;
                  break;
               default:
                  pricePerDay = 0; // Default price if hotel name not recognized
            }
            hotelPricePerDay.value = pricePerDay;
            document.getElementById("hotelName").value = hotelName;
         });

         // Add event Listener to check-out and check-in to update the price
         function updateTotalPrice() {
            if (checkInDate.value && checkOutDate.value) {
               if (checkInDate.value === checkOutDate.value) {
                  // Show Bootstrap alert for same check-in and check-out date
                  showAlert("Check-out date cannot be the same as check-in date.");
                  // Clear checkOutDate if same as checkInDate
                  checkOutDate.value = "";
               } else if (new Date(checkInDate.value) > new Date(checkOutDate.value)) {
                  // Show Bootstrap alert for check-out earlier than check-in
                  showAlert("Check-out date cannot be earlier than check-in date.");
                  // Clear checkOutDate if invalid
                  checkOutDate.value = "";
               } else {
                  // Dates are valid, calculate total price
                  let totalPrice = parseInt(hotelPricePerDay.value) * daysBetween(checkInDate.value, checkOutDate.value);

                  // Adjust the price based on room type
                  switch (roomType.value) {
                     case 'Double':
                        totalPrice += (100 * daysBetween(checkInDate.value, checkOutDate.value));
                        break;
                     case 'Suite':
                        totalPrice += (300 * daysBetween(checkInDate.value, checkOutDate.value));
                        break;
                     case 'Family':
                        totalPrice += (500 * daysBetween(checkInDate.value, checkOutDate.value));
                        break;
                     case '':
                        totalPrice = 0; // Handle no room type selected case
                        break;
                     default:
                        break; // No extra charge for 'Single'
                  }

                  actualHotelBookingTotalPrice.value = totalPrice;
                  hotelTotalPrice.innerText = formatPriceWithCommas(totalPrice);
                  hideAlert(); // Hide alert if dates are valid
               }
            }
         }

         checkInDate.addEventListener("change", updateTotalPrice);
         checkOutDate.addEventListener("change", updateTotalPrice);
         roomType.addEventListener("change", updateTotalPrice);

         // Function to calculate the difference in days between two dates
         function daysBetween(date1, date2) {
            const oneDay = 24 * 60 * 60 * 1000;
            const firstDate = new Date(date1);
            const secondDate = new Date(date2);

            const diffInMilliseconds = Math.abs(secondDate - firstDate);

            // Convert back to days and return
            return Math.round(diffInMilliseconds / oneDay);
         }

         // Function to show alert with specified message
         function showAlert(message) {
            alertContainer.innerText = message;
            alertContainer.classList.remove("d-none");
         }

         // Function to hide alert
         function hideAlert() {
            alertContainer.innerText = "";
            alertContainer.classList.add("d-none");
         }
      });



      // Car Booking
      document.addEventListener('DOMContentLoaded', function() {
         const regions = {
            'Palawan': ['Puerto Prinsesa', 'Coron', 'El Nido'],
            'Albay': ['Cagsawa Ruins', 'Daraga Church', 'Mayon Volcano'],
            'Vigan': ['Baluarte', 'Plaza Salcedo', 'Calle Crisologo'],
            'Bohol': ['Philippine Tarsier Sanctuary', 'Hinagdanan Cave', 'Chocolate Hills'],
            'Siargao': ['Nikka\'s Bridge', 'La Prinsesa Strawberry Farm', 'Cloud 9']
         };

         function findRegion(value) {
            for (const region in regions) {
               if (regions[region].includes(value)) {
                  return region;
               }
            }
            return null;
         }

         function populateDropdown(dropdown, options, selectedValue = null) {
            dropdown.innerHTML = '';
            options.forEach(location => {
               const option = document.createElement('option');
               option.value = location;
               option.textContent = `${location} - ${findRegion(location)}`;
               dropdown.appendChild(option);
            });
            if (selectedValue) {
               dropdown.value = selectedValue;
            } else {
               dropdown.selectedIndex = 0;
            }
         }

         function resetDropoffDropdown(pickupValue, dropoffValue = null) {
            const dropoffDropdown = document.getElementById('dropoff');
            if (pickupValue) {
               dropoffDropdown.removeAttribute('disabled');
               const pickupRegion = findRegion(pickupValue);
               const filteredLocations = regions[pickupRegion].filter(location => location !== pickupValue);
               populateDropdown(dropoffDropdown, filteredLocations, dropoffValue);
            } else {
               dropoffDropdown.setAttribute('disabled', true);
               dropoffDropdown.innerHTML = '<option value="" disabled selected>Select Pickup Location First</option>';
            }
         }

         // Event listener for pickup location change
         document.getElementById('pickup-location').addEventListener('change', function() {
            const pickupValue = this.value;
            resetDropoffDropdown(pickupValue);
         });

         // Function to handle form submission and re-populate drop-off location
         function handleFormSubmission() {
            const pickupDropdown = document.getElementById('pickup-location');
            const dropoffDropdown = document.getElementById('dropoff');
            const pickupValue = pickupDropdown.value;
            const dropoffValue = dropoffDropdown.value;

            // Re-initialize the dropdowns to retain their values
            resetDropoffDropdown(pickupValue, dropoffValue);
         }

         // Initialize dropdowns on page load
         resetDropoffDropdown(null);

         // Ensure dropdowns retain values on form submission
         const bookingForm = document.getElementById('bookingForm');
         bookingForm.addEventListener('submit', function(event) {
            handleFormSubmission();
         });
      });
   </script>

   <script defer src="../shared/global.js?<?php time(); ?>"></script>
   <script src="../shared/scrollToTop.js"></script>

</body>

</html>
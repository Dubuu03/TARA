<?php
session_start();
include("./config.php");


// Determine which modal to show based on user session
if (isset($_SESSION['user'])) {
   $user = $_SESSION['user'];
   $modalTarget = "#accountModal";
} else {
   $modalTarget = "#loginModal";
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

function formatToPHP($number)
{
   // Format the number to 2 decimal places with comma as thousand separator and dot as decimal point
   $formatted_number = number_format($number, 2, '.', ',');

   // Append the Philippine Peso symbol (₱)
   return '₱ ' . $formatted_number;
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link href="./global.css?<?php echo time(); ?>" rel="stylesheet" />
   <link href="./styles/home.css?<?php echo time(); ?>" rel="stylesheet" />

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

   <!-- BOOTSTRAP CSS -->
   <link rel="stylesheet" href="./assets/bootstrap-5.3.0-alpha3-dist/css/bootstrap.css">

   <!-- SCRIPTS -->
   <script src="./assets/bootstrap-5.3.0-alpha3-dist/js/bootstrap.js"></script>
   <script src="./assets/JQuery3.7.1.js"></script>

   <title>Travel Philippines</title>

   <style>
      .card-body-2col {
         column-count: 2;
         column-gap: 20px;
         padding-top: 5px;
      }

      .custom-footer {
         padding: .5rem 1rem;
         background-color: #064358;
         border-top: 1px solid rgba(0, 0, 0, 0.125);
         border-radius: 0 0 calc(.25rem - 1px) calc(.25rem - 1px);
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

      .card-title {
         font-weight: bold;
         font-family: arial;
      }

      .card-text {
         font-size: 14px;
         color: #064358;
         margin-top: 5px;
         font-family: Arial, Helvetica, sans-serif;
      }

      .card {
         border-radius: 12px;
         box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
      }
   </style>

</head>

<body>
   <!-- HEADER -->
   <header>
      <a href="#">
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
               <a href="#">
                  HOME
               </a>
            </li>
            <li class="dropdown">
               <button class="dropdown-trigger" data-dropdown-content="tourist-content">
                  <span>TOURIST ATTRACTIONS</span>
                  <span class="arrow">&#9662;</span>
               </button>
               <div id="tourist-content" class="dropdown-content">
                  <a href="./calle/index.php">Calle Crisologo</a>
                  <a href="./bohol/index.php">Chocolate Hills</a>
                  <a href="./siargao/index.php">Cloud 9</a>
                  <a href="./mayon/index.php">Mayon Volcano</a>
                  <a href="./tubbataha/index.php">Tubbataha Reef</a>
               </div>
            </li>
            <li class="dropdown">
               <button class="dropdown-trigger" data-dropdown-content="festivals-content">
                  <span>FESTIVALS</span>
                  <span class="arrow">&#9662;</span>
               </button>
               <div id="festivals-content" class="dropdown-content">
                  <a href="./ati-atihan/index.php">Ati-atihan</a>
                  <a href="./pahiyas/index.php">Pahiyas</a>
                  <a href="./panagbenga/index.php">Panagbenga</a>
                  <a href="./pavvurulun/index.php">Pavvulurun</a>
                  <a href="./sinulog/index.php">Sinulog</a>
               </div>
            </li>
            <li class="dropdown">
               <button class="dropdown-trigger" data-dropdown-content="foods-content">
                  <span>FOODS</span>
                  <span class="arrow">&#9662;</span>
               </button>
               <div id="foods-content" class="dropdown-content">
                  <a href="./adobo/index.php">Adobo</a>
                  <a href="./kare-kare/index.php">Kare-Kare</a>
                  <a href="./lechon/index.php">Lechon</a>
                  <a href="./pakbet/index.php">Pakbet</a>
                  <a href="./sisig/index.php">Sisig</a>
               </div>
            </li>
            <li>
               <a href="./services/index.php">
                  SERVICES
               </a>
            </li>
            <li>
               <a href="./about/index.php">
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

   <!-- Hero Section -->
   <section class="hero">
      <div class="hero-text">DISCOVER</div>
      <b>PHILIPPINES</b>
      <p class="hero-text">
         TARA – Your Ultimate Guide
      </p>
   </section>

   <!-- Main Content -->
   <main>
      <!-- Top Destinations -->
      <section class="section">
         <h2>
            <i class="first">TOP</i>
            <span class="second">DESTINATIONS</span>
         </h2>
         <div class="card-list-container">
            <div class="custom-card">
               <h4>Mayon Volcano</h4>
               <img src="./public/mayon.jpg" alt="Mayon Volcano">
               <p>An active stratovolcano renowned for its near-perfect cone shape and frequent eruptions.</p>
               <a href="./mayon/index.php" class="custom-btn mt-3">Read more</a>
            </div>
            <div class="custom-card">
               <h4>Siargao</h4>
               <img src="./public/siargao.png" alt="Siargao">
               <p>A stunning Philippine island known for its pristine beaches, crystal-clear waters, world-class
                  surfing, and laid-back atmosphere.</p>
               <a href="./siargao/index.php" class="custom-btn">Read more</a>
            </div>
            <div class="custom-card">
               <h4>Palawan</h4>
               <img src="./public/palawan.jpg" alt="Palawan">
               <p>A picturesque Philippine province boasting stunning landscapes with pristine beaches, crystal-clear
                  waters, and biodiverse coral reefs.</p>
               <a href="./tubbataha/index.php" class="custom-btn">Read more</a>
            </div>
         </div>
      </section>

      <!-- Famous Festivals -->
      <section class="section">
         <h2>
            <i class="first">FAMOUS</i>
            <span class="second">FESTIVALS</span>
         </h2>
         <div class="card-list-container">
            <div class="custom-card">
               <h4>Ati-Atihan</h4>
               <img src="./public/ati-atihan.jpg" alt="Ati-Atihan">
               <p>The Ati-Atihan Festival is a spirited celebration featuring exuberant street parades, tribal dances,
                  and drumming to honor the Santo Niño.</p>
               <a href="./ati-atihan/index.php" class="custom-btn">Read more</a>
            </div>
            <div class="custom-card">
               <h4>Sinulog</h4>
               <img src="./public/sinulog.png" alt="Sinulog">
               <p>The Sinulog Festival is a celebration that honors the Santo Niño through lively street parades,
                  rhythmic dances, and colorful pageantry.</p>
               <a href="./sinulog/index.php" class="custom-btn">Read more</a>
            </div>
            <div class="custom-card">
               <h4>Panagbenga</h4>
               <img src="./public/panagbenga.jpg" alt="Panagbenga">
               <p>Panagbenga Festival, a flower festival in Baguio, celebrating the blooming season with vibrant street
                  dances, floral floats, and cultural exhibitions.</p>
               <a href="./panagbenga/index.php" class="custom-btn">Read more</a>
            </div>
         </div>
      </section>

      <!-- Favorite Foods -->
      <section class="section">
         <h2>
            <i class="first">FAVORITE</i>
            <span class="second">FOODS</span>
         </h2>
         <div class="card-list-container">
            <div class="custom-card">
               <h4>Adobo</h4>
               <img src="./public/adobo.jpg" alt="Adobo">
               <p>Savor the rich blend of tangy vinegar, savory soy sauce, and aromatic spices in adobo—a flavorful
                  culinary delight that tantalizes taste buds worldwide.</p>
               <a href="./adobo/index.php" class="custom-btn ">Read more</a>
            </div>
            <div class="custom-card">
               <h4>Lechon</h4>
               <img src="./public/lechon.jpg" alt="Lechon">
               <p>Indulge in the irresistible crispy-skin and succulent meat of lechon—a Filipino delicacy boasting a
                  perfect balance of flavors and textures!</p>
               <a href="./lechon/index.php" class="custom-btn ">Read more</a>
            </div>
            <div class="custom-card">
               <h4>Sisig</h4>
               <img src="./public/sisig.jpg" alt="Sisig">
               <p>Sisig boasts a tantalizing mix of crispy pork bits, tangy flavors, and a unique blend of spices that
                  make it an irresistible dish for food enthusiasts.</p>
               <a href="./sisig/index.php" class="custom-btn">Read more</a>
            </div>
         </div>
      </section>
   </main>

   <!-- Scroll to top button -->
   <button class="scroll-top-btn">
      <i class="fas fa-arrow-up"></i>
   </button>


   <!-- Footer -->
   <footer>
      <div class="footer-container">
         <div class="footer-column">
            <a href="#"><img src="./public/footerlogo.png" alt="footer-logo"></a>
            <p class="footer-text"> © 2023 TARA. All Rights Reserved.</p>
         </div>
         <div class="footer-column">
            <h3 class="footer-title">Quick Links</h3>
            <ul class="footer-links">
               <li><a href="#">Home</a></li>
               <li><a href="./calle/index.php">Tourist Attractions</a></li>
               <li><a href="./ati-atihan/index.php">Festivals</a></li>
               <li><a href="./adobo/index.php">Foods</a></li>
               <li><a href="./services/index.php">Services</a></li>
               <li><a href="./about/index.php">About</a></li>
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
               <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ"><img class="footer-social-icon" alt="Facebook" src="./public/facebook.png">
                  <span>Facebook</span> </a>
            </div>
            <div class="footer-social">
               <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ"><img class="footer-social-icon" alt="Instagram" src="./public/instagram.png">
                  <span>Instagram</span> </a>
            </div>
            <div class="footer-social">
               <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ"><img class="footer-social-icon" alt="Twitter" src="./public/twitter.png">
                  <span>Twitter</span> </a>
            </div>
            <div class="footer-social">
               <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ"><img class="footer-social-icon" alt="YouTube" src="./public/youtube.png">
                  <span>YouTube</span> </a>
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
                  <input type="text" class="form-control" id="loginMobileOrEmail" name="mobileOrEmail" placeholder="Email address" required style="background-color: #f0f0f0;"><br>

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

                  <p class="text-muted" style="font-size: 13px;">Welcome back to TARA! Log in to explore the best
                     tourist spots in the Philippines and book your next adventure.
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
               <button type="submit" class="btn btn-primary w-100 changecol" name="changePassword" style="background-color: #00A400; border: none;">Submit</button>
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
                     <button type="submit" class="btn btn-primary w-100 changecol" name="confirmForgotPassword" style="background-color: #00A400; border: none;">Search</button>
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
                        <label for="e-firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="e-firstName" name="firstName" value="<?php echo htmlspecialchars($_SESSION['user']['FirstName']); ?>" required>
                     </div>
                     <div class="mb-3">
                        <label for="e-lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="e-lastName" name="lastName" value="<?php echo htmlspecialchars($_SESSION['user']['LastName']); ?>" required>
                     </div>
                     <div class="mb-3">
                        <label for="e-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="e-email" name="email" value="<?php echo htmlspecialchars($_SESSION['user']['Email']); ?>" required>
                     </div>
                     <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" required>
                     </div>
                     <div class="">
                        <label for="e-password" class="form-label">Password</label>
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
                                 <img src="<?php echo "./" . htmlspecialchars($user["UserImage"]); ?>" alt="<?php echo htmlspecialchars($user["Email"]); ?>">
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

   <!-- Show account modal if the changes was successful  -->
   <?php if (isset($_SESSION["changes_success"])) : ?>
      <script>
         $(document).ready(function() {
            $('#accountModal').modal('show');
         });
      </script>
   <?php endif; ?>
   <?php unset($_SESSION["changes_success"]); ?>

   <script defer src="./shared/global.js?<?php time(); ?>"></script>
   <script defer src="./shared/scrollToTop.js"></script>

</body>




</html>
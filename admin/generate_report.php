<?php
// generate_report.php

// Include database configuration
include('config.php');

// Function to output the report
function outputReport($header, $result, $fields)
{
    $output = "<h1>{$header}</h1>";
    $output .= "<table class='report-table'>";
    $output .= "<tr>";

    // Output table headers with fixed widths
    foreach ($fields as $field) {
        $output .= "<th>{$field}</th>";
    }
    $output .= "</tr>";

    // Output table rows with fixed widths
    while ($row = $result->fetch_assoc()) {
        $output .= "<tr>";
        foreach ($fields as $field) {
            // Adjust content width for each cell (if needed)
            $output .= "<td>{$row[$field]}</td>";
        }
        $output .= "</tr>";
    }

    $output .= "</table>";

    return $output;
}

// Function to generate report and set headers for download
function generateAndDownloadReport($filename, $title, $query, $fields)
{
    global $conn;
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=\"{$filename}.xls\"");

    $result = $conn->query($query);
    $reportContent = outputReport($title, $result, $fields);

    // Output the table content
    echo $reportContent;
}

// Start output buffering
ob_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Report</title>

    <style>
        /* styles.css */

        body {
            font-family: Arial, sans-serif;
        }

        .report-table {
            width: 90%;
            border-collapse: collapse;
            font-size: 11px;
            margin: 20px auto;
            text-align: center;
        }

        .report-table th,
        .report-table td {
            padding: 8px;
            border: 1px solid #ddd;
            width: 150px;
            min-width: 80px;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body>
    <?php

    // Check which button was clicked and generate the corresponding report
    if (isset($_POST['generateIndex'])) {
        generateAndDownloadReport("full_report_tours", "Tours and Tickets", "SELECT * FROM tblbookings", ['BookingID', 'UserID', 'Place', 'Date', 'Children', 'Adult', 'Payment']);
        generateAndDownloadReport("full_report_hotels", "Hotel Bookings", "SELECT * FROM tblhotels", ['HotelID', 'UserID', 'Destination', 'Check-in', 'Check-out', 'RoomType']);
        generateAndDownloadReport("full_report_cars", "Car Rentals", "SELECT * FROM tblcars", ['CarID', 'UserID', 'Pickup', 'Dropoff', 'CarType']);
        generateAndDownloadReport("full_report_users", "Users", "SELECT * FROM tbluser", ['UserID', 'LastName', 'FirstName', 'Email', 'AccessLevel']);
        generateAndDownloadReport("full_report_ratings", "Ratings", "SELECT * FROM tblratings", ['RatingID', 'UserID', 'HotelName', 'Stars']);
    } elseif (isset($_POST['generateUser'])) {
        generateAndDownloadReport("user_report", "Users", "SELECT * FROM tbluser", ['UserID', 'LastName', 'FirstName', 'Email', 'AccessLevel']);
    } elseif (isset($_POST['generateCars'])) {
        generateAndDownloadReport("cars_report", "Car Rentals", "SELECT * FROM tblcars", ['CarID', 'UserID', 'Pickup', 'Dropoff', 'CarType']);
    } elseif (isset($_POST['generateTours'])) {
        generateAndDownloadReport("tours_report", "Tours and Tickets", "SELECT * FROM tblbookings", ['BookingID', 'UserID', 'Place', 'Date', 'Children', 'Adult', 'Payment']);
    } elseif (isset($_POST['generateHotel'])) {
        generateAndDownloadReport("hotel_report", "Hotel Bookings", "SELECT * FROM tblhotels", ['HotelID', 'UserID', 'Destination', 'Check-in', 'Check-out', 'RoomType']);
    } elseif (isset($_POST['generateRating'])) {
        generateAndDownloadReport("rating_report", "Ratings", "SELECT * FROM tblratings", ['RatingID', 'UserID', 'HotelName', 'Stars']);
    }

    ?>
</body>

</html>
<?php

// Output the content
echo ob_get_clean();
?>
<?php

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"]["AccessLevel"] !== "Admin") {
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}

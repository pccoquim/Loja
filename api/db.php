<?php
    mysqli_report(MYSQLI_REPORT_ERROR);

    $con = new mysqli("localhost", "root", "", "loja");

    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }
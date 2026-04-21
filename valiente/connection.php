<?php

$connection = mysqli_connect("localhost", "root", "", "data");

if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

?>
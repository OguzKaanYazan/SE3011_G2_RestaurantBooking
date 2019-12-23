<?php

session_start();
include 'dbconnect.php';
if (!isset($_SESSION['username'])) {
    header('location:errorPage.php');//REDIRECTS TO ERRORPAGE IF SESSION DOES NOT EXIST
} else {
    $uname = $_SESSION['username'];
    $vname = $_GET['varname'];
    $sql2 = "select * from bookings where bookingId='$vname'";
    $query1 = mysqli_query($conn, $sql2);
    $arr1 = mysqli_fetch_array($query1, MYSQLI_ASSOC);
    if (mysqli_num_rows($query1) > 0) {
        $customer_uname = $arr1['customer_uname'];
        $restaurant_uname = $arr1['restaurant_uname'];
        if ($customer_uname != $_SESSION['username']) {//CHECKS IF THE CALLER IS THE OWNER OF THE BOOKING
            header('location: errorPage.php'); //REDIRECTS TO ERROR PAGE IF NOT
        } else {
            $sql = "delete from bookings where bookingId='$vname'";//DELETE THE BOOKING FROM DATABASE
            $query = mysqli_query($conn, $sql);
            header("location: viewMyBookings.php?varname=$uname");
        }
    } else {
        header('location:errorPage.php');
    }
}
?>
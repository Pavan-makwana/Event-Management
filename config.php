<?php 
session_start();
// <!-- Database Configurations -->
$servername = "localhost";
$username ="root";
$password ="";
$dbname ="event_manager";

// <!-- Create Connections -->
$conn = new mysqli($servername,$username,$password,$dbname,3307);
// <!-- Check connection -->
if($conn->connect_error){
    die("Connection failed:" .$conn->connect_error );
 }
// echo "Connected successfully";
?>

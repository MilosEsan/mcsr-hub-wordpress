<?php
// $mysqli = new mysqli('localhost', 'root', '', 'mcsr-hub1_wp426');

// if ($mysqli->connect_error) {
//     die('Connection Error: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
// }
// echo 'Successfully connected to the database!';


$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'mcsr-hub1_wp426';

// Pokušaj povezivanja
$conn = new mysqli($servername, $username, $password, $dbname);

// Provera konekcije
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully to the database";
$conn->close();


?>
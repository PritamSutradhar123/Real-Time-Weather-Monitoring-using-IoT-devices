<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "sensor_db";

$conn = mysqli_connect($hostname, $username, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Database connection is OK";

if (isset($_POST["temperature"]) && isset($_POST["humidity"])) {
    $t = $_POST["temperature"];
    $h = $_POST["humidity"];
    
    $sql ="INSERT INTO dht11 (temperature,humidity) VALUES (".$t.",".$h.")";


    if (mysqli_query($conn,$sql))
    {
        echo"new record created sucessfully";

    }
    else{
        echo "Error".$sql."<br>".mysqli_error($conn);
    }
}
?>
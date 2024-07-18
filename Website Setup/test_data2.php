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

    // Query to get the latest temperature and humidity values
    $query = "SELECT temperature, humidity FROM dht11 ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $latest_temperature = $row['temperature'];
        $latest_humidity = $row['humidity'];

        // Compare with the latest values
        if ($t == $latest_temperature && $h == $latest_humidity) {
            echo "Values are the same as the latest record. Not inserting.";
        } else {
            // Insert new record
            $stmt = $conn->prepare("INSERT INTO dht11 (temperature, humidity) VALUES (?, ?)");
            $stmt->bind_param("ii", $t, $h);

            if ($stmt->execute()) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        echo "Error retrieving latest record: " . mysqli_error($conn);
    }

    mysqli_free_result($result);
}

$conn->close();
?>

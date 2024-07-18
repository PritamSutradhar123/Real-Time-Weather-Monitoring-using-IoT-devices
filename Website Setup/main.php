<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Widget</title>
    <link rel="stylesheet" href="maincopy.css">
</head>
<body>
    <!-- PHP Code for Database Connection and Data Retrieval -->
    <?php
    // Database connection
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "sensor_db";

    $conn = mysqli_connect($hostname, $username, $password, $database);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $temperature = "N/A";
    $humidity = "N/A";
    $datetime = "N/A";
    $hourlyData = [];

    $sql = "SELECT * FROM dht11 ORDER BY id DESC LIMIT 7"; // Fetch last 7 entries including the most recent
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $hourlyData[] = $row;
        }
        // Reverse the order to get ascending time order
        $hourlyData = array_reverse($hourlyData);

        // Most recent data
        $temperature = $hourlyData[6]["temperature"];
        $humidity = $hourlyData[6]["humidity"];
        $datetime = $hourlyData[6]["datetime"]; // Assuming you have a datetime column in your table
    }

    mysqli_close($conn);
    ?> 
    <div class="container">
        <header class="header">
            <h1 class="rainbow-letters">Real-Time Weather Monitoring System</h1>
            <p class="last-updated">Last Updated <?php echo $datetime; ?></p>
        </header>
        <video autoplay muted loop id="background-video">
            <source src="rain overlay.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="weather-widget">
            <div class="current-weather">
                <div class="container2">
                    <div class="weather">
                        <img src="cloudy.gif" alt="Weather Icon" class="weather-icon">
                    </div>
                    <div class="temp-details">
                        <video autoplay muted loop id="current-weather-video">
                            <source src="rain overlay.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <div class="temp"><?php echo $temperature; ?>°C</div>
                        <div class="details">
                            <div class="location">Kokrajhar</div>
                            <div class="weather-condition" id="weather-condition">Partly Cloudy</div>
                            <div class="extra-details">
                                <div class="temp_in_far">
                                    <img src="thermometer.png" alt="Temperature Icon" class="icon-small">
                                    <span class="fahrenheit" id="fahrenheit"></span>
                                </div>
                                <div class="humidity">
                                    <img src="humidity.png" alt="Humidity Icon" class="icon-small">
                                    <?php echo $humidity . "%"; ?>
                                </div>
                                <div class="heat index">
                                    <img src="heating.png" alt="Heat Index Icon" class="icon-small">
                                    <div class="heat-index" id="heat-index"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="days">
                        <div class="day" id="day"></div>
                        <div class="time" id="time"></div>
                    </div>
                </div>
            </div>
            <div class="forecast">
                <div class="hourly">
                    <?php
                    // Displaying the last 6 entries as hourly forecast
                    for ($i = 0; $i < 6; $i++) {
                        $hour = date("H:i", strtotime($hourlyData[$i]["datetime"]));
                        $temp = $hourlyData[$i]["temperature"];
                        $hum = $hourlyData[$i]["humidity"];
                        echo "<div class='hour'>
                                <div>{$hour}</div>
                                <div>{$temp}°C</div>
                                <div>{$hum}%</div>
                                <div>🌤</div>
                            </div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="legend-box">
        <h3>Legend</h3>
        <ul>
            <li><img src="cloudy.gif" alt="Cloudy Icon"> Cloudy</li>
            <li><img src="thermometer.png" alt="Temperature Icon"> Temperature</li>
            <li><img src="humidity.png" alt="Humidity Icon"> Humidity</li>
            <li><img src="heating.png" alt="Heat Index Icon"> Heat Index</li>
            <!-- Add more icons and descriptions as needed -->
        </ul>
    </div>
    
    <script>
        function convertCelsiusToFahrenheit(celsius) {
            return (celsius * 9/5) + 32;
        }
        function getDayName() {
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const today = new Date();
            return days[today.getDay()];
        }
        function updateTime() {
            const timeElement = document.getElementById('time');
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            timeElement.textContent = `${hours}:${minutes}:${seconds}`;
        }
        function getWeatherCondition($celsius, $humidity) {
            if ($celsius >= 0 && $celsius < 15) {
                return "Cold";
            } else if ($celsius >= 15 && $celsius < 20) {
                return "Cool";
            } else if ($celsius >= 20 && $celsius < 25 || $humidity < 60) {
                return "Optimal";
            } else if ($celsius >= 25 && $celsius < 30 || $humidity > 60) {
                return "Rainy";
            } else if ($celsius >= 30 && $celsius < 40) {
                return "Heat";
            } else if ($celsius >= 40 && $celsius <= 70) {
                return "Extreme Heat";
            } else {
                return "Unknown";
            }
        }

        function calculateHeatIndex(temperature, humidity) {
        // Heat index values from the provided chart
        const heatIndexTable = [
            [27, 28, 29, 30, 31, 32, 33, 34, 35, 37, 39, 41, 43, 46, 48, 51, 54, 57],
            [27, 28, 29, 32, 33, 35, 37, 39, 41, 43, 46, 49, 51, 54, 57],
            [27, 28, 30, 31, 33, 34, 36, 38, 41, 43, 46, 49, 52, 55, 58],
            [28, 29, 30, 32, 34, 36, 38, 40, 43, 46, 48, 52, 55, 59],
            [28, 29, 31, 33, 35, 37, 40, 42, 45, 48, 51, 55, 59],
            [28, 30, 32, 34, 36, 39, 41, 44, 48, 51, 55, 59],
            [29, 31, 33, 35, 38, 40, 43, 47, 50, 54, 58],
            [29, 31, 34, 36, 39, 42, 46, 49, 53, 58],
            [30, 32, 35, 38, 41, 44, 48, 52, 57],
            [30, 33, 36, 39, 43, 47, 51, 55],
            [31, 34, 37, 41, 45, 49, 54],
            [31, 35, 38, 42, 47, 51, 57],
            [32, 36, 40, 44, 49, 54]
        ];

        const temperatureIndex = Math.min(Math.max(temperature - 27, 0), heatIndexTable[0].length - 1);
        const humidityIndex = Math.min(Math.max(Math.floor((humidity - 40) / 5), 0), heatIndexTable.length - 1);

        const heatIndex = heatIndexTable[humidityIndex][temperatureIndex];

        return heatIndex;
    }


        // Update the time every second
        setInterval(updateTime, 1000);
        // Initialize the time on page load
        updateTime();
        document.addEventListener('DOMContentLoaded', function() {
            // Fetching temperature and humidity values from PHP
            const temperatureCelsius = parseFloat("<?php echo $temperature; ?>");
            const humidity = parseFloat("<?php echo $humidity; ?>");
            
            // Displaying converted temperature in Fahrenheit
            const fahrenheitElement = document.getElementById('fahrenheit');
            const fahrenheitValue = convertCelsiusToFahrenheit(temperatureCelsius);
            fahrenheitElement.textContent = `${fahrenheitValue.toFixed(1)} F`;

            // Displaying heat index in Celsius
            const heatIndexElement = document.getElementById('heat-index');
            const heatIndexValue = calculateHeatIndex(temperatureCelsius, humidity);
            heatIndexElement.textContent = `${heatIndexValue}°C`;

            // Displaying day name and weather condition
            const dayElement = document.getElementById('day');
            dayElement.textContent = getDayName();
            const weatherConditionElement = document.getElementById('weather-condition');
            weatherConditionElement.textContent = getWeatherCondition(temperatureCelsius);
        });
        setTimeout(function() {
            location.reload();
        }, 5000);
    </script>
    
</body>
</html>

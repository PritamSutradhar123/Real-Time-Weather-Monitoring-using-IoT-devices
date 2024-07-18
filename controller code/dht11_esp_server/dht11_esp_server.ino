#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>
#define DHTPIN 19
#define DHTTYPE DHT11
DHT dht11(DHTPIN,DHTTYPE);


String URL = "http://192.168.1.169/dht11_project/test_data.php";

const char* ssid = "SUTRO 2658";
const char* password = "123456789";

float temperature = 0;
float humidity = 0;

void setup() {
  Serial.begin(115200);
  dht11.begin();
  connectWiFi();
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    connectWiFi();
  }
  Load_DHT11_DATA();

  String postData = "temperature=" + String(temperature) + "&humidity=" + String(humidity);

  HTTPClient http;
  http.begin(URL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded"); // Add header before making the POST request

  int httpCode = http.POST(postData); // POST data to server
  String payload = http.getString();

  Serial.print("URL : ");
  Serial.println(URL);
  Serial.print("Data : ");
  Serial.println(postData);
  Serial.print("httpCode : ");
  Serial.println(httpCode);
  Serial.print("payload : ");
  Serial.println(payload);

  http.end();

  delay(10000); // Delay to avoid spamming the server
}

void Load_DHT11_DATA()
{
  temperature=dht11.readTemperature();
  humidity=dht11.readHumidity();

  if(isnan(temperature)||isnan(humidity)){
    Serial.println("Faild to read from Dht sensor");
    temperature = 0;
    humidity = 0;
  }

  Serial.printf("Temperature: %d *C\n",temperature);
  Serial.printf("Humidity: %d %%\n",humidity);
}

void connectWiFi() {
  WiFi.mode(WIFI_OFF);
  delay(1000);
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  Serial.println("Connecting to WiFi");

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println();
  Serial.print("Connected to: ");
  Serial.println(ssid);
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
}

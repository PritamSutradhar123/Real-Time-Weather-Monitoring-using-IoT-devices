#include <WiFi.h>
#include <HTTPClient.h>

String URL = "http://192.168.34.35/dht11_project/test_data.php";

const char* ssid = "realmenarzo";
const char* password = "sutro123";

int temperature = 50;
int humidity = 50;

void setup() {
  Serial.begin(115200);
  connectWiFi();
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    connectWiFi();
  }

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

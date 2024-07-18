#include "WiFi.h"

const char* ssid = "SUTRO 2658";
const char* pass = "123456789";

void setup()
{
  Serial.begin(115200);

  WiFi.begin(ssid,pass);

  while (WiFi.status() != WL_CONNECTED)
  {
    delay(500);
    Serial.println("Connecting to Wifi");
  }
  Serial.println("Connected with wifi");
}

void loop()
{

}
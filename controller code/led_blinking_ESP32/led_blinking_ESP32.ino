int LED_BUILTIN=2;
String cmd;
void setup() {
  Serial.begin(115200);
  pinMode(LED_BUILTIN,OUTPUT);
  
}

void loop() {
  if(Serial.available())
  {
    cmd = Serial.readString();
    if(cmd=="on")
    {
      digitalWrite(LED_BUILTIN,HIGH);
    } 
    else{
      digitalWrite(LED_BUILTIN,LOW);
    }
  }


}
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

const char* ssid = "realme";
const char* password = "jeraldBSC2";

const char* serverLog = "http://192.168.190.130/noise_monitoring/index.php";
const char* serverInterval = "http://192.168.190.130/noise_monitoring/get_interval.php";
const char* serverCooldown = "http://192.168.190.130/noise_monitoring/cooldown_status.php";
const char* serverStatus = "http://192.168.190.130/noise_monitoring/status.php";
// const char* serverLog = "https://tagabaryo.com/group_8/noise_monitoring/index.php";
// const char* serverInterval = "https://tagabaryo.com/group_8/noise_monitoring/get_interval.php";
// const char* serverCooldown = "https://tagabaryo.com/group_8/noise_monitoring/cooldown_status.php";
// const char* serverStatus = "https://tagabaryo.com/group_8/noise_monitoring/status.php";

const int threshold = 500;

unsigned long lastSentTime = 0;
unsigned long lastCooldownSend = 0;
int sendInterval = 30000;  // Default 30 seconds

bool wasPaused = false;
unsigned long pauseStartTime = 0;

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\nConnected!");
  fetchInterval();
}

void loop() {
  bool isPlaying = isPlayMode();
  int soundValue = analogRead(A0);  // Read once for both logging and real-time sending

  // Just paused
  if (!isPlaying && !wasPaused) {
    pauseStartTime = millis();
    wasPaused = true;
    delay(100);
    return;
  }

  // Just resumed
  if (isPlaying && wasPaused) {
    unsigned long pausedDuration = millis() - pauseStartTime;
    lastSentTime += pausedDuration;
    lastCooldownSend += pausedDuration;
    wasPaused = false;
  }

  // Still paused
  if (!isPlaying) {
    delay(100);
    return;
  }

  // Only send real-time data if in play mode
  sendRealtimeLoudness(soundValue);

  // Normal operation (play mode)
  unsigned long currentTime = millis();
  unsigned long timeSinceLastSend = currentTime - lastSentTime;
  int cooldownRemaining = (sendInterval - timeSinceLastSend) / 1000;

  if (timeSinceLastSend < sendInterval) {
    if (currentTime - lastCooldownSend > 1000) {
      sendCooldownToServer(cooldownRemaining);
      lastCooldownSend = currentTime;
    }
  }

  if (soundValue > threshold && timeSinceLastSend >= sendInterval) {
    sendDataToServer(soundValue);
    lastSentTime = millis();
    fetchInterval();
  }

  delay(100);
}

void sendDataToServer(int loudness) {
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClient client;
    HTTPClient http;
    http.begin(client, String(serverLog) + "?loudness=" + loudness);
    if (http.GET() > 0) {
      Serial.println("Data sent to server.");
    } else {
      Serial.println("Failed to send data.");
    }
    http.end();
  }
}

void sendCooldownToServer(int cooldownSeconds) {
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClient client;
    HTTPClient http;
    http.begin(client, String(serverCooldown) + "?cooldown=" + cooldownSeconds);
    if (http.GET() > 0) {
      Serial.print("Cooldown sent: ");
      Serial.println(cooldownSeconds);
    } else {
      Serial.println("Failed to send cooldown.");
    }
    http.end();
  }
}

void fetchInterval() {
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClient client;
    HTTPClient http;
    http.begin(client, serverInterval);
    if (http.GET() > 0) {
      sendInterval = http.getString().toInt() * 1000;
      Serial.print("Updated interval (ms): ");
      Serial.println(sendInterval);
    } else {
      Serial.println("Failed to fetch interval.");
    }
    http.end();
  }
}

bool isPlayMode() {
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClient client;
    HTTPClient http;
    http.begin(client, serverStatus);
    if (http.GET() > 0) {
      String status = http.getString();
      http.end();
      return status == "play";
    }
    http.end();
  }
  return true;  // Assume play if status can't be fetched
}

// Realtime - new added
void sendRealtimeLoudness(int loudness) {
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClient client;
    HTTPClient http;
    http.begin(client, String("http://192.168.190.130/noise_monitoring/realtime_loudness.php") + "?value=" + loudness);
    // http.begin(client, String("https://tagabaryo.com/group_8/noise_monitoring/realtime_loudness.php") + "?value=" + loudness);
    http.GET();  // We don't need to handle the response
    http.end();
  }
}
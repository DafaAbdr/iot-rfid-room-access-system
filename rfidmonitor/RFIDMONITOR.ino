#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <Preferences.h>

  // PIN
  #define SS_PIN 5
#define RST_PIN 22
#define RELAY_PIN 26
#define BUZZER_PIN 27

// RUANGAN DEFAULT (dipakai kalau belum pernah di-set / memori kosong)
#define ROOM_ID_DEFAULT "R001"

// ============ DAFTAR KARTU KONFIGURASI RUANGAN ============
// Isi UID di bawah ini dengan UID kartu asli kamu.
// Cara dapat UID: scan kartu dulu, lihat di Serial Monitor baris "RFID: xxxx",
// lalu copy ke sini (huruf besar semua, tanpa spasi).
struct RoomCard {
  const char* uid;
  const char* roomId;
};

RoomCard roomCards[] = {
  { "1713B9BA", "R001" },   // Kartu 1 -> Ruang Keuangan
  { "056CE95E", "R002" },   // Kartu 2 -> Ruang Marbot
  { "1679A12A", "R003" },   // Kartu 3 -> Ruang X
  { "16B3EC2A", "R004" },   // Kartu 4 -> Ruang Y
  { "16FEF7FA", "R005" }    // Kartu 5 -> Ruang Z
};
const int totalRoomCards = sizeof(roomCards) / sizeof(roomCards[0]);
// ============================================================

// OBJECT
MFRC522 mfrc522(SS_PIN, RST_PIN);
LiquidCrystal_I2C lcd(0x27, 16, 2);
Preferences preferences;

// RUANGAN AKTIF (disimpan di NVS, diisi ulang saat setup())
String currentRoomId = ROOM_ID_DEFAULT;

// DATA WIFI
const char* ssid = "test1";
const char* password = "test0403";

// API
const char* serverInput = "http://192.168.137.1/RFIDMONITOR/api/rfidFuzzyMamdani.php";
const char* serverRFID = "http://192.168.137.1/RFIDMONITOR/api/rfid.php";

// BUZZER
void beepScan() {
  digitalWrite(BUZZER_PIN, HIGH);
  delay(150);
  digitalWrite(BUZZER_PIN, LOW);
}

void beepGagal() {
  digitalWrite(BUZZER_PIN, HIGH);
  delay(600);
  digitalWrite(BUZZER_PIN, LOW);
  delay(100);
  digitalWrite(BUZZER_PIN, HIGH);
  delay(600);
  digitalWrite(BUZZER_PIN, LOW);
}

void beepSukses() {
  digitalWrite(BUZZER_PIN, HIGH);
  delay(150);
  digitalWrite(BUZZER_PIN, LOW);
}

// Bunyi khusus saat ganti ruangan (2x beep pendek berbeda)
void beepGantiRuangan() {
  for (int i = 0; i < 2; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(100);
    digitalWrite(BUZZER_PIN, LOW);
    delay(100);
  }
}

// KONEKSI WIFI
void connectWiFi() {
  WiFi.begin(ssid, password);
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Menghubungkan");

  lcd.setCursor(0, 1);
  lcd.print("Wifi...");

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  lcd.clear();
  lcd.print("WiFi Terhubung");
  delay(1500);
}

// Cek apakah UID yang di-scan termasuk kartu konfigurasi ruangan.
// Kalau ya, ganti currentRoomId, simpan ke NVS, dan return true.
bool cekKartuKonfigurasi(String rfid) {
  for (int i = 0; i < totalRoomCards; i++) {
    if (rfid == String(roomCards[i].uid)) {
      currentRoomId = String(roomCards[i].roomId);

      preferences.begin("config", false);
      preferences.putString("room", currentRoomId);
      preferences.end();

      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("Ruangan Diubah:");
      lcd.setCursor(0, 1);
      lcd.print(currentRoomId);

      beepGantiRuangan();
      delay(2000);

      Serial.println("Ruangan aktif sekarang: " + currentRoomId);
      return true;
    }
  }
  return false;
}

// SETUP
void setup() {
  Serial.begin(9600);

  pinMode(RELAY_PIN, OUTPUT);
  pinMode(BUZZER_PIN, OUTPUT);

  digitalWrite(RELAY_PIN, HIGH);
  digitalWrite(BUZZER_PIN, LOW);

  SPI.begin();
  mfrc522.PCD_Init();

  lcd.init();
  lcd.backlight();

  // Ambil ruangan aktif terakhir dari memori (NVS)
  preferences.begin("config", false);
  currentRoomId = preferences.getString("room", ROOM_ID_DEFAULT);
  preferences.end();

  Serial.println("Ruangan aktif (dari memori): " + currentRoomId);

  connectWiFi();

  lcd.clear();
  lcd.print("Scan Kartu...");
}

// LOOPING
void loop() {

  // cek WiFi
  if (WiFi.status() != WL_CONNECTED) {
    connectWiFi();
    return;
  }

  // cek kartu
  if (!mfrc522.PICC_IsNewCardPresent()) return;
  if (!mfrc522.PICC_ReadCardSerial()) return;

  // ambil UID
  String rfid = "";

  for (int i = mfrc522.uid.size - 1; i >= 0; i--) {
    if (mfrc522.uid.uidByte[i] < 0x10) {
      rfid += "0";
    }
    rfid += String(mfrc522.uid.uidByte[i], HEX);
  }

  rfid.toUpperCase();

  Serial.println("RFID: " + rfid);

  // ====== CEK APAKAH INI KARTU KONFIGURASI RUANGAN ======
  if (cekKartuKonfigurasi(rfid)) {
    mfrc522.PICC_HaltA();
    mfrc522.PCD_StopCrypto1();
    lcd.clear();
    lcd.print("Scan Kartu...");
    return; // stop di sini, tidak lanjut kirim ke server sebagai akses
  }
  // ========================================================

  // tampil LCD
  lcd.clear();
  lcd.print("RFID:");
  lcd.setCursor(0, 1);
  lcd.print(rfid);

  beepScan();

  // KIRIM DATA KE WEB
  HTTPClient httpInput;
  httpInput.begin(serverRFID);
  httpInput.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String postDataInput = "rfid=" + rfid;

  int resInput = httpInput.POST(postDataInput);
  Serial.println("INPUT RES: " + String(resInput));

  httpInput.end();

  delay(1500);

  // KIRIM DATA UNTUK FUZZY LOGIC (NO RFID & ROOM ID)
  HTTPClient http;
  http.setTimeout(5000);
  http.begin(serverInput);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String postData = "rfid=" + rfid + "&ruangan=" + currentRoomId;

  int httpResponseCode = http.POST(postData);

  if (httpResponseCode > 0) {

    String response = http.getString();
    response.trim();

    Serial.println("RAW: [" + response + "]");

    if (response == "" || response.length() < 3) {
      lcd.clear();
      lcd.print("No Response");
      beepGagal();
      http.end();
      return;
    }

    if (response == "ERR") {
      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("Server Tidak");

      lcd.setCursor(0, 1);
      lcd.print("Merespon");
      beepGagal();
    }

    else if (response.indexOf('|') == -1) {
      lcd.clear();
      lcd.print("Format Error");
      beepGagal();
    }

    else {
      int p1 = response.indexOf('|');
      int p2 = response.indexOf('|', p1 + 1);

      String status = response.substring(0, p1);
      String nama = response.substring(p1 + 1, p2);

      lcd.clear();

      if (status == "Y") {
        // AKSES DIBERIKAN
        digitalWrite(RELAY_PIN, LOW);

        lcd.setCursor(0, 0);
        lcd.print("Akses Diberikan");

        lcd.setCursor(0, 1);
        lcd.print(nama);

        beepSukses();
        delay(5000);

        digitalWrite(RELAY_PIN, HIGH);
      }
      else {

    if (nama == "Tidak dikenal") {
        // INPUT KARTU BARU
        lcd.setCursor(0, 0);
        lcd.print("Kartu Tidak");

        lcd.setCursor(0, 1);
        lcd.print("Terdaftar");

        delay(1500);
    }
    else {
        // AKSES DITOLAK
        lcd.setCursor(0, 0);
        lcd.print("Akses Tidak");

        lcd.setCursor(0, 1);
        lcd.print("Diizinkan");

        beepGagal();
        delay(2000);
    }
}
    }
  }
  else {
    Serial.println("HTTP ERROR: " + String(httpResponseCode));

    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Server Tidak");

    lcd.setCursor(0, 1);
    lcd.print("Merespon");

    beepGagal();
  }

  http.end();

  // MEMBERHENTIKAN RFID
  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();

  delay(2000);

  lcd.clear();
  lcd.print("Scan Kartu...");
}

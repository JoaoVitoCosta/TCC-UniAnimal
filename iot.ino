#include <MAX30105.h>
/*
 * UniAnimal — Coleira ESP32: monitoramento de batimentos cardíacos
 * Pet: Lilo (id 24) · Coleira: COLEIRA-TAMBAU-123
 *
 * Sensor: MAX30102 (ou MAX30105) via I2C
 *   VIN -> 3V3 · GND -> GND · SDA -> GPIO21 · SCL -> GPIO22
 *
 * Bibliotecas (Arduino IDE > Gerenciar bibliotecas):
 *   - "SparkFun MAX3010x Pulse and Proximity Sensor Library"
 *   - "ArduinoJson" (Benoit Blanchon), versão 7
 *
 * Fluxo:
 *   1. Conecta no Wi-Fi e busca GET /api/v1/iot/config (faixa normal e intervalo).
 *   2. Lê o sensor continuamente e calcula o BPM (média das últimas batidas).
 *   3. A cada "intervalo_envio_seg" envia POST /api/v1/iot/telemetria.
 *   4. Mostra tudo no Monitor Serial (115200): BPM ao vivo, faixa, resposta da API.
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <Wire.h>
#include "MAX30105.h"
#include "heartRate.h"

// ===================== CONFIGURAÇÃO =====================
const char* WIFI_SSID  = "WIFI-EDUC";
const char* WIFI_SENHA = "ac3ce7ss0-EDUC";

// IP do computador que roda o Laravel (não use localhost: a ESP32 é outro aparelho).
// Rode o servidor com: php artisan serve --host=0.0.0.0 --port=8000
const char* API_BASE = "http://10.141.131.84:8000/api/v1";

// Chave do dispositivo da coleira COLEIRA-TAMBAU-123 (pet 24).
const char* CHAVE_DISPOSITIVO = "uak_SDE0RO3T2eXObhmuMram7A81LHMAgkqKEJCLAAfi";

const char* FIRMWARE_VERSAO = "esp32-bpm-1.0";
// ========================================================

MAX30105 sensor;

// Média móvel das últimas batidas
const byte TAMANHO_MEDIA = 4;
byte batidas[TAMANHO_MEDIA];
byte indiceBatida = 0;
byte batidasValidas = 0;
long ultimaBatidaMs = 0;
int bpmMedio = 0;

// Configuração vinda do servidor
unsigned long intervaloEnvioMs = 10000;
int faixaMin = 0, faixaMax = 0;
String nomeAnimal = "?";

unsigned long ultimoEnvioMs = 0;
unsigned long ultimoLogMs = 0;

const long IR_MINIMO_CONTATO = 50000;  // abaixo disso: sensor sem contato com a pele

void conectarWifi() {
  if (WiFi.status() == WL_CONNECTED) return;

  Serial.printf("[WiFi] Conectando em %s", WIFI_SSID);
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_SENHA);

  unsigned long inicio = millis();
  while (WiFi.status() != WL_CONNECTED && millis() - inicio < 15000) {
    delay(500);
    Serial.print(".");
  }
  Serial.println();

  if (WiFi.status() == WL_CONNECTED) {
    Serial.printf("[WiFi] Conectado. IP: %s\n", WiFi.localIP().toString().c_str());
  } else {
    Serial.println("[WiFi] Falhou. Tento de novo no próximo envio.");
  }
}

void prepararRequisicao(HTTPClient& http, const String& url) {
  http.begin(url);
  http.setTimeout(5000);
  http.addHeader("Authorization", String("Bearer ") + CHAVE_DISPOSITIVO);
  http.addHeader("Accept", "application/json");
}

void buscarConfig() {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  prepararRequisicao(http, String(API_BASE) + "/iot/config");
  int codigo = http.GET();

  if (codigo == 200) {
    JsonDocument doc;
    if (!deserializeJson(doc, http.getString())) {
      nomeAnimal       = doc["animal"]["nome"] | "?";
      intervaloEnvioMs = (doc["intervalo_envio_seg"] | 10) * 1000UL;
      faixaMin         = doc["faixas"]["batimento"]["min"] | 0;
      faixaMax         = doc["faixas"]["batimento"]["max"] | 0;
      Serial.printf("[API] Config OK: %s · faixa normal %d–%d bpm · envio a cada %lus\n",
                    nomeAnimal.c_str(), faixaMin, faixaMax, intervaloEnvioMs / 1000);
    }
  } else {
    Serial.printf("[API] Falha ao buscar config (HTTP %d): %s\n", codigo, http.getString().c_str());
  }
  http.end();
}

void enviarBatimento(int bpm) {
  conectarWifi();
  if (WiFi.status() != WL_CONNECTED) return;

  JsonDocument corpo;
  corpo["batimento_cardiaco"] = bpm;
  corpo["firmware_versao"] = FIRMWARE_VERSAO;
  String json;
  serializeJson(corpo, json);

  HTTPClient http;
  prepararRequisicao(http, String(API_BASE) + "/iot/telemetria");
  http.addHeader("Content-Type", "application/json");
  int codigo = http.POST(json);
  String resposta = http.getString();
  http.end();

  if (codigo == 201) {
    JsonDocument doc;
    if (!deserializeJson(doc, resposta)) {
      Serial.printf("[API] Enviado %d bpm · status do animal: %s\n", bpm, (const char*)(doc["status_animal"] | "?"));

      for (JsonObject alerta : doc["alertas_gerados"].as<JsonArray>()) {
        Serial.printf("[ALERTA] %s (%s)\n", (const char*)(alerta["titulo"] | ""), (const char*)(alerta["nivel"] | ""));
      }

      unsigned long proximo = (doc["proximo_envio_seg"] | 0) * 1000UL;
      if (proximo > 0) intervaloEnvioMs = proximo;
    }
  } else {
    // 401 = chave errada · 403 = coleira inativa/sem animal · 422 = valor inválido · 429 = envios demais
    Serial.printf("[API] Erro HTTP %d: %s\n", codigo, resposta.c_str());
  }
}

void setup() {
  Serial.begin(115200);
  delay(500);
  Serial.println("\n=== UniAnimal · Monitor de batimentos ===");

  if (!sensor.begin(Wire, I2C_SPEED_FAST)) {
    Serial.println("[Sensor] MAX30102 não encontrado. Confira a ligação (SDA 21, SCL 22, 3V3).");
    while (true) delay(1000);
  }
  sensor.setup();                    // configuração padrão da SparkFun
  sensor.setPulseAmplitudeRed(0x0A); // LED vermelho baixo: indica que está ligado
  sensor.setPulseAmplitudeGreen(0);

  conectarWifi();
  buscarConfig();
}

void loop() {
  long ir = sensor.getIR();
  bool comContato = ir >= IR_MINIMO_CONTATO;

  if (comContato && checkForBeat(ir)) {
    long agora = millis();
    long delta = agora - ultimaBatidaMs;
    ultimaBatidaMs = agora;

    float bpm = 60000.0 / delta;
    if (bpm > 20 && bpm < 300) {   // descarta ruído
      batidas[indiceBatida++] = (byte)bpm;
      indiceBatida %= TAMANHO_MEDIA;
      if (batidasValidas < TAMANHO_MEDIA) batidasValidas++;

      int soma = 0;
      for (byte i = 0; i < batidasValidas; i++) soma += batidas[i];
      bpmMedio = soma / batidasValidas;
    }
  }

  if (!comContato) {
    batidasValidas = 0;
    bpmMedio = 0;
  }

  // Monitor ao vivo, 1x por segundo
  if (millis() - ultimoLogMs >= 1000) {
    ultimoLogMs = millis();
    if (!comContato) {
      Serial.println("[Sensor] Sem contato — encoste o sensor na pele.");
    } else if (batidasValidas < TAMANHO_MEDIA) {
      Serial.printf("[Sensor] Medindo... (IR=%ld)\n", ir);
    } else {
      const char* situacao = "";
      if (faixaMax > 0) situacao = bpmMedio < faixaMin ? " ⬇ abaixo do normal" : bpmMedio > faixaMax ? " ⬆ acima do normal" : " ✓ normal";
      Serial.printf("[Sensor] %d bpm%s\n", bpmMedio, situacao);
    }
  }

  // Só envia quando há uma leitura estável
  if (batidasValidas == TAMANHO_MEDIA && millis() - ultimoEnvioMs >= intervaloEnvioMs) {
    ultimoEnvioMs = millis();
    enviarBatimento(bpmMedio);
  }
}

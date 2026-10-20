# Integração da coleira (IoT) com a API UniAnimal

Este guia é para quem vai programar o firmware da coleira (ESP32, ESP8266, Arduino com módulo Wi-Fi/GSM etc.).
O servidor já está pronto: basta a coleira enviar os dados no formato abaixo.

```
Coleira ──HTTP POST (JSON)──▶ /api/v1/iot/telemetria ──▶ banco unianimal_iot
                                                        ├─ leituras_saude (batimento, temperatura)
                                                        ├─ localizacoes   (GPS)
                                                        ├─ coleiras       (bateria, último contato)
                                                        └─ alertas        (gerados automaticamente)
                                       site e app Flutter leem desses mesmos dados
```

## 1. Credenciais da coleira

Cada coleira tem dois dados:

| Dado | Para que serve | Onde fica |
|---|---|---|
| **Código** (ex.: `COL-DC9FEAAA`) | Impresso na coleira. O tutor usa para cadastrar o animal e entrar no app | Tabela `coleiras.codigo` |
| **Chave do dispositivo** (ex.: `uak_…`) | Senha do hardware para enviar dados. Gravada no firmware | Só o hash fica no banco (`coleiras.chave_hash`) |

Gerar ou trocar a chave de uma coleira (a chave aparece **uma única vez**):

```
php artisan coleira:chave COL-DC9FEAAA
```

Criar coleiras novas em estoque, cada uma já com código e chave:

```
php artisan coleiras:provisionar 10
```

As chaves das coleiras de demonstração foram salvas em `storage/app/iot/chaves_coleiras.csv`.

> A coleira só consegue enviar dados depois que o código foi vinculado a um animal
> (cadastro pelo site ou pelo app). Coleira em estoque ou inativa recebe `403`.

## 2. Endpoints

Base: `http://SEU_SERVIDOR:8000/api/v1/iot`

Cabeçalhos obrigatórios em toda requisição:

```
Authorization: Bearer <chave do dispositivo>
Content-Type: application/json
Accept: application/json
```

### `GET /config` — chamar ao ligar a coleira

Devolve o intervalo de envio, a hora do servidor (para acertar o relógio), as faixas normais do animal e as zonas seguras.

```json
{
  "coleira": "COL-DC9FEAAA",
  "animal": { "id": 7, "nome": "Belinha", "especie": "Cachorro" },
  "intervalo_envio_seg": 60,
  "servidor_hora": "2026-10-07T10:00:00-03:00",
  "faixas": {
    "batimento":   { "chave": "cao-pequeno", "min": 80, "max": 140, "emergencia_min": 60, "emergencia_max": 170 },
    "temperatura": { "chave": "cao", "min": 37.5, "max": 39.2, "emergencia_min": 36.5, "emergencia_max": 40.0 }
  },
  "zonas_seguras": [ { "nome": "Casa", "latitude": -23.5098, "longitude": -46.6744, "raio_metros": 300 } ]
}
```

### `POST /telemetria` — envio normal (a cada `intervalo_envio_seg`)

Todos os campos são opcionais. Mande o que a coleira mediu naquele ciclo, com **pelo menos uma medição**.

| Campo | Tipo | Faixa aceita | Observação |
|---|---|---|---|
| `batimento_cardiaco` | inteiro | 10–400 | bpm |
| `temperatura` | decimal | 30–45 | °C |
| `latitude` / `longitude` | decimal | ±90 / ±180 | enviar os dois juntos |
| `precisao` | decimal | 0–5000 | metros (HDOP do GPS) |
| `bateria_percentual` | inteiro | 0–100 | |
| `firmware_versao` | texto | até 30 caracteres | ex.: `1.0.3` |
| `registrado_em` | data ISO 8601 | — | momento da medição; se omitido, usa a hora do servidor |

```json
{
  "batimento_cardiaco": 96,
  "temperatura": 38.4,
  "latitude": -23.5098123,
  "longitude": -46.6744456,
  "precisao": 6,
  "bateria_percentual": 82,
  "firmware_versao": "1.0.0"
}
```

Resposta `201`:

```json
{
  "recebido": true,
  "leitura_id": 120,
  "localizacao_id": 98,
  "alertas_gerados": [ { "id": 5, "tipo": "temperatura", "nivel": "emergencia", "titulo": "Temperatura alta" } ],
  "status_animal": "emergencia",
  "proximo_envio_seg": 60
}
```

### `POST /telemetria/lote` — envio acumulado (até 200 medições)

Quando a coleira fica sem internet, ela guarda as medições na memória e envia tudo de uma vez ao reconectar.
Cada item tem os mesmos campos acima e **deve trazer `registrado_em`**. O servidor processa da mais antiga para a mais recente.

```json
{ "leituras": [
  { "batimento_cardiaco": 90, "registrado_em": "2026-10-07T09:58:00-03:00" },
  { "batimento_cardiaco": 94, "latitude": -23.51, "longitude": -46.67, "registrado_em": "2026-10-07T09:59:00-03:00" }
] }
```

## 3. Respostas de erro

| Código | Quando | O que o firmware deve fazer |
|---|---|---|
| `401` | Chave ausente ou errada | Não insistir; sinalizar no LED/serial (chave precisa ser regravada) |
| `403` | Coleira inativa ou ainda sem animal | Aguardar alguns minutos e tentar de novo |
| `422` | Dado fora da faixa ou envio sem medição | Descartar aquela medição (a mensagem diz qual campo) |
| `429` | Mais de 120 envios por minuto | Respeitar `intervalo_envio_seg` |
| `5xx` / sem rede | Servidor fora do ar | Guardar na memória e enviar depois pelo `/telemetria/lote` |

Todas as tentativas, inclusive as recusadas, ficam registradas na tabela `telemetrias` (campo `erro`), o que ajuda a depurar.

## 4. O que o servidor faz sozinho

| Situação | Alerta gerado | Nível |
|---|---|---|
| Batimento fora da faixa normal do animal | "Batimento acima/abaixo do normal" | alerta ou emergência (tabela "Orientação de batimentos") |
| Temperatura fora da faixa | "Temperatura alta/baixa" | alerta ou emergência |
| Bateria 16–35% / ≤ 15% | "Bateria baixa" / "Bateria crítica" | alerta / emergência |
| Posição fora de todas as zonas seguras | "Fora da zona segura" | alerta |
| Coleira sem enviar por 3 intervalos (mín. 10 min) | "Coleira sem sinal" | alerta |

- Existe no máximo **um alerta aberto por tipo**: enquanto o problema continua, o mesmo alerta é atualizado.
- Quando o valor volta ao normal, o alerta é **resolvido automaticamente**.
- O status do animal (`saudavel` / `atencao` / `emergencia`) acompanha os alertas abertos.
- A verificação de "sem sinal" roda pelo agendador do Laravel. Deixe rodando: `php artisan schedule:work`.

## 5. Exemplo de firmware (ESP32 + Arduino IDE)

Bibliotecas: `WiFi`, `HTTPClient` (já vêm com o ESP32) e `ArduinoJson` (instalar pelo Gerenciador de Bibliotecas).

```cpp
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

const char* WIFI_SSID  = "SUA_REDE";
const char* WIFI_SENHA = "SUA_SENHA";
const char* SERVIDOR   = "http://192.168.0.10:8000";             // IP do PC rodando "php artisan serve --host=0.0.0.0"
const char* CHAVE      = "uak_COLE_AQUI_A_CHAVE_DA_COLEIRA";

unsigned long intervaloMs = 60000;                                  // atualizado pelo /config

int lerBatimento()   { return 95; }    // TODO: sensor de pulso (ex.: MAX30102)
float lerTemperatura(){ return 38.4; } // TODO: sensor de temperatura (ex.: MLX90614 / DS18B20)
int lerBateria()     { return 80; }    // TODO: divisor de tensão no ADC
bool lerGps(double &lat, double &lng, float &prec) { return false; } // TODO: módulo GPS (ex.: NEO-6M + TinyGPS++)

void buscarConfig() {
  HTTPClient http;
  http.begin(String(SERVIDOR) + "/api/v1/iot/config");
  http.addHeader("Authorization", String("Bearer ") + CHAVE);
  http.addHeader("Accept", "application/json");
  if (http.GET() == 200) {
    JsonDocument cfg;
    deserializeJson(cfg, http.getString());
    intervaloMs = (unsigned long) cfg["intervalo_envio_seg"].as<int>() * 1000UL;
  }
  http.end();
}

void enviarTelemetria() {
  JsonDocument doc;
  doc["batimento_cardiaco"] = lerBatimento();
  doc["temperatura"]        = lerTemperatura();
  doc["bateria_percentual"] = lerBateria();
  doc["firmware_versao"]    = "1.0.0";

  double lat, lng; float prec;
  if (lerGps(lat, lng, prec)) {
    doc["latitude"] = lat; doc["longitude"] = lng; doc["precisao"] = prec;
  }

  String corpo;
  serializeJson(doc, corpo);

  HTTPClient http;
  http.begin(String(SERVIDOR) + "/api/v1/iot/telemetria");
  http.addHeader("Authorization", String("Bearer ") + CHAVE);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");
  int status = http.POST(corpo);
  Serial.printf("Telemetria -> HTTP %d %s\n", status, http.getString().c_str());
  // Se status <= 0 ou >= 500: guardar 'corpo' + registrado_em na memória e
  // reenviar depois em /api/v1/iot/telemetria/lote.
  http.end();
}

void setup() {
  Serial.begin(115200);
  WiFi.begin(WIFI_SSID, WIFI_SENHA);
  while (WiFi.status() != WL_CONNECTED) { delay(500); Serial.print("."); }
  Serial.println("\nWi-Fi conectado");
  buscarConfig();
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) enviarTelemetria();
  delay(intervaloMs);
}
```

## 6. Testar sem hardware

Com o site rodando (`php artisan serve`), o comando abaixo faz exatamente o que a coleira fará: envia telemetria por HTTP com a chave.

```
php artisan iot:simular COL-DC9FEAAA --chave=uak_...            # dados normais a cada 10 s
php artisan iot:simular COL-DC9FEAAA --chave=uak_... --cenario=febre
```

Cenários: `normal`, `febre`, `taquicardia`, `bradicardia`, `fuga` (sai da zona segura), `bateria` (bateria crítica).
Sem `--chave`, o comando gera uma chave nova para a coleira (a antiga deixa de valer).

Pelo terminal, com curl:

```
curl -X POST http://127.0.0.1:8000/api/v1/iot/telemetria ^
  -H "Authorization: Bearer uak_..." -H "Content-Type: application/json" -H "Accept: application/json" ^
  -d "{\"batimento_cardiaco\": 96, \"temperatura\": 38.4, \"bateria_percentual\": 80}"
```

Os testes automáticos da API IoT ficam em `tests/Feature/IotTelemetriaTest.php` (`php artisan test`).

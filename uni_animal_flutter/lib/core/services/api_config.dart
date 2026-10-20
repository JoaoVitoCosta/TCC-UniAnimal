/// URL base da API Laravel.
///
/// IMPORTANTE — não é "mais um mock": esta constante é o único lugar do
/// app que precisa mudar para apontar para o backend real (dev, staging
/// ou produção). Nenhuma tela deve montar URLs na mão.
///
/// Padrões usados durante desenvolvimento local:
///   - Emulador Android:      http://10.0.2.2:8000
///   - Emulador iOS / web:    http://localhost:8000
///   - Dispositivo físico:    http://<ip-da-sua-máquina-na-rede>:8000
class ApiConfig {
  ApiConfig._();

  static const String baseUrl = String.fromEnvironment(
    'UNIANIMAL_API_URL',
    defaultValue: 'http://10.141.131.84:8000',
  );

  static const String apiV1 = '$baseUrl/api/v1';
}

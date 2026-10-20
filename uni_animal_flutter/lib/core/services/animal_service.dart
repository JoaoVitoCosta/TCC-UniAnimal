import '../models/pet.dart';
import 'api_client.dart';

/// Fonte real de dados dos animais do tutor logado. Cada método aqui é
/// uma chamada HTTP de verdade para o backend Laravel — nada de lista
/// fixa em memória.
class AnimalService {
  AnimalService._();

  static Future<List<Pet>> listar() async {
    final data = await ApiClient.get('/animais');
    final lista = (data as List).cast<Map<String, dynamic>>();
    return lista.map(Pet.fromApiJson).toList();
  }

  static Future<Pet> obter(String animalId) async {
    final data = await ApiClient.get('/animais/$animalId');
    return Pet.fromApiJson(data as Map<String, dynamic>);
  }

  /// Últimas 10 leituras de batimento cardíaco, da mais antiga para a
  /// mais recente (GET /animais/{id}/batimentos).
  static Future<List<HeartbeatPoint>> batimentos(String animalId) async {
    final data = await ApiClient.get('/animais/$animalId/batimentos');
    return (data['batimentos'] as List)
        .cast<Map<String, dynamic>>()
        .map(HeartbeatPoint.fromApiJson)
        .toList();
  }

  /// Alertas gerados pelo servidor a partir da telemetria da coleira, mais
  /// recentes primeiro (GET /animais/{id}/alertas). [abertos] = só os que
  /// ainda não foram resolvidos.
  static Future<List<AlertaApi>> alertas(String animalId, {bool abertos = true}) async {
    final data = await ApiClient.get('/animais/$animalId/alertas${abertos ? '?abertos=1' : ''}');
    return (data['alertas'] as List)
        .cast<Map<String, dynamic>>()
        .map(AlertaApi.fromApiJson)
        .toList();
  }

  /// Marca o alerta como visto pelo tutor (POST /alertas/{id}/lido).
  static Future<void> marcarAlertaLido(int alertaId) async {
    await ApiClient.post('/alertas/$alertaId/lido', {}, autenticado: true);
  }

  /// Busca o histórico de localizações e devolve um Pet com
  /// [Pet.historicoLocalizacoes] preenchido a partir de dados reais.
  static Future<Pet> obterComHistorico(String animalId) async {
    final pet = await obter(animalId);
    try {
      final data = await ApiClient.get('/animais/$animalId/localizacoes');
      final pontos = (data['localizacoes'] as List)
          .cast<Map<String, dynamic>>()
          .map((json) => LocationPoint(
                time: (json['registrado_em'] ?? '').toString(),
                latitude: (json['latitude'] as num).toDouble(),
                longitude: (json['longitude'] as num).toDouble(),
                precisao: (json['precisao'] as num?)?.toDouble(),
              ))
          .toList();

      return pet.copyWith(historicoLocalizacoes: pontos);
    } catch (_) {
      // Sem coleira/sem histórico ainda: devolve o pet mesmo assim, só
      // sem pontos de localização — não é motivo para travar a tela.
      return pet;
    }
  }

  /// Cadastra um novo animal vinculando um código de coleira JÁ existente
  /// no banco (provisionado). Espelha exatamente a regra do site.
  static Future<Pet> cadastrar({
    required String nome,
    required String especie,
    required String porte,
    required String codigoColeira,
    String? raca,
    int? idade,
    double? peso,
  }) async {
    final data = await ApiClient.post('/animais', {
      'nome': nome,
      'especie': especie,
      'porte': porte,
      'codigo_coleira': codigoColeira,
      if (raca != null) 'raca': raca,
      if (idade != null) 'idade': idade,
      if (peso != null) 'peso': peso,
    }, autenticado: true);

    return Pet.fromApiJson(data);
  }
}

import 'package:shared_preferences/shared_preferences.dart';

/// Guarda o token emitido pelo backend após o login real por código da
/// coleira. Isto NÃO é autenticação local — o token só existe depois que
/// o backend validou o código no banco de dados; aqui só persistimos o
/// resultado entre aberturas do app (para não pedir o código toda vez).
class TokenStorage {
  TokenStorage._();

  static const _tokenKey = 'uni_animal_api_token';
  static const _tutorNomeKey = 'uni_animal_tutor_nome';

  static Future<void> salvar({required String token, String? tutorNome}) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_tokenKey, token);
    if (tutorNome != null) {
      await prefs.setString(_tutorNomeKey, tutorNome);
    }
  }

  static Future<String?> obterToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_tokenKey);
  }

  static Future<String?> obterTutorNome() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_tutorNomeKey);
  }

  static Future<void> limpar() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
    await prefs.remove(_tutorNomeKey);
  }
}

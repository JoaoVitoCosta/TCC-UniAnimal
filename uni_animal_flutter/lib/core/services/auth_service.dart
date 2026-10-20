import 'api_client.dart';
import 'token_storage.dart';

/// Login do app: SEMPRE pelo código da coleira, SEMPRE validado no
/// backend. Não existe caminho alternativo aqui — nada de
/// `Future.delayed` fingindo sucesso, nada de "qualquer código entra".
class AuthService {
  AuthService._();

  static Future<void> loginComCodigoDaColeira(String codigo) async {
    final data = await ApiClient.post('/auth/coleira', {
      'codigo': codigo.trim(),
    });

    final token = data['token'] as String?;
    if (token == null) {
      throw ApiException('O servidor não retornou uma sessão válida.');
    }

    final tutor = data['tutor'] as Map<String, dynamic>?;
    await TokenStorage.salvar(token: token, tutorNome: tutor?['nome']);
  }

  static Future<bool> estaLogado() async {
    final token = await TokenStorage.obterToken();
    return token != null;
  }

  static Future<void> logout() async {
    try {
      await ApiClient.post('/auth/logout', {}, autenticado: true);
    } catch (_) {
      // Mesmo se a chamada falhar (ex.: sem internet), limpamos a sessão
      // local — o usuário pediu para sair, não podemos travá-lo aqui.
    } finally {
      await TokenStorage.limpar();
    }
  }
}

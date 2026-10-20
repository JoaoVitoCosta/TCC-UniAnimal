import 'dart:convert';
import 'dart:io';

import 'package:http/http.dart' as http;

import 'api_config.dart';
import 'token_storage.dart';

/// Erro de API com a mensagem que o backend realmente devolveu (nunca uma
/// mensagem genérica inventada no cliente, para o usuário saber o que
/// aconteceu de verdade: código inválido, sem conexão, sessão expirada...).
class ApiException implements Exception {
  final String message;
  final int? statusCode;

  ApiException(this.message, {this.statusCode});

  @override
  String toString() => message;
}

class ApiClient {
  ApiClient._();

  static Future<Map<String, String>> _headers({bool autenticado = false}) async {
    final headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    };

    if (autenticado) {
      final token = await TokenStorage.obterToken();
      if (token == null) {
        throw ApiException('Sessão não encontrada. Faça login novamente.', statusCode: 401);
      }
      headers['Authorization'] = 'Bearer $token';
    }

    return headers;
  }

  static Future<Map<String, dynamic>> post(
    String path,
    Map<String, dynamic> body, {
    bool autenticado = false,
  }) async {
    try {
      final response = await http
          .post(
            Uri.parse('${ApiConfig.apiV1}$path'),
            headers: await _headers(autenticado: autenticado),
            body: jsonEncode(body),
          )
          .timeout(const Duration(seconds: 15));

      return _tratarResposta(response);
    } on SocketException {
      throw ApiException('Sem conexão com o servidor. Verifique sua internet.');
    } on ApiException {
      rethrow;
    } catch (_) {
      throw ApiException('Não foi possível se conectar ao servidor. Tente novamente.');
    }
  }

  static Future<dynamic> get(String path, {bool autenticado = true}) async {
    try {
      final response = await http
          .get(
            Uri.parse('${ApiConfig.apiV1}$path'),
            headers: await _headers(autenticado: autenticado),
          )
          .timeout(const Duration(seconds: 15));

      return _tratarResposta(response);
    } on SocketException {
      throw ApiException('Sem conexão com o servidor. Verifique sua internet.');
    } on ApiException {
      rethrow;
    } catch (_) {
      throw ApiException('Não foi possível se conectar ao servidor. Tente novamente.');
    }
  }

  static dynamic _tratarResposta(http.Response response) {
    dynamic decoded;
    try {
      decoded = response.body.isNotEmpty ? jsonDecode(response.body) : null;
    } catch (_) {
      decoded = null;
    }

    if (response.statusCode >= 200 && response.statusCode < 300) {
      return decoded;
    }

    // Laravel devolve erros de validação em errors: { campo: [msgs] } e
    // mensagens diretas em message. Tentamos extrair a mais específica.
    String mensagem = 'Ocorreu um erro. Tente novamente.';
    if (decoded is Map) {
      if (decoded['errors'] is Map && (decoded['errors'] as Map).isNotEmpty) {
        final primeiraLista = (decoded['errors'] as Map).values.first;
        if (primeiraLista is List && primeiraLista.isNotEmpty) {
          mensagem = primeiraLista.first.toString();
        }
      } else if (decoded['message'] != null) {
        mensagem = decoded['message'].toString();
      } else if (decoded['error'] != null) {
        // Handlers de 404/429 do bootstrap/app.php usam a chave "error".
        mensagem = decoded['error'].toString();
      }
    }

    throw ApiException(mensagem, statusCode: response.statusCode);
  }
}

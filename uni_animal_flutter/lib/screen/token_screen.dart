import 'package:flutter/material.dart';

import '../core/app_colors.dart';
import '../core/services/animal_service.dart';
import '../core/services/api_client.dart';

/// Cadastro de um novo animal + vínculo do código da coleira.
///
/// Chama a mesma regra de negócio do site (POST /api/v1/animais): o
/// código precisa já existir no banco como uma coleira em estoque, ainda
/// não vinculada. Nada aqui é resolvido localmente — se o código não
/// existir ou já estiver em uso, o backend recusa e a tela mostra o erro
/// real devolvido pela API.
class TokenScreen extends StatefulWidget {
  const TokenScreen({super.key});

  @override
  State<TokenScreen> createState() => _TokenScreenState();
}

class _TokenScreenState extends State<TokenScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nomeController = TextEditingController();
  final _tokenController = TextEditingController();

  String _especie = 'Cachorro';
  String _porte = 'medio';

  bool loading = false;
  String? _erro;

  @override
  void dispose() {
    _nomeController.dispose();
    _tokenController.dispose();
    super.dispose();
  }

  Future<void> conectarColeira() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      loading = true;
      _erro = null;
    });

    try {
      await AnimalService.cadastrar(
        nome: _nomeController.text.trim(),
        especie: _especie,
        porte: _porte,
        codigoColeira: _tokenController.text.trim(),
      );

      if (!mounted) return;
      Navigator.pop(context, true);
    } on ApiException catch (e) {
      setState(() => _erro = e.message);
    } catch (_) {
      setState(() => _erro = 'Não foi possível cadastrar. Tente novamente.');
    } finally {
      if (mounted) setState(() => loading = false);
    }
  }

  InputDecoration _decoration(String hint, {IconData? icon}) {
    return InputDecoration(
      hintText: hint,
      hintStyle: TextStyle(color: Colors.white.withOpacity(.35)),
      prefixIcon: icon != null ? Icon(icon, color: AppColors.primary) : null,
      filled: true,
      fillColor: Colors.white.withOpacity(.05),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(18),
        borderSide: BorderSide(color: Colors.white.withOpacity(.08)),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(18),
        borderSide: const BorderSide(color: AppColors.primary, width: 2),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.close, color: Colors.white70),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      extendBodyBehindAppBar: true,
      body: Container(
        width: double.infinity,
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [Color(0xff090B10), Color(0xff10141F), Color(0xff161B28)],
          ),
        ),
        child: SafeArea(
          child: Center(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(30),
              child: Container(
                padding: const EdgeInsets.all(30),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(.04),
                  borderRadius: BorderRadius.circular(30),
                  border: Border.all(color: Colors.white.withOpacity(.08)),
                ),
                child: Form(
                  key: _formKey,
                  child: Column(
                    children: [
                      Container(
                        width: 80,
                        height: 80,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: AppColors.primary.withOpacity(.15),
                        ),
                        child: const Center(
                          child: Text("🐾", style: TextStyle(fontSize: 38)),
                        ),
                      ),

                      const SizedBox(height: 20),

                      const Text(
                        "Novo animal",
                        style: TextStyle(fontSize: 26, fontWeight: FontWeight.bold),
                      ),

                      const SizedBox(height: 6),

                      Text(
                        "Cadastre o animal e vincule o código\nda coleira que veio com ele.",
                        textAlign: TextAlign.center,
                        style: TextStyle(color: Colors.white.withOpacity(.65), height: 1.5),
                      ),

                      const SizedBox(height: 28),

                      TextFormField(
                        controller: _nomeController,
                        style: const TextStyle(color: Colors.white),
                        decoration: _decoration("Nome do animal", icon: Icons.pets),
                        validator: (v) =>
                            (v == null || v.trim().isEmpty) ? "Digite o nome" : null,
                      ),

                      const SizedBox(height: 16),

                      DropdownButtonFormField<String>(
                        value: _especie,
                        dropdownColor: const Color(0xff161B28),
                        style: const TextStyle(color: Colors.white),
                        decoration: _decoration("Espécie"),
                        items: const [
                          DropdownMenuItem(value: 'Cachorro', child: Text('Cachorro')),
                          DropdownMenuItem(value: 'Gato', child: Text('Gato')),
                          DropdownMenuItem(value: 'Cavalo', child: Text('Cavalo')),
                          DropdownMenuItem(value: 'Coelho', child: Text('Coelho')),
                        ],
                        onChanged: (v) => setState(() => _especie = v ?? _especie),
                      ),

                      const SizedBox(height: 16),

                      DropdownButtonFormField<String>(
                        value: _porte,
                        dropdownColor: const Color(0xff161B28),
                        style: const TextStyle(color: Colors.white),
                        decoration: _decoration("Porte"),
                        items: const [
                          DropdownMenuItem(value: 'pequeno', child: Text('Pequeno')),
                          DropdownMenuItem(value: 'medio', child: Text('Médio')),
                          DropdownMenuItem(value: 'grande', child: Text('Grande')),
                        ],
                        onChanged: (v) => setState(() => _porte = v ?? _porte),
                      ),

                      const SizedBox(height: 16),

                      TextFormField(
                        controller: _tokenController,
                        textCapitalization: TextCapitalization.characters,
                        style: const TextStyle(color: Colors.white),
                        decoration: _decoration("UNI-XXXX-XXXX", icon: Icons.key),
                        validator: (v) => (v == null || v.trim().isEmpty)
                            ? "Digite o código da coleira"
                            : null,
                      ),

                      if (_erro != null) ...[
                        const SizedBox(height: 14),
                        Container(
                          width: double.infinity,
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: const Color(0xffF04B58).withOpacity(.12),
                            borderRadius: BorderRadius.circular(14),
                          ),
                          child: Text(
                            _erro!,
                            style: const TextStyle(color: Color(0xffF04B58)),
                          ),
                        ),
                      ],

                      const SizedBox(height: 28),

                      SizedBox(
                        width: double.infinity,
                        height: 58,
                        child: ElevatedButton(
                          onPressed: loading ? null : conectarColeira,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppColors.primary,
                            foregroundColor: Colors.black,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(18),
                            ),
                          ),
                          child: loading
                              ? const CircularProgressIndicator(color: Colors.black)
                              : const Text(
                                  "Cadastrar e conectar coleira",
                                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                                ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

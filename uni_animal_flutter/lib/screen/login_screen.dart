import 'package:flutter/material.dart';
import 'package:uni_animal_flutter/core/app_colors.dart';
import 'package:uni_animal_flutter/core/services/api_client.dart';
import 'package:uni_animal_flutter/core/services/auth_service.dart';
import 'package:uni_animal_flutter/screen/pets_screen.dart';

/// Tela de acesso do app — ÚNICO fluxo de login: o código único impresso
/// na coleira, validado de verdade no backend (Flutter -> API -> banco).
///
/// Não existe login por e-mail/senha aqui de propósito: essa é a regra
/// de produto (acesso simples para o usuário final) e a arquitetura
/// (nada de autenticação simulada no cliente).
class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _codigoController = TextEditingController();

  bool _loading = false;
  String? _erro;

  @override
  void dispose() {
    _codigoController.dispose();
    super.dispose();
  }

  Future<void> _entrar() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _loading = true;
      _erro = null;
    });

    try {
      // Chamada HTTP real: POST /api/v1/auth/coleira. O backend valida o
      // código no banco e só então devolve um token de sessão de verdade.
      await AuthService.loginComCodigoDaColeira(_codigoController.text);

      if (!mounted) return;
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => const PetsScreen()),
      );
    } on ApiException catch (e) {
      setState(() => _erro = e.message);
    } catch (_) {
      setState(() => _erro = 'Não foi possível entrar. Tente novamente.');
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  InputDecoration _decoration(String hint, IconData icon) {
    return InputDecoration(
      hintText: hint,
      hintStyle: TextStyle(color: Colors.white.withOpacity(.35)),
      prefixIcon: Icon(icon, color: AppColors.primary),
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
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(18),
        borderSide: const BorderSide(color: Color(0xffF04B58)),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
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
                        width: 90,
                        height: 90,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: AppColors.primary.withOpacity(.15),
                        ),
                        child: const Center(
                          child: Text("🐾", style: TextStyle(fontSize: 42)),
                        ),
                      ),

                      const SizedBox(height: 22),

                      const Text(
                        "UniAnimal",
                        style: TextStyle(
                          fontSize: 30,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),

                      const SizedBox(height: 4),

                      Text(
                        "Coleira Inteligente IoT",
                        style: TextStyle(color: Colors.white.withOpacity(.7)),
                      ),

                      const SizedBox(height: 35),

                      Text(
                        "Digite o código único da sua coleira\npara acompanhar seu animal.",
                        textAlign: TextAlign.center,
                        style: TextStyle(color: Colors.white.withOpacity(.65), height: 1.5),
                      ),

                      const SizedBox(height: 25),

                      TextFormField(
                        controller: _codigoController,
                        textCapitalization: TextCapitalization.characters,
                        style: const TextStyle(color: Colors.white, letterSpacing: 1.2),
                        decoration: _decoration("COL-XXXX-XXXX", Icons.key),
                        validator: (v) {
                          if (v == null || v.trim().isEmpty) {
                            return "Digite o código da coleira";
                          }
                          return null;
                        },
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

                      const SizedBox(height: 30),

                      SizedBox(
                        width: double.infinity,
                        height: 58,
                        child: ElevatedButton(
                          onPressed: _loading ? null : _entrar,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppColors.primary,
                            foregroundColor: Colors.black,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(18),
                            ),
                          ),
                          child: _loading
                              ? const SizedBox(
                                  width: 24,
                                  height: 24,
                                  child: CircularProgressIndicator(
                                    color: Colors.black,
                                    strokeWidth: 2.5,
                                  ),
                                )
                              : const Text(
                                  "Entrar com o código",
                                  style: TextStyle(
                                    fontWeight: FontWeight.bold,
                                    fontSize: 17,
                                  ),
                                ),
                        ),
                      ),

                      const SizedBox(height: 22),

                      Text(
                        "O código vem impresso na coleira UniAnimal\n"
                        "e é validado com o servidor a cada acesso.",
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          color: Colors.white.withOpacity(.4),
                          fontSize: 12,
                          height: 1.5,
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

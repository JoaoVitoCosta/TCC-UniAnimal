import 'dart:async';

import 'package:flutter/material.dart';

import '../core/app_colors.dart';
import '../core/services/auth_service.dart';
import 'login_screen.dart';
import 'pets_screen.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _decidirDestino();
  }

  Future<void> _decidirDestino() async {
    // Tempo mínimo só para a marca aparecer — a decisão real de para onde
    // ir depende de já existir (ou não) um token de sessão salvo.
    final espera = Future.delayed(const Duration(seconds: 2));
    final logado = await AuthService.estaLogado();
    await espera;

    if (!mounted) return;

    Navigator.pushReplacement(
      context,
      MaterialPageRoute(
        builder: (_) => logado ? const PetsScreen() : const LoginScreen(),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,

            end: Alignment.bottomRight,

            colors: [
              AppColors.background,

              Color(0xff10141F),

              Color(0xff161B28),
            ],
          ),
        ),

        child: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,

            children: [
              Container(
                width: 100,

                height: 100,

                decoration: BoxDecoration(
                  shape: BoxShape.circle,

                  color: AppColors.primary.withOpacity(.15),
                ),

                child: const Center(
                  child: Text("🐾", style: TextStyle(fontSize: 48)),
                ),
              ),

              const SizedBox(height: 30),

              const Text(
                "UniAnimal",

                style: TextStyle(fontSize: 34, fontWeight: FontWeight.bold),
              ),

              const SizedBox(height: 8),

              Text(
                "Coleira Inteligente",

                style: TextStyle(
                  color: Colors.white.withOpacity(.7),

                  fontSize: 16,
                ),
              ),

              const SizedBox(height: 40),

              const CircularProgressIndicator(color: AppColors.primary),
            ],
          ),
        ),
      ),
    );
  }
}

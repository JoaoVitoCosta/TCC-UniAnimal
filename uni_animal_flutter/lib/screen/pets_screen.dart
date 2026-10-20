import 'package:flutter/material.dart';
import 'package:uni_animal_flutter/core/models/pet.dart';
import 'package:uni_animal_flutter/core/services/animal_service.dart';
import 'package:uni_animal_flutter/core/services/api_client.dart';
import 'package:uni_animal_flutter/core/services/auth_service.dart';
import 'package:uni_animal_flutter/screen/dashboard_screen.dart';
import 'package:uni_animal_flutter/screen/login_screen.dart';

/// Tela "Meus Animais" — hub do app depois do login. Busca os animais do
/// tutor autenticado direto da API (GET /api/v1/animais); não há mais
/// nenhuma lista fixa em memória.
class PetsScreen extends StatefulWidget {
  const PetsScreen({super.key});

  @override
  State<PetsScreen> createState() => _PetsScreenState();
}

class _PetsScreenState extends State<PetsScreen> {
  List<Pet> pets = [];
  bool _loading = true;
  String? _erro;

  @override
  void initState() {
    super.initState();
    _carregar();
  }

  Future<void> _carregar() async {
    setState(() {
      _loading = true;
      _erro = null;
    });

    try {
      final resultado = await AnimalService.listar();
      if (!mounted) return;
      setState(() {
        pets = resultado;
        _loading = false;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() {
        _erro = e.message;
        _loading = false;
      });

      // Sessão expirada/token inválido: manda de volta pro login.
      if (e.statusCode == 401) {
        await AuthService.logout();
        if (mounted) {
          Navigator.pushAndRemoveUntil(
            context,
            MaterialPageRoute(builder: (_) => const LoginScreen()),
            (route) => false,
          );
        }
      }
    }
  }

  Future<void> _sair() async {
    await AuthService.logout();
    if (!mounted) return;
    Navigator.pushAndRemoveUntil(
      context,
      MaterialPageRoute(builder: (_) => const LoginScreen()),
      (route) => false,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xff090B10),

      appBar: AppBar(
        backgroundColor: const Color(0xff090B10),
        elevation: 0,
        title: const Text(
          "Meu Animal",
          style: TextStyle(fontWeight: FontWeight.bold),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout, color: Colors.white70),
            tooltip: "Sair da conta",
            onPressed: _sair,
          ),
        ],
      ),

      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: _carregar,
          child: _buildBody(context),
        ),
      ),
    );
  }

  Widget _buildBody(BuildContext context) {
    if (_loading) {
      return const Center(child: CircularProgressIndicator(color: Color(0xff00E5A8)));
    }

    if (_erro != null) {
      return ListView(
        children: [
          const SizedBox(height: 80),
          Padding(
            padding: const EdgeInsets.all(30),
            child: Column(
              children: [
                const Icon(Icons.wifi_off, size: 48, color: Colors.white38),
                const SizedBox(height: 16),
                Text(
                  _erro!,
                  textAlign: TextAlign.center,
                  style: const TextStyle(color: Colors.white70),
                ),
                const SizedBox(height: 16),
                OutlinedButton(onPressed: _carregar, child: const Text("Tentar novamente")),
              ],
            ),
          ),
        ],
      );
    }

    if (pets.isEmpty) {
      return ListView(children: [_buildEmptyState(context)]);
    }

    return ListView.separated(
      padding: const EdgeInsets.fromLTRB(20, 8, 20, 100),
      itemCount: pets.length,
      separatorBuilder: (_, __) => const SizedBox(height: 16),
      itemBuilder: (context, index) => _PetTile(
        pet: pets[index],
        onTap: () async {
          await Navigator.push(
            context,
            MaterialPageRoute(
              builder: (_) => DashboardScreen(pet: pets[index]),
            ),
          );
          _carregar();
        },
      ),
    );
  }

  Widget _buildEmptyState(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.only(top: 60, left: 30, right: 30, bottom: 30),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text("🐾", style: TextStyle(fontSize: 56)),
            const SizedBox(height: 20),
            const Text(
              "Nenhum animal cadastrado ainda",
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 10),
            Text(
              "Nenhum animal está vinculado ao código\ndesta coleira UniAnimal.",
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.white.withOpacity(.6), height: 1.5),
            ),
          ],
        ),
      ),
    );
  }
}

class _PetTile extends StatelessWidget {
  final Pet pet;
  final VoidCallback onTap;

  const _PetTile({required this.pet, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return InkWell(
      borderRadius: BorderRadius.circular(26),
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: const Color(0xff141B27),
          borderRadius: BorderRadius.circular(26),
        ),
        child: Row(
          children: [
            Container(
              width: 60,
              height: 60,
              decoration: const BoxDecoration(
                color: Colors.white,
                shape: BoxShape.circle,
              ),
              child: Center(
                child: Text(pet.emoji, style: const TextStyle(fontSize: 30)),
              ),
            ),

            const SizedBox(width: 16),

            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    pet.nome,
                    style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 3),
                  Text(
                    "${pet.especie} · ${pet.raca}",
                    style: const TextStyle(color: Colors.white54, fontSize: 13),
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Container(
                        padding:
                            const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: pet.statusColor.withOpacity(.15),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          pet.statusLabel,
                          style: TextStyle(
                            color: pet.statusColor,
                            fontWeight: FontWeight.bold,
                            fontSize: 11,
                          ),
                        ),
                      ),
                      const SizedBox(width: 10),
                      Icon(Icons.favorite, size: 15, color: pet.batimentoColor),
                      const SizedBox(width: 3),
                      Text(
                        "${pet.batimentoLabel} bpm",
                        style: TextStyle(color: pet.batimentoColor, fontSize: 12),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            const Icon(Icons.chevron_right, color: Colors.white38),
          ],
        ),
      ),
    );
  }
}

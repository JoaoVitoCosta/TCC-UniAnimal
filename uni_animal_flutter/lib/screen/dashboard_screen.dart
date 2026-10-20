import 'dart:async';

import 'package:flutter/material.dart';
import 'package:uni_animal_flutter/components/alerts_section.dart';
import 'package:uni_animal_flutter/components/custom_app_bar.dart';
import 'package:uni_animal_flutter/components/hero_pet_card.dart';
import 'package:uni_animal_flutter/components/history_card.dart';
import 'package:uni_animal_flutter/components/location_card.dart';
import 'package:uni_animal_flutter/components/metric_grid.dart';
import 'package:uni_animal_flutter/core/models/pet.dart';
import 'package:uni_animal_flutter/core/services/animal_service.dart';
import 'package:uni_animal_flutter/screen/history_screen.dart';
import 'package:uni_animal_flutter/screen/map_screen.dart';

class DashboardScreen extends StatefulWidget {
  final Pet pet;

  const DashboardScreen({super.key, required this.pet});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  late Pet _pet;
  bool _carregandoHistorico = true;

  /// Atualização "quase em tempo real": gráfico de batimentos, alertas
  /// abertos (gerados pelo servidor a partir da coleira) e status do animal.
  static const _intervaloBatimentos = Duration(seconds: 10);
  List<HeartbeatPoint> _batimentos = [];
  List<AlertaApi> _alertas = [];
  Timer? _timerBatimentos;
  bool _buscandoBatimentos = false;

  @override
  void initState() {
    super.initState();
    _pet = widget.pet;
    _carregarHistorico();
    _carregarBatimentos();
    _timerBatimentos = Timer.periodic(_intervaloBatimentos, (_) => _carregarBatimentos());
  }

  @override
  void dispose() {
    _timerBatimentos?.cancel();
    super.dispose();
  }

  Future<void> _carregarBatimentos() async {
    // Evita empilhar requisições se o servidor demorar mais que 10s.
    if (_buscandoBatimentos) return;
    _buscandoBatimentos = true;
    try {
      final resultados = await Future.wait([
        AnimalService.batimentos(_pet.id),
        AnimalService.alertas(_pet.id),
        AnimalService.obter(_pet.id),
      ]);
      if (!mounted) return;
      setState(() {
        _batimentos = resultados[0] as List<HeartbeatPoint>;
        _alertas = resultados[1] as List<AlertaApi>;
        // Status/temperatura atualizados, mantendo a rota já carregada.
        _pet = (resultados[2] as Pet).copyWith(historicoLocalizacoes: _pet.historicoLocalizacoes);
      });
    } catch (_) {
      // Falha pontual (ex.: sem internet): mantém o último gráfico e
      // tenta de novo no próximo ciclo.
    } finally {
      _buscandoBatimentos = false;
    }
  }

  Future<void> _carregarHistorico() async {
    try {
      final completo = await AnimalService.obterComHistorico(_pet.id);
      if (!mounted) return;
      setState(() {
        _pet = completo;
        _carregandoHistorico = false;
      });
    } catch (_) {
      // Sem histórico ainda não é erro fatal — mantém os dados básicos
      // que já vieram da listagem e só some com o indicador de loading.
      if (mounted) setState(() => _carregandoHistorico = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final pet = _pet;

    return Scaffold(
      backgroundColor: const Color(0xff090B10),

      body: SafeArea(
        child: SingleChildScrollView(
          physics: const BouncingScrollPhysics(),

          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15),

            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,

              children: [
                CustomAppBar(
                  subtitle: "Acompanhando ${pet.nome}",
                  onBack: () => Navigator.pop(context),
                ),

                const SizedBox(height: 25),

                HeroPetCard(pet: pet),

                if (_carregandoHistorico) ...[
                  const SizedBox(height: 14),
                  const LinearProgressIndicator(
                    color: Color(0xff00E5A8),
                    backgroundColor: Color(0xff1B2434),
                  ),
                ],

                const SizedBox(height: 30),

                MetricGrid(pet: pet, batimentos: _batimentos),

                const SizedBox(height: 30),

                AlertsSection(pet: pet, batimentos: _batimentos, alertasServidor: _alertas),

                const SizedBox(height: 30),

                LocationCard(
                  pet: pet,
                  onTap: () => Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => MapScreen(pet: pet)),
                  ),
                ),

                const SizedBox(height: 30),

                HistoryCard(
                  pet: pet,
                  onVerTudo: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => HistoryScreen(pet: pet),
                      ),
                    );
                  },
                ),

                const SizedBox(height: 30),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

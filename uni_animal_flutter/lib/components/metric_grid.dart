import 'package:flutter/material.dart';
import 'package:uni_animal_flutter/components/heart_rate_card.dart';
import 'package:uni_animal_flutter/components/metric_card.dart';
import 'package:uni_animal_flutter/core/models/pet.dart';

class MetricGrid extends StatelessWidget {
  final Pet pet;

  /// Últimas leituras de batimento (atualizadas periodicamente pelo Dashboard).
  final List<HeartbeatPoint> batimentos;

  const MetricGrid({super.key, required this.pet, this.batimentos = const []});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        HeartRateCard(pontos: batimentos, bpmAtual: pet.batimentoCardiaco),

        const SizedBox(height: 18),

        GridView.count(
          physics: const NeverScrollableScrollPhysics(),
          shrinkWrap: true,
          crossAxisCount: 2,
          mainAxisSpacing: 18,
          crossAxisSpacing: 18,
          childAspectRatio: 1.15,
          children: [
            MetricCard(
              title: "GPS",
              value: pet.gpsOnline ? "Ativo" : "Offline",
              unit: pet.gpsOnline ? "online" : "sem sinal",
              icon: Icons.gps_fixed,
              color: pet.gpsOnline ? const Color(0xff10D078) : const Color(0xffF04B58),
            ),

            // Última temperatura enviada pela coleira (leituras_saude).
            MetricCard(
              title: "Temperatura",
              value: pet.temperatura != null ? pet.temperatura!.toStringAsFixed(1) : "--",
              unit: "°C",
              icon: Icons.thermostat,
              color: Colors.deepOrangeAccent,
            ),

            // Alertas abertos gerados pelo servidor (tabela alertas).
            MetricCard(
              title: "Alertas",
              value: "${pet.alertasAbertos}",
              unit: pet.alertasAbertos == 1 ? "aberto" : "abertos",
              icon: Icons.notifications_active,
              color: pet.alertasAbertos > 0 ? const Color(0xffF5A623) : const Color(0xff10D078),
            ),

            MetricCard(
              title: "Idade",
              value: "${pet.idade}",
              unit: pet.idade == 1 ? "ano" : "anos",
              icon: Icons.cake,
              color: Colors.purpleAccent,
            ),

            MetricCard(
              title: "Porte",
              value: pet.porte[0].toUpperCase() + pet.porte.substring(1),
              unit: "porte",
              icon: Icons.straighten,
              color: Colors.lightBlue,
            ),

            MetricCard(
              title: "Peso",
              value: pet.peso.toStringAsFixed(1),
              unit: "kg",
              icon: Icons.monitor_weight,
              color: Colors.orange,
            ),
          ],
        ),
      ],
    );
  }
}

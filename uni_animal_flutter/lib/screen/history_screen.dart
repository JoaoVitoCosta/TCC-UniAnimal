import 'package:flutter/material.dart';
import 'package:uni_animal_flutter/core/models/pet.dart';

/// Tela de histórico completo de leituras de um pet — antes deste arquivo
/// existir, não havia como ver mais do que as 4 leituras mais recentes
/// mostradas no card "Últimos Monitoramentos" do Dashboard.
class HistoryScreen extends StatelessWidget {
  final Pet pet;

  const HistoryScreen({super.key, required this.pet});

  @override
  Widget build(BuildContext context) {
    // Mostra do mais recente para o mais antigo.
    final pontos = pet.historicoLocalizacoes.reversed.toList();

    return Scaffold(
      backgroundColor: const Color(0xff090B10),

      appBar: AppBar(
        backgroundColor: const Color(0xff090B10),
        elevation: 0,
        centerTitle: true,
        title: Text(
          "Histórico — ${pet.nome}",
          style: const TextStyle(fontWeight: FontWeight.bold),
        ),
      ),

      body: pontos.isEmpty
          ? const Center(
              child: Padding(
                padding: EdgeInsets.all(30),
                child: Text(
                  "Ainda não há posições registradas para este pet.\n"
                  "Assim que a coleira enviar dados de GPS, eles aparecem aqui.",
                  textAlign: TextAlign.center,
                  style: TextStyle(color: Colors.white54, height: 1.5),
                ),
              ),
            )
          : ListView.separated(
              padding: const EdgeInsets.all(20),
              itemCount: pontos.length,
              separatorBuilder: (_, __) => const SizedBox(height: 14),
              itemBuilder: (context, index) {
                final item = pontos[index];

                return Container(
                  padding: const EdgeInsets.all(18),
                  decoration: BoxDecoration(
                    color: const Color(0xff141B27),
                    borderRadius: BorderRadius.circular(22),
                  ),
                  child: Row(
                    children: [
                      Container(
                        width: 48,
                        height: 48,
                        decoration: BoxDecoration(
                          color: const Color(0xff00E5A8).withOpacity(.15),
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: const Icon(
                          Icons.location_on,
                          color: Color(0xff00E5A8),
                        ),
                      ),

                      const SizedBox(width: 16),

                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              item.time,
                              style: const TextStyle(
                                fontWeight: FontWeight.bold,
                                fontSize: 16,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              "Lat ${item.latitude.toStringAsFixed(5)} · Lng ${item.longitude.toStringAsFixed(5)}"
                              "${item.precisao != null ? ' · ±${item.precisao!.toStringAsFixed(0)}m' : ''}",
                              style: const TextStyle(color: Colors.white60, fontSize: 13),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                );
              },
            ),
    );
  }
}

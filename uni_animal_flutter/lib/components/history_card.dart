import 'package:flutter/material.dart';
import 'package:uni_animal_flutter/core/models/pet.dart';

class HistoryCard extends StatelessWidget {
  final Pet pet;
  final VoidCallback? onVerTudo;

  const HistoryCard({super.key, required this.pet, this.onVerTudo});

  @override
  Widget build(BuildContext context) {
    // Mostra só os pontos mais recentes aqui; a tela de Histórico completa
    // (aberta pelo botão "Ver tudo") mostra a lista inteira.
    final pontos = pet.historicoLocalizacoes;
    final recentes = pontos.length > 4
        ? pontos.sublist(pontos.length - 4)
        : pontos;

    return Container(
      padding: const EdgeInsets.all(20),

      decoration: BoxDecoration(
        color: const Color(0xff141B27),
        borderRadius: BorderRadius.circular(30),
      ),

      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,

        children: [
          Row(
            children: [
              const Icon(Icons.history, color: Color(0xff00E5A8)),

              const SizedBox(width: 10),

              const Expanded(
                child: Text(
                  "Histórico de Localização",
                  style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                ),
              ),

              if (onVerTudo != null)
                TextButton(
                  onPressed: onVerTudo,
                  child: const Text(
                    "Ver tudo",
                    style: TextStyle(
                      color: Color(0xff00E5A8),
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
            ],
          ),

          const SizedBox(height: 15),

          if (recentes.isEmpty)
            const Padding(
              padding: EdgeInsets.symmetric(vertical: 20),
              child: Text(
                "Ainda não há posições registradas para este pet.",
                style: TextStyle(color: Colors.white54),
              ),
            )
          else
            ...recentes.map((item) {
              return Padding(
                padding: const EdgeInsets.only(bottom: 16),

                child: Container(
                  padding: const EdgeInsets.all(16),

                  decoration: BoxDecoration(
                    color: const Color(0xff1B2434),

                    borderRadius: BorderRadius.circular(20),
                  ),

                  child: Row(
                    children: [
                      Container(
                        width: 45,

                        height: 45,

                        decoration: BoxDecoration(
                          color: const Color(0xff00E5A8).withOpacity(.15),

                          borderRadius: BorderRadius.circular(15),
                        ),

                        child: const Icon(
                          Icons.location_on,
                          color: Color(0xff00E5A8),
                        ),
                      ),

                      const SizedBox(width: 15),

                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,

                          children: [
                            Text(
                              item.time,

                              style: const TextStyle(
                                fontWeight: FontWeight.bold,

                                fontSize: 17,
                              ),
                            ),

                            const SizedBox(height: 4),

                            Text(
                              "Lat ${item.latitude.toStringAsFixed(4)}, Lng ${item.longitude.toStringAsFixed(4)}",

                              style: const TextStyle(color: Colors.white60),
                            ),
                          ],
                        ),
                      ),

                      if (item.precisao != null)
                        Text(
                          "±${item.precisao!.toStringAsFixed(0)}m",
                          style: const TextStyle(color: Colors.lightBlue),
                        ),
                    ],
                  ),
                ),
              );
            }),
        ],
      ),
    );
  }
}

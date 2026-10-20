import 'package:flutter/material.dart';
import 'package:uni_animal_flutter/components/alert_card.dart';
import 'package:uni_animal_flutter/core/models/pet.dart';

class AlertScreen extends StatelessWidget {
  final Pet pet;

  const AlertScreen({super.key, required this.pet});

  @override
  Widget build(BuildContext context) {
    final alertas = pet.alertas;
    final temAlertaGrave = pet.status != PetStatus.saudavel;

    return Scaffold(
      backgroundColor: const Color(0xff090B10),

      appBar: AppBar(
        backgroundColor: const Color(0xff090B10),
        elevation: 0,
        centerTitle: true,
        title: Text(
          "Alertas — ${pet.nome}",
          style: const TextStyle(fontWeight: FontWeight.bold),
        ),
      ),

      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),

        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,

          children: [
            Container(
              padding: const EdgeInsets.all(22),

              decoration: BoxDecoration(
                color: (temAlertaGrave ? Colors.red.shade400 : Colors.green)
                    .withOpacity(.15),
                borderRadius: BorderRadius.circular(25),
              ),

              child: Row(
                children: [
                  Icon(
                    temAlertaGrave
                        ? Icons.warning_rounded
                        : Icons.check_circle_rounded,
                    color: temAlertaGrave ? Colors.red : Colors.green,
                    size: 45,
                  ),

                  const SizedBox(width: 20),

                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,

                      children: [
                        Text(
                          temAlertaGrave
                              ? "${alertas.length} alertas ativos"
                              : "Tudo certo com ${pet.nome}",
                          style: const TextStyle(
                            fontSize: 22,
                            fontWeight: FontWeight.bold,
                          ),
                        ),

                        const SizedBox(height: 5),

                        Text(
                          temAlertaGrave
                              ? "Verifique a saúde do animal"
                              : "Nenhum alerta crítico no momento",
                          style: const TextStyle(color: Colors.white70),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 30),

            const Text(
              "Monitoramento",
              style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
            ),

            const SizedBox(height: 18),

            ...alertas.map(
              (a) => Padding(
                padding: const EdgeInsets.only(bottom: 15),
                child: AlertCard(
                  color: a.color,
                  icon: a.icon,
                  title: a.title,
                  subtitle: a.subtitle,
                  time: a.time,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

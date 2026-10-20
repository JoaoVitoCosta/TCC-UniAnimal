import 'package:flutter/material.dart';
import 'package:uni_animal_flutter/core/models/pet.dart';

class HeroPetCard extends StatelessWidget {
  final Pet pet;

  const HeroPetCard({super.key, required this.pet});

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 240,
      width: double.infinity,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(35),
        gradient: const LinearGradient(
          colors: [Color(0xff1A2233), Color(0xff10141F)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        boxShadow: [
          BoxShadow(
            color: const Color(0xff00E5A8).withOpacity(.18),
            blurRadius: 30,
            offset: const Offset(0, 12),
          ),
        ],
      ),
      child: Stack(
        children: [
          Positioned(
            right: -30,
            top: -30,
            child: Container(
              width: 180,
              height: 180,
              decoration: BoxDecoration(
                color: const Color(0xff00E5A8).withOpacity(.08),
                shape: BoxShape.circle,
              ),
            ),
          ),

          Padding(
            padding: const EdgeInsets.all(24),
            child: Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 14,
                          vertical: 6,
                        ),
                        decoration: BoxDecoration(
                          color: (pet.gpsOnline ? Colors.green : Colors.red)
                              .withOpacity(.15),
                          borderRadius: BorderRadius.circular(50),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                              Icons.circle,
                              color: pet.gpsOnline ? Colors.green : Colors.red,
                              size: 10,
                            ),

                            const SizedBox(width: 8),

                            Text(
                              pet.gpsOnline ? "ONLINE" : "OFFLINE",
                              style: TextStyle(
                                color:
                                    pet.gpsOnline ? Colors.green : Colors.red,
                                fontWeight: FontWeight.bold,
                                fontSize: 12,
                              ),
                            ),
                          ],
                        ),
                      ),

                      const Spacer(),

                      Text(
                        pet.nome,
                        style: const TextStyle(
                          fontSize: 34,
                          fontWeight: FontWeight.bold,
                        ),
                      ),

                      const SizedBox(height: 6),

                      Text(
                        pet.raca,
                        style: const TextStyle(
                          color: Colors.white70,
                          fontSize: 17,
                        ),
                      ),

                      const SizedBox(height: 25),

                      Row(
                        children: [
                          const Icon(
                            Icons.access_time,
                            color: Colors.white54,
                            size: 18,
                          ),

                          const SizedBox(width: 8),

                          Text(
                            "Atualizado ${pet.atualizadoEm.toLowerCase()}",
                            style: const TextStyle(color: Colors.white54),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),

                const SizedBox(width: 20),

                Hero(
                  tag: "pet-${pet.id}",
                  child: CircleAvatar(
                    radius: 60,
                    backgroundColor: Colors.white,
                    child: Text(
                      pet.emoji,
                      style: const TextStyle(fontSize: 52),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

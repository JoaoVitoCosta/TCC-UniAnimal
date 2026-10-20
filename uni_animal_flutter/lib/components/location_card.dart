import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:uni_animal_flutter/components/pet_map.dart';
import 'package:uni_animal_flutter/core/models/pet.dart';

class LocationCard extends StatelessWidget {
  final Pet pet;
  final VoidCallback? onTap;

  const LocationCard({super.key, required this.pet, this.onTap});

  @override
  Widget build(BuildContext context) {
    final posicao = posicaoDoPet(pet);

    return InkWell(
      borderRadius: BorderRadius.circular(30),
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(20),

        decoration: BoxDecoration(
          color: const Color(0xff141B27),

          borderRadius: BorderRadius.circular(30),
        ),

        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,

          children: [
            const Text(
              "Localização",
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 22),
            ),

            const SizedBox(height: 20),

            ClipRRect(
              borderRadius: BorderRadius.circular(22),

              child: SizedBox(
                height: 220,

                // Prévia estática: o toque abre a tela de mapa completa.
                child: IgnorePointer(
                  child: Stack(
                    children: [
                      FlutterMap(
                        // Recria o mapa quando a coleira muda de posição.
                        key: ValueKey(posicao),
                        options: MapOptions(
                          initialCenter: posicao ?? kCentroPadrao,
                          initialZoom: posicao != null ? 16 : 3.5,
                          backgroundColor: const Color(0xff11151F),
                          interactionOptions: const InteractionOptions(
                            flags: InteractiveFlag.none,
                          ),
                        ),
                        children: [
                          petTileLayer(context),
                          if (posicao != null)
                            MarkerLayer(markers: [petMarker(pet, posicao, size: 44)]),
                        ],
                      ),

                      if (posicao == null)
                        const Center(
                          child: Icon(Icons.gps_off, size: 48, color: Colors.white38),
                        ),

                      const Positioned(
                        right: 8,
                        bottom: 6,
                        child: Text(
                          kAtribuicaoMapa,
                          style: TextStyle(color: Colors.white38, fontSize: 10),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),

            const SizedBox(height: 20),

            Row(
              children: [
                const Icon(Icons.location_on, color: Colors.green),

                const SizedBox(width: 8),

                Expanded(
                  child: Text(
                    pet.localizacaoLabel,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),

                Text(
                  pet.gpsOnline ? "GPS Ativo" : "GPS Offline",
                  style: TextStyle(
                    color: pet.gpsOnline ? Colors.green : Colors.red,
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

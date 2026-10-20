import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:uni_animal_flutter/components/pet_map.dart';
import 'package:uni_animal_flutter/core/models/pet.dart';
import 'package:uni_animal_flutter/core/services/animal_service.dart';
import 'package:url_launcher/url_launcher.dart';

class MapScreen extends StatefulWidget {
  final Pet pet;

  const MapScreen({super.key, required this.pet});

  @override
  State<MapScreen> createState() => _MapScreenState();
}

class _MapScreenState extends State<MapScreen> {
  /// Mesmo ritmo do dashboard: a posição da coleira é buscada de novo no
  /// backend a cada 10s, e o marcador anda sozinho no mapa.
  static const _intervaloAtualizacao = Duration(seconds: 10);
  static const _zoomPet = 17.0;

  /// O card de baixo cobre parte do mapa — desloca o centro para cima
  /// para o pet ficar visível na área livre.
  static const _deslocamentoCentro = Offset(0, -140);

  final _mapController = MapController();
  late Pet _pet;
  Timer? _timer;
  bool _buscando = false;

  /// Segue o pet automaticamente até o usuário arrastar o mapa; o botão
  /// "Centralizar" volta a seguir.
  bool _seguindo = true;

  @override
  void initState() {
    super.initState();
    _pet = widget.pet;
    _atualizar();
    _timer = Timer.periodic(_intervaloAtualizacao, (_) => _atualizar());
  }

  @override
  void dispose() {
    _timer?.cancel();
    _mapController.dispose();
    super.dispose();
  }

  Future<void> _atualizar() async {
    if (_buscando) return;
    _buscando = true;
    try {
      final atualizado = await AnimalService.obterComHistorico(_pet.id);
      if (!mounted) return;
      final posicaoAnterior = posicaoDoPet(_pet);
      setState(() => _pet = atualizado);

      final nova = posicaoDoPet(atualizado);
      if (_seguindo && nova != null && nova != posicaoAnterior) {
        _moverPara(nova, _mapController.camera.zoom < 12 ? _zoomPet : null);
      }
    } catch (_) {
      // Falha pontual (ex.: sem internet): mantém a última posição e
      // tenta de novo no próximo ciclo.
    } finally {
      _buscando = false;
    }
  }

  void _moverPara(LatLng ponto, [double? zoom]) {
    _mapController.move(
      ponto,
      zoom ?? _mapController.camera.zoom,
      offset: _deslocamentoCentro,
    );
  }

  void _centralizar() {
    final posicao = posicaoDoPet(_pet);
    if (posicao == null) {
      _avisar('A coleira ainda não enviou a localização.');
      return;
    }
    setState(() => _seguindo = true);
    _moverPara(posicao, _zoomPet);
  }

  Future<void> _tracarRota() async {
    final posicao = posicaoDoPet(_pet);
    if (posicao == null) {
      _avisar('A coleira ainda não enviou a localização.');
      return;
    }
    final url = Uri.parse(
      'https://www.google.com/maps/dir/?api=1'
      '&destination=${posicao.latitude},${posicao.longitude}',
    );
    final abriu = await launchUrl(url, mode: LaunchMode.externalApplication);
    if (!abriu) _avisar('Não foi possível abrir o app de mapas.');
  }

  void _avisar(String mensagem) {
    if (!mounted) return;
    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(SnackBar(content: Text(mensagem)));
  }

  @override
  Widget build(BuildContext context) {
    final pet = _pet;
    final posicao = posicaoDoPet(pet);
    final rota = pet.historicoLocalizacoes
        .map((p) => LatLng(p.latitude, p.longitude))
        .toList();
    // Precisão (em metros) do ponto do histórico que é a posição atual.
    final precisao = pet.historicoLocalizacoes
        .where((p) => p.latitude == pet.latitude && p.longitude == pet.longitude)
        .map((p) => p.precisao)
        .firstOrNull;

    return Scaffold(
      backgroundColor: const Color(0xff090B10),

      body: SafeArea(
        child: Stack(
          children: [
            // MAPA
            Positioned.fill(
              child: FlutterMap(
                mapController: _mapController,
                options: MapOptions(
                  initialCenter: posicao ?? kCentroPadrao,
                  initialZoom: posicao != null ? _zoomPet : 4,
                  backgroundColor: const Color(0xff11151F),
                  interactionOptions: const InteractionOptions(
                    flags: InteractiveFlag.all & ~InteractiveFlag.rotate,
                  ),
                  onMapReady: () {
                    final p = posicaoDoPet(_pet);
                    if (p != null) _moverPara(p, _zoomPet);
                  },
                  onPositionChanged: (_, hasGesture) {
                    if (hasGesture && _seguindo) {
                      setState(() => _seguindo = false);
                    }
                  },
                ),
                children: [
                  petTileLayer(context),

                  if (rota.length > 1)
                    PolylineLayer(
                      polylines: [
                        Polyline(
                          points: rota,
                          strokeWidth: 4,
                          color: const Color(0xff00E5A8).withValues(alpha: 0.6),
                        ),
                      ],
                    ),

                  if (posicao != null && precisao != null && precisao > 0)
                    CircleLayer(
                      circles: [
                        CircleMarker(
                          point: posicao,
                          radius: precisao,
                          useRadiusInMeter: true,
                          color: const Color(0xff00E5A8).withValues(alpha: 0.12),
                          borderColor: const Color(0xff00E5A8).withValues(alpha: 0.4),
                          borderStrokeWidth: 1.5,
                        ),
                      ],
                    ),

                  if (posicao != null)
                    MarkerLayer(markers: [petMarker(pet, posicao)]),
                ],
              ),
            ),

            // HEADER
            Positioned(
              top: 20,

              left: 20,

              right: 20,

              child: Row(
                children: [
                  CircleAvatar(
                    backgroundColor: const Color(0xff1B2434),

                    child: IconButton(
                      icon: const Icon(Icons.arrow_back),

                      color: Colors.white,

                      onPressed: () {
                        Navigator.pop(context);
                      },
                    ),
                  ),

                  const Spacer(),

                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                    decoration: BoxDecoration(
                      color: const Color(0xff1B2434),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: const Text(
                      "Localização",

                      style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                    ),
                  ),
                ],
              ),
            ),

            // SEM GPS
            if (posicao == null)
              Positioned(
                top: 90,
                left: 20,
                right: 20,
                child: Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: const Color(0xff1B2434),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: const Row(
                    children: [
                      Icon(Icons.gps_off, color: Colors.white60),
                      SizedBox(width: 12),
                      Expanded(
                        child: Text(
                          "A coleira ainda não enviou a localização. "
                          "O mapa atualiza sozinho quando o sinal chegar.",
                          style: TextStyle(color: Colors.white70),
                        ),
                      ),
                    ],
                  ),
                ),
              ),

            // CARD
            Positioned(
              left: 20,

              right: 20,

              bottom: 25,

              child: Container(
                padding: const EdgeInsets.all(25),

                decoration: BoxDecoration(
                  color: const Color(0xff141B27),

                  borderRadius: BorderRadius.circular(35),
                ),

                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,

                  children: [
                    Row(
                      children: [
                        CircleAvatar(
                          radius: 32,
                          child: Text(
                            pet.emoji,
                            style: const TextStyle(fontSize: 30),
                          ),
                        ),

                        const SizedBox(width: 20),

                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,

                          children: [
                            Text(
                              pet.nome,

                              style: const TextStyle(
                                fontSize: 22,

                                fontWeight: FontWeight.bold,
                              ),
                            ),

                            Text(
                              pet.raca,

                              style: const TextStyle(color: Colors.white60),
                            ),
                          ],
                        ),
                      ],
                    ),

                    const SizedBox(height: 25),

                    Row(
                      children: [
                        const Icon(Icons.location_on, color: Colors.green),

                        const SizedBox(width: 10),

                        Expanded(
                          child: Text(
                            pet.localizacaoLabel,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 15),

                    Row(
                      children: [
                        Icon(
                          Icons.circle,
                          size: 12,
                          color: pet.gpsOnline ? Colors.green : Colors.red,
                        ),

                        const SizedBox(width: 10),

                        Text(pet.gpsOnline ? "GPS Online" : "GPS Offline"),
                      ],
                    ),

                    const SizedBox(height: 15),

                    Text(
                      "Última atualização: ${pet.atualizadoEm}",

                      style: const TextStyle(color: Colors.white60),
                    ),

                    const SizedBox(height: 25),

                    Row(
                      children: [
                        Expanded(
                          child: ElevatedButton.icon(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xff00E5A8),

                              foregroundColor: Colors.black,

                              minimumSize: const Size(double.infinity, 55),
                            ),

                            onPressed: _centralizar,

                            icon: Icon(_seguindo ? Icons.gps_fixed : Icons.my_location),

                            label: const Text("Centralizar"),
                          ),
                        ),

                        const SizedBox(width: 15),

                        Expanded(
                          child: ElevatedButton.icon(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xff1B2434),

                              minimumSize: const Size(double.infinity, 55),
                            ),

                            onPressed: _tracarRota,

                            icon: const Icon(Icons.route),

                            label: const Text("Traçar rota"),
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 12),

                    const Center(
                      child: Text(
                        kAtribuicaoMapa,
                        style: TextStyle(color: Colors.white30, fontSize: 11),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

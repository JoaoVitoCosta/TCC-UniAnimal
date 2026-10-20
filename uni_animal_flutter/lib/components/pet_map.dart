import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:uni_animal_flutter/core/models/pet.dart';

/// Peças do mapa compartilhadas entre o card de localização (dashboard) e
/// a tela de mapa completa. Os tiles são do OpenStreetMap (não exigem chave
/// de API), escurecidos no cliente para combinar com o tema do app.

/// Centro usado enquanto a coleira ainda não enviou nenhuma posição.
const LatLng kCentroPadrao = LatLng(-15.7939, -47.8828);

const String kAtribuicaoMapa = '© OpenStreetMap contributors';

/// Posição atual da coleira, ou null se ainda não houver GPS.
LatLng? posicaoDoPet(Pet pet) {
  final lat = pet.latitude;
  final lng = pet.longitude;
  if (lat == null || lng == null) return null;
  return LatLng(lat, lng);
}

TileLayer petTileLayer(BuildContext context) {
  return TileLayer(
    urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
    userAgentPackageName: 'com.example.uni_animal_flutter',
    // Escurece os tiles claros do OSM para combinar com o tema do app.
    tileBuilder: darkModeTileBuilder,
  );
}

/// Marcador do pet: avatar com o emoji e borda verde (GPS online) ou cinza.
Marker petMarker(Pet pet, LatLng ponto, {double size = 54}) {
  return Marker(
    point: ponto,
    width: size,
    height: size,
    child: Container(
      decoration: BoxDecoration(
        color: Colors.white,
        shape: BoxShape.circle,
        border: Border.all(
          color: pet.gpsOnline ? const Color(0xff00E5A8) : Colors.white38,
          width: 3,
        ),
        boxShadow: const [
          BoxShadow(color: Colors.black54, blurRadius: 10, offset: Offset(0, 4)),
        ],
      ),
      alignment: Alignment.center,
      child: Text(pet.emoji, style: TextStyle(fontSize: size * 0.5)),
    ),
  );
}

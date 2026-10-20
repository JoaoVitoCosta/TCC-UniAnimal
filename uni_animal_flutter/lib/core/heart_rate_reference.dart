import 'package:flutter/material.dart';

/// Classificação de uma leitura de batimento cardíaco.
enum NivelBatimento { normal, alerta, emergencia }

extension NivelBatimentoInfo on NivelBatimento {
  String get label => switch (this) {
        NivelBatimento.normal => 'Normal',
        NivelBatimento.alerta => 'Alerta',
        NivelBatimento.emergencia => 'Emergência',
      };

  Color get color => switch (this) {
        NivelBatimento.normal => const Color(0xff10D078),
        NivelBatimento.alerta => const Color(0xffF5A623),
        NivelBatimento.emergencia => const Color(0xffF04B58),
      };

  IconData get icon => switch (this) {
        NivelBatimento.normal => Icons.check_circle_rounded,
        NivelBatimento.alerta => Icons.info_rounded,
        NivelBatimento.emergencia => Icons.warning_rounded,
      };

  /// Orientação ao tutor para cada nível.
  String get orientacao => switch (this) {
        NivelBatimento.normal => 'Dentro do esperado em repouso. Continue acompanhando.',
        NivelBatimento.alerta =>
          'Deixe o pet descansar em local calmo e confira de novo em alguns minutos. '
              'Se continuar fora da faixa, fale com o veterinário.',
        NivelBatimento.emergencia =>
          'Procure atendimento veterinário imediatamente, principalmente se houver '
              'apatia, desmaio, falta de ar ou gengivas pálidas/arroxeadas.',
      };
}

/// Faixas de referência de frequência cardíaca EM REPOUSO (bpm) de um tipo
/// de animal. Fora de [normalMin, normalMax] é alerta; fora de
/// [alertaMin, alertaMax] é emergência.
///
/// Valores aproximados para animais adultos, usados apenas como
/// orientação — filhotes, exercício, calor e estresse elevam a frequência.
/// Não substituem a avaliação de um médico-veterinário.
class FaixaBatimento {
  final String animal;
  final int normalMin;
  final int normalMax;
  final int alertaMin;
  final int alertaMax;

  const FaixaBatimento({
    required this.animal,
    required this.normalMin,
    required this.normalMax,
    required this.alertaMin,
    required this.alertaMax,
  });

  NivelBatimento classificar(int bpm) {
    if (bpm >= normalMin && bpm <= normalMax) return NivelBatimento.normal;
    if (bpm >= alertaMin && bpm <= alertaMax) return NivelBatimento.alerta;
    return NivelBatimento.emergencia;
  }

  String get normalLabel => '$normalMin–$normalMax';
  String get alertaLabel => '$alertaMin–${normalMin - 1}\n${normalMax + 1}–$alertaMax';
  String get emergenciaLabel => '< $alertaMin\n> $alertaMax';

  static const caoPequeno = FaixaBatimento(
      animal: 'Cão pequeno', normalMin: 80, normalMax: 140, alertaMin: 60, alertaMax: 170);
  static const caoMedio = FaixaBatimento(
      animal: 'Cão médio', normalMin: 70, normalMax: 120, alertaMin: 55, alertaMax: 150);
  static const caoGrande = FaixaBatimento(
      animal: 'Cão grande', normalMin: 60, normalMax: 100, alertaMin: 50, alertaMax: 130);
  static const gato = FaixaBatimento(
      animal: 'Gato', normalMin: 140, normalMax: 220, alertaMin: 120, alertaMax: 240);
  static const coelho = FaixaBatimento(
      animal: 'Coelho', normalMin: 130, normalMax: 325, alertaMin: 110, alertaMax: 350);
  static const cavalo = FaixaBatimento(
      animal: 'Cavalo', normalMin: 28, normalMax: 44, alertaMin: 24, alertaMax: 60);

  static const todas = [caoPequeno, caoMedio, caoGrande, gato, coelho, cavalo];

  /// Faixa adequada ao pet, pela espécie e (para cães) pelo porte.
  /// Espécies sem referência própria retornam null.
  static FaixaBatimento? para({required String especie, required String porte}) {
    switch (especie.toLowerCase()) {
      case 'cachorro':
      case 'cão':
        return switch (porte) {
          'pequeno' => caoPequeno,
          'grande' => caoGrande,
          _ => caoMedio,
        };
      case 'gato':
        return gato;
      case 'coelho':
        return coelho;
      case 'cavalo':
        return cavalo;
      default:
        return null;
    }
  }
}

import 'package:flutter/material.dart';
import 'package:uni_animal_flutter/components/alert_card.dart';
import 'package:uni_animal_flutter/core/heart_rate_reference.dart';
import 'package:uni_animal_flutter/core/models/pet.dart';

/// Seção de alertas exibida no próprio Dashboard: resumo, alertas ativos
/// (status de saúde, GPS e batimento atual) e a tabela de orientação de
/// batimentos normais / alerta / emergência.
class AlertsSection extends StatelessWidget {
  final Pet pet;

  /// Últimas leituras de batimento (as mesmas do gráfico, atualizadas a
  /// cada 10s pelo Dashboard).
  final List<HeartbeatPoint> batimentos;

  /// Alertas ABERTOS gerados pelo servidor a partir da telemetria da coleira
  /// (batimento, temperatura, bateria, sem sinal, zona segura).
  final List<AlertaApi> alertasServidor;

  const AlertsSection({
    super.key,
    required this.pet,
    this.batimentos = const [],
    this.alertasServidor = const [],
  });

  @override
  Widget build(BuildContext context) {
    final faixa = FaixaBatimento.para(especie: pet.especie, porte: pet.porte);
    final bpm = batimentos.isNotEmpty ? batimentos.last.bpm : pet.batimentoCardiaco;
    final nivel = (faixa != null && bpm != null) ? faixa.classificar(bpm) : null;

    // O card de batimento ao vivo já mostra a situação do batimento, então o
    // alerta "batimento" do servidor não é repetido; os demais vêm do servidor.
    final doServidor = alertasServidor
        .where((a) => !(a.tipo == 'batimento' && nivel != null))
        .map((a) => a.toPetAlert());

    final alertas = <PetAlert>[
      if (nivel != null && bpm != null)
        PetAlert(
          icon: Icons.favorite,
          color: nivel.color,
          title: 'Batimento cardíaco',
          subtitle: '$bpm bpm — ${nivel.label}',
          time: batimentos.isNotEmpty ? batimentos.last.horaLabel : 'Agora',
        ),
      ...doServidor,
      // Sem alertas do servidor: mostra o resumo local (status/GPS).
      if (alertasServidor.isEmpty) ...pet.alertas,
    ];

    final emergenciaServidor = alertasServidor.any((a) => a.emergencia);
    final grave = pet.status == PetStatus.emergencia ||
        nivel == NivelBatimento.emergencia ||
        emergenciaServidor;
    final atencao = !grave &&
        (pet.status == PetStatus.atencao ||
            nivel == NivelBatimento.alerta ||
            alertasServidor.isNotEmpty ||
            !pet.gpsOnline);
    final resumoCor = grave
        ? NivelBatimento.emergencia.color
        : atencao
            ? NivelBatimento.alerta.color
            : NivelBatimento.normal.color;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          "Alertas",
          style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
        ),

        const SizedBox(height: 18),

        Container(
          padding: const EdgeInsets.all(22),
          decoration: BoxDecoration(
            color: resumoCor.withOpacity(.15),
            borderRadius: BorderRadius.circular(25),
          ),
          child: Row(
            children: [
              Icon(
                grave
                    ? Icons.warning_rounded
                    : atencao
                        ? Icons.info_rounded
                        : Icons.check_circle_rounded,
                color: resumoCor,
                size: 45,
              ),
              const SizedBox(width: 20),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      grave
                          ? "Emergência"
                          : atencao
                              ? "Requer atenção"
                              : "Tudo certo com ${pet.nome}",
                      style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 5),
                    Text(
                      nivel?.orientacao ?? "Nenhum alerta crítico no momento.",
                      style: const TextStyle(color: Colors.white70, height: 1.4),
                    ),
                  ],
                ),
              ),
            ],
          ),
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

        const SizedBox(height: 15),

        _TabelaOrientacao(faixaDoPet: faixa, nivelAtual: nivel),
      ],
    );
  }
}

/// Tabela de referência de batimentos em repouso por tipo de animal, com
/// a linha do pet atual destacada.
class _TabelaOrientacao extends StatelessWidget {
  final FaixaBatimento? faixaDoPet;
  final NivelBatimento? nivelAtual;

  const _TabelaOrientacao({this.faixaDoPet, this.nivelAtual});

  static const _cabecalho = TextStyle(fontWeight: FontWeight.bold, fontSize: 12);

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: const Color(0xff141B27),
        borderRadius: BorderRadius.circular(30),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              Icon(Icons.monitor_heart, color: Color(0xff00E5A8)),
              SizedBox(width: 10),
              Expanded(
                child: Text(
                  "Orientação de batimentos",
                  style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                ),
              ),
            ],
          ),

          const SizedBox(height: 6),

          const Text(
            "Frequência cardíaca em repouso (bpm) de animais adultos.",
            style: TextStyle(color: Colors.white54, fontSize: 13),
          ),

          const SizedBox(height: 16),

          Table(
            columnWidths: const {
              0: FlexColumnWidth(1.3),
              1: FlexColumnWidth(1),
              2: FlexColumnWidth(1),
              3: FlexColumnWidth(1),
            },
            defaultVerticalAlignment: TableCellVerticalAlignment.middle,
            children: [
              TableRow(
                decoration: const BoxDecoration(
                  border: Border(bottom: BorderSide(color: Colors.white12)),
                ),
                children: [
                  _celula(const Text("Animal", style: _cabecalho)),
                  _celula(Text("Normal",
                      style: _cabecalho.copyWith(color: NivelBatimento.normal.color))),
                  _celula(Text("Alerta",
                      style: _cabecalho.copyWith(color: NivelBatimento.alerta.color))),
                  _celula(Text("Emergência",
                      style: _cabecalho.copyWith(color: NivelBatimento.emergencia.color))),
                ],
              ),
              ...FaixaBatimento.todas.map(_linha),
            ],
          ),

          const SizedBox(height: 18),

          ...NivelBatimento.values.map(
            (n) => Padding(
              padding: const EdgeInsets.only(bottom: 10),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(n.icon, color: n.color, size: 18),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text.rich(
                      TextSpan(children: [
                        TextSpan(
                          text: "${n.label}: ",
                          style: TextStyle(color: n.color, fontWeight: FontWeight.bold),
                        ),
                        TextSpan(text: n.orientacao),
                      ]),
                      style: const TextStyle(color: Colors.white70, fontSize: 13, height: 1.4),
                    ),
                  ),
                ],
              ),
            ),
          ),

          const SizedBox(height: 6),

          const Text(
            "Valores aproximados, apenas para orientação. Filhotes, exercício, calor e "
            "estresse aumentam a frequência. Em caso de dúvida, consulte um médico-veterinário.",
            style: TextStyle(color: Colors.white38, fontSize: 11, height: 1.4),
          ),
        ],
      ),
    );
  }

  TableRow _linha(FaixaBatimento f) {
    final doPet = identical(f, faixaDoPet);
    final estilo = TextStyle(
      fontSize: 12,
      fontWeight: doPet ? FontWeight.bold : FontWeight.normal,
      color: doPet ? Colors.white : Colors.white70,
    );

    // Na linha do pet, destaca a célula do nível em que ele está agora.
    BoxDecoration? destaque(NivelBatimento n) => doPet && nivelAtual == n
        ? BoxDecoration(
            color: n.color.withOpacity(.2),
            borderRadius: BorderRadius.circular(8),
          )
        : null;

    return TableRow(
      decoration: BoxDecoration(
        color: doPet ? const Color(0xff00E5A8).withOpacity(.08) : null,
        border: const Border(bottom: BorderSide(color: Colors.white10)),
      ),
      children: [
        _celula(Text(doPet ? "${f.animal} ★" : f.animal, style: estilo)),
        _celula(Text(f.normalLabel, style: estilo), destaque(NivelBatimento.normal)),
        _celula(Text(f.alertaLabel, style: estilo), destaque(NivelBatimento.alerta)),
        _celula(Text(f.emergenciaLabel, style: estilo), destaque(NivelBatimento.emergencia)),
      ],
    );
  }

  Widget _celula(Widget child, [BoxDecoration? decoration]) {
    return Container(
      margin: const EdgeInsets.symmetric(vertical: 4, horizontal: 2),
      padding: const EdgeInsets.symmetric(vertical: 6, horizontal: 4),
      decoration: decoration,
      child: child,
    );
  }
}

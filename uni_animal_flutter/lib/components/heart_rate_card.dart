import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:uni_animal_flutter/core/models/pet.dart';

/// Card de batimentos cardíacos com gráfico de linha das últimas leituras.
class HeartRateCard extends StatelessWidget {
  final List<HeartbeatPoint> pontos;

  /// Valor exibido quando ainda não há pontos carregados (vem do Pet).
  final int? bpmAtual;

  const HeartRateCard({super.key, required this.pontos, this.bpmAtual});

  static const _cor = Color(0xffF04B58);

  @override
  Widget build(BuildContext context) {
    final bpm = pontos.isNotEmpty ? pontos.last.bpm : bpmAtual;
    final atualizado = pontos.isNotEmpty ? pontos.last.horaLabel : null;

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(28),
        gradient: const LinearGradient(
          colors: [Color(0xff1B2433), Color(0xff121722)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        boxShadow: [
          BoxShadow(
            color: _cor.withOpacity(.15),
            blurRadius: 18,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: _cor.withOpacity(.15),
                  borderRadius: BorderRadius.circular(15),
                ),
                child: const Icon(Icons.favorite, color: _cor),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      "Batimentos",
                      style: TextStyle(color: Colors.white60, fontSize: 14),
                    ),
                    Text(
                      atualizado != null ? "Atualizado às $atualizado" : "Sem leituras ainda",
                      style: const TextStyle(color: Colors.white38, fontSize: 12),
                    ),
                  ],
                ),
              ),
              Text(
                bpm?.toString() ?? '--',
                style: const TextStyle(fontSize: 30, fontWeight: FontWeight.bold),
              ),
              const SizedBox(width: 4),
              const Padding(
                padding: EdgeInsets.only(top: 10),
                child: Text(
                  "bpm",
                  style: TextStyle(color: _cor, fontWeight: FontWeight.bold),
                ),
              ),
            ],
          ),

          const SizedBox(height: 18),

          SizedBox(
            height: 120,
            width: double.infinity,
            child: pontos.length < 2
                ? const Center(
                    child: Text(
                      "Aguardando leituras para montar o gráfico.",
                      style: TextStyle(color: Colors.white38),
                    ),
                  )
                : CustomPaint(painter: _HeartRatePainter(pontos, _cor)),
          ),

          if (pontos.length >= 2) ...[
            const SizedBox(height: 8),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(pontos.first.horaLabel,
                    style: const TextStyle(color: Colors.white38, fontSize: 11)),
                Text(pontos.last.horaLabel,
                    style: const TextStyle(color: Colors.white38, fontSize: 11)),
              ],
            ),
          ],
        ],
      ),
    );
  }
}

class _HeartRatePainter extends CustomPainter {
  final List<HeartbeatPoint> pontos;
  final Color cor;

  _HeartRatePainter(this.pontos, this.cor);

  static const _labelWidth = 30.0;

  @override
  void paint(Canvas canvas, Size size) {
    final valores = pontos.map((p) => p.bpm).toList();
    var minV = valores.reduce(math.min).toDouble();
    var maxV = valores.reduce(math.max).toDouble();

    // Folga vertical para a linha não encostar nas bordas.
    final folga = math.max(5.0, (maxV - minV) * .15);
    minV -= folga;
    maxV += folga;

    final area = Rect.fromLTWH(_labelWidth, 6, size.width - _labelWidth, size.height - 12);
    final dx = area.width / (pontos.length - 1);

    Offset ponto(int i) => Offset(
          area.left + dx * i,
          area.bottom - (valores[i] - minV) / (maxV - minV) * area.height,
        );

    // Linhas de grade + rótulos de valor (máx / mín).
    final grade = Paint()
      ..color = Colors.white.withOpacity(.06)
      ..strokeWidth = 1;
    for (final y in [area.top, area.center.dy, area.bottom]) {
      canvas.drawLine(Offset(area.left, y), Offset(area.right, y), grade);
    }
    _texto(canvas, maxV.round().toString(), Offset(0, area.top - 6));
    _texto(canvas, minV.round().toString(), Offset(0, area.bottom - 6));

    // Linha suavizada.
    final linha = Path()..moveTo(ponto(0).dx, ponto(0).dy);
    for (var i = 1; i < pontos.length; i++) {
      final a = ponto(i - 1);
      final b = ponto(i);
      final meioX = (a.dx + b.dx) / 2;
      linha.cubicTo(meioX, a.dy, meioX, b.dy, b.dx, b.dy);
    }

    // Preenchimento em degradê abaixo da linha.
    final preenchimento = Path.from(linha)
      ..lineTo(area.right, area.bottom)
      ..lineTo(area.left, area.bottom)
      ..close();
    canvas.drawPath(
      preenchimento,
      Paint()
        ..shader = LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [cor.withOpacity(.35), cor.withOpacity(0)],
        ).createShader(area),
    );

    canvas.drawPath(
      linha,
      Paint()
        ..color = cor
        ..strokeWidth = 2.5
        ..style = PaintingStyle.stroke
        ..strokeCap = StrokeCap.round,
    );

    // Pontos; o mais recente fica destacado.
    for (var i = 0; i < pontos.length; i++) {
      final ultimo = i == pontos.length - 1;
      canvas.drawCircle(ponto(i), ultimo ? 5 : 3, Paint()..color = cor);
      if (ultimo) {
        canvas.drawCircle(ponto(i), 2, Paint()..color = Colors.white);
      }
    }
  }

  void _texto(Canvas canvas, String texto, Offset pos) {
    final tp = TextPainter(
      text: TextSpan(
        text: texto,
        style: const TextStyle(color: Colors.white38, fontSize: 10),
      ),
      textDirection: TextDirection.ltr,
    )..layout();
    tp.paint(canvas, pos);
  }

  @override
  bool shouldRepaint(_HeartRatePainter old) => old.pontos != pontos;
}

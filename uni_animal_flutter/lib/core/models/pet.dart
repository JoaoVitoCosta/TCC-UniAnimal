import 'package:flutter/material.dart';

/// Estado de saúde do animal — mesmos três estados usados no site
/// (ver `animais` no banco de dados: saudavel / atencao / emergencia).
enum PetStatus { saudavel, atencao, emergencia }

/// Um ponto do histórico de localização da coleira (dado real, vindo de
/// `coleiras.localizacoes` no backend).
class LocationPoint {
  final String time;
  final double latitude;
  final double longitude;
  final double? precisao;

  const LocationPoint({
    required this.time,
    required this.latitude,
    required this.longitude,
    this.precisao,
  });
}

/// Uma leitura de sinais vitais da coleira (tabela `leituras_saude` no backend).
class HeartbeatPoint {
  final int bpm;
  final double? temperatura;
  final DateTime? registradoEm;

  const HeartbeatPoint({required this.bpm, this.temperatura, this.registradoEm});

  factory HeartbeatPoint.fromApiJson(Map<String, dynamic> json) {
    return HeartbeatPoint(
      bpm: (json['batimento_cardiaco'] as num?)?.toInt() ?? 0,
      temperatura: (json['temperatura'] as num?)?.toDouble(),
      registradoEm: DateTime.tryParse(json['registrado_em'] ?? '')?.toLocal(),
    );
  }

  /// Horário no formato "HH:mm" (ou "--:--" sem data).
  String get horaLabel {
    final d = registradoEm;
    if (d == null) return '--:--';
    return '${d.hour.toString().padLeft(2, '0')}:${d.minute.toString().padLeft(2, '0')}';
  }
}

/// Alerta gerado pelo SERVIDOR a partir da telemetria da coleira
/// (tabela `alertas`; GET /api/v1/animais/{id}/alertas).
class AlertaApi {
  final int id;
  final String tipo; // batimento, temperatura, bateria, sem_sinal, zona_segura
  final String nivel; // alerta, emergencia
  final String titulo;
  final String mensagem;
  final String? valor;
  final bool aberto;
  final bool lido;
  final DateTime? criadoEm;

  const AlertaApi({
    required this.id,
    required this.tipo,
    required this.nivel,
    required this.titulo,
    required this.mensagem,
    this.valor,
    required this.aberto,
    required this.lido,
    this.criadoEm,
  });

  factory AlertaApi.fromApiJson(Map<String, dynamic> json) {
    return AlertaApi(
      id: (json['id'] as num).toInt(),
      tipo: json['tipo'] ?? '',
      nivel: json['nivel'] ?? 'alerta',
      titulo: json['titulo'] ?? '',
      mensagem: json['mensagem'] ?? '',
      valor: json['valor'] as String?,
      aberto: json['aberto'] == true,
      lido: json['lido'] == true,
      criadoEm: DateTime.tryParse(json['criado_em'] ?? '')?.toLocal(),
    );
  }

  bool get emergencia => nivel == 'emergencia';

  Color get color => emergencia ? const Color(0xffF04B58) : const Color(0xffF5A623);

  IconData get icon => switch (tipo) {
        'batimento' => Icons.favorite,
        'temperatura' => Icons.thermostat,
        'bateria' => Icons.battery_alert,
        'sem_sinal' => Icons.signal_wifi_off,
        'zona_segura' => Icons.location_off,
        _ => Icons.warning_rounded,
      };

  String get horaLabel {
    final d = criadoEm;
    if (d == null) return 'Agora';
    return '${d.day.toString().padLeft(2, '0')}/${d.month.toString().padLeft(2, '0')} '
        '${d.hour.toString().padLeft(2, '0')}:${d.minute.toString().padLeft(2, '0')}';
  }

  /// Mesmo formato dos cards de alerta já usados nas telas.
  PetAlert toPetAlert() =>
      PetAlert(icon: icon, color: color, title: titulo, subtitle: mensagem, time: horaLabel);
}

/// Um alerta gerado para o animal (usado na tela de Alertas).
class PetAlert {
  final IconData icon;
  final Color color;
  final String title;
  final String subtitle;
  final String time;

  const PetAlert({
    required this.icon,
    required this.color,
    required this.title,
    required this.subtitle,
    required this.time,
  });
}

/// Representa um pet cadastrado pelo tutor, com sua coleira UniAnimal.
///
/// Os campos aqui espelham exatamente o que o backend Laravel expõe via
/// API (`AnimalResource` + `ColeiraResource`): dados de identificação do
/// animal, GPS da coleira, os sinais vitais mais recentes (tabela
/// `leituras_saude`) e quantos alertas estão abertos (tabela `alertas`).
class Pet {
  final String id;
  final String nome;
  final String especie;
  final String raca;
  final String porte;
  final int idade;
  final double peso;
  final PetStatus status;

  final String codigoColeira;
  final int? batimentoCardiaco;
  final String? batimentoAtualizadoEm;
  final double? temperatura;
  final String? temperaturaAtualizadaEm;
  final int alertasAbertos;
  final bool gpsOnline;
  final String localizacaoLabel;
  final String atualizadoEm;
  final double? latitude;
  final double? longitude;

  final List<LocationPoint> historicoLocalizacoes;

  const Pet({
    required this.id,
    required this.nome,
    required this.especie,
    required this.raca,
    required this.porte,
    required this.idade,
    required this.peso,
    required this.status,
    required this.codigoColeira,
    this.batimentoCardiaco,
    this.batimentoAtualizadoEm,
    this.temperatura,
    this.temperaturaAtualizadaEm,
    this.alertasAbertos = 0,
    required this.gpsOnline,
    required this.localizacaoLabel,
    required this.atualizadoEm,
    this.latitude,
    this.longitude,
    this.historicoLocalizacoes = const [],
  });

  /// Cópia com o histórico de localizações preenchido (mantém todo o resto).
  Pet copyWith({List<LocationPoint>? historicoLocalizacoes}) {
    return Pet(
      id: id,
      nome: nome,
      especie: especie,
      raca: raca,
      porte: porte,
      idade: idade,
      peso: peso,
      status: status,
      codigoColeira: codigoColeira,
      batimentoCardiaco: batimentoCardiaco,
      batimentoAtualizadoEm: batimentoAtualizadoEm,
      temperatura: temperatura,
      temperaturaAtualizadaEm: temperaturaAtualizadaEm,
      alertasAbertos: alertasAbertos,
      gpsOnline: gpsOnline,
      localizacaoLabel: localizacaoLabel,
      atualizadoEm: atualizadoEm,
      latitude: latitude,
      longitude: longitude,
      historicoLocalizacoes: historicoLocalizacoes ?? this.historicoLocalizacoes,
    );
  }

  /// Constrói um Pet a partir do JSON real devolvido pela API
  /// (`GET /api/v1/animais` / `GET /api/v1/animais/{id}`). Nenhum valor
  /// aqui é inventado no cliente — tudo vem do backend.
  factory Pet.fromApiJson(Map<String, dynamic> json) {
    final coleira = json['coleira'] as Map<String, dynamic>?;

    final status = switch (json['status']) {
      'atencao' => PetStatus.atencao,
      'emergencia' => PetStatus.emergencia,
      _ => PetStatus.saudavel,
    };

    final gpsOnline = coleira != null &&
        coleira['status'] == 'ativa' &&
        coleira['latitude'] != null;

    String localizacaoLabel = 'Sem sinal de GPS ainda';
    if (coleira != null && coleira['latitude'] != null && coleira['longitude'] != null) {
      final lat = (coleira['latitude'] as num).toStringAsFixed(4);
      final lng = (coleira['longitude'] as num).toStringAsFixed(4);
      localizacaoLabel = 'Lat $lat, Lng $lng';
    }

    return Pet(
      id: json['id'].toString(),
      nome: json['nome'] ?? '',
      especie: json['especie'] ?? '',
      raca: json['raca'] ?? '—',
      porte: json['porte'] ?? 'medio',
      idade: json['idade'] ?? 0,
      peso: (json['peso'] as num?)?.toDouble() ?? 0,
      status: status,
      codigoColeira: coleira?['codigo'] ?? '—',
      batimentoCardiaco: (json['batimento_cardiaco'] as num?)?.toInt(),
      batimentoAtualizadoEm: json['batimento_registrado_em'] as String?,
      temperatura: (json['temperatura'] as num?)?.toDouble(),
      temperaturaAtualizadaEm: json['temperatura_registrada_em'] as String?,
      alertasAbertos: (json['alertas_abertos'] as num?)?.toInt() ?? 0,
      gpsOnline: gpsOnline,
      localizacaoLabel: localizacaoLabel,
      atualizadoEm: coleira?['localizacao_atualizada_em'] != null
          ? 'em ${coleira!['localizacao_atualizada_em']}'
          : 'sem dados ainda',
      latitude: (coleira?['latitude'] as num?)?.toDouble(),
      longitude: (coleira?['longitude'] as num?)?.toDouble(),
    );
  }

  /// Emoji usado como avatar quando não há foto — mesma convenção já usada
  /// no restante do app (splash/token screen usam "🐾").
  String get emoji {
    switch (especie.toLowerCase()) {
      case 'gato':
        return '🐱';
      case 'cavalo':
        return '🐴';
      case 'cachorro':
      default:
        return '🐶';
    }
  }

  String get statusLabel {
    switch (status) {
      case PetStatus.saudavel:
        return 'Saudável';
      case PetStatus.atencao:
        return 'Atenção';
      case PetStatus.emergencia:
        return 'Emergência';
    }
  }

  Color get statusColor {
    switch (status) {
      case PetStatus.saudavel:
        return const Color(0xff10D078);
      case PetStatus.atencao:
        return const Color(0xffF5A623);
      case PetStatus.emergencia:
        return const Color(0xffF04B58);
    }
  }

  /// Texto do batimento: "95" ou "--" quando ainda não há leitura.
  String get batimentoLabel => batimentoCardiaco?.toString() ?? '--';

  Color get batimentoColor =>
      batimentoCardiaco != null ? const Color(0xffF04B58) : Colors.white38;

  /// Alertas derivados do estado atual do pet — no site isso vem da rota
  /// de alertas do dashboard; aqui é calculado localmente a partir do
  /// mesmo tipo de sinais (status de saúde, GPS).
  List<PetAlert> get alertas {
    final alerts = <PetAlert>[];

    if (status == PetStatus.emergencia) {
      alerts.add(PetAlert(
        icon: Icons.warning_rounded,
        color: const Color(0xffF04B58),
        title: 'Status de emergência',
        subtitle: statusLabel,
        time: 'Agora',
      ));
    } else if (status == PetStatus.atencao) {
      alerts.add(PetAlert(
        icon: Icons.info_rounded,
        color: const Color(0xffF5A623),
        title: 'Requer atenção',
        subtitle: statusLabel,
        time: 'Agora',
      ));
    }

    alerts.add(PetAlert(
      icon: Icons.location_on_rounded,
      color: gpsOnline ? const Color(0xff10D078) : const Color(0xffF04B58),
      title: 'GPS',
      subtitle: gpsOnline ? 'Área segura' : 'Sinal perdido',
      time: atualizadoEm,
    ));

    return alerts;
  }
}

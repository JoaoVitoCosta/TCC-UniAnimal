import 'package:flutter/material.dart';

class MetricCard extends StatelessWidget {
  final String title;
  final String value;
  final String unit;
  final IconData icon;
  final Color color;

  const MetricCard({
    super.key,
    required this.title,
    required this.value,
    required this.unit,
    required this.icon,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),

      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(28),

        gradient: LinearGradient(
          colors: [const Color(0xff1B2433), const Color(0xff121722)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),

        boxShadow: [
          BoxShadow(
            color: color.withOpacity(.15),
            blurRadius: 18,
            offset: const Offset(0, 8),
          ),
        ],
      ),

      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,

        children: [
          Container(
            width: 48,
            height: 48,

            decoration: BoxDecoration(
              color: color.withOpacity(.15),
              borderRadius: BorderRadius.circular(15),
            ),

            child: Icon(icon, color: color),
          ),

          const Spacer(),

          Text(
            value,
            style: const TextStyle(fontSize: 30, fontWeight: FontWeight.bold),
          ),

          Text(
            unit,
            style: TextStyle(color: color, fontWeight: FontWeight.bold),
          ),

          const SizedBox(height: 8),

          Text(
            title,
            style: const TextStyle(color: Colors.white60, fontSize: 14),
          ),
        ],
      ),
    );
  }
}

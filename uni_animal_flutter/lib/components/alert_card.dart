import 'package:flutter/material.dart';

class AlertCard extends StatelessWidget {
  final Color color;
  final IconData icon;
  final String title;
  final String subtitle;
  final String time;

  const AlertCard({
    super.key,
    required this.color,
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.time,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),

      decoration: BoxDecoration(
        color: const Color(0xff151A24),

        borderRadius: BorderRadius.circular(25),
      ),

      child: Row(
        children: [
          CircleAvatar(
            radius: 28,

            backgroundColor: color.withOpacity(.15),

            child: Icon(icon, color: color, size: 28),
          ),

          const SizedBox(width: 18),

          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,

              children: [
                Text(
                  title,

                  style: const TextStyle(
                    fontWeight: FontWeight.bold,

                    fontSize: 18,
                  ),
                ),

                const SizedBox(height: 6),

                Text(
                  subtitle,

                  style: TextStyle(color: color, fontWeight: FontWeight.bold),
                ),
              ],
            ),
          ),

          Text(time, style: const TextStyle(color: Colors.white54)),
        ],
      ),
    );
  }
}

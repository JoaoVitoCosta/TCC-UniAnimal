import 'package:flutter/material.dart';

class CustomBottomNavigation extends StatelessWidget {
  final int currentIndex;
  final Function(int) onTap;

  const CustomBottomNavigation({
    super.key,
    required this.currentIndex,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.all(20),

      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),

      decoration: BoxDecoration(
        color: const Color(0xff141B27),

        borderRadius: BorderRadius.circular(30),
      ),

      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,

        children: [
          item(Icons.home, "Home", 0),

          item(Icons.map, "Mapa", 1),

          item(Icons.warning_amber, "Alertas", 2),
        ],
      ),
    );
  }

  Widget item(IconData icon, String title, int index) {
    final selected = currentIndex == index;

    return InkWell(
      borderRadius: BorderRadius.circular(20),
      onTap: () => onTap(index),

      child: AnimatedContainer(
        duration: const Duration(milliseconds: 300),

        padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 10),

        decoration: BoxDecoration(
          color: selected ? const Color(0xff00E5A8) : Colors.transparent,

          borderRadius: BorderRadius.circular(20),
        ),

        child: Row(
          children: [
            Icon(icon, color: selected ? Colors.black : Colors.white60),

            if (selected) ...[
              const SizedBox(width: 8),

              Text(
                title,
                style: const TextStyle(
                  color: Colors.black,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}

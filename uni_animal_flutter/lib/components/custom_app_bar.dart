import 'package:flutter/material.dart';

class CustomAppBar extends StatelessWidget {
  final String greeting;
  final String subtitle;
  final VoidCallback? onBack;

  const CustomAppBar({
    super.key,
    this.greeting = "Boa tarde 👋",
    required this.subtitle,
    this.onBack,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        if (onBack != null) ...[
          InkWell(
            borderRadius: BorderRadius.circular(14),
            onTap: onBack,
            child: Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: const Color(0xff181F2E),
                borderRadius: BorderRadius.circular(14),
              ),
              child: const Icon(Icons.arrow_back, color: Colors.white),
            ),
          ),
          const SizedBox(width: 14),
        ],

        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                greeting,
                style: const TextStyle(fontSize: 26, fontWeight: FontWeight.bold),
                overflow: TextOverflow.ellipsis,
              ),

              const SizedBox(height: 2),

              Text(
                subtitle,
                style: const TextStyle(color: Colors.white54, fontSize: 14),
                overflow: TextOverflow.ellipsis,
              ),
            ],
          ),
        ),
      ],
    );
  }
}

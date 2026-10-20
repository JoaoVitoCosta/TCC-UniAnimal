import 'package:flutter/material.dart';
import 'package:uni_animal_flutter/screen/splash_screen.dart';

import 'core/app_theme.dart';

void main() {
  runApp(const UniAnimalApp());
}

class UniAnimalApp extends StatelessWidget {
  const UniAnimalApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(

      debugShowCheckedModeBanner: false,

      theme: AppTheme.darkTheme,

      home: const SplashScreen(),
    );
  }
}
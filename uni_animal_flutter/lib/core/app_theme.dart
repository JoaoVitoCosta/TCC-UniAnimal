import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

import 'app_colors.dart';

class AppTheme {

  static ThemeData darkTheme = ThemeData(

    brightness: Brightness.dark,

    scaffoldBackgroundColor: AppColors.background,

    primaryColor: AppColors.primary,

    textTheme: GoogleFonts.instrumentSansTextTheme(
      ThemeData.dark().textTheme,
    ),

    useMaterial3: true,
  );

}
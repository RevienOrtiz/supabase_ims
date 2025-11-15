import 'package:flutter/material.dart';
import 'home_screen.dart';
import 'notifications_screen.dart';
import 'settings_screen.dart';
import 'schedule_screen.dart';
import '../services/language_service.dart';
import '../services/font_size_service.dart';

class MainScreen extends StatefulWidget {
  const MainScreen({super.key});

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _currentIndex = 0;
  final LanguageService _languageService = LanguageService.instance;
  final FontSizeService _fontSizeService = FontSizeService.instance;

  final List<Widget> _screens = [
    const HomeScreen(), // index 0 - Home
    const NotificationsScreen(), // index 1 - Notifications
    const SettingsScreen(), // index 2 - Settings
    const ScheduleScreen(), // index 3 - Schedule
  ];

  @override
  void initState() {
    super.initState();
    _initializeLanguageService();
  }

  Future<void> _initializeLanguageService() async {
    await _languageService.init();
    setState(() {});
  }

  String _getSafeText(String key) {
    try {
      return _languageService.getText(key);
    } catch (e) {
      return key.toUpperCase();
    }
  }

  double _getSafeScaledIconSize({
    double baseSize = 24.0,
    double scaleFactor = 1.0,
  }) {
    // Check if FontSizeService is properly initialized
    if (!_fontSizeService.isInitialized) {
      // Return default icon size if service not initialized
      return baseSize * scaleFactor;
    }

    // Scale icon size based on font size
    // Use a ratio of icon size to font size (24px icon for 20px font = 1.2 ratio)
    double fontSizeRatio = _fontSizeService.fontSize / _fontSizeService.defaultFontSize;
    return baseSize * fontSizeRatio * scaleFactor;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: _screens[_currentIndex],
      bottomNavigationBar: SafeArea(
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 8),
          decoration: BoxDecoration(
            color: const Color(0xFF006662),
            borderRadius: const BorderRadius.only(
              topLeft: Radius.circular(24),
              topRight: Radius.circular(24),
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.1),
                blurRadius: 8,
                offset: const Offset(0, -2),
              ),
            ],
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceEvenly,
            children: [
              Flexible(
                  child: _buildNavItem(0, Icons.home, _getSafeText('home'))),
              Flexible(
                  child: _buildNavItem(
                      1, Icons.notifications, _getSafeText('notification'))),
              Flexible(
                  child: _buildNavItem(
                      2, Icons.settings, _getSafeText('settings'))),
              Flexible(
                  child: _buildNavItem(
                      3, Icons.schedule, _getSafeText('schedule'))),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem(int index, IconData icon, String label) {
    final isSelected = _currentIndex == index;
    return GestureDetector(
      onTap: () {
        setState(() {
          _currentIndex = index;
        });
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 8),
        decoration: BoxDecoration(
          color:
              isSelected ? Colors.white.withOpacity(0.2) : Colors.transparent,
          borderRadius: BorderRadius.circular(20), // Curved edges
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color:
                    isSelected ? Colors.white : Colors.white.withOpacity(0.2),
                borderRadius:
                    BorderRadius.circular(16), // Rounded icon container
              ),
              child: Icon(
                icon,
                size: _getSafeScaledIconSize(),
                color: isSelected ? const Color(0xFF006662) : Colors.white,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              label.toUpperCase(),
              style: TextStyle(
                fontSize: 10,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
              textAlign: TextAlign.center,
              overflow: TextOverflow.ellipsis,
              maxLines: 1,
            ),
          ],
        ),
      ),
    );
  }
}

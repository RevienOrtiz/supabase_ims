/**
 * ELDERA HEALTH APP - IMS API INTEGRATION OVERVIEW
 * 
 * This Flutter mobile application integrates with the IMS (Information Management System)
 * to provide health services for senior citizens in the Philippines.
 * 
 * BACKEND DEVELOPER: Please refer to the following files for complete API documentation:
 * 
 * 1. IMS_API_INTEGRATION_GUIDE.md - Complete API documentation and implementation guide
 * 2. lib/services/auth_service.dart - Authentication endpoints and user login flow
 * 3. lib/services/user_service.dart - User profile management endpoints
 * 4. lib/services/announcement_service.dart - Health announcements and notifications
 * 
 * KEY INTEGRATION POINTS:
 * - Base URL: https://ims-api.eldera.gov.ph/api
 * - Authentication: POST /api/auth/login, POST /api/auth/logout
 * - User Management: GET/PUT /api/users/{id}
 * - Announcements: GET /api/announcements (with filtering support)
 * 
 * SECURITY REQUIREMENTS:
 * - HTTPS only communication
 * - Proper password hashing (bcrypt recommended)
 * - Input validation and sanitization
 * - Rate limiting for authentication endpoints
 * 
 * The mobile app handles offline functionality with local storage fallbacks,
 * but requires reliable API connectivity for real-time data synchronization.
 */
import 'package:flutter/material.dart';
import 'package:timezone/data/latest.dart' as tz;
import 'package:timezone/timezone.dart' as tz;
import 'eldera.dart';
import 'services/service_manager.dart';
import 'config/environment_config.dart';
import 'services/accessibility_service.dart';
import 'package:supabase_flutter/supabase_flutter.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Initialize only critical services for fast startup
  try {
    // 1. Initialize environment configuration (essential for app to work)
    await EnvironmentConfig.initialize();
    debugPrint('Environment configuration initialized successfully');

    final supabaseUrl = await EnvironmentConfig.supabaseUrl;
    final supabaseAnonKey = await EnvironmentConfig.supabaseAnonKey;
    await Supabase.initialize(url: supabaseUrl, anonKey: supabaseAnonKey);
    debugPrint('Supabase initialized');

    // 2. Initialize timezone data (lightweight operation)
    tz.initializeTimeZones();
  } catch (e) {
    debugPrint('Critical service initialization failed: $e');
  }

  // Initialize accessibility settings (high-contrast, text size) early
  try {
    await AccessibilityService.instance.init();
    debugPrint('AccessibilityService initialized');
  } catch (e) {
    debugPrint('AccessibilityService init failed: $e');
  }

  // Start the app immediately with splash screen
  runApp(const ElderaApp());

  // Initialize heavy services in background after app starts
  _initializeBackgroundServices();
}

/// Initialize heavy services in background to avoid blocking UI
void _initializeBackgroundServices() async {
  final serviceManager = ServiceManager();

  // Initialize all services with progress tracking
  await serviceManager.initializeAllServices();

  debugPrint('🚀 Background service initialization completed');
  debugPrint('📊 Services ready: ${serviceManager.allServicesReady}');
}

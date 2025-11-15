import 'package:flutter/foundation.dart';
import '../services/secure_storage_service.dart';

/// Environment configuration for the Eldera Health app
/// Manages different settings for development, staging, and production environments
class EnvironmentConfig {
  static const String _environment = String.fromEnvironment(
    'ENVIRONMENT',
    defaultValue: 'development',
  );

  // Cached secure credentials
  static Map<String, String>? _secureCredentials;

  static bool get isDevelopment => _environment == 'development';
  static bool get isStaging => _environment == 'staging';
  static bool get isProduction => _environment == 'production';

  static String get environment => _environment;

  // Debug Configuration - Disabled for production
  static bool get enableDebugMode {
    return false; // Always disabled for production deployment
  }

  // IMS Webhook Configuration
  static Future<String> get imsWebhookSecret async {
    if (isProduction) {
      await _loadSecureCredentials();
      return _secureCredentials?['ims_webhook_secret'] ??
          const String.fromEnvironment(
            'IMS_WEBHOOK_SECRET',
            defaultValue: 'development-webhook-secret-change-in-production',
          );
    }
    return const String.fromEnvironment(
      'IMS_WEBHOOK_SECRET',
      defaultValue: 'development-webhook-secret-change-in-production',
    );
  }

  // API Configuration
  static String get apiBaseUrl {
    switch (_environment) {
      case 'production':
        return const String.fromEnvironment(
          'API_URL_PROD',
          defaultValue: 'https://eldera-ims.com',
        );
      case 'staging':
        return const String.fromEnvironment(
          'API_URL_STAGING',
          defaultValue: 'https://staging.eldera-ims.com',
        );
      default:
        // API URL configuration for different platforms:
        // - Web (Chrome): Use localhost/127.0.0.1
        // - Android emulator: Use 10.0.2.2 (special IP that points to host machine)
        // - Physical devices: Use your computer's actual IP address
        if (kIsWeb) {
          return const String.fromEnvironment(
            'API_URL_DEV',
            defaultValue:
                'http://192.168.1.115:8000', // Updated to match Laravel server IP
          );
        } else {
          // For Android devices, use 10.0.2.2 which maps to host machine's localhost
          // For physical devices, you may need to use your computer's actual IP address
          return const String.fromEnvironment(
            'API_URL_DEV',
            defaultValue:
                'http://192.168.1.115:8000', // Updated to match Laravel server IP
          );
        }
    }
  }

  // Legacy IMS API Configurationq
  static String get imsApiBaseUrl {
    switch (_environment) {
      case 'production':
        return const String.fromEnvironment(
          'IMS_API_URL_PROD',
          defaultValue: 'https://api.ims-prod.elderahealth.com',
        );
      case 'staging':
        return const String.fromEnvironment(
          'IMS_API_URL_STAGING',
          defaultValue: 'https://api.ims-staging.elderahealth.com',
        );
      default:
        return const String.fromEnvironment(
          'IMS_API_URL_DEV',
          defaultValue: 'https://api.ims-dev.elderahealth.com',
        );
    }
  }

  // Security Configuration
  static Duration get sessionTimeout {
    final defaultTimeout = _environment == 'production' ? 30 : 60;
    return Duration(
      minutes: const int.fromEnvironment(
        'SESSION_TIMEOUT_MINUTES',
        defaultValue: 30,
      ),
    );
  }

  static int get maxLoginAttempts {
    return const int.fromEnvironment(
      'MAX_LOGIN_ATTEMPTS',
      defaultValue: 5,
    );
  }

  static Duration get lockoutDuration {
    return Duration(
      minutes: const int.fromEnvironment(
        'LOCKOUT_DURATION_MINUTES',
        defaultValue: 15,
      ),
    );
  }

  // Logging Configuration
  static bool get enableSecureLogging {
    return const bool.fromEnvironment(
      'ENABLE_SECURE_LOGGING',
      defaultValue: true,
    );
  }

  static String get logLevel {
    final defaultLevel = _environment == 'production' ? 'WARNING' : 'DEBUG';
    return const String.fromEnvironment(
      'LOG_LEVEL',
      defaultValue: 'WARNING',
    );
  }

  // Rate Limiting Configuration
  static int get apiRateLimitPerMinute {
    final defaultLimit = _environment == 'production' ? 60 : 120;
    return const int.fromEnvironment(
      'API_RATE_LIMIT_PER_MINUTE',
      defaultValue: 60,
    );
  }

  // Load secure credentials from secure storage
  static Future<void> _loadSecureCredentials() async {
    if (_secureCredentials != null) return; // Already loaded

    try {
      _secureCredentials = await SecureStorageService.getApiKeys() ?? {};
    } catch (e) {
      print('Failed to load secure credentials: $e');
      _secureCredentials = {};
    }
  }

  // Store secure credentials
  static Future<void> storeSecureCredentials({
    required String supabaseUrl,
    required String supabaseAnonKey,
    required String imsWebhookSecret,
  }) async {
    try {
      final credentials = {
        'supabase_url': supabaseUrl,
        'supabase_anon_key': supabaseAnonKey,
        'ims_webhook_secret': imsWebhookSecret,
      };

      await SecureStorageService.storeApiKeys(credentials);
      _secureCredentials = credentials;
    } catch (e) {
      throw Exception('Failed to store secure credentials: $e');
    }
  }

  // Clear cached credentials (for logout)
  static void clearCachedCredentials() {
    _secureCredentials = null;
  }

  // Validation
  static Future<void> validateConfiguration() async {
    if (isProduction) {
      await _loadSecureCredentials();
      final secret = await imsWebhookSecret;

      // Debug mode is always disabled in production
      assert(secret != 'development-webhook-secret-change-in-production',
          'Production webhook secret must be changed from default');
    }
  }

  // Initialize environment configuration
  static Future<void> initialize() async {
    // Initialize secure storage
    await SecureStorageService.initialize();

    // Load secure credentials for production
    if (isProduction) {
      await _loadSecureCredentials();
    }
  }

  // Environment Info
  static Map<String, dynamic> get environmentInfo => {
        'environment': _environment,
        'isDevelopment': isDevelopment,
        'isStaging': isStaging,
        'isProduction': isProduction,
        'debugMode': false,
        'sessionTimeout': sessionTimeout.inMinutes,
        'maxLoginAttempts': maxLoginAttempts,
        'lockoutDuration': lockoutDuration.inMinutes,
      };

  static Future<String> get supabaseUrl async {
    await _loadSecureCredentials();
    final cached = _secureCredentials?['supabase_url'];
    if (cached != null && cached.isNotEmpty) return cached;
    switch (_environment) {
      case 'production':
        return const String.fromEnvironment(
          'SUPABASE_URL_PROD',
          defaultValue: 'https://gpqqeufqershuyqogpqw.supabase.co',
        );
      case 'staging':
        return const String.fromEnvironment(
          'SUPABASE_URL_STAGING',
          defaultValue: 'https://gpqqeufqershuyqogpqw.supabase.co',
        );
      default:
        return const String.fromEnvironment(
          'SUPABASE_URL_DEV',
          defaultValue: 'https://gpqqeufqershuyqogpqw.supabase.co',
        );
    }
  }

  static Future<String> get supabaseAnonKey async {
    await _loadSecureCredentials();
    final cached = _secureCredentials?['supabase_anon_key'];
    if (cached != null && cached.isNotEmpty) return cached;
    switch (_environment) {
      case 'production':
        return const String.fromEnvironment(
          'SUPABASE_ANON_KEY_PROD',
          defaultValue: 'YOUR_SUPABASE_ANON_KEY',
        );
      case 'staging':
        return const String.fromEnvironment(
          'SUPABASE_ANON_KEY_STAGING',
          defaultValue: 'YOUR_SUPABASE_ANON_KEY',
        );
      default:
        return const String.fromEnvironment(
          'SUPABASE_ANON_KEY_DEV',
          defaultValue: 'YOUR_SUPABASE_ANON_KEY',
        );
    }
  }
}

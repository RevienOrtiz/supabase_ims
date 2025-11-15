import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/user.dart' as app_user;
import '../utils/secure_logger.dart';
import '../services/secure_storage_service.dart';
import '../services/local_database_service.dart';
import '../services/optimized_api_service.dart';
import '../config/environment_config.dart';
import 'api_service.dart';
import 'user_service.dart';

/// Authentication service for the Eldera app
class AuthService {
  static bool _isAuthenticated = false;
  static app_user.User? _currentUser;
  static final String _baseUrl = EnvironmentConfig.apiBaseUrl;

  /// Sign in with OSCA ID and password
  static Future<Map<String, dynamic>> signIn(
      {required String oscaId, required String password}) async {
    try {
      // Normalize inputs to avoid accidental spaces causing auth failures
      final String normalizedOscaId = oscaId.trim();
      final String normalizedPassword = password.trim();

      // Enhanced logging for debugging
      SecureLogger.info('===== LOGIN ATTEMPT DEBUG =====');
      SecureLogger.info('Attempting login with OSCA ID: "$normalizedOscaId"');
      SecureLogger.info('Password length: ${normalizedPassword.length}');
      SecureLogger.info('API URL: $_baseUrl/api/senior/direct-login');

      // Prepare request body
      final requestBody = {
        'osca_id': normalizedOscaId,
        'password': normalizedPassword,
      };

      SecureLogger.info('Request body: ${json.encode(requestBody)}');

      // REAL API CALL
      final response = await http.post(
        Uri.parse('$_baseUrl/api/senior/direct-login'),
        headers: {'Content-Type': 'application/json'},
        body: json.encode(requestBody),
      );

      // Log the response
      SecureLogger.info('Response status code: ${response.statusCode}');
      SecureLogger.info('Response body: ${response.body}');

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        SecureLogger.info('Login successful, received token and user data');

        // Store token securely
        await SecureStorageService.storeAuthToken(data['access_token'],
            expiresIn: Duration(days: 30));
        SecureLogger.info('Token stored securely');

        // Set token in ApiService for other services to use
        ApiService.setAuthToken(data['access_token']);

        // Set authentication state
        _isAuthenticated = true;

        // Create user object from response
        try {
          _currentUser = app_user.User(
            id: data['user']['id'].toString(),
            name: data['user']['name'],
            age: 0, // Will be updated when profile is fetched
            phoneNumber: '', // Will be updated when profile is fetched
            idStatus: 'Senior Citizen',
          );
          SecureLogger.info('Created user object: ${_currentUser?.name}');
        } catch (e) {
          SecureLogger.error('Error creating user object: $e');
          // Continue with login even if user object creation fails
        }

        // Now that login is working, fetch the user profile
        try {
          SecureLogger.info('Fetching user profile...');
          await _fetchUserProfile();
          SecureLogger.info('Profile fetched successfully');
        } catch (e) {
          // Continue even if profile fetch fails
          SecureLogger.error('Error fetching profile: $e');
        }

        // Cache authentication data in local database
        try {
          await LocalDatabaseService.storeAuthData(
              'user_id', data['user']['id'].toString());
          await LocalDatabaseService.storeAuthData(
              'user_name', data['user']['name']);
          await LocalDatabaseService.storeAuthData('osca_id', normalizedOscaId);
          await LocalDatabaseService.storeAuthData(
              'login_timestamp', DateTime.now().millisecondsSinceEpoch);
          SecureLogger.info('Authentication data cached locally');
        } catch (e) {
          SecureLogger.error('Failed to cache auth data locally: $e');
        }

        return {
          'success': true,
          'message': 'Login successful',
          'user': _currentUser,
        };
      } else {
        SecureLogger.error(
            'Login failed with status code: ${response.statusCode}');
        try {
          final data = json.decode(response.body);
          SecureLogger.error(
              'Error message: ${data['message'] ?? 'No error message'}');
          return {
            'success': false,
            'message': data['message'] ?? 'Authentication failed',
          };
        } catch (e) {
          SecureLogger.error('Could not parse error response: $e');
          return {
            'success': false,
            'message': 'Authentication failed: Could not parse server response',
          };
        }
      }
    } catch (e) {
      SecureLogger.error('Authentication error: $e');
      return {
        'success': false,
        'message': 'Authentication failed: ${e.toString()}',
      };
    }
  }

  /// Fetch user profile data after login
  static Future<void> _fetchUserProfile() async {
    try {
      final token = await SecureStorageService.getAuthToken();
      if (token == null) {
        SecureLogger.error('No authentication token found for profile fetch');
        return; // Continue with basic user info instead of throwing exception
      }

      // Set token in ApiService before making requests
      ApiService.setAuthToken(token);

      final response = await http.get(
        Uri.parse('$_baseUrl/api/senior/profile'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        final profileData = data['data'];

        try {
          // Calculate age from date of birth (fallback to provided age)
          int age = 0;
          if (profileData['date_of_birth'] != null &&
              profileData['date_of_birth'].toString().isNotEmpty) {
            final DateTime dob = DateTime.parse(profileData['date_of_birth']);
            age = DateTime.now().difference(dob).inDays ~/ 365;
          } else if (profileData['age'] != null) {
            age = int.tryParse(profileData['age'].toString()) ?? 0;
          }

          final addressObj = profileData['address'] ?? {};
          final street = addressObj['street']?.toString() ?? '';
          final residence = addressObj['residence']?.toString() ?? '';
          final barangay = addressObj['barangay']?.toString() ?? '';
          final city = addressObj['city']?.toString() ?? '';
          final province = addressObj['province']?.toString() ?? '';
          final region = addressObj['region']?.toString() ?? '';
          final List<String> addressParts = [
            street,
            residence,
            barangay,
            city,
            province,
            region
          ].where((p) => p.trim().isNotEmpty).toList();

          // Normalize pension flag and status
          final hasPensionRaw = profileData['has_pension'];
          final bool hasPension = hasPensionRaw is bool
              ? hasPensionRaw
              : (hasPensionRaw is num ? hasPensionRaw != 0 : false);
          final String idStatus =
              profileData['status']?.toString() ?? 'Senior Citizen';

          final userId = profileData['id']?.toString() ?? (_currentUser?.id ?? '');
          final name = profileData['name']?.toString() ?? (_currentUser?.name ?? '');
          _currentUser = app_user.User(
            id: userId,
            name: name,
            age: age,
            phoneNumber: profileData['contact_number']?.toString() ?? '',
            idStatus: idStatus,
            birthDate: profileData['date_of_birth']?.toString(),
            address: addressParts.isEmpty ? null : addressParts.join(', '),
            profileImageUrl: profileData['photo_path']?.toString(),
            isDswdPensionBeneficiary: hasPension,
          );
        } catch (parseError) {
          SecureLogger.error('Error parsing profile data: $parseError');
          // Keep existing user data if parsing fails
        }
      } else {
        SecureLogger.error(
            'Profile fetch failed with status: ${response.statusCode}');
        // Continue with basic user info
      }
    } catch (e) {
      SecureLogger.error('Error fetching user profile: $e');
      // Continue with basic user info
    }
  }

  /// Sign out the current user
  static Future<Map<String, dynamic>> signOut() async {
    try {
      final token = await SecureStorageService.getAuthToken();

      if (token != null) {
        // Call logout API
        await http.post(
          Uri.parse('$_baseUrl/api/senior/logout'),
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer $token',
          },
        );
      }

      // Clear token and reset state
      await SecureStorageService.clearAuthToken();

      // Clear local database cache
      try {
        await LocalDatabaseService.clearAuthData();
        SecureLogger.info('Local auth cache cleared');
      } catch (e) {
        SecureLogger.error('Failed to clear local auth cache: $e');
      }

      _isAuthenticated = false;
      _currentUser = null;

      return {
        'success': true,
      };
    } catch (e) {
      SecureLogger.error('Sign out error: $e');
      return {
        'success': false,
        'message': 'Sign out failed',
      };
    }
  }

  /// Get the current authenticated user
  static Future<app_user.User?> getCurrentUser() async {
    final token = await SecureStorageService.getAuthToken();
    if (token != null) {
      _isAuthenticated = true;
      ApiService.setAuthToken(token);

      final needsProfile = _currentUser == null ||
          _currentUser!.age == 0 ||
          (_currentUser!.address == null || _currentUser!.address!.isEmpty) ||
          (_currentUser!.birthDate == null || _currentUser!.birthDate!.isEmpty) ||
          (_currentUser!.phoneNumber.isEmpty);

      if (needsProfile) {
        await _fetchUserProfile();
      }

      if (_currentUser == null ||
          _currentUser!.age == 0 ||
          (_currentUser!.address == null || _currentUser!.address!.isEmpty)) {
        final fallback = await UserService.getCurrentUser();
        if (fallback != null) {
          _currentUser = fallback;
        }
      }
    }
    return _currentUser;
  }

  /// Check if a user is currently authenticated
  static Future<bool> isAuthenticatedAsync() async {
    SecureLogger.info('=== AUTHENTICATION CHECK START ===');

    // First check in-memory state
    SecureLogger.info('Checking in-memory authentication state...');
    SecureLogger.info('_isAuthenticated: $_isAuthenticated');
    SecureLogger.info('_currentUser: ${_currentUser?.name ?? 'null'}');

    if (_isAuthenticated && _currentUser != null) {
      SecureLogger.info('User is authenticated in memory, returning true');
      return true;
    }

    // Check if we have a valid stored token
    SecureLogger.info('Checking for stored authentication token...');
    final token = await SecureStorageService.getAuthToken();

    if (token != null) {
      SecureLogger.info('Valid token found, restoring authentication state');
      // Token exists and is valid, restore authentication state
      _isAuthenticated = true;
      ApiService.setAuthToken(token);

      // Try to restore user data from local cache first
      if (_currentUser == null) {
        SecureLogger.info('No user data in memory, checking local cache...');
        try {
          final cachedUserId = LocalDatabaseService.getAuthData('user_id');
          final cachedUserName = LocalDatabaseService.getAuthData('user_name');

          if (cachedUserId != null && cachedUserName != null) {
            _currentUser = app_user.User(
              id: cachedUserId,
              name: cachedUserName,
              age: 0,
              phoneNumber: '',
              idStatus: 'Senior Citizen',
            );
            SecureLogger.info(
                'User data restored from local cache: ${_currentUser?.name}');
          } else {
            SecureLogger.info('No cached user data found, fetching profile...');
            await _fetchUserProfile();
          }
        } catch (e) {
          SecureLogger.error(
              'Error restoring from cache, fetching profile: $e');
          await _fetchUserProfile();
        }
      }

      SecureLogger.info('Authentication restored successfully');
      return true;
    }

    SecureLogger.info('No valid token found, user is not authenticated');
    return false;
  }

  /// Check if a user is currently authenticated (synchronous - for backward compatibility)
  static bool isAuthenticated() {
    return _isAuthenticated;
  }

  /// Request password reset for a user
  static Future<Map<String, dynamic>> requestPasswordReset({
    required String oscaId,
  }) async {
    try {
      SecureLogger.info('Requesting password reset for OSCA ID: $oscaId');

      final requestBody = {
        'osca_id': oscaId.trim(),
      };

      final response = await http.post(
        Uri.parse('$_baseUrl/api/senior/forgot-password'),
        headers: {'Content-Type': 'application/json'},
        body: json.encode(requestBody),
      );

      SecureLogger.info('Password reset response status: ${response.statusCode}');
      SecureLogger.info('Password reset response body: ${response.body}');

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return {
          'success': true,
          'message': data['message'] ?? 'Password reset request submitted successfully',
        };
      } else {
        try {
          final data = json.decode(response.body);
          return {
            'success': false,
            'message': data['message'] ?? 'Failed to submit password reset request',
          };
        } catch (e) {
          return {
            'success': false,
            'message': 'Failed to submit password reset request',
          };
        }
      }
    } catch (e) {
      SecureLogger.error('Password reset request error: $e');
      return {
        'success': false,
        'message': 'Network error. Please check your connection and try again.',
      };
    }
  }
}
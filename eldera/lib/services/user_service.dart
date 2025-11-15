import 'dart:typed_data';
import '../models/user.dart';
import '../utils/secure_logger.dart';
import 'api_service.dart';
import 'secure_storage_service.dart';

/// User service for the Eldera app
class UserService {
  /// Get the current user profile
  static Future<User?> getCurrentUser() async {
    try {
      final token = await SecureStorageService.getAuthToken();
      if (token != null) {
        ApiService.setAuthToken(token);
      }
      final response = await ApiService.get('senior/profile');

      if (response['success'] == true && response['data'] != null) {
        final data = response['data'];

        // Map Laravel API response to Flutter User model structure
        final intAge = () {
          final a = data['age'];
          if (a is int) return a;
          if (a is String) return int.tryParse(a) ?? 0;
          return 0;
        }();
        final mappedData = {
          'id': data['id']?.toString() ?? '',
          'name': data['name']?.toString() ?? '',
          'age': intAge,
          'phone_number': data['contact_number']?.toString() ?? '',
          'profile_image_url': data['photo_path']?.toString(),
          'id_status': (data['status'] ?? 'Senior Citizen').toString(),
          'is_dswd_pension_beneficiary': (data['has_pension'] is bool)
              ? data['has_pension']
              : ((data['has_pension'] is num) ? data['has_pension'] != 0 : false),
          'birth_date': data['date_of_birth']?.toString(),
          'address': _buildAddressString(
              (data['address'] is Map<String, dynamic>)
                  ? data['address'] as Map<String, dynamic>
                  : null),
          'guardian_name': null,
          'created_at': null,
          'updated_at': null,
        };

        return User.fromJson(mappedData);
      }
      return null;
    } catch (e) {
      SecureLogger.error('Error fetching user profile: $e');
      return null;
    }
  }

  /// Helper method to build address string from Laravel address object
  static String? _buildAddressString(Map<String, dynamic>? address) {
    if (address == null) return null;

    final parts = <String>[];
    if (address['street'] != null) parts.add(address['street']);
    if (address['barangay'] != null) parts.add(address['barangay']);
    if (address['city'] != null) parts.add(address['city']);
    if (address['province'] != null) parts.add(address['province']);
    if (address['region'] != null) parts.add(address['region']);

    return parts.isNotEmpty ? parts.join(', ') : null;
  }

  /// Update user profile
  static Future<Map<String, dynamic>> updateUserProfile(
      {required String userId,
      bool? isDswdPensionBeneficiary,
      String? name,
      String? phoneNumber,
      String? address}) async {
    try {
      // Update user profile via localhost API
      final data = {
        'user_id': userId,
        if (isDswdPensionBeneficiary != null)
          'is_dswd_pension_beneficiary': isDswdPensionBeneficiary,
        if (name != null) 'name': name,
        if (phoneNumber != null) 'phone_number': phoneNumber,
        if (address != null) 'address': address,
      };

      return await ApiService.put('user/profile', data);
    } catch (e) {
      SecureLogger.error('Error updating user profile: $e');
      return {
        'success': false,
        'message': 'Failed to update profile',
      };
    }
  }

  /// Update profile image
  static Future<Map<String, dynamic>> updateProfileImage(
      {required String userId,
      required Uint8List imageBytes,
      required String fileName}) async {
    try {
      // Simulate successful image update
      return {
        'success': true,
        'imageUrl': 'https://example.com/profile.jpg',
      };
    } catch (e) {
      SecureLogger.error('Error updating profile image: $e');
      return {
        'success': false,
        'message': 'Failed to update profile image',
      };
    }
  }

  /// Download profile image
  static Future<Map<String, dynamic>> downloadProfileImage(
      {required String userId, required String imageUrl}) async {
    try {
      // Return mock image data
      return {
        'success': true,
        'imageData': null, // Would contain actual image data
      };
    } catch (e) {
      SecureLogger.error('Error downloading profile image: $e');
      return {
        'success': false,
        'message': 'Failed to download profile image',
      };
    }
  }
}
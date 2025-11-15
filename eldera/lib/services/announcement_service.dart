import '../models/announcement.dart';
import '../utils/secure_logger.dart';
import 'api_service.dart';
import 'package:supabase_flutter/supabase_flutter.dart';

/// Announcement service for the Eldera app
class AnnouncementService {
  /// Get all announcements
  static Future<List<Announcement>> getAllAnnouncements() async {
    try {
      // Primary: fetch announcements from Supabase
      final client = Supabase.instance.client;
      final result = await client
          .from('announcements')
          .select()
          .order('posted_date', ascending: false);

      if (result is List) {
        final items = result
            .map((e) => Announcement.fromJson(
                Map<String, dynamic>.from(e as Map<String, dynamic>)))
            .toList();
        SecureLogger.info('Announcements fetched from Supabase: count=${items.length}');
        return items;
      }

      // Fallback: fetch via Laravel API if Supabase returns non-list or fails
      final response = await ApiService.get('announcements');
      if (response['success'] == true && response['data'] != null) {
        final List<dynamic> announcementsJson = response['data'];
        final items = announcementsJson
            .map((json) => Announcement.fromJson(
                Map<String, dynamic>.from(json as Map)))
            .toList();
        SecureLogger.info('Announcements fetched via API fallback: count=${items.length}');
        return items;
      }

      return [];
    } catch (e) {
      SecureLogger.error('Error fetching announcements: $e');
      return [];
    }
  }

  /// Get announcements by category
  static Future<List<Announcement>> getAnnouncementsByCategory(
      String category) async {
    try {
      // Prefer server-side filtering when possible
      try {
        final client = Supabase.instance.client;
        final result = await client
            .from('announcements')
            .select()
            .eq('category', category)
            .order('posted_date', ascending: false);
        if (result is List) {
          return result
              .map((e) => Announcement.fromJson(
                  Map<String, dynamic>.from(e as Map<String, dynamic>)))
              .toList();
        }
      } catch (_) {
        // Ignore and fallback to client-side filtering
      }

      final allAnnouncements = await getAllAnnouncements();
      return allAnnouncements.where((a) => a.category == category).toList();
    } catch (e) {
      SecureLogger.error('Error fetching announcements by category: $e');
      return [];
    }
  }

  /// Get announcements by department
  static Future<List<Announcement>> getAnnouncementsByDepartment(
      String department) async {
    try {
      // Prefer server-side filtering when possible
      try {
        final client = Supabase.instance.client;
        final result = await client
            .from('announcements')
            .select()
            .ilike('department', department)
            .order('posted_date', ascending: false);
        if (result is List) {
          return result
              .map((e) => Announcement.fromJson(
                  Map<String, dynamic>.from(e as Map<String, dynamic>)))
              .toList();
        }
      } catch (_) {
        // Ignore and fallback to client-side filtering
      }

      final allAnnouncements = await getAllAnnouncements();
      return allAnnouncements.where((a) => a.department == department).toList();
    } catch (e) {
      SecureLogger.error('Error fetching announcements by department: $e');
      return [];
    }
  }
}
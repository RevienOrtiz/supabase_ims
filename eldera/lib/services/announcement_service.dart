import '../models/announcement.dart';
import '../utils/secure_logger.dart';
import 'api_service.dart';

/// Announcement service for the Eldera app
class AnnouncementService {
  /// Get all announcements
  static Future<List<Announcement>> getAllAnnouncements() async {
    try {
      // Fetch announcements from localhost API
      final response = await ApiService.get('announcements');

      if (response['success'] == true && response['data'] != null) {
        final List<dynamic> announcementsJson = response['data'];
        SecureLogger.info(
            'Announcements fetched: count=${announcementsJson.length}');
        if (announcementsJson.isNotEmpty) {
          SecureLogger.debug(
              'Sample announcement JSON: ${announcementsJson.first}');
        }
        final items = announcementsJson
            .map((json) => Announcement.fromJson(json))
            .toList();
        SecureLogger.info('Announcements mapped: count=${items.length}');
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
      final allAnnouncements = await getAllAnnouncements();
      return allAnnouncements.where((a) => a.department == department).toList();
    } catch (e) {
      SecureLogger.error('Error fetching announcements by department: $e');
      return [];
    }
  }
}

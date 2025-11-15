import 'package:intl/intl.dart';
import '../models/announcement.dart';
import '../utils/secure_logger.dart';
import 'api_service.dart';
import 'package:supabase_flutter/supabase_flutter.dart';

/// Event service to fetch IMS events and adapt them for Home feed
class EventService {
  /// Fetch IMS events and convert them to Announcement cards
  static Future<List<Announcement>> getAllEventsAsAnnouncements() async {
    try {
      final client = Supabase.instance.client;
      final result = await client.from('events').select();
      if (result is List) {
        return result
            .map<Announcement>((e) => _eventToAnnouncement(
                Map<String, dynamic>.from(e as Map<String, dynamic>)))
            .toList();
      }
      final response = await ApiService.get('events');
      if (response['success'] == true) {
        final data = response['data'];
        List<dynamic> items = [];
        if (data is List) items = data;
        if (data is Map) {
          final list = data['data'] ?? data['events'];
          if (list is List) items = list;
        }
        return items
            .map<Announcement>(
                (e) => _eventToAnnouncement(Map<String, dynamic>.from(e)))
            .toList();
      }
      return [];
    } catch (e, st) {
      SecureLogger.error('Error fetching IMS events: $e');
      SecureLogger.debug(st.toString());
      return [];
    }
  }

  /// Map IMS event JSON into Announcement used by UI
  static Announcement _eventToAnnouncement(Map<String, dynamic> e) {
    final String id = (e['id'] ?? e['uuid'] ?? '').toString();
    final String title =
        (e['title'] ?? e['name'] ?? 'Community Event').toString();
    final String location = (e['location'] ?? e['where'] ?? '').toString();

    final String eventDateStr =
        (e['event_date'] ?? e['date'] ?? e['start'] ?? e['created_at'] ?? '')
            .toString();
    final String startTimeStr =
        (e['start_time'] ?? e['time'] ?? e['start'] ?? '').toString();
    // Only accept explicit end_time; do not fall back to date keys
    final String endTimeStr = (e['end_time'] ?? '').toString();
    final String whenFormatted =
        _formatWhen(eventDateStr, startTimeStr, endTimeStr);

    final String eventType =
        (e['event_type'] ?? e['type'] ?? 'general').toString().toLowerCase();
    final String category = _mapEventTypeToCategory(eventType);
    final String iconType = _mapCategoryToIcon(category);

    // Use created_at from IMS as the posted date; fallback to event date
    final String postedDate =
        (e['created_at'] ?? e['postedDate'] ?? eventDateStr ?? '').toString();
    final String what =
        (e['description'] ?? e['notes'] ?? 'Municipal event scheduled via IMS')
            .toString();

    return Announcement(
      id: id,
      title: title,
      postedDate: postedDate,
      what: what,
      when: whenFormatted,
      where: location,
      category: category,
      // Department field not used anymore; keep empty to avoid UI reliance
      department: (e['department'] ?? '').toString(),
      iconType: iconType,
      hasReminder: true,
      hasListen: true,
    );
  }

  /// Determine if an event occurs today or in the future based on raw fields
  static bool _isOnOrAfterToday(Map<String, dynamic> e) {
    try {
      final String rawDateStr =
          (e['event_date'] ?? e['date'] ?? e['start'] ?? '').toString();
      if (rawDateStr.isEmpty) return true; // Keep if unknown

      DateTime? dt = DateTime.tryParse(rawDateStr);
      // Handle common non-ISO formats, e.g., MM/DD/YYYY
      if (dt == null) {
        final slashFmt = RegExp(r'^(\d{1,2})/(\d{1,2})/(\d{4})');
        final m = slashFmt.firstMatch(rawDateStr);
        if (m != null) {
          final mm = int.tryParse(m.group(1)!);
          final dd = int.tryParse(m.group(2)!);
          final yy = int.tryParse(m.group(3)!);
          if (mm != null && dd != null && yy != null) {
            dt = DateTime(yy, mm, dd);
          }
        }
      }

      if (dt == null) return true; // Keep if still unparseable
      final today = DateTime.now();
      final todayDate = DateTime(today.year, today.month, today.day);
      final eventDate = DateTime(dt.year, dt.month, dt.day);
      return !eventDate.isBefore(todayDate);
    } catch (_) {
      return true; // Be permissive on parsing errors
    }
  }

  static String _formatWhen(String dateStr, String timeStr,
      [String? endTimeStr]) {
    try {
      DateTime? date = DateTime.tryParse(dateStr);
      if (date == null) {
        // Attempt to parse common formats (YYYY-MM-DD, MM/DD/YYYY)
        final cleaned = dateStr.replaceAll('/', '-');
        date = DateTime.tryParse(cleaned);
      }

      DateTime dt;
      if (date != null && timeStr.isNotEmpty) {
        // Normalize HH:mm or HH:mm:ss
        String t = timeStr.trim();
        if (!RegExp(r'^\d{2}:\d{2}(:\d{2})?$').hasMatch(t)) {
          // Fallback: remove non-digits and try to rebuild
          final m = RegExp(r'(\d{1,2}):(\d{2})').firstMatch(timeStr);
          if (m != null) {
            t = '${m.group(1)!.padLeft(2, '0')}:${m.group(2)}';
          } else {
            t = '09:00';
          }
        }
        final parts = t.split(':');
        final h = int.tryParse(parts[0]) ?? 9;
        final m = int.tryParse(parts[1]) ?? 0;
        dt = DateTime(date.year, date.month, date.day, h, m);
      } else {
        dt = date ?? DateTime.now();
      }

      final dateFmt = DateFormat('MMMM d, y');
      final timeFmt = DateFormat('h:mm a');
      if (timeStr.isEmpty) {
        return dateFmt.format(dt);
      }

      // If endTime provided, format as range: "date - start to end"
      String formattedStart = timeFmt.format(dt);

      String? formattedEnd;
      if (endTimeStr != null && endTimeStr.trim().isNotEmpty) {
        String et = endTimeStr.trim();
        // Only proceed if it resembles a time string
        final strict = RegExp(r'^\d{2}:\d{2}(:\d{2})?$');
        final loose = RegExp(r'^(\d{1,2}):(\d{2})');
        if (strict.hasMatch(et) || loose.hasMatch(et)) {
          if (!strict.hasMatch(et)) {
            final m2 = loose.firstMatch(et);
            if (m2 != null) {
              et = '${m2.group(1)!.padLeft(2, '0')}:${m2.group(2)}';
            }
          }
          final eparts = et.split(':');
          final eh = int.tryParse(eparts[0]) ?? 9;
          final em = int.tryParse(eparts[1]) ?? 0;
          final dtEnd = DateTime(dt.year, dt.month, dt.day, eh, em);
          formattedEnd = timeFmt.format(dtEnd);
        }
      }

      if (formattedEnd != null) {
        return '${dateFmt.format(dt)} - $formattedStart to $formattedEnd';
      }
      return '${dateFmt.format(dt)} at $formattedStart';
    } catch (_) {
      return dateStr.isNotEmpty ? dateStr : 'Date to be announced';
    }
  }

  static String _mapEventTypeToCategory(String t) {
    switch (t) {
      case 'health':
        return 'HEALTH';
      case 'pension':
      case 'benefits':
        return 'PENSION';
      case 'id_claiming':
        return 'ID_CLAIMING';
      case 'general':
      case 'meeting':
      default:
        return 'GENERAL';
    }
  }

  static String _mapCategoryToIcon(String c) {
    switch (c) {
      case 'HEALTH':
        return 'health';
      case 'PENSION':
        return 'card';
      default:
        return 'announcement';
    }
  }
}
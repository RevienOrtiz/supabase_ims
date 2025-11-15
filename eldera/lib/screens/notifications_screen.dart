import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:intl/intl.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/announcement.dart';
import '../services/event_service.dart';
import '../services/reminder_service.dart';
import '../services/local_notification_service.dart';
import '../services/font_size_service.dart';
import '../services/language_service.dart';
import '../services/mock_session_service.dart';
import '../services/accessibility_service.dart';

class NotificationsScreen extends StatefulWidget {
  const NotificationsScreen({super.key});

  @override
  State<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends State<NotificationsScreen> {
  List<Announcement> announcements = [];
  bool isLoading = true;
  String? errorMessage;
  final FontSizeService _fontSizeService = FontSizeService.instance;
  final LanguageService _languageService = LanguageService.instance;
  double _currentFontSize = 20.0;
  final Set<String> _viewedAnnouncementIds = <String>{};
  final Map<String, int> _viewedTimestamps = <String, int>{};
  static const String _viewedKeyLegacy = 'viewed_notification_ids';
  static const String _viewedKeyWithTimestamps =
      'viewed_notification_timestamps';
  static const Duration _viewedExpiry = Duration(days: 7);

  Color _getCategoryColor(String category) {
    switch (category.toLowerCase()) {
      case 'health':
        return const Color(0xFFFFC5CE); // Pink (updated)
      case 'pension':
        return const Color(0xFFAEE9FF); // Blue
      case 'general':
        return const Color(0xFFD1FFC8); // Light green shade as requested
      case 'id_claiming':
        return const Color(0xFFE9DE89); // Yellow
      default:
        return Colors.grey[300]!; // Default
    }
  }

  @override
  void initState() {
    super.initState();
    _loadFontSize();
    _initializeLanguageService();
    _loadViewedStates();
    _loadAnnouncements();
  }

  Future<void> _initializeLanguageService() async {
    await _languageService.init();
    setState(() {});
  }

  // Persistent viewed-state handling
  String _announcementKey(Announcement a) {
    // Prefer stable id; fallback to composite key if missing
    if (a.id.isNotEmpty) return a.id;
    return '${a.title}|${a.when}|${a.where}';
  }

  Future<void> _loadViewedStates() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final nowMs = DateTime.now().millisecondsSinceEpoch;

      // Try new format first (timestamps as JSON map)
      final jsonStr = prefs.getString(_viewedKeyWithTimestamps);
      Map<String, int> loadedMap = <String, int>{};
      if (jsonStr != null && jsonStr.isNotEmpty) {
        final dynamic decoded = jsonDecode(jsonStr);
        if (decoded is Map) {
          decoded.forEach((key, value) {
            final k = key?.toString();
            final v = (value is int)
                ? value
                : int.tryParse(value?.toString() ?? '') ?? nowMs;
            if (k != null && k.isNotEmpty) {
              loadedMap[k] = v;
            }
          });
        }
      } else {
        // Migrate legacy list (without timestamps) if present
        final legacyList = prefs.getStringList(_viewedKeyLegacy) ?? [];
        if (legacyList.isNotEmpty) {
          for (final k in legacyList) {
            if (k.isNotEmpty) loadedMap[k] = nowMs;
          }
          // Persist in new format and remove legacy key
          await prefs.setString(
              _viewedKeyWithTimestamps, jsonEncode(loadedMap));
          await prefs.remove(_viewedKeyLegacy);
        }
      }

      // Purge expired entries
      final cutoffMs = nowMs - _viewedExpiry.inMilliseconds;
      loadedMap.removeWhere((k, ts) => ts < cutoffMs);

      setState(() {
        _viewedTimestamps
          ..clear()
          ..addAll(loadedMap);
        _viewedAnnouncementIds
          ..clear()
          ..addAll(loadedMap.keys);
      });

      // Persist pruned map
      await prefs.setString(
          _viewedKeyWithTimestamps, jsonEncode(_viewedTimestamps));
    } catch (_) {
      // Ignore prefs errors; viewed-state will default to empty
    }
  }

  Future<void> _markAnnouncementViewed(Announcement a) async {
    try {
      final key = _announcementKey(a);
      final nowMs = DateTime.now().millisecondsSinceEpoch;
      setState(() {
        _viewedAnnouncementIds.add(key);
        _viewedTimestamps[key] = nowMs;
      });
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString(
          _viewedKeyWithTimestamps, jsonEncode(_viewedTimestamps));
    } catch (_) {
      // Best-effort persistence; UI state already updated
    }
  }

  String _getSafeText(String key) {
    try {
      return _languageService.getText(key);
    } catch (e) {
      return key.toUpperCase();
    }
  }

  Future<void> _loadFontSize() async {
    await _fontSizeService.init();
    setState(() {
      _currentFontSize = _fontSizeService.fontSize;
    });
  }

  double _getSafeScaledFontSize({
    double? baseSize,
    bool isTitle = false,
    bool isSubtitle = false,
  }) {
    // Check if FontSizeService is properly initialized
    if (!_fontSizeService.isInitialized) {
      // Return default font size if service not initialized
      double defaultSize = 20.0;
      double scaleFactor = baseSize ?? 1.0;

      if (isTitle) {
        scaleFactor = 1.2;
      } else if (isSubtitle) {
        scaleFactor = 1.1;
      }

      // Ensure minimum readable font size
      double calculatedSize = defaultSize * scaleFactor;
      return calculatedSize < 14.0 ? 14.0 : calculatedSize;
    }

    double scaledSize = _fontSizeService.getScaledFontSize(
      baseSize: baseSize ?? 1.0,
      isTitle: isTitle,
      isSubtitle: isSubtitle,
    );

    // Ensure minimum readable font size for better accessibility
    return scaledSize < 14.0 ? 14.0 : scaledSize;
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

  Future<void> _loadAnnouncements() async {
    try {
      setState(() {
        isLoading = true;
        errorMessage = null;
      });

      // Check if we're in mock session mode first
      final mockSession = MockSessionService.instance;
      if (mockSession.isTestMode) {
        // Load mock announcements
        final mockAnnouncements = mockSession.announcements;
        setState(() {
          announcements = mockAnnouncements;
          isLoading = false;
        });
        print(
            '📱 Loaded ${mockAnnouncements.length} mock announcements for notifications screen');
        return;
      }

      // Fetch from events/calendar (events table) and map to Announcement cards
      final loadedAnnouncements =
          await EventService.getAllEventsAsAnnouncements();

      // Sort newest-first: primary by postedDate (descending), fallback to event date
      loadedAnnouncements.sort((a, b) {
        final pa = _parsePostedDate(a.postedDate, whenForFallback: a.when);
        final pb = _parsePostedDate(b.postedDate, whenForFallback: b.when);
        final byPostedDesc = pb.compareTo(pa);
        if (byPostedDesc != 0) return byPostedDesc;
        final da = _parseAnnouncementDate(a.when);
        final db = _parseAnnouncementDate(b.when);
        return db.compareTo(da);
      });

      setState(() {
        announcements = loadedAnnouncements;
        isLoading = false;
      });
    } catch (e) {
      setState(() {
        errorMessage = 'Failed to load announcements: $e';
        isLoading = false;
      });
    }
  }

  String _getTimeAgo(String postedDate) {
    final lang = _languageService;
    // Try to parse ISO or common date formats; fallback to existing string patterns
    DateTime? dt = DateTime.tryParse(postedDate);
    if (dt == null) {
      // Support 'YYYY-MM-DD' and 'MM/DD/YYYY'
      final cleaned = postedDate.replaceAll('/', '-').trim();
      dt = DateTime.tryParse(cleaned);
      if (dt == null) {
        final m =
            RegExp(r'^(\d{1,2})/(\d{1,2})/(\d{4})').firstMatch(postedDate);
        if (m != null) {
          final mm = int.tryParse(m.group(1)!);
          final dd = int.tryParse(m.group(2)!);
          final yy = int.tryParse(m.group(3)!);
          if (mm != null && dd != null && yy != null) {
            dt = DateTime(yy, mm, dd);
          }
        }
      }
    }

    if (dt != null) {
      final now = DateTime.now();
      final diff = now.difference(dt);
      if (diff.inDays >= 1) {
        return lang
            .getText('days_ago')
            .replaceAll('%d', diff.inDays.toString());
      } else if (diff.inHours >= 1) {
        return lang
            .getText('hours_ago')
            .replaceAll('%d', diff.inHours.toString());
      } else if (diff.inMinutes >= 1) {
        return lang
            .getText('minutes_ago')
            .replaceAll('%d', diff.inMinutes.toString());
      }
      return lang.getText('just_now');
    }

    // Extract time from "Posted X hours/days ago" format
    if (postedDate.contains('hour')) {
      final hours =
          RegExp(r'(\d+)\s+hour').firstMatch(postedDate)?.group(1) ?? '0';
      return lang.getText('hours_ago').replaceAll('%d', hours);
    } else if (postedDate.contains('day')) {
      final days =
          RegExp(r'(\d+)\s+day').firstMatch(postedDate)?.group(1) ?? '0';
      return lang.getText('days_ago').replaceAll('%d', days);
    }
    return postedDate.replaceAll('Posted ', '').trim();
  }

  // Parse announcement event date from display string (e.g., "December 25, 2024")
  DateTime _parseAnnouncementDate(String when) {
    try {
      final datePattern = RegExp(r'(\w+)\s+(\d+),\s+(\d+)');
      final match = datePattern.firstMatch(when);
      if (match != null) {
        final monthName = match.group(1)!;
        final day = int.parse(match.group(2)!);
        final year = int.parse(match.group(3)!);
        final month = _getMonthNumber(monthName);
        return DateTime(year, month, day);
      }
    } catch (_) {}
    // Fallback to epoch for stable ordering when unparsable
    return DateTime.fromMillisecondsSinceEpoch(0);
  }

  // Map month names to month numbers
  int _getMonthNumber(String monthName) {
    const months = {
      'january': 1,
      'february': 2,
      'march': 3,
      'april': 4,
      'may': 5,
      'june': 6,
      'july': 7,
      'august': 8,
      'september': 9,
      'october': 10,
      'november': 11,
      'december': 12,
    };
    return months[monthName.toLowerCase()] ?? 1;
  }

  // Parse postedDate with robust fallbacks; default to event date if needed
  DateTime _parsePostedDate(String postedDate, {String? whenForFallback}) {
    // ISO or RFC-like
    DateTime? dt = DateTime.tryParse(postedDate);
    if (dt != null) return dt;

    // Common variations: replace slashes
    final cleaned = postedDate.replaceAll('/', '-').trim();
    dt = DateTime.tryParse(cleaned);
    if (dt != null) return dt;

    // MM/DD/YYYY
    final mdy = RegExp(r'^(\d{1,2})/(\d{1,2})/(\d{4})').firstMatch(postedDate);
    if (mdy != null) {
      final month = int.parse(mdy.group(1)!);
      final day = int.parse(mdy.group(2)!);
      final year = int.parse(mdy.group(3)!);
      return DateTime(year, month, day);
    }

    // YYYY-MM-DD
    final ymd = RegExp(r'^(\d{4})-(\d{2})-(\d{2})').firstMatch(postedDate);
    if (ymd != null) {
      final year = int.parse(ymd.group(1)!);
      final month = int.parse(ymd.group(2)!);
      final day = int.parse(ymd.group(3)!);
      return DateTime(year, month, day);
    }

    // Fallback: use event date if provided
    if (whenForFallback != null) {
      return _parseAnnouncementDate(whenForFallback);
    }

    // Last resort: epoch
    return DateTime.fromMillisecondsSinceEpoch(0);
  }

  String _computeEventStatus(DateTimeRange range) {
    final now = DateTime.now();
    if (now.isBefore(range.start)) return 'upcoming';
    if (now.isAfter(range.end)) return 'completed';
    if (range.start.isAtSameMomentAs(range.end)) {
      if (now.isAtSameMomentAs(range.start)) return 'ongoing';
      return now.isBefore(range.start) ? 'upcoming' : 'completed';
    }
    return 'ongoing';
  }

  Map<String, Object> _statusStyle(String status) {
    switch (status) {
      case 'upcoming':
        return {
          'text': _languageService.getText('upcoming').toUpperCase(),
          'bg': Colors.blue.shade100,
          'fg': Colors.blue.shade800,
        };
      case 'ongoing':
        return {
          'text': _languageService.getText('ongoing').toUpperCase(),
          'bg': Colors.green.shade100,
          'fg': Colors.green.shade800,
        };
      case 'completed':
        return {
          'text': _languageService.getText('completed').toUpperCase(),
          'bg': Colors.grey.shade300,
          'fg': Colors.grey.shade800,
        };
    }
    // Default style fallback
    return {
      'text': _languageService.getText('status').toUpperCase(),
      'bg': Colors.grey.shade200,
      'fg': Colors.grey.shade800,
    };
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFFFFF0), // Ivory white background
      appBar: AppBar(
        backgroundColor: const Color(0xFF2E8B8B),
        elevation: 0,
        automaticallyImplyLeading: false,
        title: Text(
          _getSafeText('notifications'),
          style: TextStyle(
            color: Colors.white,
            fontSize: _getSafeScaledFontSize(isTitle: true),
            fontWeight: FontWeight.bold,
          ),
        ),
        actions: [],
      ),
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (isLoading) {
      return const Center(
        child: CircularProgressIndicator(
          valueColor: AlwaysStoppedAnimation<Color>(Color(0xFF2E8B8B)),
        ),
      );
    }

    if (errorMessage != null) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.error_outline,
              size: _getSafeScaledIconSize(baseSize: 64.0),
              color: Color(0xFF2E8B8B),
            ),
            const SizedBox(height: 16),
            Text(
              errorMessage!,
              style: TextStyle(
                fontSize: _getSafeScaledFontSize(isSubtitle: true),
                color: Colors.black87,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: _loadAnnouncements,
              child: Text(_getSafeText('try_again')),
            ),
          ],
        ),
      );
    }

    if (announcements.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.notifications_none,
              size: _getSafeScaledIconSize(baseSize: 64.0),
              color: Color(0xFF2E8B8B),
            ),
            const SizedBox(height: 16),
            Text(
              _getSafeText('no_notifications'),
              style: TextStyle(
                fontSize: _getSafeScaledFontSize(isTitle: true),
                fontWeight: FontWeight.bold,
                color: Colors.black87,
              ),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadAnnouncements,
      child: ListView.builder(
        padding: const EdgeInsets.only(left: 8.0, right: 8.0, bottom: 16.0), // Reduced side padding for more compact layout
        itemCount: announcements.length,
        itemBuilder: (context, index) {
          final announcement = announcements[index];
          return _buildNotificationCard(announcement);
        },
      ),
    );
  }

  Widget _buildNotificationCard(Announcement announcement) {
    final timeAgo = _getTimeAgo(announcement.postedDate);
    final categoryColor = _getCategoryColor(announcement.category);
    final reminderService = ReminderService.instance;
    final range = reminderService.parseEventTimeRange(announcement.when);
    final status = _computeEventStatus(range);
    final style = _statusStyle(status);
    final key = _announcementKey(announcement);
    final isViewed = _viewedAnnouncementIds.contains(key);

    // Enhanced opacity values for better visual distinction
    final double bgOpacity = isViewed ? 0.7 : 0.9;
    final double borderOpacity = isViewed ? 0.8 : 1.0;
    final double iconBgOpacity = isViewed ? 0.85 : 1.0;

    return Container(
      margin: const EdgeInsets.only(bottom: 12, top: 0), // Removed top margin completely
      padding: const EdgeInsets.all(12), // Drastically reduced padding for ultra-compact layout
      decoration: BoxDecoration(
        color: categoryColor.withOpacity(bgOpacity),
        borderRadius: BorderRadius.circular(20), // More rounded corners for modern look
        border: Border.all(
            color: categoryColor.withOpacity(borderOpacity),
            width: 1.2), // Slightly adjusted border width
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.12), // Enhanced shadow for better depth
            spreadRadius: 0,
            blurRadius: 12, // Increased blur for softer shadow
            offset: const Offset(0, 4), // More pronounced offset
          ),
          BoxShadow(
            color: Colors.black.withOpacity(0.06), // Additional subtle shadow layer
            spreadRadius: 2,
            blurRadius: 20,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: GestureDetector(
        onTap: () {
          _markAnnouncementViewed(announcement);
          _showAnnouncementDetails(announcement);
        },
        child: Column(
          children: [
            // New notification indicator row
            if (!isViewed)
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  // "NEW" text on the left
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                    decoration: BoxDecoration(
                      color: const Color(0xFFFF4444), // Red background
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: const Text(
                      'NEW',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  // Yellow dot on the right (made more visible)
                  Container(
                    width: 12,
                    height: 12,
                    decoration: BoxDecoration(
                      color: const Color(0xFFFF4444), // Changed to red for better visibility
                      shape: BoxShape.circle,
                      border: Border.all(
                        color: Colors.white,
                        width: 2,
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: const Color(0xFFFF4444).withOpacity(0.5),
                          spreadRadius: 1,
                          blurRadius: 3,
                          offset: const Offset(0, 1),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            if (!isViewed) const SizedBox(height: 8),
            
            // Compact main content row with button on right
            Row(
              crossAxisAlignment: CrossAxisAlignment.center,
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                // Content section - takes available space
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Enhanced title
                      Text(
                        announcement.title,
                        style: TextStyle(
                          fontSize: _getSafeScaledFontSize(isSubtitle: true),
                          fontWeight: FontWeight.w700, // Bolder title
                          color: Colors.black87,
                          height: 1.1, // Reduced line height for more compact appearance
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 4), // Further reduced spacing for ultra-compact layout
                      
                      // Enhanced description
                      Text(
                        LanguageService.instance.translateFreeText(announcement.what),
                        style: TextStyle(
                          fontSize: _getSafeScaledFontSize(baseSize: 0.85), // Slightly smaller for compactness
                          color: Colors.black.withOpacity(0.75), // Better contrast
                          height: 1.3, // Reduced line height for compactness
                          fontWeight: FontWeight.w400,
                        ),
                        maxLines: 2, // Reduced to 2 lines for compactness
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 8), // Reduced spacing
                      
                      // Enhanced time ago with icon
                      Row(
                        children: [
                          Icon(
                            Icons.access_time,
                            size: _getSafeScaledIconSize(baseSize: 12.0),
                            color: Colors.black.withOpacity(0.6),
                          ),
                          const SizedBox(width: 3),
                          Text(
                            timeAgo,
                            style: TextStyle(
                              fontSize: _getSafeScaledFontSize(baseSize: 0.7), // Smaller text
                              color: Colors.black.withOpacity(0.65),
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
                
                // Compact VIEW button on the right
                GestureDetector(
                  onTap: () {
                    _markAnnouncementViewed(announcement);
                    _showAnnouncementDetails(announcement);
                  },
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6), // Compact padding
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [
                          const Color(0xFF00BFFF),
                          const Color(0xFF0099CC),
                        ],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      borderRadius: BorderRadius.circular(10), // Slightly smaller radius
                      boxShadow: [
                        BoxShadow(
                          color: const Color(0xFF00BFFF).withOpacity(0.3), // Reduced shadow
                          spreadRadius: 0,
                          blurRadius: 6,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          _getSafeText('view'),
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: _getSafeScaledFontSize(baseSize: 0.75), // Slightly smaller
                            fontWeight: FontWeight.w700,
                            letterSpacing: 0.4,
                          ),
                        ),
                        const SizedBox(width: 3),
                        Icon(
                          Icons.arrow_forward_ios,
                          color: Colors.white,
                          size: _getSafeScaledIconSize(baseSize: 10.0),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  void _showAnnouncementDetails(Announcement announcement) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return StatefulBuilder(
          builder: (context, setState) {
            final reminderService = ReminderService.instance;
            final hasReminder = reminderService.hasReminder(announcement.id);
            final reminderInfo = reminderService.getReminder(announcement.id);
            // Compute event status to control reminder availability
            final range =
                reminderService.parseEventTimeRange(announcement.when);
            final status = _computeEventStatus(range);
            final isNotUpcoming = status != 'upcoming';

            return AlertDialog(
              title: Text(
                announcement.title,
                style: const TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 18,
                ),
              ),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildDetailRow(
                      _getSafeText('what') + ':', announcement.what),
                  const SizedBox(height: 8),
                  _buildDetailRow(
                      _getSafeText('when') + ':', announcement.when),
                  const SizedBox(height: 8),
                  _buildDetailRow(
                      _getSafeText('where') + ':', announcement.where),
                  const SizedBox(height: 8),
                  // Department will be dropped; display only event-sourced fields
                  _buildDetailRow(
                      _getSafeText('category') + ':', announcement.category),
                  const SizedBox(height: 16),
                  const Divider(),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Icon(Icons.notifications,
                          size: _getSafeScaledIconSize(baseSize: 20.0), color: Colors.blue),
                      const SizedBox(width: 8),
                      Text(
                        _getSafeText('reminder') + ':',
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: _getSafeScaledFontSize(baseSize: 0.7),
                        ),
                      ),
                      const Spacer(),
                      if (hasReminder)
                        Chip(
                          label: Text(
                            ReminderService.getReminderTypeText(
                                reminderInfo?.reminderType),
                            style: TextStyle(
                                fontSize:
                                    _getSafeScaledFontSize(baseSize: 0.6)),
                          ),
                          backgroundColor: Colors.green.withOpacity(0.1),
                          deleteIcon: const Icon(Icons.close, size: 16),
                          onDeleted: () async {
                            final prefs = await SharedPreferences.getInstance();
                            final calendarSyncEnabled =
                                prefs.getBool('calendar_sync_enabled') ?? false;

                            await reminderService.removeReminder(
                              announcement.id,
                              removeFromCalendar: calendarSyncEnabled,
                            );
                            setState(() {});
                          },
                        )
                      else if (isNotUpcoming)
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 10, vertical: 4),
                          constraints: const BoxConstraints(maxWidth: 160),
                          decoration: BoxDecoration(
                            color: Colors.grey.shade300,
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(Icons.notifications_off,
                                  size: _getSafeScaledIconSize(baseSize: 16.0), color: Colors.grey),
                              const SizedBox(width: 6),
                              Text(
                                _getSafeText('set_reminder'),
                                style: const TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w500,
                                  color: Colors.grey,
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ],
                          ),
                        )
                      else
                        TextButton.icon(
                          onPressed: () {
                            _showReminderOptions(
                                context, announcement, setState);
                          },
                          icon: Icon(Icons.add, size: _getSafeScaledIconSize(baseSize: 16.0)),
                          label: Text(_getSafeText('set_reminder')),
                          style: TextButton.styleFrom(
                            foregroundColor: Colors.blue,
                          ),
                        ),
                    ],
                  ),
                  if (hasReminder && reminderInfo?.reminderTime != null)
                    Padding(
                      padding: const EdgeInsets.only(left: 28, top: 4),
                      child: Text(
                        _getSafeText('reminder') +
                            ': ' +
                            ReminderService.formatCompleteReminderInfo(
                                reminderInfo?.reminderType ?? '',
                                reminderInfo?.reminderTime ?? DateTime.now()),
                        style: const TextStyle(
                          fontSize: 12,
                          color: Colors.grey,
                        ),
                      ),
                    ),
                ],
              ),
              actions: [
                TextButton(
                  onPressed: () => Navigator.of(context).pop(),
                  child: Text(_getSafeText('close')),
                ),
              ],
            );
          },
        );
      },
    );
  }

  void _showReminderOptions(
      BuildContext context, Announcement announcement, StateSetter setState) {
    final lang = _languageService;
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(lang.getText('set_reminder')),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              ListTile(
                leading: const Icon(Icons.schedule, color: Colors.blue),
                title: Text(lang.getText('one_hour_before')),
                onTap: () async {
                  Navigator.of(context).pop();
                  await _setReminder(announcement, '1_hour_before', setState);
                },
              ),
              ListTile(
                leading: const Icon(Icons.today, color: Colors.green),
                title: Text(lang.getText('one_day_before')),
                onTap: () async {
                  Navigator.of(context).pop();
                  await _setReminder(announcement, '1_day_before', setState);
                },
              ),
              ListTile(
                leading: const Icon(Icons.date_range, color: Colors.orange),
                title: Text(lang.getText('custom_time')),
                onTap: () async {
                  Navigator.of(context).pop();
                  await _showCustomReminderPicker(announcement, setState);
                },
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: Text(lang.getText('cancel')),
            ),
          ],
        );
      },
    );
  }

  Future<void> _setReminder(Announcement announcement, String reminderType,
      StateSetter setState) async {
    final prefs = await SharedPreferences.getInstance();
    final calendarSyncEnabled = prefs.getBool('calendar_sync_enabled') ?? false;

    final reminderService = ReminderService.instance;
    final success = await reminderService.setReminder(
      announcement,
      reminderType,
      addToCalendar: calendarSyncEnabled,
    );

    if (success) {
      setState(() {});
      if (mounted) {
        final lang = _languageService;
        String message = '${lang.getText('reminder')}: ' +
            ReminderService.getReminderTypeText(reminderType);
        if (calendarSyncEnabled) {
          message += ' ' + lang.getText('added_to_calendar');
        }
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(message),
            backgroundColor: Colors.green,
          ),
        );
      }
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(_getSafeText('reminder_failed_try_again')),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  Future<void> _showCustomReminderPicker(
      Announcement announcement, StateSetter setState) async {
    // Parse the event date to set proper limits
    final reminderService = ReminderService.instance;
    final eventDateTime = reminderService.parseEventDateTime(announcement.when);

    final DateTime? pickedDate = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime.now(),
      lastDate: eventDateTime.subtract(const Duration(minutes: 1)),
    );

    if (pickedDate != null && mounted) {
      final TimeOfDay? pickedTime = await showTimePicker(
        context: context,
        initialTime: TimeOfDay.now(),
      );

      if (pickedTime != null) {
        var customDateTime = DateTime(
          pickedDate.year,
          pickedDate.month,
          pickedDate.day,
          pickedTime.hour,
          pickedTime.minute,
        );

        // If the selected time is in the past (for today), move it to tomorrow
        if (customDateTime.isBefore(DateTime.now())) {
          customDateTime = customDateTime.add(const Duration(days: 1));
        }

        final prefs = await SharedPreferences.getInstance();
        final calendarSyncEnabled =
            prefs.getBool('calendar_sync_enabled') ?? false;

        final reminderService = ReminderService.instance;
        final success = await reminderService.setReminder(
          announcement,
          'custom',
          customTime: customDateTime,
          addToCalendar: calendarSyncEnabled,
        );

        if (success) {
          setState(() {});
          if (mounted) {
            String message = _getSafeText('reminder') +
                ': ' +
                DateFormat('MMM d, y h:mm a').format(customDateTime);
            if (calendarSyncEnabled) {
              message += ' ' + _getSafeText('added_to_calendar');
            }
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(message),
                backgroundColor: Colors.green,
              ),
            );
          }
        } else {
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(_getSafeText('reminder_invalid_time')),
                backgroundColor: Colors.red,
              ),
            );
          }
        }
      }
    }
  }

  Widget _buildDetailRow(String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: 80,
          child: Text(
            label,
            style: TextStyle(
              fontWeight: FontWeight.bold,
              fontSize: _getSafeScaledFontSize(baseSize: 0.8),
            ),
          ),
        ),
        Expanded(
          child: Text(
            value,
            style: TextStyle(
              fontSize: _getSafeScaledFontSize(baseSize: 0.8),
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
      ],
    );
  }
}

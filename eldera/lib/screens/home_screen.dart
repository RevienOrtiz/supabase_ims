import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/announcement.dart';
import '../services/event_service.dart';
import '../services/reminder_service.dart';
import '../services/font_size_service.dart';
import '../services/local_notification_service.dart';
import '../services/language_service.dart';
import '../services/gemini_tts_service.dart';
import '../config/app_colors.dart';
import '../utils/contrast_utils.dart';
import '../services/accessibility_service.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  String selectedCategory = 'ALL';
  List<String> categories = ['ALL', 'PENSION', 'HEALTH', 'GENERAL'];
  late GeminiTtsService geminiTts;
  List<Announcement> announcements = [];
  bool isLoading = true;
  String? errorMessage;
  final FontSizeService _fontSizeService = FontSizeService.instance;
  final LanguageService _languageService = LanguageService.instance;
  double _currentFontSize = 20.0;

  @override
  void initState() {
    super.initState();
    _initializeGeminiTts();
    _loadFontSize();
    _initializeLanguageService();
    _loadAnnouncements();
  }

  Future<void> _initializeLanguageService() async {
    await _languageService.init();
    setState(() {
      // Update categories with localized text
      categories = [
        _getSafeText('all'),
        _getSafeText('pension'),
        _getSafeText('health'),
        _getSafeText('general')
      ];
      if (selectedCategory == 'ALL') selectedCategory = _getSafeText('all');
    });
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

      return defaultSize * scaleFactor;
    }

    return _fontSizeService.getScaledFontSize(
      baseSize: baseSize ?? 1.0,
      isTitle: isTitle,
      isSubtitle: isSubtitle,
    );
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

  Future<void> _initializeGeminiTts() async {
    try {
      geminiTts = GeminiTtsService();
      
      // Get API key from SharedPreferences
      final prefs = await SharedPreferences.getInstance();
      String? apiKey = prefs.getString('gemini_api_key');
      
      if (apiKey == null || apiKey.isEmpty) {
        print('Gemini TTS: No API key found. Please configure your Gemini API key in settings.');
        return;
      }
      
      await geminiTts.initialize(apiKey);
      print('Gemini TTS: Successfully initialized with Kore voice');
    } catch (e) {
      print('Gemini TTS: Failed to initialize: $e');
    }
  }

  Future<void> _loadAnnouncements() async {
    try {
      setState(() {
        isLoading = true;
        errorMessage = null;
      });

      // Load announcements from IMS Events API only
      final eventAnnouncements =
          await EventService.getAllEventsAsAnnouncements();

      setState(() {
        announcements = eventAnnouncements;
        isLoading = false;
      });
    } catch (e) {
      setState(() {
        errorMessage = e.toString();
        isLoading = false;
      });
    }
  }

  Future<void> _refreshAnnouncements() async {
    await _loadAnnouncements();
  }

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
    } catch (e) {
      return DateTime.now();
    }
    return DateTime.now();
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

  String _formatPostedDate(String postedDate) {
    try {
      // Try ISO 8601 parse first
      final iso = DateTime.tryParse(postedDate);
      if (iso != null) {
        return 'Posted at ${DateFormat('MMMM d, y').format(iso)}';
      }
      // Fallback for YYYY-MM-DD
      final m = RegExp(r'^(\d{4})-(\d{2})-(\d{2})').firstMatch(postedDate);
      if (m != null) {
        final y = int.parse(m.group(1)!);
        final mo = int.parse(m.group(2)!);
        final d = int.parse(m.group(3)!);
        return 'Posted at ${DateFormat('MMMM d, y').format(DateTime(y, mo, d))}';
      }
      // If it's already a friendly string (e.g., 'Posted 2 days ago'), keep it
      return postedDate.startsWith('Posted')
          ? postedDate
          : 'Posted at $postedDate';
    } catch (_) {
      return postedDate.startsWith('Posted')
          ? postedDate
          : 'Posted at $postedDate';
    }
  }

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
      'december': 12
    };
    return months[monthName.toLowerCase()] ?? 1;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFFFFF0),
      body: Column(
        children: [
          Container(
            width: double.infinity,
            color: const Color(0xFF2E8B8B),
            padding: EdgeInsets.only(
              top: MediaQuery.of(context).padding.top,
            ),
            child: _buildCategoryFilters(),
          ),
          Expanded(
            child: _buildNotificationsList(),
          ),
        ],
      ),
    );
  }

  Color _getCategoryColor(String category) {
    switch (category.toLowerCase()) {
      case 'health':
        return const Color(0xFFFFC5CE); // Pink (updated)
      case 'pension':
        return const Color(0xFFAEE9FF); // Blue
      case 'general':
        return const Color(0xFFD1FFC8); // Green
      case 'id_claiming':
        return const Color(0xFFE9DE89); // Yellow
      default:
        return Colors.grey[300]!; // Default for ALL
    }
  }

  Widget _buildCategoryFilters() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      child: Row(
        children: [
          // Category filters
          Expanded(
            child: SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: categories.map((category) {
                  final isSelected = category == selectedCategory;
                  return Padding(
                    padding: const EdgeInsets.only(right: 12),
                    child: GestureDetector(
                      onTap: () {
                        setState(() {
                          selectedCategory = category;
                        });
                      },
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 16, vertical: 8),
                        decoration: BoxDecoration(
                          color: isSelected
                              ? _getCategoryColor(category)
                              : Colors.grey[300],
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          category,
                          style: TextStyle(
                            fontSize: _getSafeScaledFontSize(baseSize: 0.8),
                            fontWeight: FontWeight.w500,
                            color: ContrastUtils.getAccessibleTextColor(
                              isSelected
                                  ? _getCategoryColor(category)
                                  : Colors.grey[300]!,
                              preferDark: true,
                            ),
                          ),
                        ),
                      ),
                    ),
                  );
                }).toList(),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildNotificationsList() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16),
      child: RefreshIndicator(
        onRefresh: _refreshAnnouncements,
        child: _buildAnnouncementContent(),
      ),
    );
  }

  Widget _buildAnnouncementContent() {
    if (isLoading) {
      return const Center(
        child: Padding(
          padding: EdgeInsets.all(50.0),
          child: CircularProgressIndicator(
            strokeWidth: 3.0,
            valueColor: AlwaysStoppedAnimation<Color>(Color(0xFF4CAF50)),
          ),
        ),
      );
    }

    if (errorMessage != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(20.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(
                Icons.error_outline,
                size: 64,
                color: Colors.red,
              ),
              const SizedBox(height: 16),
              Text(
                _getSafeText('error_loading'),
                style: TextStyle(
                  fontSize: _getSafeScaledFontSize(isTitle: true),
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                errorMessage!,
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: _getSafeScaledFontSize(baseSize: 0.8),
                  color: AppColors.textSecondaryOnLight,
                ),
              ),
              const SizedBox(height: 16),
              ElevatedButton(
                onPressed: _refreshAnnouncements,
                child: Text(_getSafeText('try_again')),
              ),
            ],
          ),
        ),
      );
    }

    if (announcements.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(50.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(
                Icons.inbox_outlined,
                size: 64,
                color: Colors.grey,
              ),
              const SizedBox(height: 16),
              Text(
                _getSafeText('no_announcements'),
                style: TextStyle(
                  fontSize: _getSafeScaledFontSize(isTitle: true),
                  fontWeight: FontWeight.bold,
                  color: Colors.grey,
                ),
              ),
            ],
          ),
        ),
      );
    }

    // Filter announcements by selected category and date
    // Requirement: Show current/future announcements, and keep completed events
    // visible for up to 10 days past their due date with a "COMPLETED" indication.
    final now = DateTime.now();
    final today = DateTime(now.year, now.month, now.day);

    List<Announcement> filteredAnnouncements =
        announcements.where((announcement) {
      // Parse announcement date (date-only key)
      final announcementDate = _parseAnnouncementDate(announcement.when);
      final announcementDay = DateTime(
        announcementDate.year,
        announcementDate.month,
        announcementDate.day,
      );

      // Visibility window: today or future, or within last 10 days
      final daysDiff =
          today.difference(announcementDay).inDays; // positive if in past
      final isWithinVisibilityWindow =
          announcementDay.isAtSameMomentAs(today) ||
              announcementDay.isAfter(today) ||
              (announcementDay.isBefore(today) && daysDiff <= 10);

      // Filter by category
      final matchesCategory = selectedCategory == _getSafeText('all') ||
          announcement.category == selectedCategory ||
          (selectedCategory == _getSafeText('pension') &&
              announcement.category == 'PENSION') ||
          (selectedCategory == _getSafeText('health') &&
              announcement.category == 'HEALTH') ||
          (selectedCategory == _getSafeText('general') &&
              announcement.category == 'GENERAL');

      return isWithinVisibilityWindow && matchesCategory;
    }).toList();

    // Sort by posted date (newest first), then by event date
    filteredAnnouncements.sort((a, b) {
      final pa = _parsePostedDate(a.postedDate, whenForFallback: a.when);
      final pb = _parsePostedDate(b.postedDate, whenForFallback: b.when);
      final byPostedDesc = pb.compareTo(pa);
      if (byPostedDesc != 0) return byPostedDesc;
      final da = _parseAnnouncementDate(a.when);
      final db = _parseAnnouncementDate(b.when);
      return db.compareTo(da);
    });

    return ListView.builder(
      physics: const BouncingScrollPhysics(),
      itemCount: filteredAnnouncements.length,
      itemBuilder: (context, index) {
        final announcement = filteredAnnouncements[index];
        return Column(
          children: [
            _buildAnnouncementCard(announcement),
            if (index < filteredAnnouncements.length - 1)
              const SizedBox(height: 16),
            if (index == filteredAnnouncements.length - 1)
              const SizedBox(height: 100),
          ],
        );
      },
    );
  }

  void _speakCardContent(Announcement announcement) async {
    // Create the text to speak
    String textToSpeak =
        "${announcement.title}. ${LanguageService.instance.translateFreeText(announcement.what)}. Scheduled for ${announcement.when} at ${announcement.where}.";

    try {
      if (geminiTts.isInitialized) {
        // Use Gemini TTS with Kore voice
        print('Using Gemini TTS with Kore voice to speak: $textToSpeak');
        await geminiTts.speak(textToSpeak);
      } else {
        print('Gemini TTS not initialized. Please configure your API key in settings.');
        // Show a snackbar to inform the user
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Please configure your Gemini API key in settings to use voice features.'),
              duration: Duration(seconds: 3),
            ),
          );
        }
      }
    } catch (e) {
      print('Error during Gemini TTS speech: $e');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Voice feature temporarily unavailable. Please check your internet connection.'),
            duration: Duration(seconds: 3),
          ),
        );
      }
    }
  }

  Future<void> _setReminder(
      String reminderType, Announcement announcement) async {
    final prefs = await SharedPreferences.getInstance();
    final calendarSyncEnabled = prefs.getBool('calendar_sync_enabled') ?? false;

    final reminderService = ReminderService.instance;
    final success = await reminderService.setReminder(
      announcement,
      reminderType,
      addToCalendar: calendarSyncEnabled,
    );

    if (success) {
      setState(() {}); // Refresh the UI to show reminder indicator
      if (mounted) {
        String message = _getSafeText('reminder') +
            ': ' +
            ReminderService.getReminderTypeText(reminderType);
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
            content: Text(_getSafeText('reminder_failed_try_again')),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  Widget _buildAnnouncementCard(Announcement announcement) {
    final categoryColor = _getCategoryColor(announcement.category);
    final reminderService = ReminderService.instance;
    final range = reminderService.parseEventTimeRange(announcement.when);
    final status = _computeEventStatus(range);
    final statusStyle = _statusStyle(status);
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: categoryColor.withOpacity(0.75),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: categoryColor.withOpacity(0.95), width: 2),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Compact header row with icon, title, date, and status
          Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              // Icon with number badge
              Stack(
                children: [
                  Container(
                    width: _getSafeScaledIconSize(baseSize: 32.0),
                    height: _getSafeScaledIconSize(baseSize: 32.0),
                    decoration: BoxDecoration(
                      color: categoryColor
                          .withOpacity(1.0)
                          .withBlue(
                              (categoryColor.blue * 0.7).round().clamp(0, 255))
                          .withGreen(
                              (categoryColor.green * 0.7).round().clamp(0, 255))
                          .withRed(
                              (categoryColor.red * 0.7).round().clamp(0, 255)),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: Center(
                      child: Icon(
                        Announcement.getIconData(announcement.iconType),
                        color: Colors.white,
                        size: _getSafeScaledIconSize(baseSize: 18.0),
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(width: 12),
              // Title and date column
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      announcement.title,
                      style: TextStyle(
                        fontSize: _getSafeScaledFontSize(isTitle: true),
                        fontWeight: FontWeight.w600,
                        color: Colors.black87,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 2),
                    Text(
                      _formatPostedDate(announcement.postedDate),
                      style: TextStyle(
                        fontSize: _getSafeScaledFontSize(baseSize: 0.8),
                        color: Colors.grey[600],
                      ),
                    ),
                  ],
                ),
              ),
              // Status pill
              _buildStatusPill(statusStyle['text'] as String,
                  statusStyle['bg'] as Color, statusStyle['fg'] as Color),
            ],
          ),
          const SizedBox(height: 12),
          // Content section
          RichText(
            text: TextSpan(
              children: [
                TextSpan(
                  text: _getSafeText('what') + ': ',
                  style: TextStyle(
                    fontSize: _getSafeScaledFontSize(baseSize: 0.9),
                    fontWeight: FontWeight.bold,
                    color: Colors.black87,
                  ),
                ),
                TextSpan(
                  text: LanguageService.instance
                      .translateFreeText(announcement.what),
                  style: TextStyle(
                    fontSize: _getSafeScaledFontSize(baseSize: 0.9),
                    fontWeight: FontWeight.w600,
                    color: Colors.black87,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 6),
          RichText(
            text: TextSpan(
              children: [
                TextSpan(
                  text: _getSafeText('when') + ': ',
                  style: TextStyle(
                    fontSize: _getSafeScaledFontSize(baseSize: 0.9),
                    fontWeight: FontWeight.bold,
                    color: Colors.black87,
                  ),
                ),
                TextSpan(
                  text: announcement.when,
                  style: TextStyle(
                    fontSize: _getSafeScaledFontSize(baseSize: 0.9),
                    fontWeight: FontWeight.w600,
                    color: Colors.black87,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 6),
          RichText(
            text: TextSpan(
              children: [
                TextSpan(
                  text: _getSafeText('where') + ': ',
                  style: TextStyle(
                    fontSize: _getSafeScaledFontSize(baseSize: 0.9),
                    fontWeight: FontWeight.bold,
                    color: Colors.black87,
                  ),
                ),
                TextSpan(
                  text: announcement.where,
                  style: TextStyle(
                    fontSize: _getSafeScaledFontSize(baseSize: 0.9),
                    fontWeight: FontWeight.w600,
                    color: Colors.black87,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 12),
          // Action buttons row
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              // Play button
              if (announcement.hasListen)
                GestureDetector(
                  onTap: () {
                    _speakCardContent(announcement);
                  },
                  child: Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 8, vertical: 6),
                    decoration: BoxDecoration(
                      color: Color(0xFF007bff),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(
                          Icons.play_arrow,
                          color: Colors.white,
                          size: _getSafeScaledIconSize(baseSize: 16.0),
                        ),
                        const SizedBox(width: 4),
                        Text(
                          _getSafeText('play'),
                          style: TextStyle(
                            fontSize: _getSafeScaledFontSize(baseSize: 0.8),
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              // Remind Me button
              if (announcement.hasReminder) _buildReminderButton(announcement),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildStatusPill(String text, Color bgColor, Color fgColor) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 3),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: Colors.black.withOpacity(0.15)),
      ),
      child: Text(
        text,
        style: TextStyle(
          color: fgColor,
          fontWeight: FontWeight.bold,
          fontSize: _getSafeScaledFontSize(baseSize: 0.65),
        ),
      ),
    );
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
          'text': _getSafeText('upcoming').toUpperCase(),
          'bg': Colors.blue.shade100,
          'fg': Colors.blue.shade800,
        };
      case 'ongoing':
        return {
          'text': _getSafeText('ongoing').toUpperCase(),
          'bg': Colors.green.shade100,
          'fg': Colors.green.shade800,
        };
      case 'completed':
        return {
          'text': _getSafeText('completed').toUpperCase(),
          'bg': Colors.grey.shade300,
          'fg': Colors.grey.shade800,
        };
    }
    // Default style fallback
    return {
      'text': _getSafeText('status').toUpperCase(),
      'bg': Colors.grey.shade200,
      'fg': Colors.grey.shade800,
    };
  }

  Widget _buildReminderButton(Announcement announcement) {
    final reminderService = ReminderService.instance;
    final hasReminder = reminderService.hasReminder(announcement.id);
    final reminderInfo = reminderService.getReminder(announcement.id);
    final range = reminderService.parseEventTimeRange(announcement.when);
    final status = _computeEventStatus(range);
    final isCompleted = status == 'completed';

    if (hasReminder) {
      // Show reminder status with option to remove
      return GestureDetector(
        onTap: () async {
          final shouldRemove = await showDialog<bool>(
            context: context,
            builder: (context) => AlertDialog(
              title: Text(_getSafeText('reminder_set')),
              content: Text(
                _getSafeText('reminder') +
                    ': ' +
                    ReminderService.getReminderTypeText(
                        reminderInfo?.reminderType) +
                    '. ' +
                    _getSafeText('confirm'),
              ),
              actions: [
                TextButton(
                  onPressed: () => Navigator.of(context).pop(false),
                  child: Text(_getSafeText('cancel')),
                ),
                TextButton(
                  onPressed: () => Navigator.of(context).pop(true),
                  child: Text(_getSafeText('delete')),
                ),
              ],
            ),
          );

          if (shouldRemove == true) {
            final prefs = await SharedPreferences.getInstance();
            final calendarSyncEnabled =
                prefs.getBool('calendar_sync_enabled') ?? false;

            await reminderService.removeReminder(
              announcement.id,
              removeFromCalendar: calendarSyncEnabled,
            );
            setState(() {}); // Refresh UI
            if (mounted) {
              String message = _getSafeText('reminder_removed');
              if (calendarSyncEnabled) {
                message += ' ' + _getSafeText('removed_from_calendar');
              }
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: Text(message),
                  backgroundColor: Colors.orange,
                ),
              );
            }
          }
        },
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
          constraints: const BoxConstraints(
            maxWidth: 120,
          ),
          decoration: BoxDecoration(
            color: Colors.green.shade200,
            borderRadius: BorderRadius.circular(20),
          ),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Icon(
                Icons.check_circle,
                color: Colors.green,
                size: 14, // Fixed size that won't scale with font
              ),
              const SizedBox(width: 4),
              Flexible(
                child: Text(
                  _getSafeText('reminder_set'),
                  style: TextStyle(
                    fontSize: _getSafeScaledFontSize(baseSize: 0.7),
                    fontWeight: FontWeight.w500,
                    color: Colors.green.shade800,
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
        ),
      );
    } else {
      // If event is completed, show disabled/greyed button; otherwise active button
      if (isCompleted) {
        return Container(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
          constraints: const BoxConstraints(
            maxWidth: 120,
          ),
          decoration: BoxDecoration(
            color: Colors.grey.shade300,
            borderRadius: BorderRadius.circular(20),
          ),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Flexible(
                child: Text(
                  _getSafeText('remind_me'),
                  style: TextStyle(
                    fontSize: _getSafeScaledFontSize(baseSize: 0.8),
                    fontWeight: FontWeight.w500,
                    color: Colors.grey.shade600,
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
        );
      }

      // Show reminder options using dialog (same as schedule screen)
      return GestureDetector(
        onTap: () {
          _showReminderOptions(context, announcement);
        },
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
          constraints: const BoxConstraints(
            maxWidth: 120,
          ),
          decoration: BoxDecoration(
            color: Colors.purple.shade200,
            borderRadius: BorderRadius.circular(20),
          ),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Flexible(
                child: Text(
                  _getSafeText('remind_me'),
                  style: TextStyle(
                    fontSize: _getSafeScaledFontSize(baseSize: 0.8),
                    fontWeight: FontWeight.w500,
                    color: Colors.black87,
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
              ),
              const SizedBox(width: 4),
              Icon(
                Icons.keyboard_arrow_down,
                color: Colors.black87,
                size: _getSafeScaledIconSize(baseSize: 18.0),
              ),
            ],
          ),
        ),
      );
    }
  }

  void _showReminderOptions(BuildContext context, Announcement announcement) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(_getSafeText('set_reminder')),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              ListTile(
                leading: Icon(Icons.schedule, color: Colors.blue, size: _getSafeScaledIconSize()),
                title: Text(_getSafeText('one_hour_before')),
                onTap: () async {
                  Navigator.of(context).pop();
                  await _setReminder('1_hour_before', announcement);
                },
              ),
              ListTile(
                leading: Icon(Icons.today, color: Colors.green, size: _getSafeScaledIconSize()),
                title: Text(_getSafeText('one_day_before')),
                onTap: () async {
                  Navigator.of(context).pop();
                  await _setReminder('1_day_before', announcement);
                },
              ),
              ListTile(
                leading: Icon(Icons.date_range, color: Colors.orange, size: _getSafeScaledIconSize()),
                title: Text(_getSafeText('custom_time')),
                onTap: () async {
                  Navigator.of(context).pop();
                  await _showCustomReminderPicker(announcement);
                },
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: Text(_getSafeText('cancel')),
            ),
          ],
        );
      },
    );
  }

  Future<void> _showCustomReminderPicker(Announcement announcement) async {
    final reminderService = ReminderService.instance;
    final eventDateTime = reminderService.parseEventDateTime(announcement.when);

    final DateTime? pickedDate = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime.now(),
      lastDate: eventDateTime
          .subtract(const Duration(minutes: 1)), // Must be before event
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
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(
                  _getSafeText('reminder') +
                      ': ' +
                      DateFormat('MMM d, y h:mm a').format(customDateTime),
                ),
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
}

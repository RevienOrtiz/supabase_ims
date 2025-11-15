import 'package:flutter/material.dart';
import 'package:table_calendar/table_calendar.dart';
import 'package:intl/intl.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/announcement.dart';
import '../models/attendance.dart';
import '../services/event_service.dart';
import '../services/attendance_service.dart';
import '../services/reminder_service.dart';
import '../services/font_size_service.dart';
import '../services/language_service.dart';
import '../services/auth_service.dart';
import '../services/mock_session_service.dart';
import '../services/accessibility_service.dart';

class ScheduleScreen extends StatefulWidget {
  const ScheduleScreen({super.key});

  @override
  State<ScheduleScreen> createState() => _ScheduleScreenState();
}

class _ScheduleScreenState extends State<ScheduleScreen>
    with TickerProviderStateMixin {
  CalendarFormat _calendarFormat = CalendarFormat.month;
  DateTime _focusedDay = DateTime.now();
  DateTime? _selectedDay;
  List<Announcement> _selectedDayAnnouncements = [];
  bool _isLoading = true;
  final FontSizeService _fontSizeService = FontSizeService.instance;
  final LanguageService _languageService = LanguageService.instance;
  final String _selectedFilter = 'CURRENT DATE';
  double _currentFontSize = 20.0;
  List<Announcement> _allAnnouncements = [];

  // Map to store announcements by date for quick lookup
  Map<DateTime, List<Announcement>> _announcementsByDate = {};

  // Tab controller and attendance-related variables
  late TabController _tabController;
  List<Attendance> _userAttendance = [];
  bool _isLoadingAttendance = false;
  Map<String, int> _attendanceStats = {'attended': 0, 'missed': 0, 'total': 0};
  String _attendanceFilter = 'all'; // 'all', 'attended', 'missed'

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _selectedDay = _focusedDay;
    _loadFontSize();
    _initializeLanguageService();
    _initializeReminderService();
    _loadAnnouncements();
    _loadUserAttendance();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _initializeLanguageService() async {
    await _languageService.init();
    setState(() {});
  }

  Future<void> _initializeReminderService() async {
    try {
      await ReminderService.instance.initialize();
      debugPrint('✅ ReminderService initialized in schedule screen');
    } catch (e) {
      debugPrint(
          '❌ ReminderService initialization failed in schedule screen: $e');
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

  Color _getCategoryColor(String category) {
    switch (category.toLowerCase()) {
      case 'health':
        return const Color(0xFFEA9BAE); // Pink - EA9BAE
      case 'pension':
        return const Color(0xFFAEE9FF); // Blue - AEE9FF
      case 'general':
        return const Color(0xFFD1FFC8); // Green - D1FFC8
      case 'id_claiming':
        return const Color(0xFFE7E09C); // Yellow - E7E09C
      default:
        return Colors.grey[300]!; // Default
    }
  }

  Widget _buildEventsTab() {
    return _isLoading
        ? const Center(child: CircularProgressIndicator())
        : SingleChildScrollView(
            child: Column(
              children: [
                _buildCalendarSection(),
                const SizedBox(height: 16),
                _buildEventsList(),
                _buildLegendsSection(),
              ],
            ),
          );
  }

  Widget _buildAttendanceTab() {
    return SingleChildScrollView(
      child: Column(
        children: [
          _buildAttendanceStats(),
          _buildAttendanceFilters(),
          const SizedBox(height: 16),
          _buildAttendanceList(),
        ],
      ),
    );
  }

  Widget _buildAttendanceStats() {
    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            spreadRadius: 1,
            blurRadius: 5,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            _getSafeText('attendance_summary'),
            style: TextStyle(
              fontSize: _getSafeScaledFontSize(isTitle: true),
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: _buildStatCard(
                  _getSafeText('attended'),
                  _attendanceStats['attended'].toString(),
                  Colors.green,
                  Icons.check_circle,
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: _buildStatCard(
                  _getSafeText('missed'),
                  _attendanceStats['missed'].toString(),
                  Colors.red,
                  Icons.cancel,
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: _buildStatCard(
                  _getSafeText('total'),
                  _attendanceStats['total'].toString(),
                  Colors.blue,
                  Icons.event,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildStatCard(
      String title, String value, Color color, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Column(
        children: [
          Icon(
            icon,
            color: color,
            size: _getSafeScaledIconSize(),
          ),
          const SizedBox(height: 8),
          Text(
            value,
            style: TextStyle(
              fontSize: _getSafeScaledFontSize(isTitle: true),
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            title,
            style: TextStyle(
              fontSize: _getSafeScaledFontSize(baseSize: 0.7),
              color: Colors.grey[600],
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  Widget _buildAttendanceFilters() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Row(
        children: [
          Text(
            _getSafeText('filter_by'),
            style: TextStyle(
              fontSize: _getSafeScaledFontSize(baseSize: 0.8),
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: [
                  _buildFilterChip(_getSafeText('all'), 'all'),
                  const SizedBox(width: 8),
                  _buildFilterChip(_getSafeText('attended'), 'attended'),
                  const SizedBox(width: 8),
                  _buildFilterChip(_getSafeText('missed'), 'missed'),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFilterChip(String label, String value) {
    final isSelected = _attendanceFilter == value;
    final color = value == 'attended'
        ? Colors.green
        : value == 'missed'
            ? Colors.red
            : Colors.blue;

    return GestureDetector(
      onTap: () {
        setState(() {
          _attendanceFilter = value;
        });
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        decoration: BoxDecoration(
          color: isSelected ? color : Colors.transparent,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: color),
        ),
        child: Text(
          label,
          style: TextStyle(
            color: isSelected ? Colors.white : color,
            fontSize: _getSafeScaledFontSize(baseSize: 0.7),
            fontWeight: FontWeight.w600,
          ),
        ),
      ),
    );
  }

  Widget _buildAttendanceList() {
    if (_isLoadingAttendance) {
      return const Center(child: CircularProgressIndicator());
    }

    final filteredAttendance = _getFilteredAttendance();

    if (filteredAttendance.isEmpty) {
      return Container(
        height: 200,
        child: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                Icons.assignment_turned_in_outlined,
                size: 64,
                color: Colors.grey[400],
              ),
              const SizedBox(height: 16),
              Text(
                _getSafeText('no_attendance_records'),
                style: TextStyle(
                  color: Colors.grey[600],
                  fontSize: _getSafeScaledFontSize(baseSize: 0.9),
                ),
              ),
            ],
          ),
        ),
      );
    }

    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16),
      child: ListView.builder(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: filteredAttendance.length,
        itemBuilder: (context, index) {
          return _buildAttendanceCard(filteredAttendance[index]);
        },
      ),
    );
  }

  Widget _buildAttendanceCard(Attendance attendance) {
    final isAttended = attendance.isAttended;
    final statusColor = isAttended ? Colors.green : Colors.red;
    final statusIcon = isAttended ? Icons.check_circle : Icons.cancel;

    return Card(
      margin: const EdgeInsets.only(bottom: 8),
      child: Container(
        decoration: BoxDecoration(
          color: statusColor.withOpacity(0.05),
          borderRadius: BorderRadius.circular(8),
          border: Border.all(
            color: statusColor.withOpacity(0.3),
            width: 1,
          ),
        ),
        child: ListTile(
          leading: Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: statusColor.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(
              statusIcon,
              color: statusColor,
              size: _getSafeScaledIconSize(),
            ),
          ),
          title: Text(
            attendance.eventTitle,
            style: TextStyle(
              fontWeight: FontWeight.w600,
              fontSize: _getSafeScaledFontSize(baseSize: 0.85),
            ),
          ),
          subtitle: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 4),
              Text(
                attendance.formattedEventDate,
                style: TextStyle(
                  fontSize: _getSafeScaledFontSize(baseSize: 0.75),
                  color: Colors.grey[600],
                ),
              ),
              if (attendance.notes?.isNotEmpty == true) ...[
                const SizedBox(height: 2),
                Text(
                  attendance.notes!,
                  style: TextStyle(
                    fontSize: _getSafeScaledFontSize(baseSize: 0.7),
                    color: Colors.grey[500],
                    fontStyle: FontStyle.italic,
                  ),
                ),
              ],
            ],
          ),
          trailing: Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
            decoration: BoxDecoration(
              color: statusColor,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Text(
              isAttended ? _getSafeText('attended') : _getSafeText('missed'),
              style: TextStyle(
                color: Colors.white,
                fontSize: _getSafeScaledFontSize(baseSize: 0.65),
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ),
      ),
    );
  }

  Future<void> _loadAnnouncements() async {
    try {
      // Check if we're in mock session mode first
      final mockSession = MockSessionService.instance;
      if (mockSession.isTestMode) {
        // Load mock announcements
        final mockAnnouncements = mockSession.announcements;
        setState(() {
          _allAnnouncements = mockAnnouncements;
          _announcementsByDate = _groupAnnouncementsByDate(mockAnnouncements);
          _selectedDayAnnouncements =
              _getFilteredAnnouncementsForDay(_selectedDay ?? _focusedDay);
          _isLoading = false;
        });
        print(
            '📱 Loaded ${mockAnnouncements.length} mock announcements for schedule screen');
        return;
      }

      // Load announcements
      final announcements = await EventService.getAllEventsAsAnnouncements();
      setState(() {
        _allAnnouncements = announcements;
        _announcementsByDate = _groupAnnouncementsByDate(announcements);
        _selectedDayAnnouncements =
            _getFilteredAnnouncementsForDay(_selectedDay ?? _focusedDay);
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error loading events: $e')),
        );
      }
    }
  }

  Map<DateTime, List<Announcement>> _groupAnnouncementsByDate(
      List<Announcement> announcements) {
    final Map<DateTime, List<Announcement>> grouped = {};
    for (final announcement in announcements) {
      final date = _parseAnnouncementDate(announcement.when);
      final dateKey = DateTime(date.year, date.month, date.day);
      if (grouped[dateKey] == null) {
        grouped[dateKey] = [];
      }
      grouped[dateKey]!.add(announcement);
    }
    return grouped;
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

  List<Announcement> _getAnnouncementsForDay(DateTime day) {
    final dateKey = DateTime(day.year, day.month, day.day);
    return _announcementsByDate[dateKey] ?? [];
  }

  List<Announcement> _getFilteredAnnouncementsForDay(DateTime day) {
    final announcements = _getAnnouncementsForDay(day);
    if (_selectedFilter == 'CURRENT DATE') {
      return announcements;
    }
    // For the original categories, we'll just return all announcements
    // since these were more like display labels than actual filters
    return announcements;
  }

  /// Convert attended field (int 1/0 or bool) to attendance status string
  String _convertAttendedToStatus(dynamic attended) {
    if (attended == null) return 'missed';

    // Handle int values (1 = attended, 0 = missed)
    if (attended is int) {
      return attended == 1 ? 'attended' : 'missed';
    }

    // Handle bool values
    if (attended is bool) {
      return attended ? 'attended' : 'missed';
    }

    // Handle string values
    if (attended is String) {
      return attended.toLowerCase() == 'true' || attended == '1'
          ? 'attended'
          : 'missed';
    }

    return 'missed';
  }

  Future<void> _loadUserAttendance() async {
    setState(() {
      _isLoadingAttendance = true;
    });

    try {
      final user = await AuthService.getCurrentUser();
      if (user != null) {
        // Use the new API endpoint to get event attendance data
        final attendanceData =
            await AttendanceService.getUserEventAttendance(user.id);
        final records =
            attendanceData['attendance_records'] as List<dynamic>? ?? [];
        final statistics =
            attendanceData['statistics'] as Map<String, dynamic>? ?? {};

        // Convert the records to Attendance objects
        final attendance = records
            .map<Attendance>((record) => Attendance(
                  id: record['event_id']?.toString() ?? '',
                  userId: user.id,
                  eventId: record['event_id']?.toString() ?? '',
                  eventTitle: record['event_title'] ?? '',
                  eventDate: record['event_date'] ?? '',
                  attendanceStatus:
                      _convertAttendedToStatus(record['attended']),
                  notes: record['attendance_notes'],
                ))
            .toList();

        // Extract statistics
        final stats = <String, int>{
          'attended': (statistics['attended'] as num?)?.toInt() ?? 0,
          'missed': (statistics['missed'] as num?)?.toInt() ?? 0,
          'total': (statistics['total'] as num?)?.toInt() ?? 0,
        };

        setState(() {
          _userAttendance = attendance;
          _attendanceStats = stats;
          _isLoadingAttendance = false;
        });
      } else {
        setState(() {
          _isLoadingAttendance = false;
        });
      }
    } catch (e) {
      setState(() {
        _isLoadingAttendance = false;
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error loading attendance: $e')),
        );
      }
    }
  }

  List<Attendance> _getFilteredAttendance() {
    switch (_attendanceFilter) {
      case 'attended':
        return _userAttendance.where((a) => a.isAttended).toList();
      case 'missed':
        return _userAttendance.where((a) => !a.isAttended).toList();
      default:
        return _userAttendance;
    }
  }

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
          title: Text(_getSafeText('schedule')),
          automaticallyImplyLeading: false,
          backgroundColor: const Color(0xFF2E8B8B),
          foregroundColor: Colors.white,
          bottom: TabBar(
            controller: _tabController,
            indicatorColor: Colors.white,
            labelColor: Colors.white,
            unselectedLabelColor: Colors.white70,
            tabs: [
              Tab(
                icon: const Icon(Icons.event),
                text: _getSafeText('events'),
              ),
              Tab(
                icon: const Icon(Icons.assignment_turned_in),
                text: _getSafeText('attendance'),
              ),
            ],
          ),
        ),
        body: TabBarView(
          controller: _tabController,
          children: [
            _buildEventsTab(),
            _buildAttendanceTab(),
          ],
        ),
      ),
    );
  }

  Widget _buildCalendarSection() {
    return Container(
      margin: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            spreadRadius: 1,
            blurRadius: 5,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: TableCalendar<String>(
        firstDay: DateTime.utc(2020, 1, 1),
        lastDay: DateTime.utc(2030, 12, 31),
        focusedDay: _focusedDay,
        calendarFormat: _calendarFormat,
        availableGestures: AvailableGestures.horizontalSwipe,
        rowHeight: 60,
        daysOfWeekHeight: 50,
        eventLoader: (day) =>
            _getAnnouncementsForDay(day).map((a) => a.what).toList(),
        startingDayOfWeek: StartingDayOfWeek.monday,
        selectedDayPredicate: (day) {
          return isSameDay(_selectedDay, day);
        },
        onDaySelected: (selectedDay, focusedDay) {
          if (!isSameDay(_selectedDay, selectedDay)) {
            setState(() {
              _selectedDay = selectedDay;
              _focusedDay = focusedDay;
              _selectedDayAnnouncements =
                  _getFilteredAnnouncementsForDay(selectedDay);
            });
          }
        },
        onFormatChanged: (format) {
          if (_calendarFormat != format) {
            setState(() {
              _calendarFormat = format;
            });
          }
        },
        onPageChanged: (focusedDay) {
          _focusedDay = focusedDay;
        },
        calendarStyle: CalendarStyle(
          outsideDaysVisible: false,
          cellMargin: const EdgeInsets.all(4),
          cellPadding: const EdgeInsets.all(0),
          defaultTextStyle: TextStyle(
            fontSize: _getSafeScaledFontSize(baseSize: 0.9),
            color: Colors.black87,
            fontWeight: FontWeight.w600,
          ),
          weekendTextStyle: TextStyle(
            fontSize: _getSafeScaledFontSize(baseSize: 0.9),
            color: Colors.grey.shade700,
            fontWeight: FontWeight.w600,
          ),
          holidayTextStyle: TextStyle(
            fontSize: _getSafeScaledFontSize(baseSize: 0.9),
            color: Colors.red.shade600,
            fontWeight: FontWeight.w600,
          ),
          defaultDecoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(8),
            border: Border.all(color: Colors.grey.shade200, width: 1),
          ),
          weekendDecoration: BoxDecoration(
            color: Colors.grey.shade50,
            borderRadius: BorderRadius.circular(8),
            border: Border.all(color: Colors.grey.shade200, width: 1),
          ),
          selectedDecoration: BoxDecoration(
            gradient: const LinearGradient(
              colors: [Color(0xFF4A90E2), Color(0xFF357ABD)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.circular(8),
            boxShadow: [
              BoxShadow(
                color: Colors.blue.withOpacity(0.3),
                spreadRadius: 2,
                blurRadius: 6,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          selectedTextStyle: TextStyle(
            fontSize: _getSafeScaledFontSize(baseSize: 0.95),
            color: Colors.white,
            fontWeight: FontWeight.bold,
          ),
          todayDecoration: BoxDecoration(
            gradient: const LinearGradient(
              colors: [Color(0xFFFF8A65), Color(0xFFFF7043)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.circular(8),
            boxShadow: [
              BoxShadow(
                color: Colors.orange.withOpacity(0.4),
                spreadRadius: 2,
                blurRadius: 6,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          todayTextStyle: TextStyle(
            fontSize: _getSafeScaledFontSize(baseSize: 0.95),
            color: Colors.white,
            fontWeight: FontWeight.bold,
          ),
          markerDecoration: BoxDecoration(
            color: Colors.pink.shade500,
            shape: BoxShape.circle,
          ),
          markersMaxCount: 1,
          markersAlignment: Alignment.bottomCenter,
        ),
        daysOfWeekStyle: DaysOfWeekStyle(
          decoration: BoxDecoration(
            color: Colors.grey.shade100,
            borderRadius: const BorderRadius.only(
              bottomLeft: Radius.circular(0),
              bottomRight: Radius.circular(0),
            ),
          ),
          dowTextFormatter: (date, locale) {
            // Return abbreviated day names to prevent cropping
            const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            return dayNames[date.weekday % 7];
          },
          weekdayStyle: TextStyle(
            fontSize: _getSafeScaledFontSize(baseSize: 0.75),
            color: Colors.grey.shade800,
            fontWeight: FontWeight.w600,
            letterSpacing: 0.2,
            height: 1.0,
          ),
          weekendStyle: TextStyle(
            fontSize: _getSafeScaledFontSize(baseSize: 0.75),
            color: Colors.grey.shade600,
            fontWeight: FontWeight.w600,
            letterSpacing: 0.2,
            height: 1.0,
          ),
        ),
        calendarBuilders: CalendarBuilders(
          markerBuilder: (context, day, events) {
            if (events.isNotEmpty) {
              return _buildCustomMarker(day);
            }
            return null;
          },
        ),
        headerStyle: HeaderStyle(
          formatButtonVisible: false,
          titleCentered: true,
          formatButtonShowsNext: false,
          decoration: BoxDecoration(
            color: Colors.grey.shade50,
            borderRadius: const BorderRadius.only(
              topLeft: Radius.circular(12),
              topRight: Radius.circular(12),
            ),
          ),
          headerPadding:
              const EdgeInsets.symmetric(vertical: 16, horizontal: 20),
          titleTextStyle: TextStyle(
            fontSize: _getSafeScaledFontSize(baseSize: 1.1),
            fontWeight: FontWeight.w700,
            color: Colors.black87,
            letterSpacing: 0.5,
          ),
          leftChevronIcon: Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(8),
              boxShadow: [
                BoxShadow(
                  color: Colors.grey.withOpacity(0.2),
                  spreadRadius: 1,
                  blurRadius: 3,
                  offset: const Offset(0, 1),
                ),
              ],
            ),
            child: Icon(
              Icons.chevron_left,
              size: _getSafeScaledIconSize(baseSize: 20.0),
              color: Colors.black87,
            ),
          ),
          rightChevronIcon: Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(8),
              boxShadow: [
                BoxShadow(
                  color: Colors.grey.withOpacity(0.2),
                  spreadRadius: 1,
                  blurRadius: 3,
                  offset: const Offset(0, 1),
                ),
              ],
            ),
            child: Icon(
              Icons.chevron_right,
              size: _getSafeScaledIconSize(baseSize: 20.0),
              color: Colors.black87,
            ),
          ),
          formatButtonDecoration: BoxDecoration(
            gradient: const LinearGradient(
              colors: [Color(0xFF4A90E2), Color(0xFF357ABD)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.circular(20),
            boxShadow: [
              BoxShadow(
                color: Colors.blue.withOpacity(0.3),
                spreadRadius: 1,
                blurRadius: 4,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          formatButtonTextStyle: const TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.w600,
            fontSize: 12,
          ),
          formatButtonPadding:
              const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        ),
      ),
    );
  }

  Widget _buildEventTypeSection() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      child: SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        child: Row(
          children: [
            _buildEventTypeChip(
                _getSafeText('current_date'), Colors.orange, false),
            const SizedBox(width: 8),
            _buildEventTypeChip('PHYSICAL RELATED EVENT', Colors.blue, false),
            const SizedBox(width: 8),
            _buildEventTypeChip('APPOINTMENT', Colors.green, false),
          ],
        ),
      ),
    );
  }

  Widget _buildEventTypeChip(String label, Color color, bool isSelected) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.transparent,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
          color: color,
          width: 1,
        ),
      ),
      child: Text(
        label,
        style: TextStyle(
          color: color,
          fontSize: _getSafeScaledFontSize(baseSize: 0.7),
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }

  Widget _buildEventsList() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.shade200, width: 1),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.08),
            spreadRadius: 1,
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Section header
          Container(
            padding: const EdgeInsets.only(bottom: 16),
            decoration: BoxDecoration(
              border: Border(
                bottom: BorderSide(color: Colors.grey.shade200, width: 1),
              ),
            ),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: Colors.blue.shade50,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Icon(
                    Icons.event_note,
                    size: 20,
                    color: Colors.blue.shade700,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Text(
                    'Events for ${DateFormat('MMMM d, y').format(_selectedDay ?? _focusedDay)}',
                    style: TextStyle(
                      fontSize: _getSafeScaledFontSize(baseSize: 1.1),
                      fontWeight: FontWeight.w700,
                      color: Colors.grey.shade800,
                      letterSpacing: 0.3,
                    ),
                  ),
                ),
                if (_selectedDayAnnouncements.isNotEmpty)
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.blue.shade100,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      '${_selectedDayAnnouncements.length}',
                      style: TextStyle(
                        fontSize: _getSafeScaledFontSize(baseSize: 0.8),
                        fontWeight: FontWeight.w600,
                        color: Colors.blue.shade700,
                      ),
                    ),
                  ),
              ],
            ),
          ),
          const SizedBox(height: 16),

          // Events content
          _selectedDayAnnouncements.isEmpty
              ? _buildEmptyEventsState()
              : ListView.builder(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: _selectedDayAnnouncements.length,
                  itemBuilder: (context, index) {
                    return _buildAnnouncementCard(
                        _selectedDayAnnouncements[index]);
                  },
                ),
        ],
      ),
    );
  }

  Widget _buildEmptyEventsState() {
    return Container(
      padding: const EdgeInsets.all(32),
      child: Column(
        children: [
          Icon(
            Icons.event_busy,
            size: 48,
            color: Colors.grey.shade400,
          ),
          const SizedBox(height: 16),
          Text(
            'No events scheduled',
            style: TextStyle(
              fontSize: _getSafeScaledFontSize(baseSize: 1.0),
              fontWeight: FontWeight.w600,
              color: Colors.grey.shade600,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'There are no events for this date',
            style: TextStyle(
              fontSize: _getSafeScaledFontSize(baseSize: 0.85),
              color: Colors.grey.shade500,
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  Widget _buildAnnouncementCard(Announcement announcement) {
    final Color categoryColor = _getCategoryColor(announcement.category);
    final Color backgroundColor = categoryColor.withOpacity(0.15);
    final iconData = Announcement.getIconData(announcement.iconType);
    final reminderService = ReminderService.instance;
    final hasReminder = reminderService.hasReminder(announcement.id);
    final range = reminderService.parseEventTimeRange(announcement.when);
    final status = _computeEventStatus(range);
    final style = _statusStyle(status);

    return GestureDetector(
      onTap: () {
        _showAnnouncementDetails(announcement);
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: backgroundColor,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: categoryColor.withOpacity(0.3),
            width: 1,
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.grey.withOpacity(0.08),
              spreadRadius: 0,
              blurRadius: 4,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header row with status and category
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                _buildStatusPill(style['text'] as String, style['bg'] as Color,
                    style['fg'] as Color),
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: categoryColor.withOpacity(0.8),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    announcement.category,
                    style: TextStyle(
                      color: Colors.black87,
                      fontSize: _getSafeScaledFontSize(baseSize: 0.7),
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),

            // Main content row
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Icon container
                Container(
                  width: 48,
                  height: 48,
                  decoration: BoxDecoration(
                    color: categoryColor.withOpacity(0.8),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Stack(
                    children: [
                      Center(
                        child: Icon(
                          iconData,
                          color: Colors.white,
                          size: _getSafeScaledIconSize(),
                        ),
                      ),
                      if (hasReminder)
                        Positioned(
                          right: 2,
                          top: 2,
                          child: Container(
                            width: 16,
                            height: 16,
                            decoration: const BoxDecoration(
                              color: Colors.orange,
                              shape: BoxShape.circle,
                            ),
                            child: const Icon(
                              Icons.notifications,
                              color: Colors.white,
                              size: 10,
                            ),
                          ),
                        ),
                    ],
                  ),
                ),
                const SizedBox(width: 16),

                // Content column
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Event title/what
                      Text(
                        announcement.what,
                        style: TextStyle(
                          fontSize: _getSafeScaledFontSize(baseSize: 1.0),
                          fontWeight: FontWeight.w600,
                          color: Colors.black87,
                          height: 1.3,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 8),

                      // Location
                      Row(
                        children: [
                          Icon(
                            Icons.location_on,
                            size: _getSafeScaledIconSize(baseSize: 16.0),
                            color: Colors.grey[600],
                          ),
                          const SizedBox(width: 4),
                          Expanded(
                            child: Text(
                              announcement.where,
                              style: TextStyle(
                                fontSize:
                                    _getSafeScaledFontSize(baseSize: 0.85),
                                color: Colors.grey[700],
                                fontWeight: FontWeight.w500,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 6),

                      // Time
                      Row(
                        children: [
                          Icon(
                            Icons.access_time,
                            size: _getSafeScaledIconSize(baseSize: 16.0),
                            color: Colors.grey[600],
                          ),
                          const SizedBox(width: 4),
                          Expanded(
                            child: Text(
                              announcement.when,
                              style: TextStyle(
                                fontSize: _getSafeScaledFontSize(baseSize: 0.8),
                                color: Colors.grey[600],
                                fontWeight: FontWeight.w400,
                              ),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),

                      // Reminder indicator
                      if (hasReminder) ...[
                        const SizedBox(height: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: Colors.orange.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(
                              color: Colors.orange.withOpacity(0.3),
                              width: 1,
                            ),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(
                                Icons.notifications_active,
                                size: _getSafeScaledIconSize(baseSize: 14.0),
                                color: Colors.orange[700],
                              ),
                              const SizedBox(width: 4),
                              Text(
                                _getSafeText('reminder_set'),
                                style: TextStyle(
                                  fontSize:
                                      _getSafeScaledFontSize(baseSize: 0.7),
                                  color: Colors.orange[700],
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ],
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatusPill(String text, Color bgColor, Color fgColor) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 3),
      margin: const EdgeInsets.only(left: 6),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.black.withOpacity(0.15)),
      ),
      child: Text(
        text,
        style: TextStyle(
          color: fgColor,
          fontWeight: FontWeight.bold,
          fontSize: _getSafeScaledFontSize(baseSize: 0.55),
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

  void _showAnnouncementDetails(Announcement announcement) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return StatefulBuilder(
          builder: (context, setState) {
            final reminderService = ReminderService.instance;
            final hasReminder = reminderService.hasReminder(announcement.id);
            final reminderInfo = reminderService.getReminder(announcement.id);

            return AlertDialog(
              title: Text(
                announcement.title,
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: _getSafeScaledFontSize(isTitle: true),
                ),
              ),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildDetailRow(
                      _getSafeText('what') + ':',
                      LanguageService.instance
                          .translateFreeText(announcement.what)),
                  const SizedBox(height: 8),
                  _buildDetailRow(
                      _getSafeText('when') + ':', announcement.when),
                  const SizedBox(height: 8),
                  _buildDetailRow(
                      _getSafeText('where') + ':', announcement.where),
                  const SizedBox(height: 8),
                  // Department removed; announcements come from events only
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
                          fontSize: _getSafeScaledFontSize(baseSize: 0.8),
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
                                    _getSafeScaledFontSize(baseSize: 0.7)),
                          ),
                          backgroundColor: Colors.green.withOpacity(0.1),
                          deleteIcon: Icon(Icons.close, size: _getSafeScaledIconSize(baseSize: 16.0)),
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
                      else
                        TextButton.icon(
                          onPressed: () {
                            _showReminderOptions(
                                context, announcement, setState);
                          },
                          icon: const Icon(Icons.add, size: 16),
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
                        style: TextStyle(
                          fontSize: _getSafeScaledFontSize(baseSize: 0.7),
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
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(_getSafeText('set_reminder')),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              ListTile(
                leading: const Icon(Icons.schedule, color: Colors.blue),
                title: Text(_getSafeText('one_hour_before')),
                onTap: () async {
                  Navigator.of(context).pop();
                  await _setReminder(announcement, '1_hour_before', setState);
                },
              ),
              ListTile(
                leading: const Icon(Icons.today, color: Colors.green),
                title: Text(_getSafeText('one_day_before')),
                onTap: () async {
                  Navigator.of(context).pop();
                  await _setReminder(announcement, '1_day_before', setState);
                },
              ),
              ListTile(
                leading: const Icon(Icons.date_range, color: Colors.orange),
                title: Text(_getSafeText('custom_time')),
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
              child: Text(_getSafeText('cancel')),
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

  Future<void> _showCustomReminderPicker(
      Announcement announcement, StateSetter setState) async {
    // Parse the event date to set proper limits
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

  bool _hasReminderForDate(DateTime date) {
    final reminderService = ReminderService.instance;
    final dayAnnouncements = _getAnnouncementsForDay(date);

    for (final announcement in dayAnnouncements) {
      if (reminderService.hasReminder(announcement.id)) {
        return true;
      }
    }
    return false;
  }

  Widget _buildCustomMarker(DateTime day) {
    final dayAnnouncements = _getAnnouncementsForDay(day);
    if (dayAnnouncements.isEmpty) return const SizedBox.shrink();

    // Get the primary category for this day (first event's category)
    final primaryCategory = dayAnnouncements.first.category.toLowerCase();
    final categoryColor = _getCategoryColor(primaryCategory);
    final hasReminder = _hasReminderForDate(day);

    // Create larger rounded rectangular marker that covers more of the date cell
    return Positioned.fill(
      child: Container(
        margin: const EdgeInsets.all(2),
        decoration: BoxDecoration(
          color: categoryColor.withOpacity(0.3),
          borderRadius: BorderRadius.circular(8),
          border: Border.all(
            color: _getCategoryBorderColor(primaryCategory),
            width: 1.5,
          ),
        ),
        child: Stack(
          children: [
            // Orange overlay for reminders
            if (hasReminder)
              Container(
                margin: const EdgeInsets.all(1),
                decoration: BoxDecoration(
                  color: Colors.orange.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(6),
                ),
              ),
            // Bell icon button at top-right corner for reminders
            if (hasReminder)
              Positioned(
                top: 2,
                right: 2,
                child: Container(
                  width: 16,
                  height: 16,
                  decoration: BoxDecoration(
                    color: Colors.orange,
                    borderRadius: BorderRadius.circular(8),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.2),
                        blurRadius: 2,
                        offset: const Offset(0, 1),
                      ),
                    ],
                  ),
                  child: Icon(
                    Icons.notifications,
                    size: _getSafeScaledIconSize(baseSize: 10.0),
                    color: Colors.white,
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }

  Color _getCategoryBorderColor(String category) {
    switch (category.toLowerCase()) {
      case 'health':
        return const Color(0xFFE91E63); // Pink border
      case 'pension':
        return const Color(0xFF2196F3); // Blue border
      case 'general':
        return const Color(0xFF4CAF50); // Green border
      case 'id_claiming':
        return const Color(0xFFFFC107); // Yellow border
      default:
        return Colors.grey[600]!; // Default border
    }
  }

  Widget _buildLegendsSection() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade200, width: 1),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.08),
            spreadRadius: 1,
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Legends header
          Row(
            children: [
              Icon(
                Icons.info_outline,
                size: 18,
                color: Colors.grey.shade700,
              ),
              const SizedBox(width: 8),
              Text(
                'LEGENDS',
                style: TextStyle(
                  fontSize: _getSafeScaledFontSize(baseSize: 0.85),
                  fontWeight: FontWeight.w700,
                  color: Colors.grey.shade800,
                  letterSpacing: 1.2,
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),

          // Event type legends
          Wrap(
            spacing: 16,
            runSpacing: 8,
            children: [
              _buildLegendItem(
                  'SELECTED DATE', Colors.blue.shade600, Icons.circle),
              _buildLegendItem(
                  'CURRENT DATE', Colors.orange.shade600, Icons.circle),
              _buildLegendItem(
                  'GENERAL', const Color(0xFFD1F2CB), Icons.circle),
              _buildLegendItem('HEALTH', const Color(0xFFEA9BAE), Icons.circle),
              _buildLegendItem(
                  'PENSION', const Color(0xFFAEDBF0), Icons.circle),
              _buildLegendItem(
                  'ID CLAIMING', const Color(0xFFE7E09C), Icons.circle),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildLegendItem(String label, Color color, IconData icon) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          width: 16,
          height: 16,
          decoration: BoxDecoration(
            color: color,
            borderRadius: BorderRadius.circular(8),
            border: Border.all(color: Colors.grey.shade300, width: 1),
          ),
          child: Icon(
            icon,
            size: _getSafeScaledIconSize(baseSize: 10.0),
            color: icon == Icons.favorite ? Colors.white : Colors.transparent,
          ),
        ),
        const SizedBox(width: 6),
        Text(
          label,
          style: TextStyle(
            fontSize: _getSafeScaledFontSize(baseSize: 0.75),
            fontWeight: FontWeight.w500,
            color: Colors.grey.shade700,
          ),
        ),
      ],
    );
  }

  Widget _buildReminderIndicator(Color color, IconData icon) {
    return Container(
      width: 20,
      height: 20,
      decoration: BoxDecoration(
        color: color,
        shape: BoxShape.circle,
        border: Border.all(color: Colors.white, width: 2),
        boxShadow: [
          BoxShadow(
            color: color.withOpacity(0.3),
            spreadRadius: 1,
            blurRadius: 2,
            offset: const Offset(0, 1),
          ),
        ],
      ),
      child: Icon(
        icon == Icons.favorite ? Icons.favorite : Icons.notifications,
        size: _getSafeScaledIconSize(baseSize: 10.0),
        color: Colors.white,
      ),
    );
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

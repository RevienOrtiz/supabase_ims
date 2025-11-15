import 'dart:async';
import 'package:supabase_flutter/supabase_flutter.dart';

class SupabaseRealtimeService {
  static RealtimeChannel? _usersChannel;
  static RealtimeChannel? _announcementsChannel;
  static RealtimeChannel? _remindersChannel;
  static RealtimeChannel? _notificationsChannel;

  static final StreamController<UserRealtimeData> _userController =
      StreamController<UserRealtimeData>.broadcast();
  static final StreamController<AnnouncementRealtimeData>
      _announcementController =
      StreamController<AnnouncementRealtimeData>.broadcast();
  static final StreamController<ReminderRealtimeData> _reminderController =
      StreamController<ReminderRealtimeData>.broadcast();
  static final StreamController<NotificationRealtimeData>
      _notificationController =
      StreamController<NotificationRealtimeData>.broadcast();

  static Future<void> initialize() async {
    final client = Supabase.instance.client;

    _usersChannel = client
        .channel('public:users')
        .onPostgresChanges(
          event: PostgresChangeEvent.all,
          schema: 'public',
          table: 'users',
          callback: (payload) {
            final record = payload.newRecord;
            if (record.isNotEmpty) {
              _userController.add(
                UserRealtimeData(Map<String, dynamic>.from(record)),
              );
            }
          },
        )
        .subscribe();

    _announcementsChannel = client
        .channel('public:announcements')
        .onPostgresChanges(
          event: PostgresChangeEvent.all,
          schema: 'public',
          table: 'announcements',
          callback: (payload) {
            final record = payload.newRecord;
            if (record.isNotEmpty) {
              final map = Map<String, dynamic>.from(record);
              if (map.containsKey('when_event') && !map.containsKey('when')) {
                map['when'] = map['when_event'];
              }
              _announcementController.add(
                AnnouncementRealtimeData(map),
              );
            }
          },
        )
        .subscribe();

    _remindersChannel = client
        .channel('public:user_reminders')
        .onPostgresChanges(
          event: PostgresChangeEvent.all,
          schema: 'public',
          table: 'user_reminders',
          callback: (payload) {
            final record = payload.newRecord;
            if (record.isNotEmpty) {
              _reminderController.add(
                ReminderRealtimeData(Map<String, dynamic>.from(record)),
              );
            }
          },
        )
        .subscribe();

    _notificationsChannel = client
        .channel('public:notification_logs')
        .onPostgresChanges(
          event: PostgresChangeEvent.all,
          schema: 'public',
          table: 'notification_logs',
          callback: (payload) {
            final record = payload.newRecord;
            if (record.isNotEmpty) {
              _notificationController.add(
                NotificationRealtimeData(Map<String, dynamic>.from(record)),
              );
            }
          },
        )
        .subscribe();
  }

  static Stream<UserRealtimeData> get userUpdateStream =>
      _userController.stream;
  static Stream<AnnouncementRealtimeData> get announcementStream =>
      _announcementController.stream;
  static Stream<ReminderRealtimeData> get reminderStream =>
      _reminderController.stream;
  static Stream<NotificationRealtimeData> get notificationStream =>
      _notificationController.stream;

  static void dispose() {
    _usersChannel?.unsubscribe();
    _announcementsChannel?.unsubscribe();
    _remindersChannel?.unsubscribe();
    _notificationsChannel?.unsubscribe();

    _userController.close();
    _announcementController.close();
    _reminderController.close();
    _notificationController.close();
  }
}

class UserRealtimeData {
  final Map<String, dynamic> data;
  UserRealtimeData(this.data);
  Map<String, dynamic> toJson() => data;
}

class AnnouncementRealtimeData {
  final Map<String, dynamic> data;
  AnnouncementRealtimeData(this.data);
  Map<String, dynamic> toJson() => data;
}

class ReminderRealtimeData {
  final Map<String, dynamic> data;
  ReminderRealtimeData(this.data);
  Map<String, dynamic> toJson() => data;
}

class NotificationRealtimeData {
  final Map<String, dynamic> data;
  NotificationRealtimeData(this.data);
  Map<String, dynamic> toJson() => data;
}
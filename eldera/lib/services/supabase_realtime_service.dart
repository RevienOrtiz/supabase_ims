import 'dart:async';

/// Minimal stub for SupabaseRealtimeService to satisfy compile-time references.
/// This provides no-op initialization and broadcast streams for update events.
class SupabaseRealtimeService {
  // Internal controllers for broadcast streams
  static final StreamController<dynamic> _userController =
      StreamController<dynamic>.broadcast();
  static final StreamController<dynamic> _announcementController =
      StreamController<dynamic>.broadcast();
  static final StreamController<dynamic> _reminderController =
      StreamController<dynamic>.broadcast();
  static final StreamController<dynamic> _notificationController =
      StreamController<dynamic>.broadcast();

  /// Initializes the realtime service (no-op stub)
  static Future<void> initialize() async {
    // Intentionally left blank for stub
  }

  /// Streams exposing realtime updates (no events emitted in stub)
  static Stream<dynamic> get userUpdateStream => _userController.stream;
  static Stream<dynamic> get announcementStream =>
      _announcementController.stream;
  static Stream<dynamic> get reminderStream => _reminderController.stream;
  static Stream<dynamic> get notificationStream =>
      _notificationController.stream;

  /// Dispose all controllers (optional utility)
  static void dispose() {
    _userController.close();
    _announcementController.close();
    _reminderController.close();
    _notificationController.close();
  }
}

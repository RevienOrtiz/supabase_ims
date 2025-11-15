import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';
import '../services/auth_service.dart';
import '../services/font_size_service.dart';
import '../services/password_validation_service.dart';
import '../services/optimized_image_service.dart';
import '../utils/memory_optimizer.dart';
import '../widgets/memory_efficient_widgets.dart';
import '../config/app_colors.dart';
import 'main_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({Key? key}) : super(key: key);

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final TextEditingController _oscaIdController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  // Using SupabaseAuthService instead of AuthService
  late final FontSizeService _fontSizeService;
  bool _isLoading = false;
  bool _obscurePassword = true;

  @override
  void initState() {
    super.initState();
    _initializeServices();
  }

  Future<void> _initializeServices() async {
    _fontSizeService = FontSizeService.instance;
    await _fontSizeService.init();
    _checkLoginStatus();
  }

  double _getSafeScaledFontSize({
    double? baseSize,
    bool isTitle = false,
    bool isSubtitle = false,
    bool isCaption = false,
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
      } else if (isCaption) {
        scaleFactor = 0.9;
      }

      return defaultSize * scaleFactor;
    }

    return _fontSizeService.getScaledFontSize(
      baseSize: baseSize ?? 1.0,
      isTitle: isTitle,
      isSubtitle: isSubtitle,
      isCaption: isCaption,
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

  Future<void> _checkLoginStatus() async {
    try {
      if (AuthService.isAuthenticated()) {
        // User is already logged in, navigate to main screen
        if (mounted) {
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(builder: (context) => const MainScreen()),
          );
        }
      }
    } catch (e) {
      // Handle initialization error
      print('Error checking auth status: $e');
    }
  }

  String? _validateOscaId(String oscaId) {
    if (oscaId.isEmpty) {
      return 'OSCA ID is required';
    }

    // Removed format constraint since validation happens at database level
    // and app_users table may have different formats

    return null;
  }

  String? _validatePassword(String password) {
    // Simplified validation for login compatibility with backend
    if (password.isEmpty) {
      return 'Password is required';
    }
    if (password.length < 6) {
      return 'Password must be at least 6 characters';
    }
    return null;
  }

  String _sanitizeInput(String input) {
    return input
        .trim()
        .replaceAll(RegExp(r'[<>"/\\]'), '')
        .replaceAll(RegExp(r"[']"), '')
        .replaceAll(RegExp(r'\s+'), ' ');
  }

  Future<void> _login() async {
    final oscaId = _oscaIdController.text.trim();
    final password = _passwordController.text;

    // Check if account is locked
    if (PasswordValidationService.isAccountLocked(oscaId)) {
      final remainingTime =
          PasswordValidationService.getRemainingLockoutTime(oscaId);
      if (remainingTime != null) {
        _showMessage(
          'Account is locked. Please try again in ${remainingTime.inMinutes} minutes.',
          isError: true,
        );
        return;
      }
    }

    // Validate inputs
    final oscaIdError = _validateOscaId(oscaId);
    if (oscaIdError != null) {
      _showMessage(oscaIdError, isError: true);
      return;
    }

    final passwordError = _validatePassword(password);
    if (passwordError != null) {
      _showMessage(passwordError, isError: true);
      return;
    }

    setState(() {
      _isLoading = true;
    });

    try {
      // CRITICAL FIX: Ensure OSCA ID is in the exact format expected by the database
      // Don't sanitize or modify it at all - send exactly what user entered
      final oscaIdForAuth = oscaId;

      // Log the OSCA ID for debugging
      print('Using OSCA ID for auth: $oscaIdForAuth');

      final result = await AuthService.signIn(
        oscaId: oscaIdForAuth,
        password:
            password, // Don't sanitize password as it might affect authentication
      );

      // Debug print to verify the result
      print('Login result: $result');

      if (result['success'] == true) {
        // Clear failed attempts on successful login
        PasswordValidationService.clearFailedAttempts(oscaId);

        _showMessage('Login successful', isError: false);

        // Add a small delay to ensure the message is shown
        await Future.delayed(Duration(milliseconds: 500));

        // Navigate to main screen
        if (mounted) {
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(builder: (context) => const MainScreen()),
          );
        }
      } else {
        // Record failed attempt and check for lockout
        final isLocked = PasswordValidationService.recordFailedAttempt(oscaId);

        if (isLocked) {
          _showMessage(
            'Too many failed attempts. Account locked for security.',
            isError: true,
          );
        } else {
          _showMessage(result['message'] ?? 'Invalid OSCA ID or password',
              isError: true);
        }
      }
    } catch (e) {
      // Record failed attempt for any authentication error
      final isLocked = PasswordValidationService.recordFailedAttempt(oscaId);

      // Sanitize error messages to prevent information disclosure
      String errorMessage = 'Login failed. Please try again.';
      if (isLocked) {
        errorMessage = 'Too many failed attempts. Account locked for security.';
      } else if (e.toString().contains('Authentication')) {
        errorMessage =
            'Invalid credentials. Please check your OSCA ID and password.';
      } else if (e.toString().contains('network') ||
          e.toString().contains('timeout')) {
        errorMessage =
            'Network error. Please check your connection and try again.';
      }
      _showMessage(errorMessage, isError: true);
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  void _showMessage(String message, {required bool isError}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: isError ? Colors.red : Colors.green,
        duration: const Duration(seconds: 3),
      ),
    );
  }

  void _showForgotPasswordDialog() {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        final TextEditingController oscaIdController = TextEditingController();
        bool isLoading = false;

        return StatefulBuilder(
          builder: (context, setState) {
            return AlertDialog(
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
              title: Text(
                'Forgot Password',
                style: TextStyle(
                  fontSize: _getSafeScaledFontSize(isSubtitle: true),
                  fontWeight: FontWeight.bold,
                  color: const Color(0xFF2D5A5A),
                ),
              ),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Enter your OSCA ID to reset your password. You will need to contact your local government office to complete the password reset process.',
                    style: TextStyle(
                      fontSize: _getSafeScaledFontSize(baseSize: 0.9),
                      color: Colors.grey[600],
                    ),
                  ),
                  const SizedBox(height: 20),
                  TextField(
                    controller: oscaIdController,
                    keyboardType: TextInputType.text,
                    decoration: InputDecoration(
                      labelText: 'OSCA ID',
                      hintText: 'Enter your OSCA ID',
                      prefixIcon: const Icon(Icons.badge),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: const BorderSide(
                          color: Color(0xFF2D5A5A),
                          width: 2,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
              actions: [
                TextButton(
                  onPressed: isLoading ? null : () {
                    Navigator.of(context).pop();
                  },
                  child: Text(
                    'Cancel',
                    style: TextStyle(
                      color: Colors.grey[600],
                      fontSize: _getSafeScaledFontSize(baseSize: 0.9),
                    ),
                  ),
                ),
                ElevatedButton(
                  onPressed: isLoading ? null : () async {
                    final oscaId = oscaIdController.text.trim();
                    
                    if (oscaId.isEmpty) {
                      _showMessage('Please enter your OSCA ID', isError: true);
                      return;
                    }

                    setState(() {
                      isLoading = true;
                    });

                    try {
                      final result = await AuthService.requestPasswordReset(
                        oscaId: oscaId,
                      );

                      if (result['success']) {
                        Navigator.of(context).pop();
                        _showMessage(
                          result['message'],
                          isError: false,
                        );
                      } else {
                        _showMessage(
                          result['message'],
                          isError: true,
                        );
                      }
                    } catch (e) {
                      _showMessage(
                        'An error occurred. Please try again.',
                        isError: true,
                      );
                    } finally {
                      setState(() {
                        isLoading = false;
                      });
                    }
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF2D5A5A),
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  child: isLoading
                      ? const SizedBox(
                          height: 16,
                          width: 16,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                          ),
                        )
                      : Text(
                          'Submit Request',
                          style: TextStyle(
                            fontSize: _getSafeScaledFontSize(baseSize: 0.9),
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                ),
              ],
            );
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF006662),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              const SizedBox(height: 30),
              // App Logo/Title
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Column(
                  children: [
                    Container(
                      width: 160,
                      height: 160,
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(40),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.1),
                            blurRadius: 20,
                            offset: const Offset(0, 6),
                          ),
                        ],
                      ),
                      child: Padding(
                        padding: const EdgeInsets.all(24.0),
                        child: FutureBuilder<Widget>(
                          future: OptimizedImageService.loadLogo(
                            size: 112,
                            isLowMemoryMode: MemoryOptimizer.isBudgetDevice(),
                          ),
                          builder: (context, snapshot) {
                            if (snapshot.connectionState == ConnectionState.waiting) {
                              return const SizedBox(
                                width: 112,
                                height: 112,
                                child: Center(
                                  child: CircularProgressIndicator(),
                                ),
                              );
                            } else if (snapshot.hasError) {
                              return Icon(
                                Icons.error,
                                size: _getSafeScaledIconSize(baseSize: 112.0),
                                color: Colors.grey,
                              );
                            } else {
                              return snapshot.data ?? Icon(
                                Icons.image,
                                size: _getSafeScaledIconSize(baseSize: 112.0),
                                color: Colors.grey,
                              );
                            }
                          },
                        ),
                      ),
                    ),
                    const SizedBox(height: 20),
                    Text(
                      'ELDERA',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: _getSafeScaledFontSize(
                            isTitle: true, baseSize: 2.0),
                        fontWeight: FontWeight.bold,
                        letterSpacing: 2,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 60),
              // Login Form
              Container(
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.1),
                      blurRadius: 10,
                      offset: const Offset(0, 5),
                    ),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Text(
                      'Login to Your Account',
                      style: TextStyle(
                        fontSize: _getSafeScaledFontSize(isSubtitle: true),
                        fontWeight: FontWeight.bold,
                        color: AppColors.textOnWhite,
                      ),
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: 32),
                    // OSCA ID Field
                    TextField(
                      controller: _oscaIdController,
                      keyboardType: TextInputType.text,
                      autocorrect: false,
                      enableSuggestions: false,
                      autofillHints: const [AutofillHints.username],
                      decoration: InputDecoration(
                        labelText: 'Enter your OSCA ID no.',
                        hintText: 'Enter your OSCA ID no.',
                        prefixIcon: const Icon(Icons.badge),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: const BorderSide(
                              color: Color(0xFF2D5A5A), width: 2),
                        ),
                      ),
                    ),
                    const SizedBox(height: 20),
                    // Password Field
                    TextField(
                      controller: _passwordController,
                      obscureText: _obscurePassword,
                      autocorrect: false,
                      enableSuggestions: false,
                      autofillHints: const [AutofillHints.password],
                      decoration: InputDecoration(
                        labelText: 'Password',
                        hintText: 'Enter your password',
                        prefixIcon: const Icon(Icons.lock),
                        suffixIcon: IconButton(
                          icon: Icon(
                            _obscurePassword
                                ? Icons.visibility
                                : Icons.visibility_off,
                          ),
                          onPressed: () {
                            setState(() {
                              _obscurePassword = !_obscurePassword;
                            });
                          },
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: const BorderSide(
                              color: Color(0xFF2D5A5A), width: 2),
                        ),
                      ),
                    ),
                    const SizedBox(height: 32),
                    // Login Button
                    ElevatedButton(
                      onPressed: _isLoading ? null : _login,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF2D5A5A),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        elevation: 2,
                      ),
                      child: _isLoading
                          ? const SizedBox(
                              height: 20,
                              width: 20,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                valueColor:
                                    AlwaysStoppedAnimation<Color>(Colors.white),
                              ),
                            )
                          : Text(
                              'LOGIN',
                              style: TextStyle(
                                fontSize:
                                    _getSafeScaledFontSize(isSubtitle: true),
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                    ),
                    const SizedBox(height: 16),
                    // Forgot Password Link
                    TextButton(
                      onPressed: _showForgotPasswordDialog,
                      child: Text(
                        'Forgot Password?',
                        style: TextStyle(
                          color: const Color(0xFF2D5A5A),
                          fontSize: _getSafeScaledFontSize(baseSize: 0.9),
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 40),
              // Admin Note
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Column(
                  children: [
                    Icon(
                      Icons.info_outline,
                      color: Colors.white70,
                      size: _getSafeScaledIconSize(),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Accounts are pre-created by administrators.\nContact your local government office if you need assistance.',
                      style: TextStyle(
                        color: Colors.white70,
                        fontSize: _getSafeScaledFontSize(baseSize: 0.9),
                      ),
                      textAlign: TextAlign.center,
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  @override
  void dispose() {
    _oscaIdController.dispose();
    _passwordController.dispose();
    super.dispose();
  }
}

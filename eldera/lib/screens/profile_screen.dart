import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:typed_data';
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:intl/intl.dart';
import '../services/font_size_service.dart';
import '../services/user_service.dart';
import '../services/auth_service.dart';
import '../services/language_service.dart';
import '../models/user.dart' as app_user;
import 'admin_simulation_screen.dart';
import 'login_screen.dart';
import '../config/app_colors.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final FontSizeService _fontSizeService = FontSizeService.instance;
  final LanguageService _languageService = LanguageService.instance;
  final ImagePicker _picker = ImagePicker();
  // Using UserService and AuthService
  Uint8List? _selectedImage; // Use Uint8List for both web and mobile
  app_user.User? _currentUser;

  Future<void> _saveProfileImage(Uint8List imageBytes, String fileName) async {
    try {
      if (_currentUser?.id == null) {
        throw Exception('User not authenticated');
      }

      // Upload via UserService
      final result = await UserService.updateProfileImage(
        userId: _currentUser!.id,
        imageBytes: imageBytes,
        fileName: fileName,
      );

      if (result['success']) {
        // Update current user with new image URL
        if (_currentUser != null) {
          _currentUser = _currentUser!.copyWith(
            profileImageUrl: result['imageUrl'],
          );
        }

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result['message']),
            backgroundColor: Colors.green,
          ),
        );
      } else {
        throw Exception(result['message']);
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error saving profile image: $e'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _loadProfileImage() async {
    try {
      if (_currentUser?.profileImageUrl != null) {
        // Download image from storage
        final result = await UserService.downloadProfileImage(
          userId: _currentUser!.id,
          imageUrl: _currentUser!.profileImageUrl!,
        );

        if (result['success']) {
          setState(() {
            _selectedImage = result['imageData'];
          });
        }
      }
    } catch (e) {
      print('Error loading profile image: $e');
    }
  }

  Future<void> _pickImage() async {
    try {
      final XFile? image = await _picker.pickImage(
        source: ImageSource.gallery,
        maxWidth: 800,
        maxHeight: 800,
        imageQuality: 85,
      );

      if (image != null) {
        // Read as bytes for both web and mobile
        final bytes = await image.readAsBytes();
        await _saveProfileImage(bytes, image.name);
        setState(() {
          _selectedImage = bytes;
        });
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error picking image: $e'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  void _showImageSourceDialog() {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(_getSafeText('select_image_source')),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              ListTile(
                leading: const Icon(Icons.photo_library),
                title: Text(_getSafeText('gallery')),
                onTap: () {
                  Navigator.of(context).pop();
                  _pickImage();
                },
              ),
              ListTile(
                leading: const Icon(Icons.camera_alt),
                title: Text(_getSafeText('camera')),
                onTap: () {
                  Navigator.of(context).pop();
                  _pickImageFromCamera();
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

  Future<void> _pickImageFromCamera() async {
    try {
      final XFile? image = await _picker.pickImage(
        source: ImageSource.camera,
        maxWidth: 800,
        maxHeight: 800,
        imageQuality: 85,
      );

      if (image != null) {
        // Read as bytes for both web and mobile
        final bytes = await image.readAsBytes();
        await _saveProfileImage(bytes, image.name);
        setState(() {
          _selectedImage = bytes;
        });
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Error taking photo: $e'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  void initState() {
    super.initState();
    _initializeUserData();
  }

  Future<void> _initializeUserData() async {
    try {
      await _fontSizeService.init();
      await _languageService.init();
      
      // Get current user via AuthService (uses IMS token and /senior/profile)
      print('ProfileScreen: Attempting to fetch user data...');
      _currentUser = await AuthService.getCurrentUser();
      if (_currentUser == null ||
          _currentUser!.age == 0 ||
          (_currentUser!.address == null || _currentUser!.address!.isEmpty)) {
        final fallbackUser = await UserService.getCurrentUser();
        if (fallbackUser != null) {
          _currentUser = fallbackUser;
        }
      }
      
      if (_currentUser != null) {
        print('ProfileScreen: User data loaded successfully: ${_currentUser!.name}');
      } else {
        print('ProfileScreen: No user data returned from AuthService');
        // Show error message to user
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Unable to load profile data. Please try logging in again.'),
              backgroundColor: Colors.red,
              action: SnackBarAction(
                label: 'Retry',
                textColor: Colors.white,
                onPressed: () => _initializeUserData(),
              ),
            ),
          );
        }
      }
      
      setState(() {});
      await _loadProfileImage();
    } catch (e) {
      print('ProfileScreen: Error initializing user data: $e');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error loading profile: ${e.toString()}'),
            backgroundColor: Colors.red,
            action: SnackBarAction(
              label: 'Retry',
              textColor: Colors.white,
              onPressed: () => _initializeUserData(),
            ),
          ),
        );
      }
    }
  }

  String _getSafeText(String key) {
    try {
      return _languageService.getText(key);
    } catch (e) {
      return key.toUpperCase();
    }
  }

  String _formatBirthDate(String? birthDate) {
    if (birthDate == null || birthDate.isEmpty) return 'Not specified';

    try {
      // Try to parse the date string
      DateTime date = DateTime.parse(birthDate);
      // Format as "Month DD, YYYY" (e.g., "January 15, 1950")
      return DateFormat('MMMM dd, yyyy').format(date);
    } catch (e) {
      // If parsing fails, return the original string
      return birthDate;
    }
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF006662),
      appBar: AppBar(
        backgroundColor: const Color(0xFF006662),
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          _getSafeText('back'),
          style: TextStyle(
            color: Colors.white,
            fontSize: _getSafeScaledFontSize(isSubtitle: true),
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            // Profile Header Section
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  // Profile Avatar
                  GestureDetector(
                    onTap: _showImageSourceDialog,
                    child: Container(
                      width: 120,
                      height: 120,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border: Border.all(color: Colors.white, width: 4),
                      ),
                      child: ClipOval(
                        child: _selectedImage != null
                            ? Image.memory(
                                _selectedImage!,
                                fit: BoxFit.cover,
                                width: 120,
                                height: 120,
                              )
                            : Container(
                                color: const Color(0xFF2D5A5A),
                                child: Stack(
                                  alignment: Alignment.center,
                                  children: [
                                    // Avatar illustration
                                    Container(
                                      width: 80,
                                      height: 80,
                                      decoration: const BoxDecoration(
                                        shape: BoxShape.circle,
                                        color: Color(0xFFE8B4A0), // Skin tone
                                      ),
                                    ),
                                    // Hair
                                    Positioned(
                                      top: 15,
                                      child: Container(
                                        width: 70,
                                        height: 40,
                                        decoration: const BoxDecoration(
                                          color: Color(0xFFD3D3D3), // Gray hair
                                          borderRadius: BorderRadius.only(
                                            topLeft: Radius.circular(35),
                                            topRight: Radius.circular(35),
                                          ),
                                        ),
                                      ),
                                    ),
                                    // Mustache
                                    Positioned(
                                      bottom: 25,
                                      child: Container(
                                        width: 30,
                                        height: 8,
                                        decoration: BoxDecoration(
                                          color: Colors.white,
                                          borderRadius:
                                              BorderRadius.circular(4),
                                        ),
                                      ),
                                    ),
                                    // Green shirt
                                    Positioned(
                                      bottom: 0,
                                      child: Container(
                                        width: 80,
                                        height: 30,
                                        decoration: const BoxDecoration(
                                          color: Color(0xFF4CAF50),
                                          borderRadius: BorderRadius.only(
                                            bottomLeft: Radius.circular(40),
                                            bottomRight: Radius.circular(40),
                                          ),
                                        ),
                                      ),
                                    ),
                                    // Camera icon overlay
                                    Positioned(
                                      bottom: 5,
                                      right: 5,
                                      child: Container(
                                        width: 24,
                                        height: 24,
                                        decoration: const BoxDecoration(
                                          color: Color(0xFF4CAF50),
                                          shape: BoxShape.circle,
                                        ),
                                        child: Icon(
                                          Icons.camera_alt,
                                          color: Colors.white,
                                          size: _getSafeScaledIconSize(baseSize: 16.0),
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  // User Name
                  Text(
                    _currentUser?.name ?? 'Loading...',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: _getSafeScaledFontSize(isTitle: true),
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 8),
                  // Age
                  Text(
                    (_currentUser != null && _currentUser!.age > 0)
                        ? '${_currentUser!.age} ${_getSafeText('years_old')}'
                        : 'Not specified',
                    style: TextStyle(
                      color: AppColors.textSecondaryOnPrimary,
                      fontSize: _getSafeScaledFontSize(),
                    ),
                  ),
                  const SizedBox(height: 4),
                  // Birth Date
                  Text(
                    _formatBirthDate(_currentUser?.birthDate),
                    style: TextStyle(
                      color: AppColors.textSecondaryOnPrimary,
                      fontSize: _getSafeScaledFontSize(),
                    ),
                  ),
                  const SizedBox(height: 4),
                  // Address
                  Text(
                    _currentUser?.address ?? _getSafeText('loading'),
                    style: TextStyle(
                      color: AppColors.textSecondaryOnPrimary,
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
    );
  }
}
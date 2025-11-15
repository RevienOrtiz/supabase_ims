import 'dart:io';
import 'dart:typed_data';
import 'dart:ui' as ui;
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_svg/flutter_svg.dart';
import '../utils/memory_optimizer.dart';
import '../utils/secure_logger.dart';

/// Optimized image service for low-end devices
/// Provides memory-efficient image loading and caching
class OptimizedImageService {
  static final OptimizedImageService _instance = OptimizedImageService._internal();
  factory OptimizedImageService() => _instance;
  OptimizedImageService._internal();

  // Cache for resized images
  static final Map<String, ui.Image> _resizedImageCache = {};
  static const int _maxCacheSize = 10; // Limit cache size for low-end devices

  /// Load and optimize image for low-end devices
  static Future<Widget> loadOptimizedImage({
    required String assetPath,
    double? width,
    double? height,
    BoxFit fit = BoxFit.contain,
    bool enableMemoryOptimization = true,
    bool preferHighQuality = false,
  }) async {
    try {
      // For low-end devices, use smaller image dimensions
      if (MemoryOptimizer.isBudgetDevice() && enableMemoryOptimization) {
        width = width != null ? width * 0.7 : null;
        height = height != null ? height * 0.7 : null;
      }

      return Image.asset(
        assetPath,
        width: width,
        height: height,
        fit: fit,
        cacheWidth: width?.toInt(),
        cacheHeight: height?.toInt(),
        // Use high filter quality for critical assets when requested
        filterQuality: preferHighQuality
            ? FilterQuality.high
            : (MemoryOptimizer.isBudgetDevice() ? FilterQuality.low : FilterQuality.medium),
        errorBuilder: (context, error, stackTrace) {
          SecureLogger.error('Failed to load image: $assetPath - $error');
          return _buildErrorPlaceholder(width, height);
        },
      );
    } catch (e) {
      SecureLogger.error('Error loading optimized image: $e');
      return _buildErrorPlaceholder(width, height);
    }
  }

  /// Create a memory-efficient placeholder widget
  static Widget _buildErrorPlaceholder(double? width, double? height) {
    return Container(
      width: width ?? 100,
      height: height ?? 100,
      decoration: BoxDecoration(
        color: Colors.grey[300],
        borderRadius: BorderRadius.circular(8),
      ),
      child: const Icon(
        Icons.image_not_supported,
        color: Colors.grey,
        size: 24,
      ),
    );
  }

  /// Load logo with optimizations for different screen sizes
  static Future<Widget> loadLogo({
    double? size,
    bool isLowMemoryMode = false,
  }) async {
    // Prefer crisp rendering for the app logo across all devices
    final logoSize = size ?? 120;

    // Try to load SVG logo for vector-quality rendering if available
    try {
      // Using SvgPicture provides HD scaling at any size
      final Widget svgLogo = SvgPicture.asset(
        'assets/images/ELDERA-sss.svg',
        width: logoSize,
        height: logoSize,
        fit: BoxFit.contain,
      );
      return svgLogo;
    } catch (e) {
      SecureLogger.error('Failed to load SVG logo, falling back to PNG: $e');
      // Fallback to PNG with high-quality filtering and without memory downsizing
      return await loadOptimizedImage(
        assetPath: 'assets/images/eldera_logo.png',
        width: logoSize,
        height: logoSize,
        fit: BoxFit.contain,
        enableMemoryOptimization: false,
        preferHighQuality: true,
      );
    }
  }

  /// Preload critical images for better performance
  static Future<void> preloadCriticalImages(BuildContext context) async {
    if (MemoryOptimizer.isBudgetDevice()) {
      // Skip preloading on budget devices to save memory
      return;
    }

    try {
      await precacheImage(
        const AssetImage('assets/images/eldera_logo.png'),
        context,
      );
      SecureLogger.info('Critical images preloaded successfully');
    } catch (e) {
      SecureLogger.error('Failed to preload critical images: $e');
    }
  }

  /// Clear image cache to free memory
  static void clearImageCache() {
    try {
      PaintingBinding.instance.imageCache.clear();
      _resizedImageCache.clear();
      SecureLogger.info('Image cache cleared successfully');
    } catch (e) {
      SecureLogger.error('Failed to clear image cache: $e');
    }
  }

  /// Get memory-efficient image widget with lazy loading
  static Widget buildLazyImage({
    required String assetPath,
    double? width,
    double? height,
    BoxFit fit = BoxFit.cover,
    Widget? placeholder,
  }) {
    return FutureBuilder<Widget>(
      future: loadOptimizedImage(
        assetPath: assetPath,
        width: width,
        height: height,
        fit: fit,
      ),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return placeholder ?? _buildLoadingPlaceholder(width, height);
        } else if (snapshot.hasError) {
          return _buildErrorPlaceholder(width, height);
        } else {
          return snapshot.data ?? _buildErrorPlaceholder(width, height);
        }
      },
    );
  }

  /// Create a lightweight loading placeholder
  static Widget _buildLoadingPlaceholder(double? width, double? height) {
    return Container(
      width: width ?? 100,
      height: height ?? 100,
      decoration: BoxDecoration(
        color: Colors.grey[200],
        borderRadius: BorderRadius.circular(8),
      ),
      child: const Center(
        child: SizedBox(
          width: 20,
          height: 20,
          child: CircularProgressIndicator(
            strokeWidth: 2,
            valueColor: AlwaysStoppedAnimation<Color>(Colors.grey),
          ),
        ),
      ),
    );
  }

  /// Optimize image cache settings for current device
  static void optimizeImageCacheForDevice() {
    final imageCache = PaintingBinding.instance.imageCache;
    
    if (MemoryOptimizer.isBudgetDevice()) {
      // Very conservative settings for budget devices
      imageCache.maximumSize = 25;
      imageCache.maximumSizeBytes = 16 << 20; // 16MB
    } else {
      // Standard settings for regular devices
      imageCache.maximumSize = 100;
      imageCache.maximumSizeBytes = 64 << 20; // 64MB
    }
    
    SecureLogger.info('Image cache optimized for device type');
  }

  /// Monitor image cache usage
  static Map<String, dynamic> getImageCacheStats() {
    final imageCache = PaintingBinding.instance.imageCache;
    return {
      'currentSize': imageCache.currentSize,
      'maximumSize': imageCache.maximumSize,
      'currentSizeBytes': imageCache.currentSizeBytes,
      'maximumSizeBytes': imageCache.maximumSizeBytes,
      'memoryPressure': imageCache.currentSize > imageCache.maximumSize * 0.8,
      'resizedCacheSize': _resizedImageCache.length,
    };
  }

  /// Reduce image quality for memory optimization
  static void reduceImageQuality() {
    // Clear high-quality cached images
    _resizedImageCache.clear();
    
    // Reduce image cache size further
    final imageCache = PaintingBinding.instance.imageCache;
    imageCache.maximumSize = (imageCache.maximumSize * 0.5).round();
    imageCache.maximumSizeBytes = (imageCache.maximumSizeBytes * 0.5).round();
    
    SecureLogger.info('Image quality reduced for memory optimization');
  }
}
<?php
/**
 * Image Optimization Configuration
 * 
 * Centralized settings for image upload and optimization
 */

return [
    // Upload settings
    'upload' => [
        'max_file_size' => 10485760, // 10MB in bytes
        'allowed_formats' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'upload_path' => '../public_html/img/',
    ],
    
    // Optimization settings
    'optimization' => [
        'max_width' => 1600, // Maximum width for main images
        'jpeg_quality' => 85, // JPEG quality (1-100, recommended: 80-90)
        'webp_quality' => 85, // WebP quality (1-100, recommended: 80-90)
        'png_compression' => 9, // PNG compression (0-9, 9 is highest)
        'create_webp' => true, // Generate WebP versions
        'create_responsive' => true, // Generate responsive image variants
    ],
    
    // Responsive image widths
    'responsive_widths' => [400, 800, 1200],
    
    // Lazy loading settings
    'lazy_load' => [
        'enabled' => true,
        'threshold' => '200px', // Load images when they're 200px from viewport
    ],
    
    // Preload settings
    'preload' => [
        'enabled' => true,
        'critical_paths' => ['home', 'about', 'contact'], // Pages where profile photo is critical
        'critical_images' => ['img/profielfoto.JPG'], // Images to preload on critical pages
    ],
];

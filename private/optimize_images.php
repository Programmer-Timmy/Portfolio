<?php
/**
 * Batch Image Optimization Script
 * 
 * This script will:
 * 1. Resize large images to reasonable dimensions
 * 2. Create WebP versions for better compression
 * 3. Generate responsive image variants
 * 
 * Usage: php optimize_images.php [directory]
 */

require_once __DIR__ . '/autoload.php';

// Configuration
$maxWidth = 1600; // Maximum width for original images
$quality = 85; // JPEG/WebP quality (1-100)
$responsiveWidths = [400, 800, 1200, 1600]; // Widths for responsive images
$targetDir = $argv[1] ?? '../public/img';

echo "Image Optimization Script\n";
echo "=========================\n\n";
echo "Target directory: $targetDir\n";
echo "Max width: {$maxWidth}px\n";
echo "Quality: {$quality}%\n";
echo "Responsive widths: " . implode(', ', $responsiveWidths) . "\n\n";

// Find all images
$images = glob($targetDir . '/*.{jpg,jpeg,png,JPG,JPEG,PNG}', GLOB_BRACE);

if (empty($images)) {
    echo "No images found in $targetDir\n";
    exit(1);
}

echo "Found " . count($images) . " images\n\n";

$stats = [
    'total' => count($images),
    'resized' => 0,
    'webp_created' => 0,
    'responsive_created' => 0,
    'errors' => 0,
    'original_size' => 0,
    'final_size' => 0
];

foreach ($images as $imagePath) {
    $filename = basename($imagePath);
    echo "Processing: $filename\n";
    
    $originalSize = filesize($imagePath);
    $stats['original_size'] += $originalSize;
    echo "  Original size: " . formatBytes($originalSize) . "\n";
    
    try {
        // Get image dimensions
        $dimensions = getimagesize($imagePath);
        if (!$dimensions) {
            echo "  ⚠ Could not read image dimensions\n\n";
            $stats['errors']++;
            continue;
        }
        
        list($width, $height) = $dimensions;
        echo "  Dimensions: {$width}x{$height}\n";
        
        // Resize if too large
        if ($width > $maxWidth) {
            $pathInfo = pathinfo($imagePath);
            $tempPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_optimized.' . $pathInfo['extension'];
            
            if (ImageOptimizer::resizeImage($imagePath, $tempPath, $maxWidth, $quality)) {
                $newSize = filesize($tempPath);
                $savings = $originalSize - $newSize;
                $percent = round(($savings / $originalSize) * 100, 1);
                
                // Replace original with optimized
                rename($tempPath, $imagePath);
                
                echo "  ✓ Resized: " . formatBytes($newSize) . " (saved " . formatBytes($savings) . ", {$percent}%)\n";
                $stats['resized']++;
            } else {
                echo "  ⚠ Failed to resize\n";
                $stats['errors']++;
            }
        } else {
            echo "  ✓ Size OK, no resize needed\n";
        }
        
        // Create WebP version
        $webpPath = ImageOptimizer::convertToWebP($imagePath, $quality);
        if ($webpPath && file_exists($webpPath)) {
            $webpSize = filesize($webpPath);
            $currentSize = filesize($imagePath);
            $savings = $currentSize - $webpSize;
            $percent = round(($savings / $currentSize) * 100, 1);
            
            echo "  ✓ WebP created: " . formatBytes($webpSize) . " (saved {$percent}%)\n";
            $stats['webp_created']++;
            $stats['final_size'] += $webpSize;
        } else {
            echo "  ⚠ Failed to create WebP\n";
            $stats['final_size'] += filesize($imagePath);
        }
        
        // Create responsive variants
        $currentDimensions = getimagesize($imagePath);
        list($currentWidth, $currentHeight) = $currentDimensions;
        
        $variantsCreated = 0;
        foreach ($responsiveWidths as $targetWidth) {
            if ($targetWidth >= $currentWidth) {
                continue; // Don't upscale
            }
            
            $pathInfo = pathinfo($imagePath);
            $variantPath = sprintf(
                '%s/%s-%dw.%s',
                $pathInfo['dirname'],
                $pathInfo['filename'],
                $targetWidth,
                $pathInfo['extension']
            );
            
            if (!file_exists($variantPath)) {
                if (ImageOptimizer::resizeImage($imagePath, $variantPath, $targetWidth, $quality)) {
                    $variantsCreated++;
                    
                    // Also create WebP version of variant
                    ImageOptimizer::convertToWebP($variantPath, $quality);
                }
            }
        }
        
        if ($variantsCreated > 0) {
            echo "  ✓ Created $variantsCreated responsive variants\n";
            $stats['responsive_created'] += $variantsCreated;
        }
        
    } catch (Exception $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
        $stats['errors']++;
    }
    
    echo "\n";
}

// Summary
echo "\n";
echo "Optimization Complete!\n";
echo "=====================\n";
echo "Total images: {$stats['total']}\n";
echo "Resized: {$stats['resized']}\n";
echo "WebP versions created: {$stats['webp_created']}\n";
echo "Responsive variants: {$stats['responsive_created']}\n";
echo "Errors: {$stats['errors']}\n";
echo "\n";
echo "Original total size: " . formatBytes($stats['original_size']) . "\n";
echo "Final total size: " . formatBytes($stats['final_size']) . "\n";

if ($stats['final_size'] < $stats['original_size']) {
    $savings = $stats['original_size'] - $stats['final_size'];
    $percent = round(($savings / $stats['original_size']) * 100, 1);
    echo "Total savings: " . formatBytes($savings) . " ({$percent}%)\n";
}

/**
 * Format bytes to human readable
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

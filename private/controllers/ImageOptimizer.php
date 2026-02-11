<?php

class ImageOptimizer
{
    /**
     * Generate responsive image HTML with lazy loading and WebP support
     * 
     * @param string $src Image source path
     * @param string $alt Alt text for accessibility
     * @param array $options Additional options (class, id, sizes, lazy, etc.)
     * @return string HTML img tag with optimizations
     */
    public static function responsiveImage(string $src, string $alt = '', array $options = []): string
    {
        $class = $options['class'] ?? '';
        $id = $options['id'] ?? '';
        $lazy = $options['lazy'] ?? true;
        
        // Add leading slash if not present
        $fullSrc = $src;
        if ($src[0] !== '/') {
            $fullSrc = '/' . $src;
        }
        
        // Simple implementation without srcset for now
        $imgAttrs = [
            'src' => htmlspecialchars($fullSrc),
            'alt' => htmlspecialchars($alt)
        ];
        
        if ($class) {
            $imgAttrs['class'] = htmlspecialchars($class);
        }
        
        if ($id) {
            $imgAttrs['id'] = htmlspecialchars($id);
        }
        
        if ($lazy) {
            $imgAttrs['loading'] = 'lazy';
            $imgAttrs['decoding'] = 'async';
        }
        
        // Build img tag
        $html = '<img';
        foreach ($imgAttrs as $attr => $value) {
            $html .= sprintf(' %s="%s"', $attr, $value);
        }
        $html .= '>';
        
        return $html;
    }
    
    /**
     * Get WebP path for an image
     */
    private static function getWebPPath(string $src): string
    {
        $pathInfo = pathinfo($src);
        return $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
    }
    
    /**
     * Build srcset attribute for responsive images
     */
    private static function buildSrcset(string $src, array $widths): ?string
    {
        $pathInfo = pathinfo($src);
        $srcsetParts = [];
        
        foreach ($widths as $width) {
            $resizedPath = sprintf(
                '%s/%s-%dw.%s',
                $pathInfo['dirname'],
                $pathInfo['filename'],
                $width,
                $pathInfo['extension']
            );
            
            // Check if resized version exists
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $resizedPath)) {
                $srcsetParts[] = $resizedPath . ' ' . $width . 'w';
            }
        }
        
        // Always add original as fallback
        $srcsetParts[] = $src . ' ' . max($widths) . 'w';
        
        return !empty($srcsetParts) ? implode(', ', $srcsetParts) : null;
    }
    
    /**
     * Get image dimensions
     */
    private static function getImageDimensions(string $src): ?array
    {
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($src, '/');
        
        if (!file_exists($fullPath)) {
            return null;
        }
        
        $size = @getimagesize($fullPath);
        
        if ($size === false) {
            return null;
        }
        
        return [
            'width' => $size[0],
            'height' => $size[1]
        ];
    }
    
    /**
     * Resize and optimize image
     * 
     * @param string $sourcePath Source image path
     * @param string $destinationPath Destination path
     * @param int $maxWidth Maximum width
     * @param int $quality JPEG quality (1-100)
     * @return bool Success status
     */
    public static function resizeImage(string $sourcePath, string $destinationPath, int $maxWidth = 1200, int $quality = 85): bool
    {
        if (!file_exists($sourcePath)) {
            return false;
        }
        
        $imageInfo = getimagesize($sourcePath);
        if ($imageInfo === false) {
            return false;
        }
        
        list($width, $height, $type) = $imageInfo;
        
        // Don't upscale images
        if ($width <= $maxWidth) {
            return copy($sourcePath, $destinationPath);
        }
        
        $ratio = $height / $width;
        $newWidth = $maxWidth;
        $newHeight = (int)($maxWidth * $ratio);
        
        // Create image resource based on type
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($sourcePath);
                break;
            default:
                return false;
        }
        
        if (!$source) {
            return false;
        }
        
        // Create new image
        $destination = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
            imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Resize
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save based on type
        $result = false;
        $pathInfo = pathinfo($destinationPath);
        
        switch (strtolower($pathInfo['extension'])) {
            case 'jpg':
            case 'jpeg':
                $result = imagejpeg($destination, $destinationPath, $quality);
                break;
            case 'png':
                $pngQuality = (int)(9 - ($quality / 10));
                $result = imagepng($destination, $destinationPath, $pngQuality);
                break;
            case 'gif':
                $result = imagegif($destination, $destinationPath);
                break;
            case 'webp':
                $result = imagewebp($destination, $destinationPath, $quality);
                break;
        }
        
        imagedestroy($source);
        imagedestroy($destination);
        
        return $result;
    }
    
    /**
     * Convert image to WebP format
     */
    public static function convertToWebP(string $sourcePath, int $quality = 85): ?string
    {
        if (!file_exists($sourcePath)) {
            return null;
        }
        
        $pathInfo = pathinfo($sourcePath);
        $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
        
        $imageInfo = getimagesize($sourcePath);
        if ($imageInfo === false) {
            return null;
        }
        
        list($width, $height, $type) = $imageInfo;
        
        // Load source image
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($sourcePath);
                break;
            default:
                return null;
        }
        
        if (!$image) {
            return null;
        }
        
        // Convert to WebP
        if (imagewebp($image, $webpPath, $quality)) {
            imagedestroy($image);
            return $webpPath;
        }
        
        imagedestroy($image);
        return null;
    }
}

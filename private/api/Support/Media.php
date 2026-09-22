<?php

/** Turns a stored image path into a client-ready image object. */
class Media
{
    private const VARIANT_WIDTHS = [400, 800];

    /** The actual served document root, same source as Projects::uploadImage(). */
    private static function publicDir(): string
    {
        global $site;
        return rtrim($site['paths']['webroot'], '/\\') . '/';
    }

    /**
     * @return array{src:string, webp?:string, thumb?:string, srcset?:string, width?:int, height?:int}|null
     */
    public static function image(?string $path): ?array
    {
        if (!$path) {
            return null;
        }

        $publicDir = self::publicDir();
        $relative = ltrim($path, '/');
        $absolute = $publicDir . $relative;
        $info = pathinfo($relative);
        $dir = $info['dirname'] === '.' ? '' : $info['dirname'] . '/';
        $name = $info['filename'];
        $ext = $info['extension'] ?? '';

        $result = ['src' => '/' . $relative];

        $webp = $dir . $name . '.webp';
        if (is_file($publicDir . $webp)) {
            $result['webp'] = '/' . $webp;
        }

        $thumb = $dir . $name . '-thumb.webp';
        if (is_file($publicDir . $thumb)) {
            $result['thumb'] = '/' . $thumb;
        }

        $srcset = [];
        foreach (self::VARIANT_WIDTHS as $width) {
            $variant = sprintf('%s%s-%dw.%s', $dir, $name, $width, $ext);
            if (is_file($publicDir . $variant)) {
                $srcset[] = '/' . $variant . ' ' . $width . 'w';
            }
        }
        if ($srcset) {
            $result['srcset'] = implode(', ', $srcset);
        }

        if (is_file($absolute)) {
            $size = @getimagesize($absolute);
            if ($size) {
                $result['width'] = $size[0];
                $result['height'] = $size[1];
            }
        }

        return $result;
    }
}

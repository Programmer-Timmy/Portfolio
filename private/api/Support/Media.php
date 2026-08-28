<?php

/** Turns a stored image path into a client-ready image object. */
class Media
{
    private const PUBLIC_DIR = __DIR__ . '/../../../public/';
    private const VARIANT_WIDTHS = [400, 800];

    /**
     * @return array{src:string, webp?:string, srcset?:string, width?:int, height?:int}|null
     */
    public static function image(?string $path): ?array
    {
        if (!$path) {
            return null;
        }

        $relative = ltrim($path, '/');
        $absolute = self::PUBLIC_DIR . $relative;
        $info = pathinfo($relative);
        $dir = $info['dirname'] === '.' ? '' : $info['dirname'] . '/';
        $name = $info['filename'];
        $ext = $info['extension'] ?? '';

        $result = ['src' => '/' . $relative];

        $webp = $dir . $name . '.webp';
        if (is_file(self::PUBLIC_DIR . $webp)) {
            $result['webp'] = '/' . $webp;
        }

        $srcset = [];
        foreach (self::VARIANT_WIDTHS as $width) {
            $variant = sprintf('%s%s-%dw.%s', $dir, $name, $width, $ext);
            if (is_file(self::PUBLIC_DIR . $variant)) {
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

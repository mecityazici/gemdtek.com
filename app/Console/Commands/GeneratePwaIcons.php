<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GeneratePwaIcons extends Command
{
    protected $signature = 'app:generate-pwa-icons';

    protected $description = 'Generate brand-colored PWA icons (192, 512, maskable, apple-touch) under public/icons/.';

    public function handle(): int
    {
        $dir = public_path('icons');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $sizes = [
            ['file' => 'icon-192.png', 'size' => 192, 'maskable' => false],
            ['file' => 'icon-512.png', 'size' => 512, 'maskable' => false],
            ['file' => 'icon-maskable-512.png', 'size' => 512, 'maskable' => true],
            ['file' => 'apple-touch-icon.png', 'size' => 180, 'maskable' => false],
        ];

        foreach ($sizes as $spec) {
            $this->renderIcon($dir.'/'.$spec['file'], $spec['size'], $spec['maskable']);
            $this->line("  ✓ {$spec['file']} ({$spec['size']}×{$spec['size']})");
        }

        $this->info('PWA ikonları üretildi: '.$dir);

        return self::SUCCESS;
    }

    private function renderIcon(string $path, int $size, bool $maskable): void
    {
        $im = imagecreatetruecolor($size, $size);

        // Brand palette
        $navy = imagecolorallocate($im, 11, 37, 69);     // navy-800
        $petrol = imagecolorallocate($im, 19, 49, 92);   // petrol
        $brass = imagecolorallocate($im, 184, 115, 51);  // brass-500
        $cream = imagecolorallocate($im, 244, 244, 242); // cream

        // Background gradient (manual approximation)
        for ($y = 0; $y < $size; $y++) {
            $t = $y / max(1, $size - 1);
            $r = (int) (11 + ($petrol >> 16 & 0xFF) - 11) * $t + 11;
            $g = (int) (37 + ($petrol >> 8 & 0xFF) - 37) * $t + 37;
            $b = (int) (69 + ($petrol & 0xFF) - 69) * $t + 69;
            $line = imagecolorallocate($im, (int) $r, (int) $g, (int) $b);
            imageline($im, 0, $y, $size, $y, $line);
        }

        // Maskable icons need 80% safe zone — letter inset slightly more
        $inset = $maskable ? (int) ($size * 0.10) : 0;

        // Center brass disc (subtle)
        $cx = $size / 2;
        $cy = $size / 2;
        $disc = (int) ($size * ($maskable ? 0.42 : 0.46));
        imagefilledellipse($im, (int) $cx, (int) $cy, $disc, $disc, $petrol);

        // "G" glyph using built-in font 5 scaled via imagettftext if font available;
        // fall back to a stylized circle + cream wedge
        $fontPath = $this->resolveFontPath();
        if ($fontPath) {
            $fontSize = $size * ($maskable ? 0.42 : 0.50);
            $bbox = imagettfbbox($fontSize, 0, $fontPath, 'G');
            $textWidth = abs($bbox[2] - $bbox[0]);
            $textHeight = abs($bbox[7] - $bbox[1]);
            $x = (int) (($size - $textWidth) / 2 - $bbox[0]);
            $y = (int) (($size + $textHeight) / 2 - 2);
            imagettftext($im, $fontSize, 0, $x, $y, $cream, $fontPath, 'G');
        } else {
            // Geometric fallback: cream arc forming a "G" silhouette
            $arcSize = (int) ($size * ($maskable ? 0.50 : 0.58));
            imagefilledellipse($im, (int) $cx, (int) $cy, $arcSize, $arcSize, $cream);
            $hole = (int) ($arcSize * 0.55);
            imagefilledellipse($im, (int) $cx, (int) $cy, $hole, $hole, $petrol);
            // Brass bar (the "G" middle stroke)
            $barW = (int) ($arcSize * 0.55);
            $barH = (int) ($size * 0.06);
            imagefilledrectangle($im, (int) $cx, (int) ($cy - $barH / 2), (int) ($cx + $barW / 2), (int) ($cy + $barH / 2), $brass);
        }

        imagepng($im, $path);
        imagedestroy($im);
    }

    private function resolveFontPath(): ?string
    {
        // Try common Windows / Linux locations for a bold sans-serif
        $candidates = [
            'C:/Windows/Fonts/arialbd.ttf',
            'C:/Windows/Fonts/segoeuib.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            '/System/Library/Fonts/Supplemental/Arial Bold.ttf',
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}

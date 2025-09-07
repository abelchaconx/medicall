<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Specialty extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name','description'];

    // Return a deterministic hex color for this specialty (based on id/name hash)
    public function getColorAttribute()
    {
        // use name if available, fallback to id
        $seed = $this->name ?? $this->id;
        // simple hash to integer
        $hash = crc32($seed);
        // map to H (0..360)
        $h = $hash % 360;
        // fixed sat and light for pleasant colors
        $s = 50; // percent
        $l = 35; // percent

        // convert HSL to RGB
        list($r, $g, $b) = $this->hslToRgb($h, $s, $l);
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    // Return an rgba string with 0.5 alpha
    public function getColorTranslucentAttribute()
    {
        $hex = $this->color;
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "rgba({$r}, {$g}, {$b}, 0.5)";
    }

    // Helper: convert HSL to RGB (H: 0-360, S: 0-100, L: 0-100)
    protected function hslToRgb($h, $s, $l)
    {
        $h /= 360;
        $s /= 100;
        $l /= 100;

        if ($s == 0) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : ($l + $s - $l * $s);
            $p = 2 * $l - $q;
            $r = $this->hue2rgb($p, $q, $h + 1/3);
            $g = $this->hue2rgb($p, $q, $h);
            $b = $this->hue2rgb($p, $q, $h - 1/3);
        }
        return [round($r * 255), round($g * 255), round($b * 255)];
    }

    protected function hue2rgb($p, $q, $t)
    {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1/2) return $q;
        if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
        return $p;
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_specialty');
    }
}

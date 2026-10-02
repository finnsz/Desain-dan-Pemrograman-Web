<?php
/**
 * includes/helpers.php
 * Fungsi utility untuk keamanan dan formatting.
 */

/**
 * Escape output untuk mencegah XSS.
 * Mengubah karakter HTML berbahaya (<, >, &, ", ') menjadi HTML entity.
 *
 * @param mixed $value Nilai yang akan di-escape
 * @return string Nilai yang sudah di-escape
 */
function e($value)
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

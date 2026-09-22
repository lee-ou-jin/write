<?php
namespace App\Services;
use Illuminate\Support\Str;

class SuperSlug{
    public static function slugKo($title, $separator = '-'): string
    {
        $title = trim($title);
        return strtolower(preg_replace('/[^a-zA-Z가-힣0-9]+/', $separator, $title));
    }

    public static function slugOriginal($title, $separator = '-', $language = 'en', $dictionary = ['@' => 'at']): string
    {
        $title = $language ? Str::ascii($title, $language) : $title;

        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';

        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

        // Replace dictionary words
        foreach ($dictionary as $key => $value) {
            $dictionary[$key] = $separator.$value.$separator;
        }

        $title = str_replace(array_keys($dictionary), array_values($dictionary), $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', Str::lower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }
}

<?php

/**
 * Extracts an 11-character YouTube video ID from common URL formats, or
 * returns null if the string doesn't look like a YouTube link.
 * Handles: youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID,
 * youtube.com/shorts/ID — with optional extra query params, https/www.
 */
function extract_youtube_id(string $url): ?string {
    $url = trim($url);
    if ($url === '') {
        return null;
    }

    $pattern = '~(?:youtube\.com/(?:watch\?v=|embed/|v/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})~i';
    if (preg_match($pattern, $url, $matches)) {
        return $matches[1];
    }

    return null;
}

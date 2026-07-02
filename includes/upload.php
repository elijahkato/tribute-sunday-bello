<?php
require_once __DIR__ . '/../config.php';

/**
 * Validates and moves an uploaded file (a single $_FILES[...] entry) into
 * UPLOAD_DIR/{photos|videos} under a random filename.
 *
 * Returns ['ok' => true, 'path' => 'photos/ab12cd34.jpg', 'type' => 'photo']
 * or ['ok' => false, 'error' => 'message'] on failure.
 */
function handle_file_upload(array $fileField): array {
    if (!isset($fileField['error']) || $fileField['error'] === UPLOAD_ERR_NO_FILE) {
        return ['ok' => false, 'error' => 'no_file'];
    }
    if ($fileField['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'upload_error_' . $fileField['error']];
    }

    $ext = strtolower(pathinfo($fileField['name'], PATHINFO_EXTENSION));

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $fileField['tmp_name']);
    finfo_close($finfo);

    if (in_array($ext, ALLOWED_PHOTO_EXT, true) && in_array($mime, ALLOWED_PHOTO_MIME, true)) {
        $kind = 'photo';
        $subdir = 'photos';
        $maxBytes = MAX_PHOTO_BYTES;
    } elseif (in_array($ext, ALLOWED_VIDEO_EXT, true) && in_array($mime, ALLOWED_VIDEO_MIME, true)) {
        $kind = 'video_file';
        $subdir = 'videos';
        $maxBytes = MAX_VIDEO_BYTES;
    } else {
        return ['ok' => false, 'error' => 'unsupported_type'];
    }

    if ($fileField['size'] > $maxBytes) {
        return ['ok' => false, 'error' => 'too_large'];
    }

    $randomName = bin2hex(random_bytes(16)) . '.' . $ext;
    $destRelative = $subdir . '/' . $randomName;
    $destAbsolute = UPLOAD_DIR . '/' . $destRelative;

    if (!move_uploaded_file($fileField['tmp_name'], $destAbsolute)) {
        return ['ok' => false, 'error' => 'move_failed'];
    }

    return ['ok' => true, 'path' => $destRelative, 'type' => $kind];
}

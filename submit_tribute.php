<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/upload.php';
require_once __DIR__ . '/includes/youtube.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Honeypot: a hidden field real visitors never fill in. Bots often do.
if (!empty($_POST['website'])) {
    header('Location: tributes.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$relationship = trim($_POST['relationship'] ?? '');
$message = trim($_POST['message'] ?? '');
$caption = trim($_POST['caption'] ?? '') ?: null;
$source = $_POST['source'] ?? 'file';

if ($name === '') {
    http_response_code(422);
    exit('Name is required.');
}

if (!in_array($relationship, TRIBUTE_RELATIONSHIPS, true)) {
    http_response_code(422);
    exit('Please choose a valid relationship.');
}

// A pasted YouTube link is validated up front so a bad link never creates an
// orphan tribute with nothing meaningful attached to it.
$youtubeId = null;
if ($source === 'youtube') {
    $youtubeUrl = trim($_POST['youtube_url'] ?? '');
    if ($youtubeUrl !== '') {
        $youtubeId = extract_youtube_id($youtubeUrl);
        if (!$youtubeId) {
            http_response_code(422);
            exit('That does not look like a valid YouTube link.');
        }
    }
}

$hasFile = $source !== 'youtube' && !empty($_FILES['media']['name']);
$hasYoutube = $youtubeId !== null;

// A tribute needs a written message, a photo/video, or both — not neither.
if ($message === '' && !$hasFile && !$hasYoutube) {
    http_response_code(422);
    exit('Please share a message, or a photo/video.');
}

$pdo = get_pdo();
$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare('INSERT INTO tributes (name, relationship, message, status) VALUES (:name, :relationship, :message, "pending")');
    $stmt->execute(['name' => $name, 'relationship' => $relationship, 'message' => $message !== '' ? $message : null]);
    $tributeId = (int) $pdo->lastInsertId();

    if ($hasYoutube) {
        $stmt = $pdo->prepare('INSERT INTO media (tribute_id, type, youtube_id, caption, status) VALUES (:tid, "youtube", :yid, :caption, "pending")');
        $stmt->execute(['tid' => $tributeId, 'yid' => $youtubeId, 'caption' => $caption]);
    } elseif ($hasFile) {
        $result = handle_file_upload($_FILES['media']);
        if ($result['ok']) {
            $stmt = $pdo->prepare('INSERT INTO media (tribute_id, type, file_path, caption, status) VALUES (:tid, :type, :path, :caption, "pending")');
            $stmt->execute(['tid' => $tributeId, 'type' => $result['type'], 'path' => $result['path'], 'caption' => $caption]);
        } elseif ($message === '') {
            // The photo/video was the whole point of this submission — fail loudly
            // instead of silently posting an empty tribute.
            $pdo->rollBack();
            http_response_code(422);
            exit('Upload failed: ' . h($result['error']));
        }
        // Otherwise the tribute text stands on its own; a failed optional
        // attachment alongside a real message isn't worth failing over.
    }

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    http_response_code(500);
    exit('Something went wrong. Please try again.');
}

header('Location: tributes.php?submitted=1#tributes');
exit;

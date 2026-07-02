<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/upload.php';
require_once __DIR__ . '/../includes/youtube.php';

$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = trim($_POST['caption'] ?? '') ?: null;
    $source = $_POST['source'] ?? 'file';
    $pdo = get_pdo();

    if ($source === 'youtube') {
        $youtubeId = extract_youtube_id(trim($_POST['youtube_url'] ?? ''));
        if (!$youtubeId) {
            $error = 'That does not look like a valid YouTube link.';
        } else {
            // Admin uploads are trusted and go live immediately — no moderation queue.
            $stmt = $pdo->prepare('INSERT INTO media (tribute_id, type, youtube_id, caption, status) VALUES (NULL, "youtube", :yid, :caption, "approved")');
            $stmt->execute(['yid' => $youtubeId, 'caption' => $caption]);
            $success = true;
        }
    } elseif (empty($_FILES['media']['name'])) {
        $error = 'Please choose a photo or video file.';
    } else {
        $result = handle_file_upload($_FILES['media']);
        if (!$result['ok']) {
            $error = 'Upload failed: ' . $result['error'];
        } else {
            $stmt = $pdo->prepare('INSERT INTO media (tribute_id, type, file_path, caption, status) VALUES (NULL, :type, :path, :caption, "approved")');
            $stmt->execute(['type' => $result['type'], 'path' => $result['path'], 'caption' => $caption]);
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Media</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-page { max-width: 900px; margin: 3rem auto; padding: 0 1.5rem; }
        .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; }
        .admin-notice { padding: 0.9rem 1.1rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.9rem; }
        .admin-notice.success { background: #eaf6ec; color: #256029; border: 1px solid #bfe3c5; }
        .admin-notice.error { background: #fbeaea; color: #8a2b2b; border: 1px solid #f0c2c2; }
    </style>
</head>
<body>
    <div class="admin-page">
        <div class="admin-header">
            <h1 class="profile-name" style="font-size:1.6rem; margin:0;">Upload Photo or Video</h1>
            <a href="dashboard.php" class="share-btn" style="text-decoration:none;">Back to Dashboard</a>
        </div>

        <?php if ($success): ?>
            <p class="admin-notice success">Published to the Gallery.</p>
        <?php elseif ($error): ?>
            <p class="admin-notice error"><?= h($error) ?></p>
        <?php endif; ?>

        <div class="upload-box">
            <form action="upload_media.php" method="POST" enctype="multipart/form-data" class="tribute-form">
                <div class="media-type-toggle">
                    <label><input type="radio" name="source" value="file" checked> Upload a photo or video</label>
                    <label><input type="radio" name="source" value="youtube"> Paste a YouTube link</label>
                </div>

                <div class="file-input-wrapper" id="admin-file-group">
                    <label for="admin-media-upload" class="custom-file-btn">Choose Photo or Video</label>
                    <input type="file" id="admin-media-upload" name="media" accept="image/*,video/*">
                    <span class="file-name" id="admin-file-name">No file chosen</span>
                </div>

                <div id="admin-youtube-group" style="display:none;">
                    <label class="field-label" for="admin-youtube">YouTube URL</label>
                    <input type="text" id="admin-youtube" name="youtube_url" placeholder="https://www.youtube.com/watch?v=...">
                </div>

                <label class="field-label" for="admin-caption">Caption (optional)</label>
                <input type="text" id="admin-caption" name="caption" placeholder="Describe this photo or video&hellip;">

                <button type="submit" class="submit-btn">Publish to Gallery</button>
            </form>
        </div>
    </div>
    <script>
        const input = document.getElementById('admin-media-upload');
        const label = document.getElementById('admin-file-name');
        input.addEventListener('change', () => {
            label.textContent = input.files.length ? input.files[0].name : 'No file chosen';
        });

        const sourceRadios = document.querySelectorAll('input[name="source"]');
        const fileGroup = document.getElementById('admin-file-group');
        const youtubeGroup = document.getElementById('admin-youtube-group');
        sourceRadios.forEach((radio) => {
            radio.addEventListener('change', () => {
                const isYoutube = radio.value === 'youtube' && radio.checked;
                fileGroup.style.display = isYoutube ? 'none' : 'flex';
                youtubeGroup.style.display = isYoutube ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>

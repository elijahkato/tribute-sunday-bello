<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

function delete_uploaded_file(array $media): void {
    if (empty($media['file_path'])) {
        return;
    }
    $path = UPLOAD_DIR . '/' . $media['file_path'];
    if (is_file($path)) {
        @unlink($path);
    }
}

$type = $_POST['type'] ?? '';
$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $pdo = get_pdo();

    if ($type === 'tribute') {
        // A tribute's attached photos/videos have no life of their own once
        // the tribute is gone, so delete them (and their files) along with it.
        $stmt = $pdo->prepare('SELECT * FROM media WHERE tribute_id = :id');
        $stmt->execute(['id' => $id]);
        foreach ($stmt->fetchAll() as $m) {
            delete_uploaded_file($m);
        }
        $pdo->prepare('DELETE FROM media WHERE tribute_id = :id')->execute(['id' => $id]);
        $pdo->prepare('DELETE FROM tributes WHERE id = :id')->execute(['id' => $id]);
    } elseif ($type === 'media') {
        $stmt = $pdo->prepare('SELECT * FROM media WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $media = $stmt->fetch();
        if ($media) {
            delete_uploaded_file($media);
            $pdo->prepare('DELETE FROM media WHERE id = :id')->execute(['id' => $id]);
        }
    }
}

header('Location: dashboard.php');
exit;

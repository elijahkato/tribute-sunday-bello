<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = get_pdo();

$pendingTributes = $pdo->query(
    "SELECT * FROM tributes WHERE status = 'pending' ORDER BY created_at ASC"
)->fetchAll();

$publishedTributes = $pdo->query(
    "SELECT * FROM tributes WHERE status = 'approved' ORDER BY created_at DESC"
)->fetchAll();

$allTributeIds = array_merge(array_column($pendingTributes, 'id'), array_column($publishedTributes, 'id'));
$tributeMediaById = [];
if ($allTributeIds) {
    $in = implode(',', array_fill(0, count($allTributeIds), '?'));
    $stmt = $pdo->prepare("SELECT * FROM media WHERE tribute_id IN ($in) ORDER BY created_at ASC");
    $stmt->execute($allTributeIds);
    foreach ($stmt->fetchAll() as $m) {
        $tributeMediaById[$m['tribute_id']][] = $m;
    }
}

$pendingMedia = $pdo->query(
    "SELECT * FROM media WHERE status = 'pending' AND tribute_id IS NULL ORDER BY created_at ASC"
)->fetchAll();

$publishedMedia = $pdo->query(
    "SELECT * FROM media WHERE status = 'approved' AND tribute_id IS NULL ORDER BY created_at DESC"
)->fetchAll();

function render_admin_media_thumb(array $m): string {
    if ($m['type'] === 'photo') {
        return '<img class="admin-thumb" src="../uploads/' . h($m['file_path']) . '" alt="">';
    }
    if ($m['type'] === 'video_file') {
        return '<video class="admin-thumb" src="../uploads/' . h($m['file_path']) . '" muted preload="metadata"></video>';
    }
    if ($m['type'] === 'youtube') {
        return '<img class="admin-thumb" src="https://img.youtube.com/vi/' . h($m['youtube_id']) . '/mqdefault.jpg" alt="YouTube thumbnail">';
    }
    return '';
}

function render_admin_delete_form(string $type, int $id, string $label = 'Delete'): string {
    return '<form method="POST" action="delete.php" onsubmit="return confirm(\'Delete this permanently? This cannot be undone.\');">'
        . '<input type="hidden" name="type" value="' . h($type) . '">'
        . '<input type="hidden" name="id" value="' . $id . '">'
        . '<button type="submit" class="submit-btn btn-delete">' . h($label) . '</button>'
        . '</form>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-page { max-width: 900px; margin: 3rem auto; padding: 0 1.5rem; }
        .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; }
        .admin-queue-title { font-family: 'Playfair Display', serif; font-size: 1.3rem; margin: 2.5rem 0 1rem; color: #1a1a1a; }
        .admin-row { background: #fff; border: 1px solid #eaeaea; border-radius: 8px; padding: 1.25rem 1.5rem; margin-bottom: 1rem; display: flex; gap: 1.25rem; align-items: flex-start; }
        .admin-row-body { flex: 1; min-width: 0; }
        .admin-row-meta { font-size: 0.8rem; color: #999; margin: 0.35rem 0 0.75rem; }
        .admin-thumbs { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.75rem; }
        .admin-thumb { width: 90px; height: 90px; object-fit: cover; border-radius: 6px; background: #eee; }
        .admin-actions { display: flex; gap: 0.5rem; }
        .admin-actions form { display: inline; }
        .btn-reject { background: #a33333; }
        .btn-reject:hover { background: #7e2323; }
        .btn-delete { background: #555555; }
        .btn-delete:hover { background: #333333; }
        .admin-empty { color: #999; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="admin-page">
        <div class="admin-header">
            <h1 class="profile-name" style="font-size:1.6rem; margin:0;">Moderation Dashboard</h1>
            <div style="display:flex; gap:0.75rem;">
                <a href="upload_media.php" class="share-btn" style="text-decoration:none;">+ Upload Photo/Video</a>
                <a href="logout.php" class="share-btn" style="text-decoration:none;">Log Out</a>
            </div>
        </div>

        <h2 class="admin-queue-title">Pending Tributes (<?= count($pendingTributes) ?>)</h2>
        <?php if (!$pendingTributes): ?>
            <p class="admin-empty">Nothing waiting for review.</p>
        <?php endif; ?>
        <?php foreach ($pendingTributes as $t): ?>
            <div class="admin-row">
                <div class="admin-row-body">
                    <?php if (!empty($tributeMediaById[$t['id']])): ?>
                        <div class="admin-thumbs">
                            <?php foreach ($tributeMediaById[$t['id']] as $m): ?>
                                <?= render_admin_media_thumb($m) ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <p class="tribute-body" style="margin:0;"><?= nl2br(h($t['message'])) ?></p>
                    <p class="admin-row-meta">Left by <?= h($t['name']) ?> &middot; <?= h(date('F j, Y g:ia', strtotime($t['created_at']))) ?></p>
                    <div class="admin-actions">
                        <form method="POST" action="update_status.php">
                            <input type="hidden" name="type" value="tribute">
                            <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="submit-btn">Approve</button>
                        </form>
                        <form method="POST" action="update_status.php">
                            <input type="hidden" name="type" value="tribute">
                            <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="submit-btn btn-reject">Reject</button>
                        </form>
                        <?= render_admin_delete_form('tribute', (int) $t['id']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <h2 class="admin-queue-title">Pending Media (<?= count($pendingMedia) ?>)</h2>
        <?php if (!$pendingMedia): ?>
            <p class="admin-empty">Nothing waiting for review.</p>
        <?php endif; ?>
        <?php foreach ($pendingMedia as $m): ?>
            <div class="admin-row">
                <div class="admin-thumbs" style="margin-bottom:0;">
                    <?= render_admin_media_thumb($m) ?>
                </div>
                <div class="admin-row-body">
                    <?php if (!empty($m['caption'])): ?>
                        <p class="tribute-body" style="margin:0;"><?= h($m['caption']) ?></p>
                    <?php endif; ?>
                    <p class="admin-row-meta">
                        <?= h(strtoupper($m['type'])) ?>
                        <?php if (!empty($m['uploader_name'])): ?> &middot; Submitted by <?= h($m['uploader_name']) ?><?php endif; ?>
                        &middot; <?= h(date('F j, Y g:ia', strtotime($m['created_at']))) ?>
                    </p>
                    <div class="admin-actions">
                        <form method="POST" action="update_status.php">
                            <input type="hidden" name="type" value="media">
                            <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="submit-btn">Approve</button>
                        </form>
                        <form method="POST" action="update_status.php">
                            <input type="hidden" name="type" value="media">
                            <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="submit-btn btn-reject">Reject</button>
                        </form>
                        <?= render_admin_delete_form('media', (int) $m['id']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <h2 class="admin-queue-title">Published Tributes (<?= count($publishedTributes) ?>)</h2>
        <?php if (!$publishedTributes): ?>
            <p class="admin-empty">Nothing published yet.</p>
        <?php endif; ?>
        <?php foreach ($publishedTributes as $t): ?>
            <div class="admin-row">
                <div class="admin-row-body">
                    <?php if (!empty($tributeMediaById[$t['id']])): ?>
                        <div class="admin-thumbs">
                            <?php foreach ($tributeMediaById[$t['id']] as $m): ?>
                                <?= render_admin_media_thumb($m) ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($t['message'])): ?>
                        <p class="tribute-body" style="margin:0;"><?= nl2br(h($t['message'])) ?></p>
                    <?php endif; ?>
                    <p class="admin-row-meta">
                        Left by <?= h($t['name']) ?> &middot; <?= h(date('F j, Y g:ia', strtotime($t['created_at']))) ?>
                        &middot; <a href="../tribute.php?id=<?= (int) $t['id'] ?>" target="_blank">View live</a>
                    </p>
                    <div class="admin-actions">
                        <?= render_admin_delete_form('tribute', (int) $t['id']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <h2 class="admin-queue-title">Published Media (<?= count($publishedMedia) ?>)</h2>
        <?php if (!$publishedMedia): ?>
            <p class="admin-empty">Nothing published yet.</p>
        <?php endif; ?>
        <?php foreach ($publishedMedia as $m): ?>
            <div class="admin-row">
                <div class="admin-thumbs" style="margin-bottom:0;">
                    <?= render_admin_media_thumb($m) ?>
                </div>
                <div class="admin-row-body">
                    <?php if (!empty($m['caption'])): ?>
                        <p class="tribute-body" style="margin:0;"><?= h($m['caption']) ?></p>
                    <?php endif; ?>
                    <p class="admin-row-meta">
                        <?= h(strtoupper($m['type'])) ?>
                        <?php if (!empty($m['uploader_name'])): ?> &middot; Submitted by <?= h($m['uploader_name']) ?><?php endif; ?>
                        &middot; <?= h(date('F j, Y g:ia', strtotime($m['created_at']))) ?>
                        &middot; <a href="../gallery.php" target="_blank">View in gallery</a>
                    </p>
                    <div class="admin-actions">
                        <?= render_admin_delete_form('media', (int) $m['id']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>

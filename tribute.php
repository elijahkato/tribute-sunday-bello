<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = get_pdo();

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM tributes WHERE id = :id AND status = 'approved'");
$stmt->execute(['id' => $id]);
$tribute = $stmt->fetch();

if (!$tribute) {
    http_response_code(404);
}

$media = [];
if ($tribute) {
    $stmt = $pdo->prepare("SELECT * FROM media WHERE tribute_id = :id AND status = 'approved' ORDER BY created_at ASC");
    $stmt->execute(['id' => $id]);
    $media = $stmt->fetchAll();
}

$heightClasses = ['ph-tall', 'ph-medium', 'ph-square', 'ph-wide'];

$pageTitle = $tribute ? 'Tribute from ' . $tribute['name'] : 'Tribute Not Found';
require __DIR__ . '/includes/header.php';
?>

        <section id="tribute-detail" class="page-section">
            <?php if (!$tribute): ?>
                <h2 class="section-title">Tribute Not Found</h2>
                <p class="tributes-intro">This tribute may have been removed, or is still awaiting approval.</p>
                <a class="view-all-link" href="tributes.php">&larr; Back to all tributes</a>
            <?php else: ?>
                <a class="view-all-link" href="tributes.php">&larr; Back to all tributes</a>

                <article class="tribute-card tribute-card--full">
                    <?php if (!empty($tribute['message'])): ?>
                        <p class="tribute-body"><?= nl2br(h($tribute['message'])) ?></p>
                    <?php endif; ?>
                    <p class="tribute-meta">
                        Left by <?= h($tribute['name']) ?>
                        <?php $relationship = relationship_label($tribute['relationship'] ?? null); ?>
                        <?php if ($relationship !== ''): ?>
                            <span class="tribute-flow-relationship"><?= h($relationship) ?></span>
                        <?php endif; ?>
                        &middot; <?= h(date('F j, Y', strtotime($tribute['created_at']))) ?>
                    </p>
                </article>

                <?php if ($media): ?>
                    <div class="fluid-gallery" id="tribute-media">
                        <?php foreach ($media as $i => $m): ?>
                            <?= render_gallery_item($m, $heightClasses[$i % count($heightClasses)]) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>

<?php require __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = get_pdo();

// All approved photos/videos, whether attached to a tribute or shared standalone
// (via the admin panel or a tribute submission).
$media = $pdo->query(
    "SELECT * FROM media WHERE status = 'approved' ORDER BY created_at DESC"
)->fetchAll();

$heightClasses = ['ph-tall', 'ph-medium', 'ph-square', 'ph-wide'];

$pageTitle = 'Gallery — Sunday Makatarehi Bello';
require __DIR__ . '/includes/header.php';
?>

        <!-- GALLERY: every approved photo/video, tribute-attached or standalone -->
        <section id="gallery" class="page-section">
            <h2 class="section-title">Gallery</h2>
            <p class="tributes-intro">Photos and videos shared by everyone who loved him.</p>

            <div class="gallery-filters" id="gallery-filters">
                <button type="button" class="filter-btn active" data-filter="all">All</button>
                <button type="button" class="filter-btn" data-filter="photo">Photos</button>
                <button type="button" class="filter-btn" data-filter="video">Videos</button>
            </div>

            <div class="fluid-gallery" id="gallery-grid">
                <?php if (!$media): ?>
                    <p>No photos or videos shared yet &mdash; be the first from the <a href="tributes.php">Tributes page</a>.</p>
                <?php endif; ?>
                <?php foreach ($media as $i => $m): ?>
                    <?= render_gallery_item($m, $heightClasses[$i % count($heightClasses)]) ?>
                <?php endforeach; ?>
            </div>

            <p class="tributes-intro"><a href="tributes.php#add-tribute">Leave a tribute</a> to share your own photo or video.</p>
        </section>

<?php require __DIR__ . '/includes/footer.php'; ?>

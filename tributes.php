<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = get_pdo();

// --- Tribute Wall data: approved tributes + their approved attached media ---
$tributes = $pdo->query(
    "SELECT * FROM tributes WHERE status = 'approved' ORDER BY created_at DESC"
)->fetchAll();

$tributeIds = array_column($tributes, 'id');
$tributeMediaById = [];
if ($tributeIds) {
    $in = implode(',', array_fill(0, count($tributeIds), '?'));
    $stmt = $pdo->prepare("SELECT * FROM media WHERE status = 'approved' AND tribute_id IN ($in) ORDER BY created_at ASC");
    $stmt->execute($tributeIds);
    foreach ($stmt->fetchAll() as $m) {
        $tributeMediaById[$m['tribute_id']][] = $m;
    }
}

$pageTitle = 'Tributes — Sunday Makatarehi Bello';
require __DIR__ . '/includes/header.php';
?>

        <!-- TRIBUTE WALL / GUESTBOOK -->
        <section id="tributes" class="page-section">
            <h2 class="section-title">Wall of Remembrance</h2>
            <p class="tributes-intro">&ldquo;To live in the hearts we leave behind is not to die.&rdquo; Please share your photos, videos, and memories.</p>

            <?php if (!$tributes): ?>
                <p class="tributes-intro">Be the first to leave a tribute below.</p>
            <?php else: ?>
                <div class="tribute-search-bar">
                    <input type="search" id="tribute-search" placeholder="Search tributes by name&hellip;" aria-label="Search tributes by name">
                </div>
                <p class="tributes-intro" id="tribute-search-empty" style="display:none;">No tributes match that name.</p>
                <div class="tribute-flow" id="tribute-flow">
                    <?php foreach ($tributes as $t): ?>
                        <?= render_tribute_flow_card($t, $tributeMediaById[$t['id']] ?? []) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="upload-box" id="add-tribute">
                <h3>Leave a Tribute</h3>
                <form action="submit_tribute.php" method="POST" enctype="multipart/form-data" class="tribute-form">
                    <input type="text" name="website" class="honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">

                    <label class="field-label" for="tribute-name">Name</label>
                    <input type="text" id="tribute-name" name="name" placeholder="Your name" required>

                    <label class="field-label" for="tribute-relationship">Relationship to Sunday</label>
                    <select id="tribute-relationship" name="relationship" required>
                        <option value="" disabled selected>Choose one&hellip;</option>
                        <?php foreach (TRIBUTE_RELATIONSHIPS as $option): ?>
                            <option value="<?= h($option) ?>"><?= h(relationship_label($option)) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label class="field-label" for="tribute-message">Message (optional if you're sharing a photo or video)</label>
                    <textarea id="tribute-message" name="message" rows="4" placeholder="Share a memory or message&hellip;"></textarea>

                    <div class="media-type-toggle">
                        <label><input type="radio" name="source" value="file" checked> Upload a photo or video</label>
                        <label><input type="radio" name="source" value="youtube"> Paste a YouTube link</label>
                    </div>

                    <div class="file-input-wrapper" id="tribute-file-group">
                        <label for="media-upload" class="custom-file-btn">Choose Photo or Video</label>
                        <input type="file" id="media-upload" name="media" accept="image/*,video/*">
                        <span class="file-name" id="file-name">No file chosen</span>
                    </div>

                    <div id="tribute-youtube-group" style="display:none;">
                        <label class="field-label" for="tribute-youtube">YouTube URL</label>
                        <input type="text" id="tribute-youtube" name="youtube_url" placeholder="https://www.youtube.com/watch?v=...">
                    </div>

                    <label class="field-label" for="tribute-caption">Photo/Video Caption (optional)</label>
                    <input type="text" id="tribute-caption" name="caption" placeholder="Describe this photo or video&hellip;">

                    <button type="submit" class="submit-btn">Post Tribute</button>
                </form>
            </div>
        </section>

<?php require __DIR__ . '/includes/footer.php'; ?>

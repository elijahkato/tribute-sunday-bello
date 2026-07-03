<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = get_pdo();

// --- Tribute teaser: latest 3 approved tributes + their approved media ---
$tributeTeasers = $pdo->query(
    "SELECT * FROM tributes WHERE status = 'approved' ORDER BY created_at DESC LIMIT 3"
)->fetchAll();

$tributeTeaserIds = array_column($tributeTeasers, 'id');
$tributeTeaserMediaById = [];
if ($tributeTeaserIds) {
    $in = implode(',', array_fill(0, count($tributeTeaserIds), '?'));
    $stmt = $pdo->prepare("SELECT * FROM media WHERE status = 'approved' AND tribute_id IN ($in) ORDER BY created_at ASC");
    $stmt->execute($tributeTeaserIds);
    foreach ($stmt->fetchAll() as $m) {
        $tributeTeaserMediaById[$m['tribute_id']][] = $m;
    }
}

// --- Gallery teaser: latest 8 approved media, tribute-attached or standalone ---
$galleryTeasers = $pdo->query(
    "SELECT * FROM media WHERE status = 'approved' ORDER BY created_at DESC LIMIT 8"
)->fetchAll();

$heightClasses = ['ph-tall', 'ph-medium', 'ph-square', 'ph-wide'];

$pageTitle = 'Sunday Makatarehi Bello — In Loving Memory';
require __DIR__ . '/includes/header.php';
?>

        <div class="poster-banner">
            <img src="images/banner.jpeg" alt="Obituary announcement for Sunday Makatarehi Bello">
        </div>

        <blockquote class="memorial-quote">
            &ldquo;It is of the LORD&rsquo;s mercies that we are not consumed, because His compassions fail not. They are new every morning: great is Thy faithfulness.&rdquo; &mdash; Lamentations 3:22&ndash;23
        </blockquote>

        <!-- BIOGRAPHY -->
        <section id="biography" class="page-section">
            <h2 class="section-title">Biography</h2>
            <div class="bio-text">
                <p><span class="drop-cap">L</span>ate Sunday Makatarehi Bello was born on 14 April 1974 in Lokoja, Kogi State, to the family of Late Pa Aliu Owuda Bello and Late Mrs. Esther Adetutu Aliu. He was the second child and first son in a family of nine children. From childhood, Sunday displayed courage, responsibility, and a caring heart, known for standing up for his younger siblings whenever they were bullied &mdash; a trait that reflected the protective and generous spirit that would define his life.</p>
                <p>He began his education at St. Paul Primary School, Olle-Bunu, Kogi State, and later attended St. Barnabas Secondary School, Kabba. From an early age, he developed a passion for entrepreneurship and automobile engineering, laying the foundation for a remarkable career in business.</p>
                <p>Through diligence, determination, and hard work, Sunday established SunBell, a registered company specializing in vehicle maintenance and repairs. He also ventured into automobile dealership, earning the respect of colleagues, customers, and business associates alike. His leadership qualities led to his emergence as Chairman of NATO, Jikwoyi, Abuja.</p>
                <p>Sunday was married to Victoria Bello, and together they built a loving family blessed with five children. Their home was further enriched by their adopted daughter, whom he lovingly raised and sponsored through university. He was also a proud grandfather of two grandchildren.</p>
                <p>A devoted Christian, Sunday worshipped at The Redeemed Christian Church of God, Praise Assembly Parish, Karu, Abuja. According to his pastor, he was a born-again believer, a committed supporter of kingdom projects, and a cheerful giver whose generosity touched many lives.</p>
                <p>Hard work and generosity were the hallmarks of Sunday's life. He was always willing to lend a helping hand to those in need, earning the love and respect of family, friends, neighbours, church members, and colleagues. To many, he was not only a successful businessman but also a mentor, an encourager, and a man who believed that true success was measured by the lives one was able to uplift.</p>
                <p>After a courageous battle with illness, Sunday peacefully went to be with the Lord on Wednesday, 24 June 2026, at the age of 52. He is survived by his beloved wife, Victoria; five children; his adopted children; two grandchildren; six siblings; and many relatives, friends, church members, and associates whose lives were enriched by his generosity and kindness.</p>
                <p>Sleep on, Sunday Makatarehi Bello. Your labour was not in vain, your kindness will not be forgotten, and your memory will remain a blessing to generations yet unborn.</p>
            </div>
        </section>

        <hr class="section-divider">

        <!-- FAVORITES -->
        <section id="favorites" class="page-section">
            <h2 class="section-title">Favorites</h2>
            <div class="favorites-grid">
                <div class="fav-card">
                    <h3 class="fav-title">Favorite Saying</h3>
                    <p class="fav-desc">&ldquo;Live and let live.&rdquo; &middot; &ldquo;You're never too old to learn.&rdquo;</p>
                </div>
                <div class="fav-card">
                    <h3 class="fav-title">Favorite Book</h3>
                    <p class="fav-desc">A timeless classic he re-read every few years.</p>
                </div>
                <div class="fav-card">
                    <h3 class="fav-title">Favorite Movie</h3>
                    <p class="fav-desc">Anything that made the whole family laugh together.</p>
                </div>
                <div class="fav-card">
                    <h3 class="fav-title">Favorite Travel Destination</h3>
                    <p class="fav-desc">The coast &mdash; any coast, any season.</p>
                </div>
                <div class="fav-card">
                    <h3 class="fav-title">Favorite Color</h3>
                    <p class="fav-desc">Soft, pale blues and greens.</p>
                </div>
                <div class="fav-card">
                    <h3 class="fav-title">Fun Fact</h3>
                    <p class="fav-desc">Could never resist a bad pun.</p>
                </div>
            </div>
        </section>

        <hr class="section-divider">

        <!-- LIFE TIMELINE -->
        <section id="timeline" class="page-section">
            <h2 class="section-title">Life Timeline</h2>
            <div class="timeline-container">
                <div class="timeline-item">
                    <div class="timeline-date"><span class="year">1974</span><span class="day">April 14</span></div>
                    <div class="timeline-content">
                        <h3>Born in Lokoja, Kogi State</h3>
                        <p>Second child and first son in a family of nine, to Pa Aliu Owuda Bello and Mrs. Esther Adetutu Aliu.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date"><span class="year">Childhood</span><span class="day">Kogi State</span></div>
                    <div class="timeline-content">
                        <h3>A Protective Spirit</h3>
                        <p>Known for standing up for his younger siblings, showing the courage and generosity that would define his life.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date"><span class="year">Education</span><span class="day">Kogi State</span></div>
                    <div class="timeline-content">
                        <h3>St. Paul &amp; St. Barnabas Schools</h3>
                        <p>Attended St. Paul Primary School, Olle-Bunu, then St. Barnabas Secondary School, Kabba &mdash; where an early passion for entrepreneurship and automobile engineering took root.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date"><span class="year">SunBell</span><span class="day">Abuja</span></div>
                    <div class="timeline-content">
                        <h3>Founded SunBell</h3>
                        <p>Built a registered company specializing in vehicle maintenance, repairs, and automobile dealership through diligence and hard work.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date"><span class="year">Family</span><span class="day">Abuja</span></div>
                    <div class="timeline-content">
                        <h3>Marriage &amp; Family</h3>
                        <p>Married Victoria Bello; together they raised five children and an adopted daughter, later welcoming two grandchildren.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date"><span class="year">Leadership</span><span class="day">Jikwoyi, Abuja</span></div>
                    <div class="timeline-content">
                        <h3>Chairman of NATO, Jikwoyi</h3>
                        <p>His leadership qualities and standing among colleagues led to his emergence as Chairman of NATO, Jikwoyi, Abuja.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date"><span class="year">2026</span><span class="day">June 24</span></div>
                    <div class="timeline-content">
                        <h3>Passed Away Peacefully</h3>
                        <p>After a courageous battle with illness, Sunday went to be with the Lord at the age of 52, surrounded by the legacy of a life well lived.</p>
                    </div>
                </div>
            </div>
        </section>

        <hr class="section-divider">

        <!-- GALLERY TEASER -->
        <section id="gallery-preview" class="page-section">
            <h2 class="section-title">Gallery</h2>
            <p class="tributes-intro">A glimpse of the moments shared so far.</p>
            <?php if (!$galleryTeasers): ?>
                <p>No photos shared yet &mdash; visit the gallery to be the first.</p>
            <?php else: ?>
                <div class="fluid-gallery" id="gallery-preview-grid">
                    <?php foreach ($galleryTeasers as $i => $m): ?>
                        <?= render_gallery_item($m, $heightClasses[$i % count($heightClasses)]) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <a class="view-all-link" href="gallery.php">View the full gallery &rarr;</a>
        </section>

        <hr class="section-divider">

        <!-- TRIBUTES TEASER -->
        <section id="tributes-preview" class="page-section">
            <h2 class="section-title">Wall of Remembrance</h2>
            <p class="tributes-intro">&ldquo;To live in the hearts we leave behind is not to die.&rdquo;</p>

            <?php if (!$tributeTeasers): ?>
                <p class="tributes-intro">Be the first to leave a tribute.</p>
            <?php else: ?>
                <div class="tribute-flow">
                    <?php foreach ($tributeTeasers as $t): ?>
                        <?= render_tribute_flow_card($t, $tributeTeaserMediaById[$t['id']] ?? []) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <a class="view-all-link" href="tributes.php#add-tribute">Read all tributes &amp; share your own &rarr;</a>
        </section>

        <hr class="section-divider">

        <!-- FAMILY TREE -->
        <section id="tree" class="page-section">
            <h2 class="section-title">Family Tree</h2>
            <div class="family-tree">
                <div class="tree-row">
                    <div class="tree-node">
                        <div class="tree-avatar-placeholder" role="img" aria-label="Photo placeholder"></div>
                        <span class="tree-name">Pa Aliu Owuda Bello</span>
                    </div>
                    <div class="tree-node">
                        <div class="tree-avatar-placeholder" role="img" aria-label="Photo placeholder"></div>
                        <span class="tree-name">Mrs. Esther Adetutu Aliu</span>
                    </div>
                </div>
                <div class="tree-row">
                    <div class="tree-node">
                        <div class="tree-avatar-placeholder" role="img" aria-label="Photo placeholder"></div>
                        <span class="tree-name">8 Siblings</span>
                    </div>
                    <div class="tree-node tree-node--self">
                        <div class="tree-avatar-placeholder" role="img" aria-label="Portrait photo placeholder"></div>
                        <span class="tree-name">Sunday Makatarehi Bello</span>
                    </div>
                    <div class="tree-node">
                        <div class="tree-avatar-placeholder" role="img" aria-label="Photo placeholder"></div>
                        <span class="tree-name">Victoria Bello</span>
                    </div>
                </div>
                <div class="tree-row">
                    <div class="tree-node">
                        <div class="tree-avatar-placeholder" role="img" aria-label="Photo placeholder"></div>
                        <span class="tree-name">Niyi (Sunday Omoniyi)</span>
                    </div>
                    <div class="tree-node">
                        <div class="tree-avatar-placeholder" role="img" aria-label="Photo placeholder"></div>
                        <span class="tree-name">Matthew</span>
                    </div>
                    <div class="tree-node">
                        <div class="tree-avatar-placeholder" role="img" aria-label="Photo placeholder"></div>
                        <span class="tree-name">Adopted Daughter</span>
                    </div>
                    <div class="tree-node">
                        <div class="tree-avatar-placeholder" role="img" aria-label="Photo placeholder"></div>
                        <span class="tree-name">3 More Children</span>
                    </div>
                </div>
                <div class="tree-row">
                    <div class="tree-node">
                        <div class="tree-avatar-placeholder" role="img" aria-label="Photo placeholder"></div>
                        <span class="tree-name">Matthew Mitchelle</span>
                    </div>
                    <div class="tree-node">
                        <div class="tree-avatar-placeholder" role="img" aria-label="Photo placeholder"></div>
                        <span class="tree-name">Adetunla Tiwa Tayo</span>
                    </div>
                </div>
            </div>
        </section>

<?php require __DIR__ . '/includes/footer.php'; ?>

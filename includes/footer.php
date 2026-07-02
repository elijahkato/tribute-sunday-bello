    </main>

    <?php $programPath = __DIR__ . '/../assets/program.pdf'; ?>

    <!-- FOOTER / SERVICE DETAILS -->
    <footer id="service" class="service-footer">
        <div class="content-wrapper">
            <h2 class="section-title">Service</h2>
            <div class="service-grid">
                <iframe class="map-embed" title="Map to Gudu Cemetery, Abuja" src="https://www.google.com/maps?q=Gudu+Cemetery,+Abuja,+Nigeria&output=embed" loading="lazy" allowfullscreen></iframe>
                <div class="service-details">
                    <p>Please join the family in paying a last tribute and celebrating a life well lived.</p>
                    <dl>
                        <dt>Wake Keep</dt>
                        <dd>Friday, 3rd July 2026 &middot; 4:00 PM &middot; His Residence</dd>
                        <dt>Interment</dt>
                        <dd>Saturday, 4th July 2026 &middot; 10:00 AM &middot; Gudu Cemetery, Abuja</dd>
                    </dl>
                    <div class="service-actions">
                        <a class="submit-btn" href="https://www.google.com/maps/dir/?api=1&destination=Gudu+Cemetery,+Abuja,+Nigeria" target="_blank" rel="noopener">Get Directions</a>
                        <?php if (file_exists($programPath)): ?>
                            <a class="submit-btn submit-btn--outline" href="assets/program.pdf" download>Download Program</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <p class="footer-credit">In loving memory &mdash; crafted with care by family and friends.</p>
        </div>
    </footer>

    <!-- LIGHTBOX: full-size photo/video/YouTube viewer shared by the Gallery pages -->
    <div class="lightbox" id="lightbox" aria-hidden="true">
        <button type="button" class="lightbox-close" aria-label="Close">&times;</button>
        <button type="button" class="lightbox-nav lightbox-prev" aria-label="Previous item">&#8249;</button>
        <img class="lightbox-image" id="lightboxImage" src="" alt="">
        <video class="lightbox-video" id="lightboxVideo" controls></video>
        <iframe class="lightbox-iframe" id="lightboxIframe" src="" title="YouTube video" frameborder="0" allowfullscreen allow="autoplay; encrypted-media"></iframe>
        <button type="button" class="lightbox-nav lightbox-next" aria-label="Next item">&#8250;</button>
    </div>

    <script src="script.js"></script>
</body>
</html>

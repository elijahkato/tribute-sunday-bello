// Tribute search: live-filter the tribute flow cards by submitter name.
const tributeSearch = document.getElementById("tribute-search");
const tributeFlow = document.getElementById("tribute-flow");

if (tributeSearch && tributeFlow) {
  const cards = Array.from(tributeFlow.querySelectorAll(".tribute-flow-card"));
  const emptyMessage = document.getElementById("tribute-search-empty");

  tributeSearch.addEventListener("input", () => {
    const query = tributeSearch.value.trim().toLowerCase();
    let visibleCount = 0;

    cards.forEach((card) => {
      const matches = !query || card.dataset.name.includes(query);
      card.classList.toggle("is-hidden", !matches);
      if (matches) visibleCount++;
    });

    if (emptyMessage) emptyMessage.style.display = visibleCount ? "none" : "block";
  });
}

// Gallery filtering (photo vs video), scoped to the gallery section.
function wireGalleryFilters(sectionSelector) {
  const section = document.querySelector(sectionSelector);
  if (!section) return;

  const filterButtons = section.querySelectorAll(".filter-btn");
  const galleryItems = section.querySelectorAll(".gallery-item");

  filterButtons.forEach((button) => {
    button.addEventListener("click", () => {
      filterButtons.forEach((btn) => btn.classList.remove("active"));
      button.classList.add("active");

      const filter = button.dataset.filter;
      galleryItems.forEach((item) => {
        const itemValue = item.dataset.category || item.dataset.filterType;
        const matches = filter === "all" || itemValue === filter;
        item.classList.toggle("is-hidden", !matches);
      });
    });
  });
}

wireGalleryFilters("#gallery");

// Show chosen file name next to the upload button
function wireFileNameDisplay(inputId, labelId) {
  const input = document.getElementById(inputId);
  const label = document.getElementById(labelId);
  if (!input || !label) return;

  input.addEventListener("change", () => {
    label.textContent = input.files.length ? input.files[0].name : "No file chosen";
  });
}

wireFileNameDisplay("media-upload", "file-name");

// Tribute form: toggle between file upload and YouTube link, and do a quick
// client-side sanity check on the YouTube URL before submit (the real
// validation happens server-side in submit_tribute.php).
const tributeForm = document.querySelector(".tribute-form");

if (tributeForm) {
  const sourceRadios = tributeForm.querySelectorAll('input[name="source"]');
  const fileGroup = document.getElementById("tribute-file-group");
  const youtubeGroup = document.getElementById("tribute-youtube-group");
  const youtubeInput = document.getElementById("tribute-youtube");

  sourceRadios.forEach((radio) => {
    radio.addEventListener("change", () => {
      const isYoutube = radio.value === "youtube" && radio.checked;
      if (fileGroup) fileGroup.style.display = isYoutube ? "none" : "flex";
      if (youtubeGroup) youtubeGroup.style.display = isYoutube ? "block" : "none";
    });
  });

  tributeForm.addEventListener("submit", (e) => {
    const usingYoutube = tributeForm.querySelector('input[name="source"]:checked')?.value === "youtube";
    if (usingYoutube && youtubeInput) {
      const ytPattern = /(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/;
      if (youtubeInput.value.trim() && !ytPattern.test(youtubeInput.value.trim())) {
        e.preventDefault();
        alert("Please paste a valid YouTube link.");
      }
    }
  });
}

// Lightbox: click any gallery tile (photo, video, or YouTube thumbnail) to
// view it full-size without leaving the page. Photos show inline; videos and
// YouTube links only start loading/playing once opened here, with smooth
// fade/scale transitions and next/prev navigation between the other
// (currently visible) items in that same gallery.
const lightbox = document.getElementById("lightbox");
const lightboxImage = document.getElementById("lightboxImage");
const lightboxVideo = document.getElementById("lightboxVideo");
const lightboxIframe = document.getElementById("lightboxIframe");

if (lightbox && lightboxImage && lightboxVideo && lightboxIframe) {
  const closeBtn = lightbox.querySelector(".lightbox-close");
  const prevBtn = lightbox.querySelector(".lightbox-prev");
  const nextBtn = lightbox.querySelector(".lightbox-next");
  const mediaEls = [lightboxImage, lightboxVideo, lightboxIframe];

  let currentItems = [];
  let currentIndex = 0;

  function showPhoto(index) {
    currentIndex = (index + currentItems.length) % currentItems.length;
    const item = currentItems[currentIndex];
    const type = item.dataset.mediaType;

    mediaEls.forEach((el) => {
      el.classList.remove("is-visible");
      el.style.display = "none";
    });
    lightboxVideo.pause();
    lightboxIframe.src = "";

    if (type === "photo") {
      lightboxImage.src = item.dataset.src;
      lightboxImage.alt = "";
      lightboxImage.style.display = "block";
      requestAnimationFrame(() => lightboxImage.classList.add("is-visible"));
    } else if (type === "video_file") {
      lightboxVideo.src = item.dataset.src;
      lightboxVideo.style.display = "block";
      requestAnimationFrame(() => lightboxVideo.classList.add("is-visible"));
    } else if (type === "youtube") {
      lightboxIframe.src = "https://www.youtube-nocookie.com/embed/" + item.dataset.youtube + "?autoplay=1";
      lightboxIframe.style.display = "block";
      requestAnimationFrame(() => lightboxIframe.classList.add("is-visible"));
    }

    const hasMultiple = currentItems.length > 1;
    prevBtn.classList.toggle("is-hidden-nav", !hasMultiple);
    nextBtn.classList.toggle("is-hidden-nav", !hasMultiple);
  }

  function openLightbox(clickedItem, sectionItems) {
    currentItems = sectionItems;
    lightbox.classList.add("is-open");
    lightbox.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
    showPhoto(currentItems.indexOf(clickedItem));
  }

  function closeLightbox() {
    lightbox.classList.remove("is-open");
    lightbox.setAttribute("aria-hidden", "true");
    mediaEls.forEach((el) => {
      el.classList.remove("is-visible");
      el.style.display = "none";
    });
    lightboxVideo.pause();
    lightboxVideo.removeAttribute("src");
    lightboxIframe.src = "";
    document.body.style.overflow = "";
  }

  function wireLightboxSection(sectionSelector) {
    const section = document.querySelector(sectionSelector);
    if (!section) return;

    section.querySelectorAll(".gallery-item").forEach((item) => {
      item.addEventListener("click", () => {
        const visibleItems = Array.from(
          section.querySelectorAll(".gallery-item:not(.is-hidden)")
        );
        openLightbox(item, visibleItems);
      });
    });
  }

  wireLightboxSection("#gallery");
  wireLightboxSection("#gallery-preview-grid");
  wireLightboxSection("#tribute-media");

  closeBtn.addEventListener("click", closeLightbox);
  prevBtn.addEventListener("click", () => showPhoto(currentIndex - 1));
  nextBtn.addEventListener("click", () => showPhoto(currentIndex + 1));

  lightbox.addEventListener("click", (e) => {
    if (e.target === lightbox) closeLightbox();
  });

  document.addEventListener("keydown", (e) => {
    if (!lightbox.classList.contains("is-open")) return;
    if (e.key === "Escape") closeLightbox();
    if (e.key === "ArrowLeft") showPhoto(currentIndex - 1);
    if (e.key === "ArrowRight") showPhoto(currentIndex + 1);
  });
}

// Fade in the mini profile row (photo + name) above the sticky nav once
// the hero header scrolls out of view
const heroHeader = document.getElementById("top");
const stickyHeader = document.getElementById("stickyHeader");

if (heroHeader && stickyHeader) {
  const headerObserver = new IntersectionObserver(
    ([entry]) => {
      stickyHeader.classList.toggle("scrolled", !entry.isIntersecting);
    },
    { rootMargin: "-72px 0px 0px 0px" }
  );
  headerObserver.observe(heroHeader);
}

// Fade + rise sections into view as the user scrolls to them
const prefersReducedMotion = window.matchMedia(
  "(prefers-reduced-motion: reduce)"
).matches;

const revealTargets = document.querySelectorAll(
  ".memorial-quote, .section-title, .bio-text, .fav-card, .timeline-item, .gallery-item, .tribute-card, .tribute-flow-card, .upload-box, .tree-row, .service-grid"
);

revealTargets.forEach((el) => el.classList.add("reveal"));

if (!prefersReducedMotion && revealTargets.length) {
  const revealObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("in-view");
          revealObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.15, rootMargin: "0px 0px -60px 0px" }
  );
  revealTargets.forEach((el) => revealObserver.observe(el));
} else {
  revealTargets.forEach((el) => el.classList.add("in-view"));
}

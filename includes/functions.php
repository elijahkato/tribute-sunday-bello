<?php

// Fixed list of relationship options offered on the tribute form, in display order.
const TRIBUTE_RELATIONSHIPS = [
    'WIFE', 'SISTER', 'EXTENDED FAMILY', 'CLOSE FRIEND', 'COLLEAGUE',
    'NEIGHBORHOOD', 'ACQUAINTANCE', 'ATTEND SAME CHURCH', 'BROTHER',
    'SON', 'DAUGHTER', 'UNCLE', 'BUSINESS PARTNER',
];

function h(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Converts a stored relationship value (e.g. "CLOSE FRIEND") into a
 * readable label (e.g. "Close Friend") for display.
 */
function relationship_label(?string $relationship): string {
    return $relationship ? ucwords(strtolower($relationship)) : '';
}

/**
 * Shortens a tribute message for card previews, breaking on a word boundary.
 */
function tribute_excerpt(?string $message, int $limit = 160): string {
    $message = trim($message ?? '');
    if (mb_strlen($message) <= $limit) {
        return $message;
    }
    return rtrim(mb_substr($message, 0, $limit)) . '…';
}

/**
 * Renders one tribute as a clickable preview card for the masonry tribute
 * flow (used on the Tributes page and the home page teaser). Links through
 * to tribute.php for the full message and full-size media.
 */
function render_tribute_flow_card(array $tribute, array $mediaItems): string {
    $html = '<a class="tribute-flow-card" href="tribute.php?id=' . (int) $tribute['id']
        . '" data-name="' . h(mb_strtolower($tribute['name'])) . '">';

    if ($mediaItems) {
        $first = $mediaItems[0];
        $html .= '<div class="tribute-flow-media">';
        if ($first['type'] === 'photo') {
            $html .= '<img src="uploads/' . h($first['file_path']) . '" alt="" loading="lazy">';
        } elseif ($first['type'] === 'video_file') {
            $html .= '<video muted preload="metadata" src="uploads/' . h($first['file_path']) . '"></video><span class="play-icon" aria-hidden="true"></span>';
        } else {
            $html .= '<img src="https://img.youtube.com/vi/' . h($first['youtube_id']) . '/mqdefault.jpg" alt="" loading="lazy"><span class="play-icon" aria-hidden="true"></span>';
        }
        if (count($mediaItems) > 1) {
            $html .= '<span class="tribute-flow-more">+' . (count($mediaItems) - 1) . '</span>';
        }
        $html .= '</div>';
    }

    $excerpt = tribute_excerpt($tribute['message'] ?? '');
    $html .= '<div class="tribute-flow-body">';
    if ($excerpt !== '') {
        $html .= '<p class="tribute-flow-excerpt">' . nl2br(h($excerpt)) . '</p>';
    } else {
        $html .= '<p class="tribute-flow-excerpt tribute-flow-excerpt--media">Shared a memory</p>';
    }
    $relationship = relationship_label($tribute['relationship'] ?? null);
    $html .= '<p class="tribute-flow-meta">' . h($tribute['name'])
        . ($relationship !== '' ? ' <span class="tribute-flow-relationship">' . h($relationship) . '</span>' : '')
        . ' &middot; ' . h(date('M j, Y', strtotime($tribute['created_at']))) . '</p>';
    $html .= '</div></a>';

    return $html;
}

/**
 * Renders one photo/video/YouTube row from the `media` table as a masonry
 * gallery tile. Every type renders as a clickable thumbnail (a poster frame
 * for videos, a YouTube cover image for links) carrying data-* attributes
 * that the shared lightbox (script.js) reads to play the full item without
 * ever eagerly embedding a video player or iframe in the grid itself.
 */
function render_gallery_item(array $m, string $heightClass = ''): string {
    $isVideo = $m['type'] !== 'photo';
    $classes = 'gallery-item ' . ($isVideo ? 'is-video' : 'is-photo') . ($heightClass !== '' ? ' ' . $heightClass : '');

    $html = '<div class="' . $classes . '" data-filter-type="' . ($isVideo ? 'video' : 'photo') . '" data-media-type="' . h($m['type']) . '"';

    if ($m['type'] === 'photo') {
        $html .= ' data-src="uploads/' . h($m['file_path']) . '">';
        $html .= '<img src="uploads/' . h($m['file_path']) . '" alt="' . h($m['caption'] ?? 'Shared memory') . '" loading="lazy">';
    } elseif ($m['type'] === 'video_file') {
        $html .= ' data-src="uploads/' . h($m['file_path']) . '">';
        $html .= '<div class="gallery-thumb-video"><video muted preload="metadata" src="uploads/' . h($m['file_path']) . '"></video><span class="play-icon" aria-hidden="true"></span></div>';
    } else {
        $html .= ' data-youtube="' . h($m['youtube_id']) . '">';
        $html .= '<div class="gallery-thumb-video"><img src="https://img.youtube.com/vi/' . h($m['youtube_id']) . '/hqdefault.jpg" alt="YouTube video" loading="lazy"><span class="play-icon" aria-hidden="true"></span></div>';
    }

    if (!empty($m['caption'])) {
        $html .= '<span class="memory-caption">' . h($m['caption']) . '</span>';
    }

    $html .= '</div>';

    return $html;
}

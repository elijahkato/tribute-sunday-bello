<?php
// ============================================================
// Site configuration TEMPLATE.
//
// Copy this file to config.php (which is gitignored and never
// committed) and fill in real values there. Never put real
// credentials in this file — it's the one that stays in git.
// ============================================================

// --- Database connection ---
// Local dev (Laragon/XAMPP) defaults shown; replace with the values
// Hostinger's hPanel gives you when you create the database.
define('DB_HOST', 'localhost');
define('DB_NAME', 'sunday');
define('DB_USER', 'root');
define('DB_PASS', '');

// --- Admin login ---
// Change the username to whatever you like — it's not secret, just an identifier.
define('ADMIN_USERNAME', 'admin');

// Generate a hash for your chosen password by running this once
// (in a terminal, or a scratch .php file you delete afterwards):
//   php -r "echo password_hash('yourpassword', PASSWORD_DEFAULT);"
// Then paste the output below. Never store the plain password here.
define('ADMIN_PASSWORD_HASH', '$2y$10$replace/this/with/a/real/generated/hash.......');

// --- Upload limits ---
define('MAX_PHOTO_BYTES', 10 * 1024 * 1024);   // 10 MB
define('MAX_VIDEO_BYTES', 150 * 1024 * 1024);  // 150 MB
define('UPLOAD_DIR', __DIR__ . '/uploads');

define('ALLOWED_PHOTO_EXT', ['jpg', 'jpeg', 'png', 'webp']);
define('ALLOWED_VIDEO_EXT', ['mp4', 'mov', 'webm']);
define('ALLOWED_PHOTO_MIME', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_VIDEO_MIME', ['video/mp4', 'video/quicktime', 'video/webm']);

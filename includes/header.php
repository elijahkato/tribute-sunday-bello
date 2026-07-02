<?php
// Shared page shell: <head>, hero banner, and sticky nav.
// Included by index.php, gallery.php, and tributes.php.
// Expects an optional $pageTitle string to be set before including this file.
$pageTitle = $pageTitle ?? 'In Loving Memory';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- HERO -->
    <header class="hero" id="top">
        <button type="button" class="hero-share" aria-label="Share this page">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M18 6m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M18 18m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M8.7 10.7l6.6 -3.4" /><path d="M8.7 13.3l6.6 3.4" /></svg>
        </button>
        <div class="hero-inner">
            <div class="hero-text">
                <p class="hero-eyebrow">In Loving Memory</p>
                <h1 class="hero-name">Sunday Makatarehi Bello</h1>
                <p class="hero-dates">14 April 1974 &ndash; 24 June 2026</p>
                <p class="hero-intro">We gather to celebrate a life fully lived &mdash; a husband, father, mentor, and friend who touched every soul he met.</p>
                <div class="hero-actions">
                    <a class="hero-btn hero-btn--primary" href="tributes.php">Leave a Tribute &rarr;</a>
                    <a class="hero-btn hero-btn--outline" href="index.php#biography">Read Biography</a>
                </div>
            </div>
            <div class="hero-photo-frame">
                <img class="hero-photo" src="images/profilepix.png" alt="Portrait of Sunday Makatarehi Bello">
            </div>
        </div>
    </header>

    <!-- STICKY HEADER: mini profile row + icon navigation row -->
    <div class="sticky-header" id="stickyHeader">
        <div class="mini-profile">
            <img class="mini-avatar-placeholder" src="images/profilepix.png" alt="Portrait photo">
            <span class="mini-name">Sunday Makatarehi Bello</span>
        </div>
        <nav class="sticky-nav" aria-label="Section navigation">
            <a href="index.php#biography">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M10 19h-6a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1h6a2 2 0 0 1 2 2a2 2 0 0 1 2 -2h6a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-6a2 2 0 0 0 -2 2a2 2 0 0 0 -2 -2z" /><path d="M12 5v16" /><path d="M7 7h1" /><path d="M7 11h1" /><path d="M16 7h1" /><path d="M16 11h1" /><path d="M16 15h1" /></svg>
                <span>Biography</span>
            </a>
            <a href="index.php#timeline">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 20m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M10 20h-6" /><path d="M14 20h6" /><path d="M12 15l-2 -2h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h10a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-3l-2 2z" /></svg>
                <span>Timeline</span>
            </a>
            <a href="gallery.php">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M15 8h.01" /><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" /></svg>
                <span>Gallery</span>
            </a>
            <a href="tributes.php">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8 9h8" /><path d="M8 13h6" /><path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" /></svg>
                <span>Tributes</span>
            </a>
            <a href="index.php#tree">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6 20a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" /><path d="M16 4a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" /><path d="M16 20a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" /><path d="M11 12a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" /><path d="M21 12a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z" /><path d="M5.058 18.306l2.88 -4.606" /><path d="M10.061 10.303l2.877 -4.604" /><path d="M10.065 13.705l2.876 4.6" /><path d="M15.063 5.7l2.881 4.61" /></svg>
                <span>Family Tree</span>
            </a>
            <a href="index.php#service">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" /><path d="M16 3v4" /><path d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z" /><path d="M19 18v.01" /><path d="M8 3v4" /><path d="M4 11h16" /></svg>
                <span>Service</span>
            </a>
        </nav>
    </div>

    <main class="content-wrapper">

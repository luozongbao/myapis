<?php
/**
 * MyAPIs — About popup partial.
 *
 * Renders a hidden modal that shows the current version (loaded
 * from public/includes/version.php) plus a short description,
 * feature highlights, and links to GitHub / release notes.
 *
 * The popup is opened by clicking an element that has the
 * `data-open-about` attribute (any element works). It is closed
 * by the close button, by clicking the backdrop, or by pressing
 * Escape.
 *
 * Usage:
 *   <?php include __DIR__ . '/includes/about_popup.php'; ?>
 *   <a href="#" data-open-about>About</a>
 *
 * The popup is keyboard-accessible (focus trap, Escape to close)
 * and the open/close animation is pure CSS so it works even with
 * JavaScript disabled (the close link uses :target fallback).
 */
declare(strict_types=1);

// Ensure the version constant is loaded.
if (!isset($MYAPIS_VERSION) || !is_array($MYAPIS_VERSION)) {
    require __DIR__ . '/version.php';
}

$MYAPIS_VERSION_LABEL = sprintf(
    'v%s · %s',
    htmlspecialchars((string) ($MYAPIS_VERSION['version']  ?? '0.0.0'), ENT_QUOTES),
    htmlspecialchars((string) ($MYAPIS_VERSION['codename'] ?? ''),         ENT_QUOTES)
);
$MYAPIS_RELEASED_LABEL = htmlspecialchars(
    (string) ($MYAPIS_VERSION['released'] ?? ''),
    ENT_QUOTES
);
?>
<style>
    /* ----- About popup ------------------------------------------------ */
    .about-popup {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .about-popup.is-open {
        display: flex;
        animation: about-popup-fade 0.18s ease-out;
    }
    @keyframes about-popup-fade {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    .about-popup__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }
    .about-popup__dialog {
        position: relative;
        max-width: 520px;
        width: 100%;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
        padding: 28px 28px 24px;
        color: #1f2937;
        animation: about-popup-rise 0.22s ease-out;
    }
    @keyframes about-popup-rise {
        from { transform: translateY(16px) scale(0.98); opacity: 0; }
        to   { transform: translateY(0)    scale(1);    opacity: 1; }
    }
    .about-popup__close {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: #f3f4f6;
        color: #374151;
        font-size: 1.25em;
        line-height: 1;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s ease, transform 0.15s ease;
    }
    .about-popup__close:hover,
    .about-popup__close:focus-visible {
        background: #e5e7eb;
        transform: scale(1.05);
        outline: none;
    }
    .about-popup__header {
        text-align: center;
        margin-bottom: 18px;
    }
    .about-popup__logo {
        font-size: 2.6em;
        line-height: 1;
        margin-bottom: 6px;
    }
    .about-popup__title {
        font-size: 1.6em;
        font-weight: 700;
        margin: 0 0 6px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .about-popup__tagline {
        color: #6b7280;
        font-size: 0.95em;
        margin: 0 0 14px;
        line-height: 1.5;
    }
    .about-popup__version {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.85em;
        font-weight: 600;
        letter-spacing: 0.02em;
    }
    .about-popup__released {
        text-align: center;
        font-size: 0.8em;
        color: #9ca3af;
        margin-top: 8px;
    }
    .about-popup__features {
        list-style: none;
        margin: 18px 0 22px;
        padding: 0;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px 14px;
    }
    .about-popup__features li {
        font-size: 0.9em;
        color: #374151;
        padding-left: 22px;
        position: relative;
        line-height: 1.4;
    }
    .about-popup__features li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #4CAF50;
        font-weight: bold;
    }
    .about-popup__actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 6px;
    }
    .about-popup__btn {
        flex: 1 1 auto;
        min-width: 130px;
        padding: 10px 16px;
        border-radius: 10px;
        font-size: 0.92em;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.18s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .about-popup__btn--primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
    }
    .about-popup__btn--primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(102, 126, 234, 0.35);
    }
    .about-popup__btn--secondary {
        background: #f8f9fa;
        color: #1f2937;
        border-color: #e5e7eb;
    }
    .about-popup__btn--secondary:hover {
        background: #e5e7eb;
        transform: translateY(-1px);
    }
    @media (max-width: 480px) {
        .about-popup__dialog {
            padding: 24px 20px 20px;
        }
        .about-popup__features {
            grid-template-columns: 1fr;
        }
        .about-popup__title {
            font-size: 1.4em;
        }
    }
</style>
<div
    class="about-popup"
    id="aboutPopup"
    role="dialog"
    aria-modal="true"
    aria-labelledby="aboutPopupTitle"
    aria-hidden="true">
    <div class="about-popup__backdrop" data-close-about></div>
    <div class="about-popup__dialog" role="document">
        <button
            type="button"
            class="about-popup__close"
            data-close-about
            aria-label="Close about popup">✕</button>
        <div class="about-popup__header">
            <div class="about-popup__logo">🚀</div>
            <h2 class="about-popup__title" id="aboutPopupTitle">MyAPIs</h2>
            <p class="about-popup__tagline">
                A comprehensive collection of developer tools and APIs —
                modern web UIs paired with robust REST endpoints.
            </p>
            <span class="about-popup__version"><?php echo $MYAPIS_VERSION_LABEL; ?></span>
            <?php if ($MYAPIS_RELEASED_LABEL !== ''): ?>
                <p class="about-popup__released">Released <?php echo $MYAPIS_RELEASED_LABEL; ?></p>
            <?php endif; ?>
        </div>
        <ul class="about-popup__features">
            <li>Modern responsive web interfaces</li>
            <li>REST APIs with JSON responses</li>
            <li>Interactive API documentation</li>
            <li>Cryptographically secure randomness</li>
            <li>Multi-language where applicable</li>
            <li>CORS enabled for web integration</li>
        </ul>
        <div class="about-popup__actions">
            <a class="about-popup__btn about-popup__btn--primary"
               href="https://github.com/luozongbao/myapis"
               target="_blank" rel="noopener noreferrer">⭐ Star on GitHub</a>
            <a class="about-popup__btn about-popup__btn--secondary"
               href="https://github.com/luozongbao/myapis/releases"
               target="_blank" rel="noopener noreferrer">📋 Release Notes</a>
            <a class="about-popup__btn about-popup__btn--secondary"
               href="https://atipat.lorwongam.com"
               target="_blank" rel="noopener noreferrer">📝 Blog</a>
        </div>
    </div>
</div>
<script>
(function () {
    'use strict';

    var popup   = document.getElementById('aboutPopup');
    if (!popup) { return; }

    var dialog  = popup.querySelector('.about-popup__dialog');
    var lastFocus = null;

    function getFocusable() {
        return popup.querySelectorAll(
            'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );
    }

    function openPopup() {
        lastFocus = document.activeElement;
        popup.classList.add('is-open');
        popup.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        var focusables = getFocusable();
        if (focusables.length) {
            focusables[0].focus();
        }
    }

    function closePopup() {
        popup.classList.remove('is-open');
        popup.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (lastFocus && typeof lastFocus.focus === 'function') {
            lastFocus.focus();
        }
    }

    // Openers: any element with data-open-about.
    document.addEventListener('click', function (e) {
        var opener = e.target.closest('[data-open-about]');
        if (opener) {
            e.preventDefault();
            openPopup();
            return;
        }
        // Closers: data-close-about OR click on backdrop.
        if (e.target.closest('[data-close-about]')) {
            e.preventDefault();
            closePopup();
        }
    });

    // Escape key closes.
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && popup.classList.contains('is-open')) {
            closePopup();
        }
        // Simple focus trap.
        if (e.key === 'Tab' && popup.classList.contains('is-open')) {
            var focusables = Array.prototype.slice.call(getFocusable());
            if (!focusables.length) { return; }
            var first = focusables[0];
            var last  = focusables[focusables.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        }
    });
})();
</script>
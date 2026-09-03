<?php
/**
 * Shared footer partial.
 *
 * Variables (optional):
 *   $footer_variant = 'glass' (default) | 'simple'
 *     - 'glass'  : translucent card style used on the homepage / gradient backgrounds.
 *     - 'simple' : lighter style for tool pages with their own opaque container.
 */
$footer_variant = $footer_variant ?? 'glass';
?>
<style>
    .site-footer {
        text-align: center;
        margin-top: 40px;
        padding: 24px 20px;
    }
    .site-footer p {
        margin: 0 0 10px 0;
        line-height: 1.5;
    }
    .site-footer .footer-tagline {
        font-size: 1.05em;
        font-weight: 500;
    }
    .site-footer .footer-copyright {
        font-size: 0.9em;
        opacity: 0.85;
    }
    .site-footer .footer-links {
        display: flex;
        justify-content: center;
        gap: 24px;
        flex-wrap: wrap;
        margin-top: 8px;
    }
    .site-footer .footer-links a {
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .site-footer .footer-links a:hover {
        transform: translateY(-2px);
    }
    <?php if ($footer_variant === 'glass'): ?>
    .site-footer {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        backdrop-filter: blur(10px);
    }
    .site-footer p,
    .site-footer .footer-links a {
        color: #ffffff;
    }
    .site-footer .footer-links a:hover {
        color: #ffd700;
    }
    <?php else: ?>
    .site-footer {
        color: #555;
    }
    .site-footer p {
        color: #555;
    }
    .site-footer .footer-links a {
        color: #667eea;
    }
    .site-footer .footer-links a:hover {
        color: #764ba2;
    }
    <?php endif; ?>
    @media (max-width: 600px) {
        .site-footer .footer-links {
            flex-direction: column;
            gap: 12px;
        }
    }
</style>
<footer class="site-footer">
    <p class="footer-tagline">Built with ❤️ for developers by developers</p>
    <p class="footer-copyright">&copy; 2026 Atipat Lorwongam</p>
    <div class="footer-links">
        <a href="#" data-open-about>ℹ️ About</a>
        <a href="https://github.com/luozongbao/myapis" target="_blank" rel="noopener noreferrer">🔗 GitHub</a>
        <a href="https://atipat.lorwongam.com" target="_blank" rel="noopener noreferrer">📝 Blog</a>
    </div>
</footer>
<?php
// About popup (version + info). Renders only once per request.
if (!defined('MYAPIS_ABOUT_POPUP_RENDERED')) {
    define('MYAPIS_ABOUT_POPUP_RENDERED', true);
    include __DIR__ . '/about_popup.php';
}
?>
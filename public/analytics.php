<?php
/**
 * =============================================================
 * MyAPIs - Analytics partial (SHARED-HOSTING EDITION)
 * -------------------------------------------------------------
 * Lives at public/analytics.php and is included directly from
 * every page in public/ and public/api-specs/ with:
 *
 *     <?php require __DIR__ . '/analytics.php'; ?>
 *
 * Reads Umami / Google Analytics configuration from either
 * environment variables (Docker / VPS) or a sibling
 * public/config.php (shared hosting via putenv()).
 *
 * Configuration (any of these works):
 *
 *   1) Environment variables
 *      - ANALYTICS_PROVIDER    "umami" | "ga4" | "none"  (none = default)
 *      - UMAMI_SCRIPT_URL      e.g. https://cloud.umami.is/script.js
 *      - UMAMI_WEBSITE_ID      Umami website UUID
 *      - GA4_MEASUREMENT_ID    GA4 ID, e.g. G-XXXXXXXXXX
 *
 *   2) public/config.php (shared hosting — copy from
 *      public/config.php.example and edit your values).
 *
 * When ANALYTICS_PROVIDER is unset / "none", nothing is emitted.
 * The snippet is skipped for CLI invocations and API JSON
 * responses so JSON output is never polluted.
 * =============================================================
 */

if (!defined('MYAPIS_ANALYTICS_INCLUDED')) {
    define('MYAPIS_ANALYTICS_INCLUDED', true);
}

// CLI invocations are never user-facing
if (PHP_SAPI === 'cli') {
    return;
}

// ---------------------------------------------------------------
// Shared-hosting fallback: load sibling config.php (which calls
// putenv()) if env vars are not set. The example file lives at
// public/config.php.example.
//
// Search order (first match wins):
//   ./config.php                       (this directory)
//   ../docker/php/../../public/config.php  (repo checkout)
// ---------------------------------------------------------------
if (!getenv('ANALYTICS_PROVIDER')) {
    $configCandidates = [
        __DIR__ . '/config.php',
        __DIR__ . '/../public/config.php',
        __DIR__ . '/../../public/config.php',
        __DIR__ . '/../config.php',
    ];
    foreach ($configCandidates as $cfg) {
        if (is_file($cfg)) {
            require_once $cfg;
            break;
        }
    }
}

$provider = strtolower(trim((string) (getenv('ANALYTICS_PROVIDER') ?: 'none')));

if ($provider === '' || $provider === 'none' || $provider === 'off' || $provider === 'false') {
    return;
}

// Skip API / JSON responses so we never corrupt JSON output
$accept = $_SERVER['HTTP_ACCEPT']    ?? '';
$uri    = $_SERVER['REQUEST_URI']   ?? '';
if (strpos($uri, '/api/') === 0 || stripos($accept, 'application/json') !== false) {
    return;
}

$html = '';

// ---------------------------------------------------------------
// Umami - self-hosted, cookie-less, works in China
// ---------------------------------------------------------------
if ($provider === 'umami') {
    $scriptUrl = trim((string) getenv('UMAMI_SCRIPT_URL'));
    $websiteId = trim((string) getenv('UMAMI_WEBSITE_ID'));

    if ($scriptUrl !== '' && $websiteId !== '') {
        $scriptUrl = htmlspecialchars($scriptUrl, ENT_QUOTES, 'UTF-8');
        $websiteId = htmlspecialchars($websiteId, ENT_QUOTES, 'UTF-8');

        $html .= "<!-- Umami Analytics -->\n";
        $html .= sprintf(
            '<script async defer data-website-id="%s" src="%s"></script>' . "\n",
            $websiteId,
            $scriptUrl
        );
    }
}

// ---------------------------------------------------------------
// Google Analytics 4 (gtag.js)
// ---------------------------------------------------------------
if ($provider === 'ga4' || $provider === 'google') {
    $measurementId = trim((string) getenv('GA4_MEASUREMENT_ID'));

    if ($measurementId !== '') {
        $measurementId = htmlspecialchars($measurementId, ENT_QUOTES, 'UTF-8');

        $html .= "<!-- Google Analytics 4 -->\n";
        $html .= sprintf(
            '<script async src="https://www.googletagmanager.com/gtag/js?id=%s"></script>' . "\n",
            $measurementId
        );
        $html .= "<script>\n";
        $html .= "  window.dataLayer = window.dataLayer || [];\n";
        $html .= "  function gtag(){dataLayer.push(arguments);}\n";
        $html .= "  gtag('js', new Date());\n";
        $html .= sprintf("  gtag('config', '%s');\n", $measurementId);
        $html .= "</script>\n";
    }
}

if ($html === '') {
    return;
}

echo $html;
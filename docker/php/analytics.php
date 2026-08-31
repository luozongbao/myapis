<?php
/**
 * =============================================================
 * MyAPIs - Analytics partial (auto-prepended to every response)
 * -------------------------------------------------------------
 * Reads Umami / Google Analytics configuration from environment
 * variables and emits the appropriate tracking snippets.
 *
 * Variables:
 *   ANALYTICS_PROVIDER   "umami" | "ga4" | "none"  (default: none)
 *   UMAMI_SCRIPT_URL     Full script URL, e.g.
 *                        https://umami.example.com/script.js
 *   UMAMI_WEBSITE_ID     Umami website UUID
 *   GA4_MEASUREMENT_ID   GA4 measurement ID, e.g. G-XXXXXXXXXX
 *
 * When ANALYTICS_PROVIDER is unset or "none", nothing is emitted.
 * The script is skipped for CLI invocations and API JSON responses
 * (anything whose Accept header asks for JSON).
 * =============================================================
 */

if (PHP_SAPI === 'cli') {
    return;
}

// ---------------------------------------------------------------
// Shared-hosting fallback: if the env vars are not set, try
// loading `public/config.php` (or a `config.php` placed next to
// this file). The example file lives at
// public/config.php.example - copy it to public/config.php and
// edit your values there.
// ---------------------------------------------------------------
if (!getenv('ANALYTICS_PROVIDER')) {
    $configCandidates = [
        __DIR__ . '/../../public/config.php',
        __DIR__ . '/../config.php',
        __DIR__ . '/config.php',
    ];
    foreach ($configCandidates as $cfg) {
        if (is_file($cfg)) {
            require_once $cfg;
            break;
        }
    }
}

$provider = strtolower(getenv('ANALYTICS_PROVIDER') ?: 'none');

if ($provider === 'none' || $provider === '' || $provider === 'off' || $provider === 'false') {
    return;
}

// Skip API / JSON responses so we do not pollute JSON output
$accept = $_SERVER['HTTP_ACCEPT'] ?? '';
$uri    = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($uri, '/api/') === 0 || stripos($accept, 'application/json') !== false) {
    return;
}

$html = '';

// ---------------------------------------------------------------
// Umami - self-hosted, cookie-less, works in China
// ---------------------------------------------------------------
if ($provider === 'umami') {
    $scriptUrl  = getenv('UMAMI_SCRIPT_URL');
    $websiteId  = getenv('UMAMI_WEBSITE_ID');

    if (!empty($scriptUrl) && !empty($websiteId)) {
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
    $measurementId = getenv('GA4_MEASUREMENT_ID');

    if (!empty($measurementId)) {
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

// Output directly to the buffer so we appear inside <head> when the
// script is auto-prepended. Falls back to echoing for non-HTML routes.
echo $html;
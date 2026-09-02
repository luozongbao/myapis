<?php
/**
 * Shared layout for /public/api-specs/*.php documentation pages.
 *
 * Usage at the top of a spec file:
 *
 *   $spec = [
 *       'slug'    => 'fortune-teller',           // URL slug
 *       'title'   => 'Fortune Teller API',       // <title> + <h1>
 *       'tagline' => 'Get multilingual …',       // <p> under <h1>
 *       'crumb'   => 'Fortune Teller',           // breadcrumb label
 *   ];
 *   require __DIR__ . '/../includes/apispec_layout.php';
 *
 * The include:
 *   - Defines getBaseUrl() (used by $spec['slug']) and $baseUrl
 *   - Emits <!DOCTYPE>, <html>, <head>, styles, analytics
 *   - Emits the page header (gradient banner with title + tagline)
 *   - Emits the breadcrumb (← Back to Main / <Crumb> / API Documentation)
 *   - Opens .container > .content
 *
 * The spec file then writes its own sections and closes:
 *
 *       </div><!-- /.content -->
 *   </div><!-- /.container -->
 *   </body></html>
 *
 * Variables exposed to the spec file:
 *   $baseUrl  — fully-qualified base URL (already htmlspecialchars-safe
 *               to embed in HTML; we still escape on output).
 *   $spec     — the array passed by the caller.
 */
declare(strict_types=1);

if (!isset($spec) || !is_array($spec)) {
    http_response_code(500);
    echo 'apispec_layout.php: $spec array is required.';
    exit;
}

foreach (['slug', 'title', 'tagline', 'crumb'] as $required) {
    if (!isset($spec[$required])) {
        http_response_code(500);
        echo "apispec_layout.php: \$spec['{$required}'] is required.";
        exit;
    }
}

/**
 * Build a fully-qualified base URL like https://host/api/<slug>/.
 *
 * Exposed to the spec file in case it needs the URL outside HTML
 * (e.g. building an href to the live tool).
 */
function getBaseUrl(string $toolName): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $protocol . '://' . $host . '/api/' . $toolName . '/';
}

$baseUrl = getBaseUrl($spec['slug']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($spec['title']); ?> Documentation</title>
    <?php require __DIR__ . '/apispec_styles.php'; ?>
    <?php /** MyAPIs Analytics (Hostinger / shared-hosting friendly) */ if (file_exists(__DIR__ . '/../analytics.php')) { require __DIR__ . '/../analytics.php'; } ?>
</head>
<body>
    <div class="container">

        <!-- Header -->
        <div class="header">
            <h1><?php echo htmlspecialchars($spec['title']); ?></h1>
            <p><?php echo htmlspecialchars($spec['tagline']); ?></p>
        </div>

        <!-- Navigation -->
        <div class="nav">
            <div class="breadcrumb">
                <a href="../index.php">← Back to Main</a>
                <span>/</span>
                <a href="../<?php echo htmlspecialchars($spec['slug']); ?>.php"><?php echo htmlspecialchars($spec['crumb']); ?></a>
                <span>/</span>
                <span>API Documentation</span>
            </div>
        </div>

        <!-- Content -->
        <div class="content">

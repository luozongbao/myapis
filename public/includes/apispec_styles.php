<?php
/**
 * Shared stylesheet for /public/api-specs/*.php documentation pages.
 *
 * Each spec file's <head> includes this once.  Tool-specific classes
 * (security-badge, types-grid, theme-list, info-box, warning-box, …)
 * are kept here as well so the CSS lives in a single place.
 *
 * Output: emits a single <style>…</style> block. No echo needed at
 * the call site — the include writes the tag itself.
 */
?>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px;
        line-height: 1.6;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px;
        text-align: center;
    }
    .header h1 { font-size: 2.5em; margin-bottom: 10px; }
    .header p  { font-size: 1.2em; opacity: 0.9; }

    .nav {
        background: #f8f9fa;
        padding: 20px;
        border-bottom: 1px solid #e9ecef;
    }
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.9em;
        color: #666;
        flex-wrap: wrap;
    }
    .breadcrumb a { color: #667eea; text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }

    .content { padding: 40px; }

    .section { margin-bottom: 40px; }
    .section h2 {
        color: #333;
        font-size: 1.8em;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #667eea;
    }
    .section h3 {
        color: #444;
        font-size: 1.3em;
        margin-bottom: 15px;
        margin-top: 25px;
    }
    .section h4 {
        color: #555;
        margin: 15px 0 8px;
    }

    .endpoint {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 4px solid #667eea;
    }

    .method {
        display: inline-block;
        background: #28a745;
        color: white;
        padding: 4px 12px;
        border-radius: 4px;
        font-weight: bold;
        font-size: 0.9em;
        margin-right: 10px;
    }
    .method.post { background: #007bff; }
    .method.get  { background: #28a745; }

    .url {
        font-family: 'Courier New', monospace;
        background: #e9ecef;
        padding: 8px 12px;
        border-radius: 4px;
        display: inline-block;
        margin-left: 10px;
        word-break: break-all;
    }

    .code-block {
        background: #2d3748;
        color: #e2e8f0;
        padding: 20px;
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        font-size: 0.9em;
        overflow-x: auto;
        margin: 15px 0;
        white-space: pre;
    }

    .parameter-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    .parameter-table th,
    .parameter-table td {
        border: 1px solid #dee2e6;
        padding: 12px;
        text-align: left;
    }
    .parameter-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #333;
    }
    .parameter-table td {
        color: #555;
        font-size: 0.92em;
    }
    .parameter-table code {
        background: #e9ecef;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
        font-size: 0.85em;
    }

    .required { color: #dc3545; font-weight: bold; }
    .optional { color: #6c757d; }

    .response-box {
        background: #f0f8f0;
        border: 1px solid #d4edda;
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
    }
    .error-box {
        background: #fdf2f2;
        border: 1px solid #f5c6cb;
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
    }
    .warning-box {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
        color: #856404;
    }
    .info-box {
        background: #d1ecf1;
        border: 1px solid #bee5eb;
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
        color: #0c5460;
    }

    .btn {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: transform 0.2s ease;
    }
    .btn:hover { transform: translateY(-2px); }
    .btn-secondary { background: #6c757d; }

    .try-it {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        text-align: center;
        margin: 30px 0;
    }
    .try-it h3 { margin-bottom: 15px; }
    .try-it p  { margin-bottom: 20px; opacity: 0.9; }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
        margin: 20px 0;
    }
    .feature-card {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        border-left: 4px solid #667eea;
    }
    .feature-card h4 { color: #333; margin-bottom: 10px; }
    .feature-card p  { color: #666; font-size: 0.9em; }

    /* Compact grids used by several tools */
    .lang-grid,
    .types-grid,
    .theme-list,
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin: 20px 0;
    }

    .lang-item,
    .type-item,
    .theme-item,
    .category-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        border: 2px solid #e9ecef;
    }
    .category-item {
        border-left: 4px solid #667eea;
        border-width: 0 0 0 4px;
        text-align: left;
    }
    .lang-item h5,
    .type-item h5,
    .theme-item h5,
    .category-item h6 {
        color: #333;
        margin-bottom: 8px;
    }
    .category-item h6 { margin-bottom: 5px; }
    .lang-item p,
    .type-item p,
    .theme-item p,
    .category-item p {
        color: #666;
        font-size: 0.9em;
    }
    .category-item p { font-size: 0.8em; }

    .security-badge {
        display: inline-block;
        background: #28a745;
        color: white;
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 0.8em;
        font-weight: bold;
        margin: 5px;
    }

    @media (max-width: 768px) {
        .header h1    { font-size: 2em; }
        .content      { padding: 20px; }
        .code-block   { font-size: 0.8em; }
    }
</style>

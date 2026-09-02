<?php
/**
 * =============================================================
 * MyAPIs — Per-tool API security configuration
 * =============================================================
 *
 * Optional file that customises the rate-limit policy for each
 * tool. Included by api/<tool>/index.php via:
 *
 *     $config = require __DIR__ . '/../includes/api_config.php';
 *     RateLimiter::policy('api:password-generator', $config['password-generator']);
 *
 * Each entry controls how aggressively that endpoint is
 * throttled. Values can also be overridden via environment
 * variables (see example.env).
 *
 * Defaults (when this file is absent or an entry is missing)
 * are 60 requests per minute per client identity.
 */

declare(strict_types=1);

return [
    // Lightweight endpoints (randomizer, fortune-teller): cheap to
    // run, so the defaults are generous.
    'randomizer'       => ['limit' => 120, 'window' => 60],

    // Heavy-ish endpoints that touch the filesystem for every hit.
    'fortune-teller'   => ['limit' => 120, 'window' => 60],

    // Endpoints that do cryptographic work — CPU-bound.
    'password-generator' => ['limit' => 60,  'window' => 60],
    'username-generator' => ['limit' => 60,  'window' => 60],

    // Numeric endpoints with no external dependencies.
    'health-calculator'  => ['limit' => 60,  'window' => 60],

    // Endpoints that call out to a third-party (goQR.me) or do
    // heavy image generation (PromptPay). These are the most
    // expensive — and the most likely abuse target.
    'qr-code-generator'   => ['limit' => 30,  'window' => 60],
    'promptpay-qr-generator' => ['limit' => 30, 'window' => 60],
];
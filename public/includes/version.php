<?php
/**
 * MyAPIs — single source of truth for version metadata.
 *
 * The About popup and any other consumer (footer micro-badge,
 * analytics, health endpoint, etc.) should `include` this file
 * rather than hard-coding a version string.
 *
 *   $MYAPIS_VERSION = [
 *       'version'  => '2.6.2',
 *       'codename' => 'Unified Site Footer',
 *       'released' => '2026-09-03',
 *   ];
 *
 * To bump the version, edit this file and (optionally) add a
 * matching entry in RELEASE.md.
 */
declare(strict_types=1);

$MYAPIS_VERSION = [
    'version'  => '2.6.3',
    'codename' => 'About Popup',
    'released' => '2026-09-03',
];
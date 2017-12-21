<?php

if (!defined('ABSPATH')) exit;

try {
    if (file_exists($composer = WPMU_PLUGIN_DIR . '/xel-xwp-plugin/vendor/autoload.php')) {
        require_once $composer;
    }
} catch(\Throwable $t) {}

use Xel\XWP\Plugin;

try { new Plugin(); } catch (\Throwable $t) {}
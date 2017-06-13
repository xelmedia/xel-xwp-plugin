<?php
use Xel\XWP\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit;
if ( file_exists($composer = WPMU_PLUGIN_DIR . '/xel-xwp-plugin/vendor/autoload.php') ) {
    require_once $composer;
}

new Plugin();
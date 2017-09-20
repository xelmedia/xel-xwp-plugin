<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( file_exists($composer = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php') ) {
    require_once $composer;
}

use Xel\XWP\Plugin;

new Plugin();
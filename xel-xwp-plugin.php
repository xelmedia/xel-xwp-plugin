<?php
/*
Plugin Name: Xel Xwp
Plugin URI: https://github.com/xelmedia/xel-xwp-plugin/
Description: Provides REST endpoints for getting information about the database and installed plugins/themes
Version: 1
Author: Mohamed Ahmed, Ramon Schriks, Samy Ascha, Theo van Oostrum
Author URI: http://www.xel.nl
License: http://www.apache.org/licenses/LICENSE-2.0
Text Domain: xel-xwp-plugin
Network: true
Copyright 2017: Xel Media BV (email: samy@xel.nl)
Original Author: Samy Ascha
	This file is part of Xel Xwp plugin, a plugin for WordPress used on sites hosted by Xel Media BV.
	Xel Xwp Plugin is free software: you can redistribute it and/or modify
	it under the terms of the Apache License 2.0 license.
	Xel Xwp Plugin is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if (!defined('ABSPATH')) exit;

try {
    if (file_exists($composer = WPMU_PLUGIN_DIR . '/xel-xwp-plugin/vendor/autoload.php')) {
        require_once $composer;
    }
} catch(\Throwable $t) {}

use Xel\XWP\Plugin;

try { new Plugin(); } catch (\Throwable $t) {}
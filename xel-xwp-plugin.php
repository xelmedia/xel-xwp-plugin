<?php
/*
Plugin Name: xel assistent
Plugin URI: https://github.com/xelmedia/xel-xwp-plugin/
Description: Assisteert ons zodat je optimaal gebruik kan maken van de functionaliteit binnen jouw xel omgeving.
Version: 1
Author: xels
Author URI: https://www.xel.nl
License: http://www.apache.org/licenses/LICENSE-2.0
Text Domain: xel-xwp-plugin
Network: true
Copyright 2017: Xel Media BV (email: info@xel.nl)
Original Author: Samy Ascha
	This file is part of xel assistent plugin, a plugin for WordPress used on sites hosted by Xel Media BV.
	xel assistent is free software: you can redistribute it and/or modify
	it under the terms of the Apache License 2.0 license.
	xel assistent  is distributed in the hope that it will be useful,
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
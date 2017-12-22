<?php
namespace Xel\XWP;


class Util {
    /**
     * Converts a plugin basename back into a friendly slug.
     * @source https://github.com/wp-cli/wp-cli/blob/master/php/utils-wp.php
     * @param $basename of the plugin file
     * @return string
     */
    public static function get_plugin_name($basename) {
        if ( false === strpos($basename, '/' ) )
            $name = basename($basename, '.php' );
        else
            $name = dirname($basename);
        return $name;
    }
}
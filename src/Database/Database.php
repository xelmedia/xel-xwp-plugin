<?php
namespace Xel\XWP\Database;


use Xel\XWP\Domain\WpData;
use Xel\XWP\Util;

class Database implements IDatabase {

    /**
     * Obtains all the postTypes in the database with a few exclusions.
     * @return array
     */
    public static function get_post_types(): array {
        $postTypes = get_post_types('', 'objects');

        $excludePostTypes = ['customize_changeset', 'attachment', 'revision', 'nav_menu_item', 'custom_css'];

        $response = [];
        foreach ($postTypes as $postType) {
            if(!in_array($postType->name, $excludePostTypes)) {
                $response[] =  WpData::builder()
                                ->name($postType->name)
                                ->label($postType->label)
                                ->build();
            }
        }

        return $response;
    }

    /**
     * Obtains all the Wordpress tables, except the WP-core tables that are regulated by the wpcli commands (i.e postTypes)
     * @return array
     */
    public static function get_tables(): array {
        global $wpdb;
        $excludeTables = ['options', 'users', 'usermeta', 'comments', 'commentmeta', 'termmeta', 'terms', 'term_taxonomy', 'term_relationships', 'posts', 'postmeta' ];
        foreach ($excludeTables as $key => $value) {
            $excludeTables[$key] = $wpdb->prefix . $value;
        }

        $tables = $wpdb->get_results("SHOW TABLES");
        $tablesIn = "Tables_in_{$wpdb->dbname}";

        $response = [];
        foreach ($tables as $table) {
            if(!in_array($table->$tablesIn, $excludeTables)) {
                $response[] =  WpData::builder()
                                ->name($table->$tablesIn)
                                ->build();
            }
        }
        return $response;
    }

    /**
     * Obtains all the plugins which are currently disabled.
     * @return array
     */
    public static function get_deactivated_plugins(): array {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();
        $response = [];

        foreach ($all_plugins as $file => $plugin) {
            if(is_plugin_inactive($plugin)) {
                $response[] = WpData::builder()
                                ->name(Util::get_plugin_name($file))
                                ->label($plugin["Name"])
                                ->build();
            }
        }
        return $response;
    }

    /**
     * Obtains all the themes which are currently disabled.
     * @return array
     */
    public static function get_deactivated_themes(): array {
        $wpThemes = wp_get_themes();
        $currentTheme = get_current_theme();
        $response = [];

        foreach ($wpThemes as $theme => $value) {
            if(strcmp($currentTheme, $value["Name"])) {
                $response[] = WpData::builder()
                                ->name($theme)
                                ->label($value["Name"])
                                ->build();
            }
        }
        return $response;
    }

    /**
     * Obtains all the plugins, regardless of whether they are activated or deactivated.
     * @return array
     */
    public static function get_plugins(): array {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();
        $response = [];

        foreach ($all_plugins as $plugin => $value) {
            $response[] = WpData::builder()
                            ->name(Util::get_plugin_name($plugin))
                            ->label($value["Name"])
                            ->build();
        }
        return $response;
    }

    /**
     * Obtains all the themes, regardless of whether they are activated or deactivated.
     * @return array
     */
    public static function get_themes(): array  {
        $wpThemes = wp_get_themes();
        $response = [];
        foreach ($wpThemes as $theme => $value) {
            $response[] = WpData::builder()
                            ->name($theme)
                            ->label($value["Name"])
                            ->build();
        }
        return $response;
    }
}
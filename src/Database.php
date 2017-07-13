<?php
namespace Xel\XWP;


class Database {

    /**
     * Obtains all the postTypes in the database with a few exclusions.
     * @return array
     */
    public static function get_post_types() {
        $postTypes = get_post_types('', 'objects');

        $excludePostTypes = ['customize_changeset', 'attachment', 'revision', 'nav_menu_item', 'custom_css'];

        $postTypesList = [];
        foreach ($postTypes as $postType) {
            if(!in_array($postType->name, $excludePostTypes)) {
                $postTypesList[] = [
                    "label" => $postType->label,
                    "name" => $postType->name,
                ];
            }
        }

        return $postTypesList;
    }

    /**
     * Obtains all the Wordpress tables, except the WP-core tables that are regulated by the wpcli commands (i.e postTypes)
     * @return array
     */
    public static function get_tables() {
        global $wpdb;
        $excludeTables = ['options', 'users', 'usermeta', 'comments', 'commentmeta', 'termmeta', 'terms', 'term_taxonomy', 'term_relationships', 'posts', 'postmeta' ];
        foreach ($excludeTables as $key => $value) {
            $excludeTables[$key] = $wpdb->prefix . $value;
        }

        $tables = $wpdb->get_results("SHOW TABLES");
        $tablesIn = "Tables_in_{$wpdb->dbname}";

        $tableList = [];
        foreach ($tables as $table) {
            if(!in_array($table->$tablesIn, $excludeTables)) {
                $tableList[] = [
                    "name" => $table->$tablesIn
                ];
            }
        }
        return $tableList;
    }

    /**
     * Obtains all the plugins which are currently disabled.
     * @return array
     */
    public static function get_deactivated_plugins() {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();
        $deactivatedPlugins = [];

        foreach ($all_plugins as $file => $plugin) {
            if(is_plugin_inactive($plugin)) {
                $deactivatedPlugins[] = [
                    "name"  => Util::get_plugin_name($file),
                    "label" => $plugin["Name"]
                ];
            }
        }
        return $deactivatedPlugins;
    }

    /**
     * Obtains all the themes which are currently disabled.
     * @return array
     */
    public static function get_deactivated_themes() {
        $wpThemes = wp_get_themes();
        $themes = [];
        $currentTheme = get_current_theme();

        foreach ($wpThemes as $theme => $value) {
            if(strcmp($currentTheme, $value["Name"])) {
                $themes[] = [
                    "name" => $theme,
                    "label" => $value["Name"]
                ];
            }
        }
        return $themes;
    }

    /**
     * Obtains all the plugins, regardless of whether they are activated or deactivated.
     * @return array
     */
    public static function get_plugins() {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();
        $plugins = [];

        foreach ($all_plugins as $plugin => $value) {
            $plugins[] = [
                "name" => Util::get_plugin_name($plugin),
                "label" => $value["Name"]
            ];
        }
        return $plugins;
    }

    /**
     * Obtains all the themes, regardless of whether they are activated or deactivated.
     * @return array
     */
    public static function get_themes() {
        $wpThemes = wp_get_themes();
        $themes = [];
        foreach ($wpThemes as $theme => $value) {
            $themes[] = [
                "name" => $theme,
                "label" => $value["Name"]
            ];
        }
        return $themes;
    }
}
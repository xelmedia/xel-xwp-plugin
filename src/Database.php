<?php
namespace Xel\XWP;


class Database {

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

    public static function get_tables() {
        global $wpdb;

        $tables = $wpdb->get_results("SHOW TABLES");
        $tablesIn = "Tables_in_{$wpdb->dbname}";

        $tableList = [];
        foreach ($tables as $table) {
            $tableList[] = [
                "name" => $table->$tablesIn
            ];
        }

        return $tableList;
    }

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
                    "label" => $plugin["Name"],
                    "version" => $plugin["Version"]
                ];
            }
        }
        return $deactivatedPlugins;
    }
}
<?php
namespace Xel\XWP\Database;


use Xel\XWP\Domain\WpData;
use Xel\XWP\Util;

class Database implements IDatabase {

    /**
     * Obtains all the postTypes in the database with a few exclusions.
     * @param $request -> send from the client containing values such as api_key
     * @return array
     */
    public static function get_post_types($request): array {
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
     * @param $request -> send from the client containing values such as api_key
     * @return array
     */
    public static function get_tables($request): array {
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
     * @param $request -> send from the client containing values such as api_key
     * @return array
     */
    public static function get_deactivated_plugins($request): array {
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
     * Obtains all the themes which are currently disabled
     * @param $request -> send from the client containing values such as api_key
     * @return array
     */
    public static function get_deactivated_themes($request): array {
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
     * @param $request -> send from the client containing values such as api_key
     * @return array
     */
    public static function get_plugins($request): array {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();
        $response = [];

        foreach ($all_plugins as $plugin => $value) {
            $disabled = is_plugin_inactive($plugin);
            $response[] = WpData::builder()
                            ->name(Util::get_plugin_name($plugin))
                            ->label($value["Name"])
                            ->enabled(!$disabled)
                            ->build();
        }
        return $response;
    }

    /**
     * Obtains all the themes, regardless of whether they are activated or deactivated.
     * @param $request -> send from the client containing values such as api_key
     * @return array
     */
    public static function get_themes($request): array  {
        $wpThemes = wp_get_themes();
        $currentTheme = get_current_theme();

        $response = [];
        foreach ($wpThemes as $theme => $value) {
            $enabled = strcmp($currentTheme, $value["Name"]) ? true : false;
            $response[] = WpData::builder()
                            ->name($theme)
                            ->label($value["Name"])
                            ->enabled($enabled)
                            ->build();
        }
        return $response;
    }
}
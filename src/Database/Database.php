<?php
namespace Xel\XWP\Database;


use Xel\XWP\Domain\WpData;
use Xel\XWP\Util;

class Database implements IDatabase {

    /**
     * Obtains the WP core version string.
     * @param $request -> send from the client containing values such as api_key
     * @return string
     */
    public static function get_core_version($request): string {
        return get_bloginfo('version');
    }

    /**
     * Obtains all the postTypes in the database with a few exclusions.
     * @param $request -> send from the client containing values such as api_key
     * @return array
     */
    public static function get_post_types($request): array {
        $postTypes = get_post_types('', 'objects');
        $excludePostTypes = ['customize_changeset', 'attachment', 'revision', 'custom_css'];

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
            if(is_plugin_inactive($file)) {
                $wpData = WpData::builder()
                                ->name(Util::get_plugin_name($file))
                                ->enabled(false)
                                ->label($plugin["Name"]);

                if($version = self::get_plugin_version($plugin)) {
                    $wpData->versionNumber($version);
                }

                if($websiteUrl = self::get_plugin_website_url($plugin)) {
                    $wpData->websiteUrl($websiteUrl);
                }

                $response[] = $wpData->build();
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
                $wpData = WpData::builder()
                                ->name($theme)
                                ->label($value["Name"]);

                if($version = self::get_theme_version($value)) {
                    $wpData->versionNumber($version);
                }

                if($websiteUrl = self::get_theme_website_url($value)) {
                    $wpData->websiteUrl($websiteUrl);
                }
                $response[] = $wpData->build();
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

        foreach ($all_plugins as $file => $plugin) {
            $disabled = is_plugin_inactive($file);
            $wpData = WpData::builder()
                            ->name(Util::get_plugin_name($file))
                            ->label($plugin["Name"])
                            ->enabled(!$disabled);

            if($version = self::get_plugin_version($plugin)) {
                $wpData->versionNumber($version);
            }

            if($websiteUrl = self::get_plugin_website_url($plugin)) {
                $wpData->websiteUrl($websiteUrl);
            }

            $response[] = $wpData->build();
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
            $enabled = $currentTheme === $value["Name"];
            $wpData = WpData::builder()
                            ->name($theme)
                            ->label($value["Name"])
                            ->enabled($enabled);

            if($version = self::get_theme_version($value)) {
                $wpData->versionNumber($version);
            }

            if($websiteUrl = self::get_theme_website_url($value)) {
                $wpData->websiteUrl($websiteUrl);
            }

            $response[] = $wpData->build();
        }
        return $response;
    }

    private static function get_theme_version($themeData) {
        if(is_object($themeData) && !empty($themeData->get("Version"))) return $themeData->get("Version");
        return static::read_property_from_array("Version", $themeData);
    }

    private static function get_theme_website_url($themeData) {
        if(is_object($themeData) && !empty($themeData->get("ThemeURI"))) return $themeData->get("ThemeURI");
        return static::read_property_from_array("ThemeURI", $themeData);
    }

    private static function get_plugin_version($pluginData) {
        return static::read_property_from_array("Version", $pluginData);
    }

    private static function get_plugin_website_url($pluginData) {
        return static::read_property_from_array("PluginURI", $pluginData);
    }

    private static function read_property_from_array($property, $data) {
        $inData = array_key_exists($property, is_array($data) ? $data : array());
        if($inData && !empty($data[$property])) return $data[$property];
        return null;
    }
}
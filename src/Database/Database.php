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
                $wpData = WpData::builder()
                                ->name(Util::get_plugin_name($file))
                                ->label($plugin["Name"]);

                if($version = self::get_plugin_version($file)) $wpData->versionNumber($version);
                if($websiteUrl = self::get_plugin_website_url($file)) $wpData->websiteUrl($websiteUrl);
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

                if($version = self::get_theme_version($theme)) $wpData->versionNumber($version);
                if($websiteUrl = self::get_theme_website_url($theme)) $wpData->websiteUrl($websiteUrl);
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

        foreach ($all_plugins as $plugin => $value) {
            $disabled = is_plugin_inactive($plugin);
            $wpData = WpData::builder()
                            ->name(Util::get_plugin_name($plugin))
                            ->label($value["Name"])
                            ->enabled(!$disabled);

            if($version = self::get_plugin_version($plugin)) $wpData->versionNumber($version);
            if($websiteUrl = self::get_plugin_website_url($plugin)) $wpData->websiteUrl($websiteUrl);
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

            if($version = self::get_theme_version($theme)) $wpData->versionNumber($version);
            if($websiteUrl = self::get_theme_website_url($theme)) $wpData->websiteUrl($websiteUrl);
            $response[] = $wpData->build();
        }
        return $response;
    }

    private static function get_theme_version($theme) {
        return self::get_theme_property($theme, "Version:");
    }

    private static function get_theme_website_url($theme) {
        return self::get_theme_property($theme, "Theme URI:");
    }

    private static function get_plugin_version($plugin) {
        return self::get_plugin_property($plugin, "Version:");
    }

    private static function get_plugin_website_url($plugin) {
        return self::get_plugin_property($plugin, "Plugin URI:");
    }

    private static function get_plugin_property($plugin, string $property) {
        $pluginFileName = Util::get_plugin_name($plugin);
        $pathToPlugin = ABSPATH . "wp-content/plugins/{$pluginFileName}";

        if(!file_exists($path = "{$pathToPlugin}.php")) {
            if (!file_exists($path = "{$pathToPlugin}/{$pluginFileName}.php")) {
                $pathToPlugin .= "/{$pluginFileName}";
                if (!file_exists($path = "{$pathToPlugin}/{$pluginFileName}/{$pluginFileName}.php")) {
                    return null;
                }
            }
        }
        return self::read_property_from_file($path, $property);
    }

    private static function get_theme_property($theme, string $property) {
        $stylePath = dirname(get_template_directory()) . "/{$theme}/style.css";
        if(!file_exists($stylePath)) {
            return null;
        }
        return self::read_property_from_file($stylePath, $property);
    }

    private static function read_property_from_file(string $filePath, string $property) {
        $fileContentsArray = @file($filePath) ?? [];
        foreach($fileContentsArray as $line) {
            if(strpos($line, $property) !== false) {
                $value = substr($line, strpos($line, ":") + 1);
                $value = preg_replace('/\s*/m', '', $value);
            }
        }
        return $value ?? null;
    }
}
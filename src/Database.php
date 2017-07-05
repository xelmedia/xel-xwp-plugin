<?php
namespace Xel\Xwp;


class Database {

    public function get_post_types() {
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

    public function get_tables() {
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
}
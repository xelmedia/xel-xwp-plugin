<?php
namespace Xel\Xwp;


class Database {

    public function get_post_types() {
        $postTypes = get_post_types('', 'objects');

        $postTypesList = [];
        foreach ($postTypes as $postType) {
            $postTypesList[] = [
                "label" => $postType->label,
                "name" => $postType->name,
            ];
        }

        return $postTypesList;
    }

}
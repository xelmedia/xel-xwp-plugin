<?php
declare(strict_types=1);


namespace Xel\XWP\Database;


interface IDatabase {
    public static function get_post_types($request);
    public static function get_tables($request);
    public static function get_deactivated_plugins($request);
    public static function get_deactivated_themes($request);
    public static function get_plugins($request);
    public static function get_themes($request);
}
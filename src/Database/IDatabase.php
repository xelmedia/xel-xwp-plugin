<?php
declare(strict_types=1);


namespace Xel\XWP\Database;


use Xel\XWP\Domain\WpDataResponse;

interface IDatabase {
    public static function get_post_types();
    public static function get_tables();
    public static function get_deactivated_plugins();
    public static function get_deactivated_themes();
    public static function get_plugins();
    public static function get_themes();
}
<?php
declare(strict_types=1);

namespace Xel\XWP\Rest;


use Xel\XWP\Database\Database;

class RestConstants {
    private $routes;

    public function __construct() {
        $routes = [];
        // GET ROUTES
        $requestType = 'GET';
        $routes[] = new RestRoute('get_post_types',Database::class,'/post-types/',$requestType);
        $routes[] = new RestRoute('get_tables',Database::class,'/db-tables/',$requestType);
        $routes[] = new RestRoute('get_deactivated_plugins',Database::class,'/deactivated-plugins/', $requestType);
        $routes[] = new RestRoute('get_deactivated_themes',Database::class,'/deactivated-themes/', $requestType);
        $routes[] = new RestRoute('get_plugins',Database::class,'/plugins/', $requestType);
        $routes[] = new RestRoute('get_themes',Database::class,'/themes/', $requestType);

        $this->routes = $routes;
    }

    public function getRoutes() {
        return $this->routes;
    }
}
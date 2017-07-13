<?php
namespace Xel\XWP;

use Xel\XWP\Rest\RestConstants;
use Xel\XWP\Rest\RestRoute;

class Plugin {

    public function __construct() {
        add_action( 'rest_api_init',  array(__CLASS__ , 'xel_rest_init'));
    }

    private static function xel_header_add() {
        remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );

        add_filter( 'rest_pre_serve_request', function( $value ) {
            header( 'Access-Control-Allow-Origin: *' );
            header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
            header( 'Access-Control-Allow-Credentials: true' );

            return $value;
        });
    }

    private static function xel_rest_add(RestRoute $route) {
        $version = 'v1';
        $namespace = 'xel-xwp/' . $version;

        register_rest_route( $namespace, $route->getPathUri(), array(
            'methods'  => $route->getRequestType(),
            'callback' =>  array($route->getClassPath(), $route->getMethodName())
        ) );
    }

    public static function xel_rest_init() {
        $restConstants = new RestConstants();
        $routes = $restConstants->getRoutes();
        foreach ($routes as $route) {
            self::xel_rest_add($route);
        }

        self::xel_header_add();
    }
}
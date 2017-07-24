<?php
namespace Xel\XWP;

use Xel\XWP\Rest\RestConstants;
use Xel\XWP\Rest\RestRoute;

class Plugin {

    public function __construct() {
        add_action('rest_api_init', array(__CLASS__ , 'xel_rest_init'));
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

    /**
     * Authorization method that checks whether the api-key is valid.
     * If not equal, request api-key not set or there is no api-key set for this site, it'll exit using an unauthorized
     * response (401).
     * @return bool || WP_ERROR
     */
    public static function xel_authorize() {
        if (array_key_exists(RestConstants::PATH_PARAM_API_KEY, $_REQUEST)) {
            $apiKeyRequest = $_REQUEST[RestConstants::PATH_PARAM_API_KEY];
            $apiKeyResponse = get_option(RestConstants::WP_TABLE_OPTION_API_KEY);

            if($apiKeyResponse && $apiKeyRequest === $apiKeyResponse) {
                return true;
            }
        }
        return new \WP_Error( 'rest_cannot_access', RestConstants::ERROR_MSG_API_KEY, array( 'status' => rest_authorization_required_code() ) );
    }

    public static function xel_rest_init() {
        add_filter('rest_authentication_errors', array(__CLASS__ , 'xel_authorize'));

        $restConstants = new RestConstants();
        $routes = $restConstants->getRoutes();
        foreach ($routes as $route) {
            self::xel_rest_add($route);
        }

        self::xel_header_add();
    }
}
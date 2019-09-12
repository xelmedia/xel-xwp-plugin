<?php
namespace Xel\XWP;

use Xel\XWP\Rest\RestConstants;
use Xel\XWP\Rest\RestRoute;

class Plugin {
    const ENDPOINT_NAMESPACE = "xel-xwp/v1";

    public function __construct() {
        self::xel_manage_filters();
        add_action('rest_api_init', array(__CLASS__ , 'xel_rest_init'));
    }

    private static function xel_manage_filters() {
        add_filter('automatic_updater_disabled', '__return_true');
        add_filter('auto_core_update_send_email', '__return_false');
    }

    private static function xel_header_add() {
        remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
    }

    private static function xel_rest_add(RestRoute $route) {
        register_rest_route(self::ENDPOINT_NAMESPACE, $route->getPathUri(), array(
            'methods'  => $route->getRequestType(),
            'callback' =>  array($route->getClassPath(), $route->getMethodName())
        ) );
    }

    /**
     * Authorization method that checks whether the api-key is valid.
     * If not equal, request api-key not set or there is no api-key set for this site, it'll exit using an unauthorized
     * response (401).
     * @return bool || WP_ERROR || null
     *
     */
    public static function xel_authorize() {
        if(strpos($_SERVER['REQUEST_URI'], self::ENDPOINT_NAMESPACE) === false) return null;

        if (isset($_REQUEST[RestConstants::PATH_PARAM_API_KEY])) {
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
<?php
namespace Xel\XWP;

class Plugin {

    public function __construct() {
        add_action( 'rest_api_init',  array(__CLASS__ , 'xel_rest_init'));
    }

    public function xel_rest_init() {
        $version = 'v1';
        $namespace = 'xel-xwp/' . $version;

        register_rest_route( $namespace, '/post-types/', array(
            'methods'  => 'GET',
            'callback' =>  array(__NAMESPACE__ .'\Database', 'get_post_types')
        ) );

        register_rest_route( $namespace, '/db-tables/', array(
            'methods'  => 'GET',
            'callback' =>  array(__NAMESPACE__ .'\Database', 'get_tables')
        ) );

        register_rest_route( $namespace, '/deactivated-plugins/', array(
            'methods'  => 'GET',
            'callback' =>  array(__NAMESPACE__ .'\Database', 'get_deactivated_plugins')
        ) );

        remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );

        add_filter( 'rest_pre_serve_request', function( $value ) {
            header( 'Access-Control-Allow-Origin: *' );
            header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
            header( 'Access-Control-Allow-Credentials: true' );

            return $value;
        });
    }
}
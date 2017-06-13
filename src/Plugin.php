<?php
namespace Xel\XWP;

class Plugin {

    public function __construct() {
        add_action( 'rest_api_init',  array(__CLASS__ , 'register_routes'));
    }

    public function register_routes() {
        $version = 'v1';
        $namespace = 'xel-xwp/' . $version;

        register_rest_route( $namespace, '/post-types/', array(
            'methods'  => 'GET',
            'callback' =>  array(__NAMESPACE__ .'\Database', 'get_post_types')
        ) );

    }
}
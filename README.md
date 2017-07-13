## Xel XWP MU plugin (RestAPI) 

Extending WP RestAPI v2 in our Must Use Plugin. 

####Installation: 

- Clone project in the must use directory
- Composer install
- mv xel-xwp-plugin.php outside the plugin dir 

That's it! Verify if the url works! 

###Usage

Receive all post types of the WordPress site: 

`/wp-json/xel-xwp/v1/post-types`

`/wp-json/xel-xwp/v1/db-tables`

`/wp-json/xel-xwp/v1/themes`

`/wp-json/xel-xwp/v1/plugins`

`/wp-json/xel-xwp/v1/deactivated-themes`

`/wp-json/xel-xwp/v1/deactivated-plugins`



####WP REST API v2 docs: 

http://v2.wp-api.org/
<?php
/**
 * Plugin Name:     User Geo Redirects
 * Description:     The simplest way to redirect users or close website to maintenance based on geolocation. The <a href="https://ip-api.com/docs" target="_blank">IP-API</a> is used to determine the location.
 * Author:          Daria Levchenko
 * Author URI:      https://github.com/levenyatko
 * Plugin URI:      https://github.com/levenyatko/ip-api-wp-redirects
 * Version:         1.0.0
 * Text Domain:     ipapi_redirects
 * License:         GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'IPAPI_GEO_REDIRECTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'IPAPI_GEO_REDIRECTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require plugin_dir_path( __FILE__ ) . 'src/autoload.php';

new IpApiGeoRedirects();

function sent_503_headers()
{
    nocache_headers();

    $protocol = 'HTTP/1.0';
    if (isset($_SERVER['SERVER_PROTOCOL']) && 'HTTP/1.1' === $_SERVER['SERVER_PROTOCOL']) {
        $protocol = 'HTTP/1.1';
    }
    header("$protocol 503 Service Unavailable", true, 503);
    header('Retry-After: 3600');
}
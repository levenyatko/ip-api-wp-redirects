<?php

/**
 * Plugin Class autoloader.
 */

spl_autoload_register( function( $class_name ) {

	if ( false === strpos( $class_name, 'IpApi' ) ) {
		return false;
	}

	$class_name = ltrim( $class_name, '\\' );

	$include_path = IPAPI_GEO_REDIRECTS_PLUGIN_DIR . '/src/Classes/' . $class_name . '.php';

	if ( ! empty( $include_path ) && is_readable( $include_path ) ) {
		require $include_path;
	}
});
<?php
/**
 * Class IpApiGetLocation
 *
 * Get user location by IP
 */

class IpApiGetLocation
{
    /**
     * Api base url
     */
    private static $api_base = 'http://ip-api.com/json/';

    public static function get_user_location()
    {
        $user_ip = self::get_user_ip();

        if ($user_ip) {

            $option = get_option('ipapi_redirect_settings');
            $default_location = (empty($option['default_location'])) ? 'EN' : $option['default_location'];

            $response = wp_remote_get( self::$api_base . $user_ip );

            if ( is_wp_error( $response ) ) {
                return $default_location;
            } elseif ( wp_remote_retrieve_response_code( $response ) === 200 ) {

                $body = wp_remote_retrieve_body( $response );
                $body = json_decode($body);

                if ( !empty($body->status) && $body->status == 'success' ) {
                    return $body->countryCode;
                }

            }

        }

        return $default_location;
    }

    private static function get_user_ip()
    {
        foreach ( ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ] as $key ) {
            if ( array_key_exists( $key, $_SERVER ) === true ) {
                foreach ( explode( ',', $_SERVER[$key] ) as $ip ) {
                    $ip = trim( $ip );
                    if ( filter_var( $ip, FILTER_VALIDATE_IP ) !== false ) {
                        return esc_attr( $ip );
                    }
                }
            }
        }

        return '';
    }

}
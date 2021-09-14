<?php

class IpApiGeoRedirects
{
    /**
     * Field with redirect settings
     */
    private $redirect_settings_field = 'ipapi_redirect_settings';

    public function __construct()
    {
        // Load core functions when `wp_loaded` is ready.
        add_action( 'wp_loaded', [ $this, 'init' ] );

        add_action( 'wp_loaded', [ $this, 'run' ] );
    }

    /**
     * Initialize everything the plugin needs.
     */
    public function init()
    {
        // Only load controllers in admin pannel
        if ( is_admin() ) {
            $settings = new IpApiRedirectsSettings();
            $settings->init();
        }

    }

    public function run()
    {
        if ( !is_admin() && !is_user_logged_in() && !wp_doing_ajax() && !wp_doing_cron() ) {

            $user_location = IpApiGetLocation::get_user_location();

            $options = get_option( $this->redirect_settings_field );

            if ( $options ) {
                // check if show user maintenance page
                if ( !empty($options['maintenance_locations']) ) {
                    $maintenance_locations = explode(',', $options['maintenance_locations'] );
                    if ($maintenance_locations) {
                        foreach ($maintenance_locations as $maintenance_location) {
                            $location = trim( strtoupper($maintenance_location) );
                            if ( $user_location == $location ) {
                                $maintenance = new IpApiMaintenance();
                                $maintenance->run();
                                return;
                            }
                        }
                    }
                }

                if ( !empty($options['locations_to_redirect']) ) {
                    $redirect = new IpApiDoRedirect();
                    $redirect->run( $options['locations_to_redirect'], $user_location );
                    return;
                }

            }
        }
    }
}
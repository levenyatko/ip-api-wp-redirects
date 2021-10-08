<?php
/**
 * Class IpApiMaintenance
 *
 * Close website to maintenance
 */

class IpApiMaintenance
{

    public function run()
    {
        add_action('template_include', [ $this, 'include_template' ], 999999);
        add_action('do_feed_rdf', [ $this, 'disable_feed' ], 0, 1);
        add_action('do_feed_rss', [ $this, 'disable_feed' ], 0, 1);
        add_action('do_feed_rss2', [ $this, 'disable_feed' ], 0, 1);
        add_action('do_feed_atom', [ $this, 'disable_feed' ], 0, 1);
    }

    public function disable_feed()
    {
        if ( !is_user_logged_in() ) {
            nocache_headers();
            echo '<?xml version="1.0" encoding="UTF-8" ?><status>Service unavailable.</status>';
            exit;
        }
    }

    public function include_template()
    {
        $file_name = 'maintenance-page.php';
        $theme_filder = 'ipapi/';

        if ( locate_template( $theme_filder.$file_name ) ) {
            $template = locate_template( $theme_filder.$file_name );
        } else {
            // Template not found in theme's folder, use plugin's template as a fallback
            $template = IPAPI_GEO_REDIRECTS_PLUGIN_DIR . '/templates/' . $file_name;
        }

        return $template;
    }

}
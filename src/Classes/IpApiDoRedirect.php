<?php
/**
 * Class IpApiDoRedirect
 *
 * Redirect user if needed
 */

class IpApiDoRedirect
{

    private $redirect_options;

    public function run ( $options, $user_location )
    {
        if ( !$this->is_login_page() ) {
            $this->prepare_options( $options );
            $this->maybe_redirect( $user_location );
        }

    }

    private function prepare_options ( $options )
    {
        if ( empty($options) ) {
            $this->redirect_options = false;
        } else {
            if ( empty($options['locations']) ) {
                $this->redirect_options = false;
            } else {
                foreach ($options['locations'] as $i => $row) {

                    $locations = explode(',', $row);

                    if ($locations) {

                        $locations = array_map('trim', $locations);
                        $locations = array_map('strtoupper', $locations);

                        $options['locations'][$i] = $locations;

                    }
                }
                $this->redirect_options = $options;
            }
        }
    }

    private function maybe_redirect ( $user_location )
    {
        if ( $this->redirect_options ) {
            foreach ($this->redirect_options['locations'] as $i => $locations) {

                $exclude = ( empty( $this->redirect_options['exclude'][$i] ) ) ? 0 : $this->redirect_options['exclude'][$i];

                if ( $this->is_redirect_needed(
                    $locations,
                    $exclude,
                    $user_location
                ) ) {

                    if ( empty($this->redirect_options['urls'][$i]) ) {
                        return;
                    }

                    $url = $this->get_redirect_url($this->redirect_options['urls'][$i]);

                    wp_redirect( wp_sanitize_redirect($url) );
                    exit;

                }
            }
        }
    }

    private function is_redirect_needed ($locations, $excluded, $user_location )
    {
        if ( is_array($locations) ) {
            if ( $excluded && !in_array($user_location, $locations) ) {
                // locations. whicn is not in array
                return true;
            }

            if ( !$excluded && in_array($user_location, $locations) ) {
                // only locations in array
                return true;
            }

        } else {
            if ( $excluded && $locations !== $user_location ) {
                // all locations exclude field value
                return true;
            }

            if ( ! $excluded && $locations === $user_location) {
                // only in field location
                return true;
            }
        }

        return false;
    }

    private function get_redirect_url ( $link )
    {
        $link = str_replace('/%page-url%/', $_SERVER['REQUEST_URI'], $link);
        $link = str_replace('%page-url%', $_SERVER['REQUEST_URI'], $link);

        return $link;
    }

    private function is_login_page() {
        return !strncmp($_SERVER['REQUEST_URI'], '/wp-login.php', strlen('/wp-login.php'));
    }

}
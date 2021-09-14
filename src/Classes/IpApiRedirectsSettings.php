<?php

/**
 * Class IpApiRedirectsSettings
 *
 * Plugin Settings Page
 */
class IpApiRedirectsSettings
{
    /**
     * Settings menu item slug.
     */
    public $settings_page_slug = 'ipapi-redirects-settings';

    /**
     * Settings DB Field
     */
    private $settings_id = 'ipapi_redirects';

    /**
     * Field with redirect settings
     */
    private $redirect_settings_field = 'ipapi_redirect_settings';

    /**
     * Initialize.
     */
    public function init()
    {
        add_action( 'admin_init', [ $this, 'settings_admin_init' ] );
        add_action( 'admin_menu', [ $this, 'register_settings_page' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
        add_filter( 'pre_update_option_ipapi_redirect_settings', [ $this, 'filter_redirect_locations_data' ]);
    }

    /**
     * enqueue settings page styles and scripts
     */
    public function admin_enqueue_scripts()
    {
        wp_enqueue_style( 'ipapi-settings-page-styles', IPAPI_GEO_REDIRECTS_PLUGIN_URL . 'assets/css/settings.css' );
        wp_enqueue_script( 'ipapi-settings-redirect-field', IPAPI_GEO_REDIRECTS_PLUGIN_URL . 'assets/js/redirect-settings-field.js', ['jquery', 'wp-util'] );
    }

    /**
     * Register the plugin Settings page.
     */
    public function register_settings_page()
    {
        add_options_page(
            __( 'User Geo Redirects', 'ipapi_redirects' ),
            __( 'User Geo Redirects', 'ipapi_redirects' ),
            'manage_options',
            $this->settings_page_slug,
            [ $this, 'render_settings_page' ]
        );
    }

    private function get_sections()
    {
        return [
                [
                    'id' => 'ipapi_defaults_section',
                    'name' => __( 'Plugin defaults', 'ipapi_redirects' ),
                    'callback' => [$this, 'render_empty_section_description'],
                ],
                [
                    'id' => 'ipapi_redirect_section',
                    'name' => __( 'Geo Redirect Settings', 'ipapi_redirects' ),
                    'callback' => [$this, 'render_locations_redirect_section'],
                ],
                [
                    'id' => 'ipapi_maintenance_section',
                    'name' => __( 'Maintenance locations', 'ipapi_redirects' ),
                    'callback' => [$this, 'render_multiple_locations_section'],
                ]
        ];
    }

    /**
     * Registers the plugin settings sections and fields to WordPress
     */
    public function settings_admin_init()
    {
        register_setting( $this->settings_id, $this->redirect_settings_field );

        $sections = $this->get_sections();

        foreach ($sections as $section) {
            add_settings_section(
                $section['id'],
                $section['name'],
                $section['callback'],
                $this->settings_id
            );
        }

        add_settings_field(
            'default_location',
            __( 'Default user country code', 'ipapi_redirects' ),
            [$this, 'render_default_location_field'],
            $this->settings_id,
            'ipapi_defaults_section',
            [
                'label_for' => 'ipapi-default-location-field',
                'required' => 1,
            ]
        );

        add_settings_field(
            'maintenance_locations',
            __( 'Locations that will be redirected to the Maintenance page', 'ipapi_redirects' ),
            [$this, 'render_maintenance_location_field'],
            $this->settings_id,
            'ipapi_maintenance_section',
            [
                'label_for' => 'ipapi-maintenance-location-field',
                'classes' => 'ipapi-section-form--long-row',
            ]
        );

        add_settings_field(
            'locations_to_redirect',
            __( 'Locations to Redirect users', 'ipapi_redirects' ),
            [$this, 'render_redirect_locations_field'],
            $this->settings_id,
            'ipapi_redirect_section',
            [
                'label_for' => 'ipapi-redirect-locations-field',
                'classes' => 'ipapi-section-form--long-row',
            ]
        );

    }

    /**
     * Display the plugin settings options page.
     */
    public function render_settings_page()
    {
        $section_description =  __('All supported country codes can be found here %s', 'ipapi_redirects');
        $help_link = '<a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2" target="_blank"><span class="dashicons dashicons-external"></span></a>';

        $section_description = sprintf($section_description, $help_link);

        ?>
        <div class="ipapi-settings--wrap">
            <div class="ipapi-settings--title">
                <h1><?php _e('User Geo Redirects Plugin Settings', 'ipapi_redirects'); ?></h1>
                <div class="notice notice-info">
                    <p><?php echo $section_description; ?></p>
                </div>
            </div>
            <div class="ipapi-settings-form">
                <form method="post" action="options.php">
                    <?php
                    settings_fields( $this->settings_id );
                    $this->do_settings_sections( $this->settings_id );
                    ?>
                    <div class="ipapi-settings-form--button">
                        <?php submit_button(); ?>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Render settings section description
     */
    public function render_multiple_locations_section()
    {
        $section_description = __('To specify multiple locations, separate them with a comma.', 'ipapi_redirects');
        ?>
        <div class="ipapi-redirect-locations-description">
            <p><?php echo $section_description; ?></p>
        </div>
        <?php
    }

    public function render_locations_redirect_section()
    {
        $this->render_multiple_locations_section();

        $old_url = 'https://current.domain/page-url/';
        $new_url = 'https://new.domain/page-url/';
        $pattern = html_entity_decode('<code>https://new.domain<strong>/%page-url%/</strong></code>');

        $section_description = __('To redirect user from %s to %s page use %s string', 'ipapi_redirects');

        $section_description = sprintf($section_description, $old_url, $new_url, $pattern);
        ?>
        <div class="ipapi-redirect-locations-description">
            <p><?php echo $section_description; ?></p>
        </div>
        <?php
    }

    public function render_empty_section_description()
    {

    }

    function do_settings_sections( $page )
    {
        global $wp_settings_sections, $wp_settings_fields;

        if ( !isset($wp_settings_sections) || !isset($wp_settings_sections[$page]) )
            return;

        foreach ( $wp_settings_sections[$page] as $section ) {

            echo "<div class='ipapi-settings-section--wrap'>\n";
            echo "<h2>{$section['title']}</h2>\n";

            call_user_func($section['callback'], $section);

            if ( !isset($wp_settings_fields) ||
                !isset($wp_settings_fields[$page]) ||
                !isset($wp_settings_fields[$page][$section['id']]) )
                continue;

            echo '<div class="ipapi-settings-section-form">';
            $this->do_settings_fields($page, $section['id']);
            echo '</div>';

            echo '</div>';

        }
    }

    function do_settings_fields($page, $section)
    {
        global $wp_settings_fields;

        if ( !isset($wp_settings_fields) ||
            !isset($wp_settings_fields[$page]) ||
            !isset($wp_settings_fields[$page][$section]) )
            return;

        foreach ( $wp_settings_fields[$page][$section] as $field ) {

            $classes = 'ipapi-section-form--row';
            if ( !empty($field['args']['classes']) ) {
                $classes .= ' ' . $field['args']['classes'];
            }

            ?>
            <div class="<?php echo esc_attr($classes); ?>">
                <?php
                if ( empty($field['args']['label_for']) ) {
                    echo $field['title'];
                    if ( !empty($field['args']['required']) ) { ?>
                        <span class="required"> *</span>
                    <?php }
                } else { ?>
                    <label for="<?php echo $field['args']['label_for']; ?>">
                        <?php echo $field['title']; ?>
                        <?php if ( !empty($field['args']['required']) ) { ?>
                            <span class="required"> *</span>
                        <?php } ?>
                    </label>
                <?php } ?>
                <br>
                <?php call_user_func($field['callback'], $field['args']); ?>
            </div>
            <?php
        }
    }

    /**
     * Render fields
     */
    public function render_default_location_field( $args )
    {
        $options = get_option( $this->redirect_settings_field );
        $option_name = 'default_location';

        $field_data = (isset( $options[$option_name] )) ? $options[$option_name] : 'EN';

        $field_name = $this->redirect_settings_field . '[' . $option_name . ']';

        $this->show_input_text_field($field_name, $field_data, $args);
    }

    public function render_maintenance_location_field( $args )
    {
        $options = get_option( $this->redirect_settings_field );
        $option_name = 'maintenance_locations';

        $field_data = (isset( $options[$option_name] )) ? $options[$option_name] : '';

        $field_name = $this->redirect_settings_field . '[' . $option_name . ']';

        $this->show_input_text_field($field_name, $field_data, $args);
    }

    private function show_input_text_field($name, $value, $args)
    {
        $attributes = '';
        if ( !empty($args['required']) ) {
            $attributes = 'required';
        }
        ?>
        <input type="text" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>"<?php echo " " . $attributes; ?>>
        <?php
    }

    public function render_redirect_locations_field()
    {
        $option_name = 'locations_to_redirect';
        $options = get_option( $this->redirect_settings_field );

        $field_data = (isset( $options[$option_name] )) ? $options[$option_name] : false;
        $field_name = $this->redirect_settings_field . '[' . $option_name . ']';

        ?>
        <table id="ipapi-redirect-locations-list" class="ipapi-redirect--table widefat">
            <tr class="ipapi-redirect--table-heading">
                <th><?php _e('Countries list', 'ipapi_redirects'); ?><span class="required">*</span></th>
                <th><?php _e('Exclude selected', 'ipapi_redirects'); ?></th>
                <th><?php _e('Redirect URL', 'ipapi_redirects'); ?><span class="required">*</span></th>
                <th></th>
            </tr>
            <?php
            if ($field_data) {

                $rows_count = max( count($field_data['locations']), count($field_data['urls']) );

                for ( $i = 0 ; $i < $rows_count ; $i++ ) {

                    $values = [
                        'location' => ( empty($field_data['locations'][$i]) ) ? '' : $field_data['locations'][$i],
                        'redirect' => ( empty($field_data['urls'][$i]) ) ? '' : $field_data['urls'][$i],
                        'excluded' => ( empty($field_data['exclude'][$i]) ) ? 0 : $field_data['exclude'][$i],
                    ];

                    $this->locations_to_redirect_field_partital($field_name, $values);
                }

                if ( $rows_count < 10) {
                    $this->locations_to_redirect_field_partital($field_name, []);
                }

            } else {
                $this->locations_to_redirect_field_partital($field_name, []);
            }
            ?>
        </table>
        <div class="ipapi-redirect-locations--footer">
            <button type="button" id="ipapi-redirect-locations-add" class="button"><?php _e( 'Add Row', 'ipapi_redirects' ); ?></button>
            <div class="ipapi_locations-description">
                <p><?php _e('You can add no more than 10 rows', 'ipapi_redirects'); ?></p>
            </div>
        </div>
        <script type="text/html" id="tmpl-redirect-locations-row">
            <?php $this->locations_to_redirect_field_partital($field_name, []); ?>
        </script>
        <?php
    }

    private function locations_to_redirect_field_partital($field_name, $values)
    {
        $location = (empty($values['location'])) ? "" : $values['location'];
        $redirect = (empty($values['redirect'])) ? "" : $values['redirect'];
        $excluded = (empty($values['excluded'])) ? 0 : $values['excluded'];

        ?>
        <tr class="ipapi_locations_to_redirect--group both-required-fields">
            <td>
                <?php $this->show_input_text_field($field_name . '[locations][]', $location, []); ?>
            </td>
            <td>
                <input type="checkbox" class="ipapi-fake-checkbox" value="1" <?php checked($excluded, 1); ?> autocomplete="off">
                <input type="hidden" name="<?php echo esc_attr( $field_name . '[exclude][]' ); ?>" value="<?php echo esc_attr($excluded ); ?>">
            </td>
            <td>
                <?php $this->show_input_text_field($field_name . '[urls][]', $redirect, []); ?>
            </td>
            <td>
                <a href="#" class="ipapi-row-remove">
                    <?php _e('delete', 'ipapi_redirects'); ?>
                </a>
            </td>
        </tr>
        <?php
    }

    /**
     * Filter fields data before save
     */

    public function filter_redirect_locations_data( $value )
    {
        if ( !empty($value['locations_to_redirect']) ) {

            $locations = $value['locations_to_redirect'];

            $rows_count = max( count($locations['locations']), count($locations['urls']) );

            for ( $i = 0 ; $i < $rows_count ; $i++ ) {
                if ( empty($locations['locations'][$i]) || empty($locations['urls'][$i]) ) {
                    unset($locations['locations'][$i]);
                    unset($locations['urls'][$i]);
                    unset($locations['exclude'][$i]);
                }
            }

            $value['locations_to_redirect'] = $locations;
        }

        return $value;
    }

}
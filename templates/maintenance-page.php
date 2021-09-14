<?php
sent_503_headers();

$page_title = __('Website is in Maintenance', 'ipapi_redirects');
$site_title       = get_bloginfo( 'title' );
$site_description = get_bloginfo( 'description' );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php esc_attr( bloginfo( 'charset' ) ); ?>" />

        <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, minimum-scale=1">
        <meta name="description" content="<?php echo esc_attr( $site_description ); ?>"/>
        <meta http-equiv="X-UA-Compatible" content="" />
        <meta property="og:site_name" content="<?php echo esc_attr( $site_title ) . ' - ' . esc_attr( $site_description ); ?>"/>
        <meta property="og:title" content="<?php echo esc_attr( $page_title ); ?>"/>
        <meta property="og:type" content="Maintenance"/>
        <meta property="og:url" content="<?php echo esc_url( site_url() ); ?>"/>
        <meta property="og:description" content="<?php echo esc_attr( $site_description ); ?>"/>

        <title><?php echo $page_title ; ?></title>

    </head>
    <body>
        <h1><?php _e('Something cool is coming', 'ipapi_redirects'); ?></h1>
    </body>
</html>
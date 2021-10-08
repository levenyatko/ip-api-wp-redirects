# User Geo Redirects

The simplest way to redirect users or close website to maintenance based on geolocation.

The IP-API is used to determine the location.

## Basic Usage

Install the plugin

In the admin panel go to Settings ->User Geo Redirects

The "Default user country code" field specifies the user's region if the IP location could not be determined. For example, on the local site.

In the "Geo Redirect Settings" section, you can specify the pages to redirect users with a specific location.

Add the locations for which you want to show the maintenance page to the "Maintenance locations" field.

**Important:** Admin panel, ajax links and logged in users are not redirected.

To override the maintenance page template in your theme: create an "ipapi" folder and copy the ip-api-wp-redirects/templates/maintenance-page.php file into it.


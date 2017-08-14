<?php
/*

Plugin Name: Lodgix.com Vacation Rental Listing, Management & Booking Plugin
Plugin URI: http://www.lodgix.com/vacation-rental-wordpress-plugin.html
Description: Build a sophisticated vacation rental website in seconds using the Lodgix.com vacation rental software. Vacation rental CMS for WordPress.
Version: 3.8.1
Author: Lodgix
Author URI: http://www.lodgix.com

*/
/*

Changelog:
v3.8.1: New "Search by Tags" feature in the rental search widget.
v3.8.0: Unstable. New "Search by Tags" feature in the rental search widget.
v3.7.6: Removed the area text from the property title.
v3.7.5: Removed CeeBox files.
v3.7.4: Added missing files.
v3.7.3: Adjusted lity library.
v3.7.2: Replaced video lightbox.
v3.7.1: Ability to hide some categories. Custom category URLs.
v3.7.0: Unstable. Ability to hide some categories. Custom category URLs.
v3.6.1: Fixed bug in translations.
v3.6.0: Added custom URLs for property pages and Vacation Rentals page.
v3.5.7: Fixed regression with translations.
v3.5.6: Fixed issue when pages were not generated for some properties.
v3.5.5: Fixed regression in Save and Regenerate.
v3.5.4: Clean DB and settings when plugin is deactivated.
v3.5.3: Automatically clean DB when Customer ID is changed.
v3.5.2: Added subcategories.
v3.5.1: Unstable. Added subcategories.
v3.5.0: Unstable. Added subcategories.
v3.4.4: Keep aspect ratio for thumbnails in the property list.
v3.4.3: Fixed grid view thumbnail size.
v3.4.2: Fixed thumbnail pixelation.
v3.4.1: Added ability to create URLs to display properties having specific tags.
v3.4.0: Added ability to create URLs to display properties having specific tags.
v3.3.5: Restored compatibility with PHP 5.3.
v3.3.4: Improved translation.
v3.3.3: Added CSS class for area.
v3.3.2: Added ability to search by multiple property IDs separated by comma.
v3.3.1: Added "Pet Friendly" filter to the Rental Search widget.
v3.3.0: Added "Minimum Nights" setting to the Rental Search widget.
v3.2.13: Linked Book Now button in search results to Lodgix booking page.
v3.2.12: Improved full size thumbnails.
v3.2.11: Tested with WP 4.7.
v3.2.10: Updated the localization for the recent enhancements.
v3.2.9: Updated the localization for the recent enhancements.
v3.2.8: Updated the localization for the recent enhancements.
v3.2.7: Search widget can search for numeric keywords. Added a message when nothing is found.
v3.2.6: Merged rates table became more responsive.
v3.2.5: Fixed property URLs in the Availability Calendar when marketing title is used as property name.
v3.2.4: Fixed admin Google Map display.
v3.2.3: Added Google Map API Key option.
v3.2.2: Fixed bug with "High rate in the table".
v3.2.1: Rates and merged rates additional fix.
v3.2.0: Improved rates and merged rates.
v3.1.8: Improved rental search results: if bedrooms is selected, search results are sorted by bedrooms.
v3.1.6: Restoring Owner ID and API Key after cleaning database.
v3.1.4: Added ability to hide tabs. Misc improvements.
v3.1.3: Fixed unexpected T_OBJECT_OPERATOR error in PHP older than 5.4.
v3.1.2: Removed anonymous functions causing issues in PHP older than 5.3.
v3.1.1: Fixed bug in the class auto-loader.
v3.1.0: New plugin configuration interface.
v3.0.6: Fixed "Instructions on Multi Unit Calendar" config option.
v3.0.5: Fixed "Instructions on Single Unit Calendar" config option.
v3.0.4: Added advance days limit to the rentals search widget.
v3.0.3: New plugin configuration interface.
v2.2.6: Fixed space between the Marketing Teaser and the Description.
v2.2.5: Fixed issue with the Featured Rentals widget.
v2.2.4: Improved "Save and Regenerate" process.
v2.2.3: Fixed bug in "Save and Regenerate".
v2.2.2: Updated description.
v2.2.1: Added new "Currency Symbol" config option to the Search Widget. Added settings to customize tab titles on the Property Page.
v2.2.0: Reduced memory usage.
v2.1.5: Bug fix.
v2.1.4: Added display option for "Book Now" button on Property Page.
v2.1.3: Added display option to enable icons in Search / Sort Page.
v2.1.2: Added new display options.
v2.1.1: Added "# of Guests" in Search / Sort Page. Added new display options.
v2.1.0: Added new Search / Sort Page Design "Rows New". Added new display options.
v2.0.12: Fixed a bug in the Rental Search widget.
v2.0.11: Added sorting in the Vacation Rentals grid view. Fixed bugs in the Rental Search widget.
v2.0.10: Made the thumbnail image on the results page clickable. Fixed bug in text expander.
v2.0.9: Improved Rental Search Widget: updated CSS, added custom button text option.
v2.0.8: Prepared for Wordpress 4.4
v2.0.7: Improved automatic review titles.
v2.0.6: Fixed bug in reviews.
v2.0.5: Added stars and title to reviews.
v2.0.4: Fixed CSS for the new rental search widget.
v2.0.3: Fixed CSS for the Arrival field in the new rental search widget.
v2.0.2: Improved new rental search widget. Removed margin in featured rentals widget. Fixed bug with deposit and cancellation policies.
v2.0.1: New rental search widget.
v2.0.0: New rental search widget. Icon sets. Ability to customize CSS.
v1.9.1: Added sorting of search results. Misc improvements and bug fixes.
v1.9.0: New responsive image gallery with configurable size, more crisp images, full screen view. Misc improvements.
v1.8.10: Added CSS classes and customization options to property page. Improved full size thumbnails.
v1.8.9: Added CSS classes to customize search results page. Added new Display Options to customize search results page.
v1.8.8: Fixed bedroom type studio
v1.8.7: Fixed amenity order
v1.8.6: Fixed duplicated amenities
v1.8.5: Prepared for Wordpress 4.3
v1.8.4: Changed plugin filenames
v1.8.3: Removed Book Now button when online booking not available
v1.8.2: Fixed tabbed interface contact link
v1.8.1: Fixed special case boooking URL - Part 2
v1.8.0: Fixed special case boooking URL
v1.7.9: Added HTTPS Gallery option
v1.7.8: Removed property description header when empty
v1.7.7: Changed Book Now button CSS
v1.7.6: Fixed property link CSS
v1.7.5: Removed sort by Pet Allowed
v1.7.4: Prevent regenerate when Lodgix is not accessible
v1.7.3: Escape permalink URL
v1.7.2: Added referer to calendar link
v1.7.1: Fixed mobile tabbed display
v1.7.0: Fixed warning string concatenation
v1.6.9: Fixed default calendar code
v1.6.8: Added policies last updated date
v1.6.7: Added multi language calendars
v1.6.6: Fixed Google Maps distorted controls
v1.6.5: Added legend to default zoom level
v1.6.4: Added Google Map zoom level option
v1.6.3: Fixed en_UK locale
v1.6.2: Fixed datatables AJAX URL
v1.6.1: Fixed CSS conflict with some themes
v1.6.0: Deprecated menu options
v1.5.9: Fixed settings CSS
v1.5.8: Fixed regeneration
v1.5.7: Fixed Lodgix menu
v1.5.6: Fixed property details locale
v1.5.5: Added plugin icon
v1.5.4: Added new plugin settings design
v1.5.3: Fixed multi unit calendar
v1.5.2: Added HTTPS support to availability calendars
v1.5.1: Fixed single unit help text
v1.5.0: Fixed Chrome featured image display issue
v1.4.9: Changed property sorting for grid template
v1.4.8: Fixed css template bug
v1.4.7: Added Vacation Rental grid template file
v1.4.6: Added Vacation Rental grid template
v1.4.5: Fixed Chrome display issue on tabbed layout
v1.4.4: Fixed rate table not appearing on Chrome
v1.4.3: Fixed single unit calendar appearence in tabbed layout
v1.4.2: Fixed single unit help text
v1.4.1: Adjusted Slider for null width
v1.4.0: Adjusted JS for compatibility
v1.3.9: Improved notification fault tolerance
v1.3.8: Added support for HTML in property description
v1.3.7: Added ids to tabbed interface
v1.3.6: Added theme default template
v1.3.5: Added theme page templates
v1.3.4: Fixed badge CSS
v1.3.3: Fixed contact link on new installations
v1.3.2: Fixed weekend rates display
v1.3.1: Fixed Book Now button url
v1.3.0: Fixed empty city name
v1.2.12: Added plugin DB version to notify
v1.2.11: Added plugin DB version to query string
v1.2.10: Fixed rental search localization filter
v1.2.9: Changed image source to CDN
v1.2.8: Fixed not default wpbd prefix
v1.2.7: Fixed default weekly and daily rates options
v1.2.6: Fixed german translation. Fixed Rental Search Studio option. Added option to not display weekly and monthly rates
v1.2.5: Fixed jQuery UI tabs calendar conflict
v1.2.4: Fixed jQuery UI tabs height bug
v1.2.3: Optimized image download for a large number of properties
v1.2.2: Fixed german translation
v1.2.1: Fixed picture array value
v1.2.0: Fixed svn merge error
v1.1.62: Fixed property pictures captions
v1.1.61: Added no policies rates display option
v1.1.60: Fixed Lodgix notify
v1.1.59: Fixed Virtual Tour link
v1.1.58: Wordpress 3.9 adjustments
v1.1.57: Fixed widgets.php bug
v1.1.56: Fixed add_action bug
v1.1.55: Fixed Search Bug II
v1.1.54: Fixed Search Bug
v1.1.53: Widgets updated II
v1.1.52: Widgets updated
v1.1.51: Non SSL support disabled
v1.1.50: Fixed Bug - single property
v1.1.49: Fixed Bug - single property
v1.1.48: Fixed Bug - rates
v1.1.47: Fixed Bug - single quote area
v1.1.46: Localization - part V
v1.1.45: Localization - part IV
v1.1.44: Localization - part III
v1.1.43: Localization - part II
v1.1.42: Localization - part I
v1.1.41: Changed datepicker z-index
v1.1.40: Fixed Widget header width
v1.1.39: Fixed Amenities UL
v1.1.38: Added Pets CSS class
v1.1.37: Added Thesis 2 support
v1.1.36: Fixed Merged Rates
v1.1.35: Added Merged Rates
v1.1.34: Added full size thumbnails option
v1.1.33: Added plugin rate button
v1.1.32: Fixed Book Now button II
v1.1.31: Fixed Custom Amenity Search
v1.1.30: Fixed Book Now button
v1.1.29: Added extra search rental widget
v1.1.28: Fixed search availability bug II
v1.1.27: Fixed search availability bug
v1.1.26: Fixed Policies formatting
v1.1.25: Added amenities to search widget
v1.1.24: Altered featured CSS
v1.1.23: Altered featured CSS
v1.1.22: Implemented text expander in property listings
v1.1.21: Fixed search widget
v1.1.20: Responsive header for the single property page
v1.1.19: Allow multiple websites
v1.1.18: Added AJAX search details
v1.1.17: Responsive features table 
v1.1.16: Fixed regressions on the vacation rental listings page
v1.1.15: Responsive Design
v1.1.14: Fix lodgix-custom.css path
v1.1.11: Fix German Description
v1.1.10: Fix German Details
v1.1.09: Add VR scrollbars
v1.1.08: Fix CSS width
v1.1.07: Responsive Design
v1.1.06: Fixed matching areas
v1.1.05: Fixed template CSS
v1.1.04: Fixed wpdb functions
v1.1.03: Fixed IE CSS
v1.1.02: Fixed jquery validate
v1.1.01: Fixed search widget
v1.1.00: Fixed CeeBox
v1.0.99: Tabbed CSS minor adjustments - 2
v1.0.98: Tabbed CSS minor adjustments
v1.0.97: Added option to load css/lodgix-custom.css
v1.0.96: Fixed Reviews Separator
v1.0.95: Fixed Single Unit Calendar
v1.0.94: Added Theme Support
v1.0.93: Added Link Rotation
v1.0.92: CSS Adjustment
v1.0.91: Search Widget CSS Adjustment
v1.0.90: Fixed Datepicker Current Date
v1.0.89: Fixed Search Widget
v1.0.88: Changed Nights Input
v1.0.87: Bug Fix
v1.0.86: Search Widget Enhancements, HTML5 Single Calendar
v1.0.85: German Language correction
v1.0.84: Fixed German Multi Unit Instructions
v1.0.83: Updated Google Maps API Sensor
v1.0.82: Updated Google Maps API Version Zoom
v1.0.81: Updated Google Maps API Version
v1.0.80: Fixed sidebar position
v1.0.79: Changed multi unit calendar to HTML5
v1.0.78: Added property Wordpress Status
v1.0.77: Fixed german language path
v1.0.76: Fixed video and virtual tour links
v1.0.75: Fixed page encoding
v1.0.74: Fixed plugin path 
v1.0.73: Fixed Include Bug
v1.0.72: Fixed Per Person Rates
v1.0.71: Added Per Person Rates
v1.0.70: Fixed Availability Links
v1.0.69: Added New Tabbed Design
v1.0.68: Added GetURLs AJAX
v1.0.67: Added Studio support
v1.0.66: CSS Adjusted
v1.0.65: Fixed shortcode issue
v1.0.64: Added property id feature
v1.0.62: Fixed gravity forms bug
v1.0.61: Added gravity forms properties
v1.0.60: Add single page content wrapper
v1.0.59: Fixed Area Page
v1.0.58: Fixed IE Calendar borders
v1.0.57: Addional calendar fix
v1.0.56: Addional calendar fix
v1.0.55: Added non flash single calendar
v1.0.54: Added user german amenities
v1.0.53: Altered Search Rentals Widget CSS
v1.0.52: Dynamic Rental Pages
v1.0.51: Altered Search Rentals Widget CSS
v1.0.50: Fixed Search Rentals Widget
v1.0.49: Added Search Rentals Widget
v1.0.48: Changed policies position
v1.0.47: Fixed featured rentals image path
v1.0.46: Added beds setup to property description
v1.0.45: Added video/virtual tour to property page
v1.0.44: Added new registration link.
v1.0.43: Added plugin installation check.
v1.0.42: Changed options text.
v1.0.41: Changed options text. Allow_url_fopen no longer required.
v1.0.40: Fixed version issue
v1.0.39: Increased Contact URL size
v1.0.38: Added property name to contact url querystring
v1.0.37: Fixed small css items
v1.0.36: Fixed featured properties IE8
v1.0.35: Added Lodgix.com links
v1.0.34: Fixed Wordpress 3.2.0 incompatibility
v1.0.33: Added float right option to widget
v1.0.32: Added option to display widget horizontally
v1.0.31: Fixed Featured Widget
v1.0.30: Fixed rate CSS
v1.0.29: Fixed extra draft post
v1.0.28: Fixed small CSS issues
v1.0.27: Fixed availability link
v1.0.26: Fixed Gravity Forms compatibility
v1.0.25: Added option for Custom Page Templates
v1.0.24: Added Purevision theme compatibility
v1.0.23: Replace check icon
v1.0.22: Added FlexSqueeze theme compatibility
v1.0.21: Changed guest reviews
v1.0.20: Fixed captions length
v1.0.19: Implemented new upgrade
v1.0.18: Fixed no pets allowed
v1.0.17: Fixed number of bathrooms
v1.0.16: Added German Contact URL
v1.0.15: New property page design
v1.0.14: Fixed area array
v1.0.10: Implemented areas
v1.0.9: Fixed multi-language update issue
v1.0.7: Fix single property availability
v1.0.4: Fixed directory
v1.0.0: Initial release

*/

define('PLUGIN_PATH', __FILE__);

function p_lodgix_autoloader($className) {
    $fileName = dirname(PLUGIN_PATH) . '/classes/' . strtolower($className) . '.php';
    if (file_exists($fileName)) {
        include $fileName;
    }
}

spl_autoload_register('p_lodgix_autoloader');

include_once dirname(PLUGIN_PATH) . '/lodgix_widget_rental_search_v1.php';
include_once dirname(PLUGIN_PATH) . '/lodgix_widget_rental_search_v2.php';
include_once dirname(PLUGIN_PATH) . '/lodgix_widget_featured_rentals.php';

function p_lodgix_reorder_plugins() {
    $plugins = get_option('active_plugins');
    if ($plugins[count($plugins) - 1] != 'lodgixcom-vacation-rental-listing-management-booking-plugin/lodgix.php') {
        $counter = 0;
        foreach ($plugins as $plugin) {
            if ($plugin == 'lodgixcom-vacation-rental-listing-management-booking-plugin/lodgix.php') {
                unset($plugins[$counter]);
            }
            $counter = $counter + 1;
        }
        array_push($plugins, 'lodgixcom-vacation-rental-listing-management-booking-plugin/lodgix.php');
        $plugins = array_values($plugins);
        update_option('active_plugins', $plugins);
    }
}

add_action('activated_plugin', 'p_lodgix_reorder_plugins');

function p_lodgix_deactivate() {
    $config = new LodgixServiceConfig();
    $install = new LodgixServiceInstall($config);
    $install->deleteAll();
}

register_deactivation_hook(PLUGIN_PATH, 'p_lodgix_deactivate');

if (version_compare(PHP_VERSION, '5.5', '<')) {
    add_action('admin_notices', create_function('', "echo '<div class=\"error\"><p>".__('Lodgix plugin requires PHP 5.5 to function properly. Please upgrade PHP or deactivate Lodgix plugin.', 'plugin-name') ."</p></div>';"));
} elseif (class_exists('p_lodgix')) {
    new p_lodgix();
}

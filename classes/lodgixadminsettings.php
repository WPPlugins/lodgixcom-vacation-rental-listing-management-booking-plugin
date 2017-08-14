<?php

class LodgixAdminSettings {

    function __construct($config) {
        $this->config = $config;
        $property = (new LodgixServiceProperties($this->config))->getRandom();
        if ($property) {
            $this->property = $property;
            $this->lodgixPropertyListing = new LodgixPropertyListing($property);
            $this->lodgixPropertyDetail = new LodgixPropertyDetail($property, $this->config->get('p_lodgix_date_format'), $this->config->get('p_lodgix_gmap_api_key'));
        } else {
            $this->property = null;
            $this->lodgixPropertyListing = null;
            $this->lodgixPropertyDetail = null;
        }
    }

    function page() {
        $css = plugins_url('css/admin.css', PLUGIN_PATH);
        $nonce = wp_nonce_field('p_lodgix-update-options', '_wpnonce', true, false);
        $tabSubscriber = $this->tabSubscriber();
        $tabInventory = $this->tabInventory();
        $tabPropList = $this->tabPropList();
        $tabPropDetail = $this->tabPropDetail();
        $tabLegacy = $this->tabLegacy();
        $sidebar = $this->sidebar();
        if ($this->property) {
            $disabled = '';
            $href2 = 'href="#ldgxSettingsPanelInventory" data-toggle="tab"';
            $href3 = 'href="#ldgxSettingsPanelPropList" data-toggle="tab"';
            $href4 = 'href="#ldgxSettingsPanelPropDetail" data-toggle="tab"';
            $href5 = 'href="#ldgxSettingsPanelLegacy" data-toggle="tab"';
        } else {
            $disabled = 'class="disabled"';
            $href2 = '';
            $href3 = '';
            $href4 = '';
            $href5 = '';
        }
        $html = <<<EOT
<link href="$css" rel="stylesheet" type="text/css">
<form method="post" id="p_lodgix_options">
    $nonce
    <div class="wrap">
        <div class="row">
      		<div class="col col-lg-12 col-sm-12">
       			<h1>Lodgix Settings</h1>
 				<p>&nbsp;</p>
  				<div class="row">
          			<div class="col col-lg-8 col-sm-8">
                        <ul class="nav nav-tabs" id="ldgxSettingsTabs">
                            <li class="active">
                                <a id="ldgxSettingsTabGeneral" href="#ldgxSettingsPanelGeneral" data-toggle="tab">General</a>
                            </li>
                            <li $disabled>
                                <a id="ldgxSettingsTabInventory" $href2>Inventory</a>
                            </li>
                            <li $disabled>
                                <a id="ldgxSettingsTabPropList" $href3>Property List</a>
                            </li>
                            <li $disabled>
                                <a id="ldgxSettingsTabPropDetail" $href4>Property Detail</a>
                            </li>
                            <li $disabled>
                                <a id="ldgxSettingsTabLegacy" $href5>Legacy Options</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active ldgxSettingsPanel" id="ldgxSettingsPanelGeneral">
                                $tabSubscriber
                            </div>
                            <div class="tab-pane ldgxSettingsPanel" id="ldgxSettingsPanelInventory">
                                $tabInventory
                            </div>
                            <div class="tab-pane ldgxSettingsPanel" id="ldgxSettingsPanelPropList">
                                $tabPropList
                            </div>
                            <div class="tab-pane ldgxSettingsPanel" id="ldgxSettingsPanelPropDetail">
                                $tabPropDetail
                            </div>
                            <div class="tab-pane ldgxSettingsPanel" id="ldgxSettingsPanelLegacy">
                                $tabLegacy
                            </div>
                        </div>
                    </div>
           			<div class="col col-lg-4 col-sm-4" align="center">
                        $sidebar
                    </div>
      			</div>
      		</div>
        </div>
    </div> 
</form>
<div class="clear"></div>
EOT;
        return $html;
    }

    protected function tabSubscriber() {
        $valueId = $this->config->get('p_lodgix_owner_id');
        $valueKey = $this->config->get('p_lodgix_api_key');
        $languages = $this->languages();
        $html = <<<EOT
<div class="form-group">
    <label for="p_lodgix_owner_id">Customer ID:</label>
    <input class="form-control" name="p_lodgix_owner_id" type="text" id="p_lodgix_owner_id" value="$valueId">
    <p class="help-block">
        Enter your Lodgix Customer ID
        (<a href="javascript:void(0)" onclick="p_lodgix_set_demo_credentials(); return false;">use demo ID</a>)
    </p>
</div>
<div class="form-group">
    <label for="p_lodgix_api_key">API Key:</label>
    <input class="form-control" name="p_lodgix_api_key" type="text" id="p_lodgix_api_key" value="$valueKey">
    <p class="help-block">
        Enter your Lodgix API Key
    </p>
</div>
<p>
    To setup your properties for use with the plug-in, a Lodgix.com subscription is required.
</p>
<div class="lodgix-tryit">
    <a href="http://www.lodgix.com/register/0?wordpress=1">
        <h2>TRY IT FREE</h2>
        <div>No Credit Card Required</div>
    </a>
</div>
<p>
    If you just wish to test the plug-in within your website using demo property images and data, and do not wish
    to sign up for a Lodgix.com subscription at this time, use the demo ID and API key provided.
</p>
<p>
    If you are a current Lodgix.com subscriber, please login to your Lodgix.com account and go to
    "Settings > Important Settings" to obtain your "Customer ID" and "API Key".
</p>
<br>
$languages
<p>
    To select other languages, please enable it within WPML setup first.
</p>
EOT;
        $html .= $this->buttonBar();
        return $html;
    }

    protected function tabInventory() {
        $checkedSelectAll = $this->config->get('p_lodgix_featured_select_all') ? 'checked' : '';
        $html = <<<EOT
<br>
<table id="lodgix_properties_table" class="table table-striped table-bordered" width="100%" cellspacing="0">
    <thead>
        <tr>
            <th>Order</td>
            <th>ID</td>
            <th>Name</td>
            <th>Featured?</td>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th colspan="3" style="text-align:right"></th>
            <th colspan="1" style="text-align:center;white-space:nowrap;">
                <input type="checkbox" id="lodgix_select_all" onclick="javascript:lodgix_toggle_select_all();"
                $checkedSelectAll style="margin:0"> Select All
            </th>
        </tr>
    </tfoot>
</table>
EOT;
        return $html;
    }

    protected function tabPropList() {
        $preview = $this->previewPropList();
        $html = '';
        $html .= $this->adminOption('p_lodgix_vacation_rentals_page_design', 'Layout:', array(
            0 => 'Rows 1',
            2 => 'Rows 2',
            1 => 'Grid 1'
        ), 'Select Property List and Search Results layout (rows or grid) and style.');
        $html .= $this->adminOption('p_lodgix_icon_set', 'Icon Set:', array(
            LodgixConst::ICON_SET_OLD => LodgixConst::ICON_SET_OLD,
            LodgixConst::ICON_SET_CIRCLE => LodgixConst::ICON_SET_CIRCLE,
            LodgixConst::ICON_SET_FILLED => LodgixConst::ICON_SET_FILLED,
            LodgixConst::ICON_SET_GRADIENT_COLOR => LodgixConst::ICON_SET_GRADIENT_COLOR,
            LodgixConst::ICON_SET_GRADIENT_GRAY => LodgixConst::ICON_SET_GRADIENT_GRAY,
            LodgixConst::ICON_SET_OUTLINED => LodgixConst::ICON_SET_OUTLINED,
            LodgixConst::ICON_SET_SQUARED_COLOR => LodgixConst::ICON_SET_SQUARED_COLOR,
            LodgixConst::ICON_SET_SQUARED_GRAY => LodgixConst::ICON_SET_SQUARED_GRAY
        ), 'Select icon style.');
        $html .= <<<EOT
<div id="ldgxAdminPreviewPropList" style="display:none">
    $preview
</div>
EOT;
        $html .= $this->adminOptionLabel('Display:');
        $html .= <<<EOT
<div class="ldgxAdminCols">
    <div class="ldgxAdminCol ldgxAdminCol2">
EOT;
        $html .= $this->adminOptionYesNo('p_lodgix_full_size_thumbnails', 'Full Size Thumbnails?', 'ldgxSettingsRowsOnly');
        $html .= $this->adminOptionYesNo('p_lodgix_use_ssl_pictures', 'HTTPS Gallery?');
        $html .= $this->adminOptionYesNo('p_lodgix_display_search_learn_more_book_now_button', '"Learn More / Book Now" button?', 'ldgxSettingsRowsOnly');
        $html .= $this->adminOptionYesNo('p_lodgix_display_search_areas', 'Areas?');
        $html .= $this->adminOptionYesNo('p_lodgix_display_search_text_expander', '"More" button for long descriptions?', 'ldgxSettingsRowsOnly');
        $html .= $this->adminOptionYesNo('p_lodgix_display_search_bedrooms', '"Bedrooms"?');
        $html .= $this->adminOptionYesNo('p_lodgix_display_search_bathrooms', '"Bathrooms"?', 'ldgxSettingsRowsOnly');
        $html .= $this->adminOptionYesNo('p_lodgix_display_search_guests', '"# of Guests"?', 'ldgxSettingsRowsOnly');
        $html .= $this->adminOptionYesNo('p_lodgix_display_search_type', '"Rental Type"?', 'ldgxSettingsRowsOnly');
        $html .= <<<EOT
    </div>
    <div class="ldgxAdminCol ldgxAdminCol2">
EOT;
        $html .= $this->adminOptionYesNo('p_lodgix_display_search_pets', '"Pets"?', 'ldgxSettingsRowsOnly');
        $html .= $this->adminOptionYesNo('p_lodgix_display_search_daily_rates', 'Daily rates?');
        $html .= $this->adminOptionYesNo('p_lodgix_display_search_weekly_rates', 'Weekly rates?');
        $html .= $this->adminOptionYesNo('p_lodgix_display_search_monthly_rates', 'Monthly rates?');
        $html .= $this->adminOptionYesNo('p_lodgix_display_search_table_icons', 'Icons in the table?', 'ldgxSettingsRowsOnly');
        $html .= $this->adminOptionYesNo('p_lodgix_display_search_table_high_rate', 'High rate in the table?');
        $html .= $this->adminOptionYesNo('p_lodgix_display_availability_icon', '"Availability" button?', 'ldgxSettingsRowsOnly');
        $html .= $this->adminOptionYesNo('p_lodgix_display_icons', '"Google Map", "Contact Us" and "Details" icons?', 'ldgxSettingsRowsOnly');
        $html .= <<<EOT
    </div>
</div>
EOT;
        $html .= $this->buttonBar();
        return $html;
    }

    protected function tabPropDetail() {
        global $wpdb;
        $tableLanguages = $wpdb->prefix . LodgixConst::TABLE_LANGUAGES;
        $html = '';
        $html .= $this->adminOption('p_lodgix_single_page_design', 'Layout:', array(
            0 => 'One Page',
            1 => 'Tabbed'
        ));
        $html .= $this->adminOption('p_lodgix_display_title', 'Property Name:', array(
            'title' => 'Marketing Title',
            'name' => 'Name'
        ));
        $html .= $this->adminOptionLabel('Display:');
        $html .= <<<EOT
<div class="ldgxAdminCols">
    <div class="ldgxAdminCol ldgxAdminCol3">
EOT;
        $html .= $this->adminOptionYesNo('p_lodgix_display_daily_rates', 'Daily rates?');
        $html .= $this->adminOptionYesNo('p_lodgix_display_property_book_now_always', '"Book Now" button?');
        $html .= <<<EOT
    </div>
    <div class="ldgxAdminCol ldgxAdminCol3">
EOT;
        $html .= $this->adminOptionYesNo('p_lodgix_display_weekly_rates', 'Weekly rates?');
        $html .= <<<EOT
    </div>
    <div class="ldgxAdminCol ldgxAdminCol3">
EOT;
        $html .= $this->adminOptionYesNo('p_lodgix_display_monthly_rates', 'Monthly rates?');
        $html .= <<<EOT
    </div>
</div>
EOT;
        $html .= $this->previewPropDetailHeader();
        $html .= $this->previewPropDetailTabs();
        $settingTabTitleDetails = $this->adminOptionInput('p_lodgix_single_page_tab_details', 'Title:', 255, 'ldgxSettingsTabTitle');
        $settingTabTitlePolicies = $this->adminOptionInput('p_lodgix_single_page_tab_policies', 'Title:', 255, 'ldgxSettingsTabTitle');
        $settingTabTitleCalendar = $this->adminOptionInput('p_lodgix_single_page_tab_calendar', 'Title:', 255, 'ldgxSettingsTabTitle');
        $settingTabTitleLocation = $this->adminOptionInput('p_lodgix_single_page_tab_location', 'Title:', 255, 'ldgxSettingsTabTitle');
        $settingTabTitleAmenities = $this->adminOptionInput('p_lodgix_single_page_tab_amenities', 'Title:', 255, 'ldgxSettingsTabTitle');
        $settingTabTitleReviews = $this->adminOptionInput('p_lodgix_single_page_tab_reviews', 'Title:', 255, 'ldgxSettingsTabTitle');
        $settingTabTitleDetailsIsVisible = $this->adminOptionYesNo('p_lodgix_single_page_tab_details_is_visible', 'Visible');
        $settingTabTitlePoliciesIsVisible = $this->adminOptionYesNo('p_lodgix_single_page_tab_policies_is_visible', 'Visible');
        $settingTabTitleCalendarIsVisible = $this->adminOptionYesNo('p_lodgix_single_page_tab_calendar_is_visible', 'Visible');
        $settingTabTitleLocationIsVisible = $this->adminOptionYesNo('p_lodgix_single_page_tab_location_is_visible', 'Visible');
        $settingTabTitleAmenitiesIsVisible = $this->adminOptionYesNo('p_lodgix_single_page_tab_amenities_is_visible', 'Visible');
        $settingTabTitleReviewsIsVisible = $this->adminOptionYesNo('p_lodgix_single_page_tab_reviews_is_visible', 'Visible');
        $settingImageSize = $this->adminOption('p_lodgix_image_size', 'Image Size:', array(
            LodgixConst::IMAGE_640x480 => '640 x 480',
            LodgixConst::IMAGE_800x600 => '800 x 600',
            LodgixConst::IMAGE_ORIGINAL => 'Original Image'
        ), 'Select maximum photo width and height.');
        $settingDisplayBedrooms =
            $this->adminOptionLabel('Details:') .
            $this->adminOptionYesNo('p_lodgix_display_beds', 'Display Bedrooms?');
        $settingInstructions =
            $this->adminOptionLabel('Booking Calendar Instructions:') .
            $this->adminOptionYesNo('p_lodgix_display_single_instructions', 'Display "Instructions" under Single Unit Calendar?') .
            $this->adminOptionYesNo('p_lodgix_display_multi_instructions', 'Display "Instructions" under Multi Unit Calendar?');
        $settingMapApiKey = $this->adminOptionInput('p_lodgix_gmap_api_key', 'Google Map Api Key:', 255, 'ldgxSettingsMapApiKey');
        $settingMapZoom = $this->adminOption('p_lodgix_gmap_zoom_level', 'Google Map zoom level:', array(
            0 => 'Default (' . ($this->config->get('p_lodgix_single_page_design') == 0 ? '10' : '13') . ')',
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            7 => '7',
            8 => '8',
            9 => '9',
            10 => '10',
            11 => '11',
            12 => '12',
            13 => '13',
            14 => '14',
            15 => '15',
            16 => '16',
            17 => '17',
            18 => '18'
        ), 'Select initial map zoom.');
        $settingRates = $this->adminOption('p_lodgix_rates_display', 'Rates Display:', array(
            0 => 'Regular',
            1 => 'Merged',
            2 => 'None'
        ), 'Display Rates? Regular or Merged?');
        if ($this->lodgixPropertyDetail) {
            $slider = $this->lodgixPropertyDetail->slider(false);
            $details = $this->lodgixPropertyDetail->details();
            $rates = $this->lodgixPropertyDetail->rates();
            $calendar = $this->lodgixPropertyDetail->calendar();
            $map = $this->lodgixPropertyDetail->map(false);
            $amenities = $this->lodgixPropertyDetail->amenities();
            $reviews = $this->lodgixPropertyDetail->reviews();
        } else {
            $slider = '';
            $details = '';
            $rates = '';
            $calendar = '';
            $map = '';
            $amenities = '';
            $reviews = '';
        }
        $html .= <<<EOT
<div class="ldgxSettingsPreviewSingle ldgxSettingsPreviewSingleDetails">
    <div class="ldgxSettingsPreviewContent ldgxSettingsPreviewContentDetails">
        $settingTabTitleDetailsIsVisible
        $settingTabTitleDetails
        $settingImageSize
        $slider
        $settingDisplayBedrooms
        $details
    </div>
</div>
<div class="ldgxSettingsPreviewSingle ldgxSettingsPreviewSinglePolicies">
    <div class="ldgxSettingsPreviewContent ldgxSettingsPreviewContentPolicies">
        $settingTabTitlePoliciesIsVisible
        $settingTabTitlePolicies
        $settingRates
        $rates
    </div>
</div>
<div class="ldgxSettingsPreviewSingle ldgxSettingsPreviewSingleCalendar">
    <div class="ldgxSettingsPreviewContent ldgxSettingsPreviewContentCalendar">
        $settingTabTitleCalendarIsVisible
        $settingTabTitleCalendar
        $settingInstructions
        $calendar
    </div>
</div>
<div class="ldgxSettingsPreviewSingle ldgxSettingsPreviewSingleLocation">
    <div class="ldgxSettingsPreviewContent ldgxSettingsPreviewContentLocation">
        $settingTabTitleLocationIsVisible
        $settingTabTitleLocation
        $settingMapApiKey
        $settingMapZoom
        $map
    </div>
</div>
<div class="ldgxSettingsPreviewSingle ldgxSettingsPreviewSingleAmenities">
    <div class="ldgxSettingsPreviewContent ldgxSettingsPreviewContentAmenities">
        $settingTabTitleAmenitiesIsVisible
        $settingTabTitleAmenities
        $amenities
    </div>
</div>
<div class="ldgxSettingsPreviewSingle ldgxSettingsPreviewSingleReviews">
    <div class="ldgxSettingsPreviewContent ldgxSettingsPreviewContentReviews">
        $settingTabTitleReviewsIsVisible
        $settingTabTitleReviews
        $reviews
    </div>
</div>
EOT;
        $languages = $wpdb->get_results("SELECT * FROM $tableLanguages WHERE enabled=1");
        if ($languages) {
            foreach ($languages as $l) {
                $html .= $this->adminOptionInput('p_lodgix_contact_url_' . $l->code, $l->name . ' Contact URL:', 255, 'ldgxSettingsContactUrl');
            }
        }
        $html .= $this->adminOptionLabel('Wordpress:');
        $html .= $this->adminOptionYesNo('p_lodgix_allow_comments', 'Allow comments?');
        $html .= $this->adminOptionYesNo('p_lodgix_allow_pingback', 'Allow ping-backs?');
        $html .= $this->buttonBar();
        return $html;
    }

    protected function tabLegacy() {
        $html = '';

        $html .= $this->adminOptionLabel('Theme Options');
        $html .= $this->adminOptionYesNo('p_lodgix_thesis_compatibility', 'Thesis 1 Compatibility');

        $thesis_2_template_options = Array();
        array_push($thesis_2_template_options,Array('class' => '', 'title' => 'Default'));
        try {
            $thesis_skin = get_option('thesis_skin');
            if ($thesis_skin) {
                $thesis_classic_r_templates = get_option('thesis_classic_r_templates');
                if (is_array($thesis_classic_r_templates) || $thesis_classic_r_templates instanceof Traversable) {
                    foreach ($thesis_classic_r_templates as $key => $value) {
                        if (strpos($key, 'custom_') === 0) {
                            $title = $value['title'];
                        } else {
                            $title = ucwords($key);
                        }
                        array_push($thesis_2_template_options, Array('class' => $key, 'title' => $title));
                    }
                }
            }
        } catch (SomeException $e) {}

        $checked = $this->config->get('p_lodgix_thesis_2_compatibility') == 1 ? 'checked' : '';
        $disabled = !$this->config->get('p_lodgix_thesis_2_compatibility') ? 'disabled style="display:none"' : '';
        $optHtml = '';
        foreach($thesis_2_template_options as $to) {
            $value = $to['class'];
            $title = $to['title'];
            $selected = $this->config->get('p_lodgix_thesis_2_template') == $to['class'] ? 'selected' : '';
            $optHtml .= <<<EOT
<option value="$value" $selected>$title</option>
EOT;
        }
        $html .= <<<EOT
<div class="checkbox">
    <label>
        <input type="checkbox" name="p_lodgix_thesis_2_compatibility" id="p_lodgix_thesis_2_compatibility" $checked>Thesis 2 Compatibility
    </label>
</div>
<div class="form-group">
    <select class="form-control ldgxAdminOption2ndField"
        name="p_lodgix_thesis_2_template" id="p_lodgix_thesis_2_template" $disabled>
        $optHtml
    </select>
</div>
EOT;

        $pageTemplate = $this->config->get('p_lodgix_page_template');
        $customPageTemplate = $this->config->get('p_lodgix_custom_page_template');
        $selectedDefault = $pageTemplate == 'page.php' ? 'selected' : '';
        $selectedCustom = $pageTemplate == 'CUSTOM' ? 'selected' : '';
        $disabledCustom = $pageTemplate != 'CUSTOM' ? 'disabled style="display:none"' : '';
        $optHtml = '';
        $templates = get_page_templates();
        foreach ( $templates as $tn => $tf) {
            $selected = $pageTemplate == $tf ? 'selected' : '';
            $optHtml .= <<<EOT
<option value="$tf" $selected>$tn</option>
EOT;
        }
        $html .= <<<EOT
<div class="form-group">
    <label for="p_lodgix_page_template">Page Template:</label>
    <select class="form-control" name="p_lodgix_page_template" id="p_lodgix_page_template"
        onchange="javascript:set_lodgix_page_template_enabled();">
        <option value="NONE">Lodgix Default</option>
        <option value="page.php" $selectedDefault>Theme Default</option>
        $optHtml
        <option value="CUSTOM" $selectedCustom>Custom</option>
    </select>
</div>
<div class="form-group">
    <input type="text" class="form-control" name="p_lodgix_custom_page_template" id="p_lodgix_custom_page_template"
        value="$customPageTemplate" $disabledCustom>
</div>
EOT;

        $html .= $this->adminOptionLabel('Menu Display Options');
        $help = 'This option is no longer supported (or needed). Please set this option to "None" and add back the menu items using WordPress menus instead (Appearance > Menu).';
        $html .= $this->adminOption(
            'p_lodgix_vacation_rentals_page_pos',
            'Vacation Rentals Menu Position:',
            array(
                '-1' => 'None',
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '8' => '8',
                '9' => '9'
            ),
            $help
        );
        $html .= $this->adminOption(
            'p_lodgix_availability_page_pos',
            'Availability Page Menu Position:',
            array(
                '-1' => 'None',
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '8' => '8',
                '9' => '9'
            ),
            $help
        );

        $html .= $this->buttonBar();
        return $html;
    }

    protected function sidebar() {
        $logo = plugins_url('images/logo_250_63.png', PLUGIN_PATH);
        $likeUrl = 'http://www.lodgix.com';
        $html = <<<EOT
<div class="ldgxAdminRight">
    <div><a href="http://www.lodgix.com"><img src="$logo" class="ldgxLogo"></a></div>
    <div class="ldgxAdminBox">
        <h2>Like this Plugin?</h2>

        <a href="http://wordpress.org/plugins/lodgixcom-vacation-rental-listing-management-booking-plugin/" target="_blank"><button type="submit" class="ldgxAdminRateButton">Rate this plugin	&#9733;	&#9733;	&#9733;	&#9733;	&#9733;</button></a><br><br>

        <div id="fb-root"></div>
        <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
        <div class="fb-like" data-href="$likeUrl" data-send="true" data-layout="button_count" data-width="450" data-show-faces="true"></div>
        <br>

        <a href="https://twitter.com/share" class="twitter-share-button" data-text="Just been using #Lodgix #WordPress plugin" data-via="lodgix" data-related="lodgix">Tweet</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
        <br>

        <a href="http://bufferapp.com/add" class="buffer-add-button" data-text="Just been using #Lodgix #WordPress plugin" data-url="$likeUrl" data-count="horizontal" data-via="lodgix">Buffer</a><script type="text/javascript" src="http://static.bufferapp.com/js/button.js"></script>
        <br>

        <div class="g-plusone" data-size="medium" data-href="$likeUrl"></div>
        <script type="text/javascript">
          (function() {
            var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
            po.src = 'https://apis.google.com/js/plusone.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
          })();
        </script>
        <br>
        <su:badge layout="2" location="$likeUrl"></su:badge>
        <script type="text/javascript">
          (function() {
            var li = document.createElement('script'); li.type = 'text/javascript'; li.async = true;
            li.src = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//platform.stumbleupon.com/1/widgets.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(li, s);
          })();
        </script>			

    </div>
    <div class="ldgxAdminBox">
        <h2>About Lodgix.com</h2>
        <p>
            <img class="ldgxAdminAvatar" src="http://gravatar.com/avatar/b06319de949d4ce08bbafd6306a9f6f9?s=70">
            <a href="https://twitter.com/lodgix" class="twitter-follow-button" data-show-count="false">Follow @lodgix</a>
        </p>
        <p>
            <a href="http://www.lodgix.com">Lodgix.com</a> is a leading provider of web-based vacation rental management software.
            We do not charge setup fees or require a contract of any kind. We do not collect a percentage of every reservation.
            We simply charge a flat monthly fee and seek to provide value to property owners and managers who seek an easy to
            use application to manage and grow their business.
        </p>
    </div>
</div>
EOT;
        return $html;
    }

    protected function languages() {
        global $wpdb;
        $tableWpmlLanguages = $wpdb->prefix . LodgixConst::TABLE_WPML_LANGUAGES;
        $tableLanguages = $wpdb->prefix . LodgixConst::TABLE_LANGUAGES;
        if ($wpdb->get_var("SHOW TABLES LIKE '$tableWpmlLanguages'") == $tableWpmlLanguages) {
            $sql = "SELECT * FROM $tableLanguages WHERE code='en' OR code IN (SELECT code FROM $tableWpmlLanguages WHERE active=1) order by case when code='en' then 0 else 1 end, name";
            $languages = $wpdb->get_results($sql);
        } else {
            $languages = null;
        }
        if (!$languages) {
            $languages = $wpdb->get_results("SELECT * FROM $tableLanguages WHERE code='en'");
        }
        $html = '';
        if ($languages) {
            foreach ($languages as $l) {
                $code = $l->code;
                $checked = $l->enabled ? 'checked' : '';
                $disabled = $code == 'en' ? 'disabled="disabled" onclick="return false"' : '';
                $html .= <<<EOT
<p><span style="vertical-align: middle;">
    <input name="p_lodgix_generate_$code" type="checkbox" id="p_lodgix_generate_$code" $checked $disabled>
    $l->name
</span></p>
EOT;
            }
        }
        return $html;
    }

    protected function buttonBar() {
        $pluginUrl = plugin_dir_url(plugin_basename(PLUGIN_PATH));
        $progressIndicator = $pluginUrl . 'images/throbber.gif';
        $reload = $this->property ? '' : 'true';
        $html = <<<EOT
<p class="submit">
    <div class="lodgix_processing_throbber" style="display: none;">
        <img src="$progressIndicator">&nbsp;
        <b>Please wait while database is updated. Time will depend on the number of properties to process.</b>
        <br><br>
    </div>
    <div class="lodgix_processing_message"></div>
    <input type="button" onclick="lodgix_submit_save($reload)" name="p_lodgix_save" id="p_lodgix_save" class="button-primary" value="Save and Regenerate">&nbsp;
    <input onclick="lodgix_submit_clean()" type="button" name="p_lodgix_clean" id="p_lodgix_clean" class="button-primary" value="Clean Database">
</p>
EOT;
        return $html;
    }

    function adminOption($optionName, $label, $values, $help='', $class='') {
        if ($help) {
            $help = "<p class='help-block'>$help</p>";
        }
        $optHtml = '';
        foreach ($values as $k => $v) {
            $selected = $this->config->get($optionName) == $k ? 'selected' : '';
            $optHtml .= <<<EOT
<option value="$k" $selected>$v</option>
EOT;
        }
        $html = <<<EOT
<div class="form-group $class">
    <label for="$optionName">$label</label>
    <select class="form-control" name="$optionName" id="$optionName">
        $optHtml
    </select>
    $help
</div>
EOT;
        return $html;
    }

    function adminOptionYesNo($optionName, $label, $class='') {
        $checked = $this->config->get($optionName) ? 'checked' : '';
        $html = <<<EOT
<div class="checkbox $class">
    <label>
        <input type="checkbox" name="$optionName" id="$optionName" $checked>$label
    </label>
</div>
EOT;
        return $html;
    }

    function adminOptionInput($optionName, $label, $maxLength=255, $class='') {
        $value = $this->config->get($optionName);
        $html = <<<EOT
<div class="form-group $class">
    <label for="$optionName">$label</label>
    <input type="text" class="form-control" name="$optionName" id="$optionName" value="$value" maxlength="$maxLength">
</div>
EOT;
        return $html;
    }

    function adminOptionTextarea($optionName, $label, $class='') {
        $value = $this->config->get($optionName);
        $html = <<<EOT
<div class="form-group $class">
    <label for="$optionName">$label</label>
    <textarea class="form-control" cols="55" name="$optionName" id="$optionName">$value</textarea>
</div>
EOT;
        return $html;
    }

    function adminOptionLabel($label, $class='') {
        $html = <<<EOT
<div class="form-group $class">
    <label>$label</label>
</div>
EOT;
        return $html;
    }

    protected function previewPropList() {
        $html = '';
        if ($this->lodgixPropertyListing) {
            $cell = $this->lodgixPropertyListing->gridCell();
            $row = $this->lodgixPropertyListing->row();
            $html .= <<<EOT
<div id="lodgix_vc_content_grid" class="ldgxInventoryGrid">
    $cell
</div>
<div id="lodgix_vc_content" class="ldgxInventoryList">
    $row
</div>
EOT;
        }
        return $html;
    }

    protected function previewPropDetailHeader() {
        $html = '';
        if ($this->property && $this->lodgixPropertyDetail) {
            $propertyName = $this->property->property_name;
            $propertyTitle = $this->property->property_title;
            $headerSingle = $this->lodgixPropertyDetail->header();
            $headerTabs = $this->lodgixPropertyDetail->header(true);
            $html .= <<<EOT
<div id="ldgxSettingsPropertyName" style="display:none">$propertyName</div>
<div id="ldgxSettingsPropertyTitle" style="display:none">$propertyTitle</div>
<div class="ldgxPropertySingle">
    $headerSingle
</div>
<div class="ldgxPropertyTabs">
    $headerTabs
</div>
EOT;
        }
        return $html;
    }

    protected function previewPropDetailTabs() {
        $html = '';
        if ($this->lodgixPropertyDetail) {
            $tabs = $this->lodgixPropertyDetail->tabsTemplate(
                $this->config->get('p_lodgix_single_page_tab_details'),
                $this->config->get('p_lodgix_single_page_tab_calendar'),
                $this->config->get('p_lodgix_single_page_tab_location'),
                $this->config->get('p_lodgix_single_page_tab_amenities'),
                $this->config->get('p_lodgix_single_page_tab_policies'),
                $this->config->get('p_lodgix_single_page_tab_reviews'),
                '<div class="ldgxSettingsPreviewTabsDetails"></div>',
                '<div class="ldgxSettingsPreviewTabsCalendar"></div>',
                '<div class="ldgxSettingsPreviewTabsLocation"></div>',
                '<div class="ldgxSettingsPreviewTabsAmenities"></div>',
                '<div class="ldgxSettingsPreviewTabsPolicies"></div>',
                '<div class="ldgxSettingsPreviewTabsReviews"></div>'
            );
            $html .= <<<EOT
<div class="ldgxPropertyTabs">
    <div class="ldgxTabbedContent">
        $tabs
    </div>
</div>
EOT;
        }
        return $html;
    }

}

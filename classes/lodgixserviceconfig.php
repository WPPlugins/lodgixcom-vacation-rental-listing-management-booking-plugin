<?php

class LodgixServiceConfig {

    protected $config;

    function __construct() {
        $this->config = null;
    }

    protected function getDefaultConfig() {
        return array(
            'p_lodgix_owner_id'=> NULL,
            'p_lodgix_api_key'=> NULL,
            'p_lodgix_vacation_rentals_page_en' => NULL,
            'p_lodgix_vacation_rentals_page_de' => NULL,
            'p_lodgix_availability_page_en' => NULL,
            'p_lodgix_availability_page_de' => NULL,
            'p_lodgix_search_rentals_page' => NULL,
            'p_lodgix_search_rentals_page_de' => NULL,
            'p_lodgix_allow_comments' => false,
            'p_lodgix_allow_pingback' => false,

            'p_lodgix_display_search_learn_more_book_now_button' => true,
            'p_lodgix_display_search_areas' => true,
            'p_lodgix_display_search_text_expander' => true,
            'p_lodgix_display_search_bedrooms' => true,
            'p_lodgix_display_search_bathrooms' => true,
            'p_lodgix_display_search_guests' => false,
            'p_lodgix_display_search_type' => true,
            'p_lodgix_display_search_pets' => true,
            'p_lodgix_display_search_daily_rates' => true,
            'p_lodgix_display_search_weekly_rates' => true,
            'p_lodgix_display_search_monthly_rates' => false,
            'p_lodgix_display_search_table_icons' => true,
            'p_lodgix_display_search_table_high_rate' => false,
            'p_lodgix_display_availability_icon' => false,
            'p_lodgix_display_icons' => false,

            'p_lodgix_display_daily_rates' => true,
            'p_lodgix_display_weekly_rates' => true,
            'p_lodgix_display_monthly_rates' => true,
            'p_lodgix_display_property_book_now_always' => false,
            'p_lodgix_display_beds' => true,
            'p_lodgix_display_title' => 'name',
            'p_lodgix_display_multi_instructions' => false,
            'p_lodgix_display_single_instructions' => false,
            'p_lodgix_rates_display' => 0,
            'p_lodgix_single_page_design' => 0,
            'p_lodgix_single_page_tab_details' => 'Details',
            'p_lodgix_single_page_tab_details_is_visible' => true,
            'p_lodgix_single_page_tab_calendar' => 'Booking Calendar',
            'p_lodgix_single_page_tab_calendar_is_visible' => true,
            'p_lodgix_single_page_tab_location' => 'Location',
            'p_lodgix_single_page_tab_location_is_visible' => true,
            'p_lodgix_single_page_tab_amenities' => 'Amenities',
            'p_lodgix_single_page_tab_amenities_is_visible' => true,
            'p_lodgix_single_page_tab_policies' => 'Policies',
            'p_lodgix_single_page_tab_policies_is_visible' => true,
            'p_lodgix_single_page_tab_reviews' => 'Reviews',
            'p_lodgix_single_page_tab_reviews_is_visible' => true,
            'p_lodgix_icon_set' => LodgixConst::ICON_SET_OUTLINED,
            'p_lodgix_vacation_rentals_page_design' => 2,
            'p_lodgix_vacation_rentals_page_pos' => '-1',
            'p_lodgix_availability_page_pos' => '-1',
            'p_lodgix_thesis_compatibility' => false,
            'p_lodgix_thesis_2_compatibility' => false,
            'p_lodgix_date_format' => '%m/%d/%Y',
            'p_lodgix_time_format' => '12',
            'p_lodgix_custom_page_template' => '',
            'p_lodgix_page_template' => '',
            'p_lodgix_thesis_2_template' => '',
            'p_lodgix_full_size_thumbnails' => false,
            'p_lodgix_use_ssl_pictures' => false,
            'p_lodgix_image_size' => LodgixConst::IMAGE_800x600,
            'p_lodgix_featured_select_all' => false,
            'p_lodgix_gmap_zoom_level' => 0,
            'p_lodgix_gmap_api_key' => ''
        );
    }

    protected function getCopyConfig() {
        return array(
            'p_lodgix_display_search_daily_rates' => 'p_lodgix_display_daily_rates',
            'p_lodgix_display_search_weekly_rates' => 'p_lodgix_display_weekly_rates',
            'p_lodgix_display_search_monthly_rates' => 'p_lodgix_display_monthly_rates'
        );
    }

    protected function init() {
        if (!$this->config) {
            $defaultConfig = $this->getDefaultConfig();
            $config = get_option(LodgixConst::OPTION_CONFIG);
            if ($config) {
                $copyConfig = $this->getCopyConfig();
                $updated = false;
                foreach ($defaultConfig as $key => $value) {
                    if (!array_key_exists($key, $config)) {
                        if (array_key_exists($key, $copyConfig) && array_key_exists($copyConfig[$key], $config)) {
                            $config[$key] = $config[$copyConfig[$key]];
                        } else {
                            $config[$key] = $value;
                        }
                        $updated = true;
                    }
                }
                $this->config = $config;
                if ($updated) {
                    $this->save();
                }
            } else {
                $this->config = $defaultConfig;
            }
        }
    }

    function get($key) {
        $this->init();
        if (in_array($key, $this->config)) {
            return $this->config[$key];
        }
        return null;
    }

    function set($key, $value) {
        $this->init();
        $this->config[$key] = $value;
    }

    function deleteKey($key) {
        $this->init();
        unset($this->config[$key]);
    }

    function save() {
        if ($this->config) {
            return update_option(LodgixConst::OPTION_CONFIG, $this->config);
        }
        return false;
    }

    function delete() {
        $this->init();
        $config = $this->getDefaultConfig();
        $config['p_lodgix_owner_id'] = $this->config['p_lodgix_owner_id'];
        $config['p_lodgix_api_key'] = $this->config['p_lodgix_api_key'];
        $this->config = $config;
        return $this->save();
    }

}

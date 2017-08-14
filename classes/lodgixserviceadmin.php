<?php

class LodgixServiceAdmin {

    function __construct($config, $post) {
        $this->config = $config;
        $this->post = $post;
    }

    function saveConfig() {
        // General
        $reload = false;

        $this->config->set('p_lodgix_owner_id', $this->post['p_lodgix_owner_id']);
        $this->config->set('p_lodgix_api_key', $this->post['p_lodgix_api_key']);

        // Inventory
        // (nothing here)

        // Property List

        $this->config->set('p_lodgix_vacation_rentals_page_design', (int)$this->post['p_lodgix_vacation_rentals_page_design']);

        if (array_key_exists($this->post['p_lodgix_icon_set'], LodgixConst::$ICON_SET_CLASS)) {
            $this->config->set('p_lodgix_icon_set', $this->post['p_lodgix_icon_set']);
        }

        $this->setSettingYesNo('p_lodgix_full_size_thumbnails');
        $this->setSettingYesNo('p_lodgix_use_ssl_pictures');
        $this->setSettingYesNo('p_lodgix_display_search_learn_more_book_now_button');
        $this->setSettingYesNo('p_lodgix_display_search_areas');
        $this->setSettingYesNo('p_lodgix_display_search_text_expander');
        $this->setSettingYesNo('p_lodgix_display_search_bedrooms');
        $this->setSettingYesNo('p_lodgix_display_search_bathrooms');
        $this->setSettingYesNo('p_lodgix_display_search_guests');
        $this->setSettingYesNo('p_lodgix_display_search_type');
        $this->setSettingYesNo('p_lodgix_display_search_pets');
        $this->setSettingYesNo('p_lodgix_display_search_daily_rates');
        $this->setSettingYesNo('p_lodgix_display_search_weekly_rates');
        $this->setSettingYesNo('p_lodgix_display_search_monthly_rates');
        $this->setSettingYesNo('p_lodgix_display_search_table_icons');
        $this->setSettingYesNo('p_lodgix_display_search_table_high_rate');
        $this->setSettingYesNo('p_lodgix_display_availability_icon');
        $this->setSettingYesNo('p_lodgix_display_icons');

        // Property Detail

        $this->config->set('p_lodgix_single_page_design', (int)$this->post['p_lodgix_single_page_design']);

        $this->config->set('p_lodgix_single_page_tab_details', $this->post['p_lodgix_single_page_tab_details']);
        $this->config->set('p_lodgix_single_page_tab_calendar', $this->post['p_lodgix_single_page_tab_calendar']);
        $this->config->set('p_lodgix_single_page_tab_location', $this->post['p_lodgix_single_page_tab_location']);
        $this->config->set('p_lodgix_single_page_tab_amenities', $this->post['p_lodgix_single_page_tab_amenities']);
        $this->config->set('p_lodgix_single_page_tab_policies', $this->post['p_lodgix_single_page_tab_policies']);
        $this->config->set('p_lodgix_single_page_tab_reviews', $this->post['p_lodgix_single_page_tab_reviews']);

        $this->setSettingYesNo('p_lodgix_single_page_tab_details_is_visible');
        $this->setSettingYesNo('p_lodgix_single_page_tab_calendar_is_visible');
        $this->setSettingYesNo('p_lodgix_single_page_tab_location_is_visible');
        $this->setSettingYesNo('p_lodgix_single_page_tab_amenities_is_visible');
        $this->setSettingYesNo('p_lodgix_single_page_tab_policies_is_visible');
        $this->setSettingYesNo('p_lodgix_single_page_tab_reviews_is_visible');

        if (array_key_exists($this->post['p_lodgix_image_size'], LodgixConst::$IMAGE_WIDTH)) {
            $this->config->set('p_lodgix_image_size', $this->post['p_lodgix_image_size']);
        }

        $this->setSettingYesNo('p_lodgix_display_daily_rates');
        $this->setSettingYesNo('p_lodgix_display_weekly_rates');
        $this->setSettingYesNo('p_lodgix_display_monthly_rates');
        $this->setSettingYesNo('p_lodgix_display_property_book_now_always');
        $this->setSettingYesNo('p_lodgix_display_beds');

        $this->config->set('p_lodgix_gmap_zoom_level', (int)$this->post['p_lodgix_gmap_zoom_level']);

        $old_p_lodgix_gmap_api_key = $this->config->get('p_lodgix_gmap_api_key');
        if ($old_p_lodgix_gmap_api_key != $this->post['p_lodgix_gmap_api_key']) {
            $reload = true;
        }

        $this->config->set('p_lodgix_gmap_api_key', $this->post['p_lodgix_gmap_api_key']);

        $this->config->set('p_lodgix_display_title', $this->post['p_lodgix_display_title']);

        $this->setSettingYesNo('p_lodgix_display_single_instructions');
        $this->setSettingYesNo('p_lodgix_display_multi_instructions');

        $this->config->set('p_lodgix_rates_display', (int)$this->post['p_lodgix_rates_display']);

        $this->setSettingYesNo('p_lodgix_allow_comments');
        $this->setSettingYesNo('p_lodgix_allow_pingback');

        // Legacy Options

        $this->setSettingYesNo('p_lodgix_thesis_compatibility');
        $this->setSettingYesNo('p_lodgix_thesis_2_compatibility');

        $this->config->set('p_lodgix_thesis_2_template', $this->post['p_lodgix_thesis_2_template']);
        $this->config->set('p_lodgix_page_template', $this->post['p_lodgix_page_template']);
        if ($this->post['p_lodgix_custom_page_template']) {
            $this->config->set('p_lodgix_custom_page_template', $this->post['p_lodgix_custom_page_template']);
        }
        $this->config->set('p_lodgix_vacation_rentals_page_pos', $this->post['p_lodgix_vacation_rentals_page_pos']);
        $this->config->set('p_lodgix_availability_page_pos', $this->post['p_lodgix_availability_page_pos']);

        $this->config->save();

        return $reload;
    }

    protected function setSettingYesNo($optionName) {
        if (!empty($this->post[$optionName])) {
            $this->config->set($optionName, true);
        } else {
            $this->config->set($optionName, false);
        }
    }

}

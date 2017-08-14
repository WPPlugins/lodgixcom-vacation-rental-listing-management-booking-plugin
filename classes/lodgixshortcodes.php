<?php

class LodgixShortcodes {

    function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->config = new LodgixServiceConfig();
        $this->locale = get_locale();
        $this->languageCode = substr($this->locale,0,2);
    }

    public function availability($args) {
        return (new LodgixAvailability($this->config, $this->languageCode))->page();
    }

    public function vacationRentals($args) {
        if ((int)$this->config->get("p_lodgix_vacation_rentals_page_$this->languageCode") > 0) {
            $inventory = new LodgixInventory($this->config, $this->locale, $this->languageCode);
            $inventoryItems = $inventory->inventoryItems();
            return "<div class=\"ldgxInventoryContainer\">$inventoryItems</div>";
        }
        return '';
    }

    public function searchRentals($args) {
        $sort = @esc_sql($_POST['lodgix-property-list-sort']);
        $categoryId = @esc_sql($_POST['lodgix-custom-search-area']);
        $bedrooms = @esc_sql($_POST['lodgix-custom-search-bedrooms']);
        $priceFrom = @esc_sql($_POST['lodgix-custom-search-daily-price-from']);
        $priceTo = @esc_sql($_POST['lodgix-custom-search-daily-price-to']);
        if (isset($_POST['lodgix-custom-search-id'])) {
            $id = @esc_sql($_POST['lodgix-custom-search-id']);
        } elseif (isset($_GET['id'])) {
            $id = @esc_sql($_GET['id']);
        } else {
            $id = '';
        }
        $arrival = @esc_sql($_POST['lodgix-custom-search-arrival']);
        $nights = @esc_sql($_POST['lodgix-custom-search-nights']);
        $petFriendly = ($_POST['lodgix-custom-search-pet-friendly'] == 'on');
        $amenities = $_POST['lodgix-custom-search-amenity'];
        if (isset($_POST['lodgix-custom-search-tag'])) {
            $tags = $_POST['lodgix-custom-search-tag'];
        } elseif (isset($_GET['tag'])) {
            $tags = trim(preg_replace('/\s+/', ' ', $_GET['tag']));
            $tags = preg_split('/\s*,\s*/', $tags);
        } else {
            $tags = null;
        }
        $inventory = new LodgixInventory($this->config, $this->locale, $this->languageCode);
        $inventoryItems = $inventory->inventoryItems($sort, $categoryId, $bedrooms, $id, $arrival, $nights, $amenities, $priceFrom, $priceTo, $petFriendly, $tags);
        return "<div class=\"ldgxInventoryContainer\">$inventoryItems</div>";
    }

    public function category($args) {
        $categoryId = $args[0];
        $inventory = new LodgixInventory($this->config, $this->locale, $this->languageCode);
        $inventoryItems = $inventory->inventoryItems('', $categoryId);
        return "<div class=\"ldgxInventoryContainer\">$inventoryItems</div>";
    }

    public function property($args) {
        $propertyId = $args[0];

        $tableProperties = $this->db->prefix . LodgixConst::TABLE_PROPERTIES;
        $tableLangProperties = $this->db->prefix . LodgixConst::TABLE_LANG_PROPERTIES;
        $tableLangPages = $this->db->prefix . LodgixConst::TABLE_LANG_PAGES;

        $html = '';
        $properties = $this->db->get_results("SELECT * FROM $tableProperties WHERE id=$propertyId");
        if ($properties) {
            $property = $properties[0];

            $book_dates = @esc_sql($_GET['bookdates']);
            if ($book_dates) {
                $property->booklink = (new LodgixServiceProperty($property))->bookLink($this->config->get('p_lodgix_owner_id'), $book_dates);
                $property->really_available = true;
            } else {
                $property->really_available = false;
            }

            if (strpos($this->locale, 'en') !== false) {
                $permalink = get_permalink($property->post_id);
            } else {
                $translated_details = $this->db->get_results("SELECT * FROM $tableLangProperties WHERE id=$property->id AND language_code='$this->languageCode'");
                $translated_details = $translated_details[0];
                $property->description = $translated_details->description;
                $property->description_long = $translated_details->description_long;
                $property->details = $translated_details->details;
                $post_id = $this->db->get_var("select page_id from $tableLangPages WHERE property_id=$property->id AND language_code='$this->languageCode'");
                $permalink = get_permalink($post_id);
            }

            $contactUrlOption = $this->config->get("p_lodgix_contact_url_$this->languageCode");
            $emailUrl = $contactUrlOption ? $contactUrlOption : '';

            $mapZoomLevel = $this->config->get('p_lodgix_gmap_zoom_level');
            $mapZoom = $mapZoomLevel == 0 ? 13 : $mapZoomLevel;

            $mapApiKey = $this->config->get('p_lodgix_gmap_api_key');

            $lpd = new LodgixPropertyDetail(
                $property,
                $this->config->get('p_lodgix_date_format'),
                $mapApiKey,
                $this->config->get('p_lodgix_display_daily_rates'),
                $this->config->get('p_lodgix_display_weekly_rates'),
                $this->config->get('p_lodgix_display_monthly_rates'),
                $this->config->get('p_lodgix_rates_display') == 0,
                $this->config->get('p_lodgix_rates_display') == 1,
                $permalink,
                $emailUrl,
                $this->config->get('p_lodgix_icon_set'),
                $this->config->get('p_lodgix_display_property_book_now_always'),
                $this->config->get('p_lodgix_display_beds'),
                $this->config->get('p_lodgix_image_size'),
                $this->config->get('p_lodgix_display_single_instructions'),
                $mapZoom,
                $this->languageCode,
                false
            );
            if ($this->config->get('p_lodgix_single_page_design') == 1) {
                $html = $lpd->tabs(
                    $this->config->get('p_lodgix_single_page_tab_details_is_visible') ? $this->config->get('p_lodgix_single_page_tab_details') : '',
                    $this->config->get('p_lodgix_single_page_tab_calendar_is_visible') ? $this->config->get('p_lodgix_single_page_tab_calendar') : '',
                    $this->config->get('p_lodgix_single_page_tab_location_is_visible') ? $this->config->get('p_lodgix_single_page_tab_location') : '',
                    $this->config->get('p_lodgix_single_page_tab_amenities_is_visible') ? $this->config->get('p_lodgix_single_page_tab_amenities') : '',
                    $this->config->get('p_lodgix_single_page_tab_policies_is_visible') ? $this->config->get('p_lodgix_single_page_tab_policies') : '',
                    $this->config->get('p_lodgix_single_page_tab_reviews_is_visible') ? $this->config->get('p_lodgix_single_page_tab_reviews') : ''
                );
            } else {
                $html = $lpd->single();
            }
        }
        return do_shortcode($html);
    }

}

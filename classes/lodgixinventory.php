<?php

class LodgixInventory
{

    function __construct ($config, $locale='en_US', $lang='en')
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->wpurl = get_bloginfo('wpurl');
        $this->config = $config;
        $this->ownerId = $this->config->get('p_lodgix_owner_id');
        $this->dbVersion = LodgixConst::DB_VERSION;
        $this->locale = $locale;
        $this->lang = $lang;
    }

    function inventoryItems (
        $sortBy='',
        $categoryId='',
        $bedrooms=null,
        $propertyNameOrId='',
        $arrival='',
        $nights='',
        $amenities=null,
        $priceFrom=0,
        $priceTo=0,
        $isPetFriendly=false,
        $tags=null
    )
    {
        $tableLangProperties = $this->db->prefix . LodgixConst::TABLE_LANG_PROPERTIES;
        $tableLangPages = $this->db->prefix . LodgixConst::TABLE_LANG_PAGES;

        if ($this->config->get('p_lodgix_vacation_rentals_page_design') == 1) {
            $content = '<div id="lodgix_vc_content_grid" class="ldgxInventoryGrid">';
        } elseif ($this->config->get('p_lodgix_vacation_rentals_page_design') == 2) {
            $content = '<div id="lodgix_vc_content" class="ldgxInventoryList ldgxInventoryTheme2">';
        } else {
            $content = '<div id="lodgix_vc_content" class="ldgxInventoryList ldgxInventoryTheme1">';
        }

        $content .= $this->sortForm($categoryId);

        $serviceProperties = new LodgixServiceProperties($this->config);

        $properties = $serviceProperties->getAvailableProperties(
            $arrival,
            $nights,
            $categoryId,
            $bedrooms,
            $priceFrom,
            $priceTo,
            $isPetFriendly,
            $propertyNameOrId,
            $amenities,
            $tags,
            $sortBy
        );

        $availableAfterRules = $serviceProperties->getAvailablePropertyIdsAfterRules($arrival, $nights);

        $counter = 0;

        if ($properties) {

            if (strtotime($arrival) !== false && is_numeric($nights)) {
                $differentiate = true;
                $departure = date('Y-m-d', strtotime("$arrival + $nights days"));
            } else {
                $differentiate = false;
                $departure = '';
            }

            foreach ($properties as $property) {
                if ($counter == 0) {
                    $oldCategoryId = $property->category_id;
                }

                if (is_array($availableAfterRules)) {
                    $lsp = new LodgixServiceProperty($property);
                    foreach ($availableAfterRules as $id) {
                        if ($id == $property->id) {
                            $property->bookdates = $arrival . ',' . $departure;
                            $property->booklink = $lsp->bookLink($this->ownerId, $property->bookdates);
                            $property->really_available = true;
                            break;
                        }
                    }
                }

                if (strpos($this->locale, 'en') !== false) {
                    $permalink = get_permalink($property->post_id);
                } else {
                    $sql = "SELECT * FROM $tableLangProperties WHERE language_code='$this->lang' AND id=$property->id";
                    $translatedDetails = $this->db->get_results($sql);
                    $translatedDetails = $translatedDetails[0];
                    $property->description = $translatedDetails->description;
                    $property->description_long = $translatedDetails->description_long;
                    $property->details = $translatedDetails->details;
                    $postId = $this->db->get_var("select page_id from $tableLangPages WHERE property_id=$property->id AND language_code='$this->lang'");
                    $permalink = get_permalink($postId);
                }

                if ($property->main_image) {

                    if ($differentiate && $property->really_available) {
                        $permalink = add_query_arg(array('bookdates' => $property->bookdates), $permalink);
                        $permalink = esc_url($permalink);
                    }

                    $displayHighRate = $this->config->get('p_lodgix_display_search_table_high_rate');
                    $displayLowRate = !$displayHighRate;

                    $lpl = new LodgixPropertyListing(
                        $property,
                        $this->config->get('p_lodgix_display_search_daily_rates'),
                        $this->config->get('p_lodgix_display_search_weekly_rates'),
                        $this->config->get('p_lodgix_display_search_monthly_rates'),
                        $permalink,
                        $displayLowRate,
                        $displayHighRate,
                        $this->config->get('p_lodgix_display_search_bedrooms'),
                        $this->config->get('p_lodgix_display_search_areas')
                    );

                    if ($this->config->get('p_lodgix_vacation_rentals_page_design') == 1) {

                        if (!$sortBy && !empty($oldCategoryId)) {
                            if (($counter == 0) || ($oldCategoryId != $property->category_id)) {
                                if ($property->category_id) {
                                    $content .= "<h2>$property->category_title_long</h2>";
                                } else {
                                    $content .= "<h2>Other Areas</h2>";
                                }
                                $counter = 0;
                            }
                        }
                        $oldCategoryId = $property->category_id;

                        $nofloat = false;
                        $isNewRow = ($counter % 3 == 0);

                        $content .= $lpl->gridCell($nofloat, $isNewRow);

                    } else {

                        if ($this->config->get('p_lodgix_contact_url_' . $this->lang) != "") {
                            $mailUrl = $this->config->get('p_lodgix_contact_url_' . $this->lang);
                            if (strpos($mailUrl, '__PROPERTY__') != false) {
                                $mailUrl = str_replace('__PROPERTY__', $property->description, $mailUrl);
                            }
                            if (strpos($mailUrl, '__PROPERTYID__') != false) {
                                $mailUrl = str_replace('__PROPERTYID__', $property->id, $mailUrl);
                            }
                        } else {
                            $mailUrl = '';
                        }

                        if ($differentiate && !$property->really_available) {
                            $warning =
                                '<div style="margin:10px"><span class="lodgix-icon lodgix-icon-alert" style="float:left;margin-right:.3em"></span><strong>'
                                . LodgixTranslate::translate('Rules may exist that prevent this booking from proceeding. Please check availability.',
                                    LodgixTranslate::LOCALIZATION_DOMAIN)
                                . '</strong></div>';
                        } else {
                            $warning = '';
                        }

                        $fullSizeThumbnails = $this->config->get('p_lodgix_full_size_thumbnails');
                        $smallThumbnails = !$fullSizeThumbnails;

                        $content .= $lpl->row(
                            $smallThumbnails,
                            $fullSizeThumbnails,
                            $this->config->get('p_lodgix_display_search_learn_more_book_now_button'),
                            $warning,
                            $this->config->get('p_lodgix_icon_set'),
                            $this->config->get('p_lodgix_display_search_table_icons'),
                            $this->config->get('p_lodgix_display_search_bathrooms'),
                            $this->config->get('p_lodgix_display_search_guests'),
                            $this->config->get('p_lodgix_display_search_type'),
                            $this->config->get('p_lodgix_display_search_pets'),
                            $this->config->get('p_lodgix_display_availability_icon'),
                            $this->config->get('p_lodgix_display_icons'),
                            $mailUrl,
                            $differentiate,
                            $property->booklink
                        );

                    }
                    $counter += 1;
                }

            }

            $content .= '<script type="text/javascript">jQueryLodgix(".ldgxListingFeats").LodgixResponsiveTable()</script>';

            if ($this->config->get('p_lodgix_display_search_text_expander')) {
                $content .= '<script type="text/javascript">jQueryLodgix(".ldgxListingDesc").LodgixTextExpander()</script>';
            }

        } else {
            $content .= '<p>Your search did not return any results. Please amend your search criteria and try again.</p>';
        }

        $content .= '</div>';
        $content .= '<div class="ldgxPowered">' . $this->linkBack() . ' by Lodgix.com</div>';
        return $content;
    }

    protected function sortForm ($categoryId='')
    {
        $sort = isset($_POST['lodgix-property-list-sort']) ? htmlspecialchars($_POST['lodgix-property-list-sort'], ENT_QUOTES) : '';
        $categoryId = isset($_POST['lodgix-custom-search-area']) ? htmlspecialchars($_POST['lodgix-custom-search-area'], ENT_QUOTES) : $categoryId;
        $bedrooms = isset($_POST['lodgix-custom-search-bedrooms']) ? htmlspecialchars($_POST['lodgix-custom-search-bedrooms'], ENT_QUOTES) : '';
        $priceFrom = isset($_POST['lodgix-custom-search-daily-price-from']) ? htmlspecialchars($_POST['lodgix-custom-search-daily-price-from'], ENT_QUOTES) : '';
        $priceTo = isset($_POST['lodgix-custom-search-daily-price-to']) ? htmlspecialchars($_POST['lodgix-custom-search-daily-price-to'], ENT_QUOTES) : '';
        if (isset($_POST['lodgix-custom-search-id'])) {
            $id = htmlspecialchars($_POST['lodgix-custom-search-id'], ENT_QUOTES);
        } elseif (isset($_GET['id'])) {
            $id = htmlspecialchars($_GET['id'], ENT_QUOTES);
        } else {
            $id = '';
        }
        $arrival = isset($_POST['lodgix-custom-search-arrival']) ? htmlspecialchars($_POST['lodgix-custom-search-arrival'], ENT_QUOTES) : '';
        $nights = isset($_POST['lodgix-custom-search-nights']) ? htmlspecialchars($_POST['lodgix-custom-search-nights'], ENT_QUOTES) : '';
        $petFriendly = isset($_POST['lodgix-custom-search-pet-friendly']) ? htmlspecialchars($_POST['lodgix-custom-search-pet-friendly'], ENT_QUOTES) : '';
        $content = '
            <div id="lodgix_sort_div" class="ldgxInventorySort">
                <form method="post" action="' . $this->wpurl . '/wp-admin/admin-ajax.php">
                    <strong>' . LodgixTranslate::translate('Sort Results by') . ':</strong>
                    <input type="hidden" name="action" value="p_lodgix_sort_vr">
                    <input type="hidden" name="lodgix-custom-search-lang" value="' . $this->lang . '">
                    <input type="hidden" name="lodgix-custom-search-area" value="' . $categoryId . '">
                    <input type="hidden" name="lodgix-custom-search-bedrooms" value="' . $bedrooms . '">
                    <input type="hidden" name="lodgix-custom-search-daily-price-from" value="' . $priceFrom . '">
                    <input type="hidden" name="lodgix-custom-search-daily-price-to" value="' . $priceTo . '">
                    <input type="hidden" name="lodgix-custom-search-id" value="' . $id . '">
                    <input type="hidden" name="lodgix-custom-search-arrival" value="' . $arrival . '">
                    <input type="hidden" name="lodgix-custom-search-nights" value="' . $nights . '">
                    <input type="hidden" name="lodgix-custom-search-pet-friendly" value="' . $petFriendly . '">
        ';

        if (isset($_POST['lodgix-custom-search-amenity']) && is_array($_POST['lodgix-custom-search-amenity'])) {
            foreach ($_POST['lodgix-custom-search-amenity'] as $a) {
                $content .= '<input type="hidden" name="lodgix-custom-search-amenity[]" value="' . htmlspecialchars($a, ENT_QUOTES) . '">';
            }
        }

        if (isset($_POST['lodgix-custom-search-tag'])) {
            $tags = $_POST['lodgix-custom-search-tag'];
        } elseif (isset($_GET['tag'])) {
            $tags = trim(preg_replace('/\s+/', ' ', $_GET['tag']));
            $tags = preg_split('/\s*,\s*/', $tags);
        } else {
            $tags = null;
        }
        if (is_array($tags)) {
            foreach ($tags as $t) {
                $content .= '<input type="hidden" name="lodgix-custom-search-tag[]" value="' . htmlspecialchars($t, ENT_QUOTES) . '">';
            }
        }

        $content .= '
                    <select name="lodgix-property-list-sort" onchange="jQLodgix(this.form).ajaxSubmit({target:\'.ldgxInventoryContainer\'})">
                        <option value=""' . ($sort == '' ? ' selected' : '') . '>' . LodgixTranslate::translate('None') . '</option>
                        <option value="bedrooms"' . ($sort == 'bedrooms' ? ' selected' : '') . '>' . LodgixTranslate::translate('Bedrooms') . '</option>
                        <option value="bathrooms"' . ($sort == 'bathrooms' ? ' selected' : '') . '>' . LodgixTranslate::translate('Bathrooms') . '</option>
                        <option value="proptype"' . ($sort == 'proptype' ? ' selected' : '') . '>' . LodgixTranslate::translate('Rental Type') . '</option>
                        <option value="min_daily_rate"' . ($sort == 'min_daily_rate' ? ' selected' : '') . '>' . LodgixTranslate::translate('Daily Rate') . '</option>
                        <option value="min_weekly_rate"' . ($sort == 'min_weekly_rate' ? ' selected' : '') . '>' . LodgixTranslate::translate('Weekly Rate') . '</option>
                        <option value="min_monthly_rate"' . ($sort == 'min_monthly_rate' ? ' selected' : '') . '>' . LodgixTranslate::translate('Monthly Rate') . '</option>
                    </select>
                </form>
            </div>
        ';
        return $content;
    }

    protected function linkBack ()
    {
        $tableLinkRotators = $this->db->prefix . LodgixConst::TABLE_LINK_ROTATORS;
        $rotators = $this->db->get_results("SELECT url,title FROM `$tableLinkRotators` ORDER BY RAND() LIMIT 1");
        if ($rotators) {
            foreach ($rotators as $rotator) {
                return '<a target="_blank" href="' . $rotator->url . '">' . $rotator->title . '</a>';
            }
        }
        return '<a href="http://www.lodgix.com">Vacation Rental Software</a>';
    }

    function inventoryJson ()
    {
        $tp = $this->db->prefix . LodgixConst::TABLE_PROPERTIES;
        $tpa = $this->db->prefix . LodgixConst::TABLE_PAGES;
        $properties = $this->db->get_results("
            SELECT $tp.id,`order`,property_id,description,enabled,featured
            FROM $tp LEFT JOIN $tpa ON $tp.id=$tpa.property_id
            ORDER BY $tp.`order`
        ");
        $items = array();
        foreach ($properties as $property) {
            $checked = $property->featured ? 'checked' : '';
            $disabled = $this->config->get('p_lodgix_featured_select_all') ? 'disabled' : '';
            $featured = '
                <input type="checkbox" id="lodgix_featured_property_' . $property->property_id . '"
                    class="lodgix_featured_property" ' . $checked . ' ' . $disabled . ' 
                    onclick="javascript:toggle_lodgix_featured_property(' . $property->property_id . ');"
                >
            ';
            $items[] = array(
                'order' => $property->order + 1,
                'id' => $property->property_id,
                'name' => $property->description,
                'featured' => $featured
            );
        }
        $output = array(
            'sColumns' => 'ID, Name, Featured',
            'sEcho' => intval($_GET['sEcho']),
            'iTotalRecords' => count($properties),
            'iTotalDisplayRecords' => 50,
            'aaData' => $items
        );
        return json_encode($output);
    }

}
<?php

class LodgixWidgetRentalSearch2 extends WP_Widget {

    private static $DEFAULT_SETTINGS;

    public function __construct() {
		parent::__construct(
			'lodgix_rental_search',
            LodgixTranslate::translate('Lodgix Rental Search'),
			array('description' => LodgixTranslate::translate('Lodgix Rental Search Widget'))
		);
        self::$DEFAULT_SETTINGS = array(
            'title' => 'Rental Search',
            'button_text' => 'Display Results',
            'horizontal' => false,
            'min_nights' => 1,
            'location' => true,
            'bedrooms' => true,
            'price' => true,
            'from_price' => 50,
            'to_price' => 1000,
            'price_increment' => 50,
            'currency_symbol' => '$',
            'pet_friendly' => false,
            'amenities' => false,
            'tags' => false,
            'name' => true
        );
	}

    public function form($instance) {
        global $wpdb;

        $tableProperties = $wpdb->prefix . 'lodgix_properties';

        $title = self::$DEFAULT_SETTINGS['title'];
        $button_text = self::$DEFAULT_SETTINGS['button_text'];
        $horizontal = self::$DEFAULT_SETTINGS['horizontal'];
        $minNights = self::$DEFAULT_SETTINGS['min_nights'];
        $location = self::$DEFAULT_SETTINGS['location'];
        $bedrooms = self::$DEFAULT_SETTINGS['bedrooms'];
        $price = self::$DEFAULT_SETTINGS['price'];
        $fromPrice = self::$DEFAULT_SETTINGS['from_price'];
        $toPrice = self::$DEFAULT_SETTINGS['to_price'];
        $priceIncrement = self::$DEFAULT_SETTINGS['price_increment'];
        $properties = $wpdb->get_results("SELECT currency_symbol FROM $tableProperties LIMIT 1");
        if ($properties) {
            $currencySymbol = $properties[0]->currency_symbol;
        } else {
            $currencySymbol = self::$DEFAULT_SETTINGS['currency_symbol'];
        }
        $petFriendly = self::$DEFAULT_SETTINGS['pet_friendly'];
        $amenities = self::$DEFAULT_SETTINGS['amenities'];
        $tags = self::$DEFAULT_SETTINGS['tags'];
        $name = self::$DEFAULT_SETTINGS['name'];
		if ($instance) {
            if (array_key_exists('title', $instance)) {
                $title = esc_attr($instance['title']);
            }
            if (array_key_exists('button_text', $instance)) {
                $button_text = esc_attr($instance['button_text']);
            }
            if (array_key_exists('horizontal', $instance)) {
                $horizontal = esc_attr($instance['horizontal']);
            }
            if (array_key_exists('min_nights', $instance)) {
                $minNights = esc_attr($instance['min_nights']);
            }
            if (array_key_exists('location', $instance)) {
                $location = esc_attr($instance['location']);
            }
            if (array_key_exists('bedrooms', $instance)) {
                $bedrooms = esc_attr($instance['bedrooms']);
            }
            if (array_key_exists('price', $instance)) {
                $price = esc_attr($instance['price']);
            }
            if (array_key_exists('from_price', $instance)) {
                $fromPrice = esc_attr($instance['from_price']);
            }
            if (array_key_exists('to_price', $instance)) {
                $toPrice = esc_attr($instance['to_price']);
            }
            if (array_key_exists('price_increment', $instance)) {
                $priceIncrement = esc_attr($instance['price_increment']);
            }
            if (array_key_exists('currency_symbol', $instance)) {
                $currencySymbol = esc_attr($instance['currency_symbol']);
            }
            if (array_key_exists('pet_friendly', $instance)) {
			    $petFriendly = esc_attr($instance['pet_friendly']);
            }
            if (array_key_exists('amenities', $instance)) {
			    $amenities = esc_attr($instance['amenities']);
            }
            if (array_key_exists('tags', $instance)) {
			    $tags = esc_attr($instance['tags']);
            }
            if (array_key_exists('name', $instance)) {
                $name = esc_attr($instance['name']);
            }
		}
		?>
			<p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo LodgixTranslate::translate('Title:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>"><br>
            </p>
			<p>
                <label for="<?php echo $this->get_field_id('button_text'); ?>"><?php echo LodgixTranslate::translate('Button Text:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" type="text" value="<?php echo $button_text; ?>"><br>
            </p>
            <p>
                <input id="<?php echo $this->get_field_id('horizontal'); ?>" name="<?php echo $this->get_field_name('horizontal'); ?>" type="checkbox" <?php checked(true, $horizontal); ?>>
                <label for="<?php echo $this->get_field_id('horizontal'); ?>"><?php echo LodgixTranslate::translate('Horizontal Layout'); ?></label>
			</p>
            <p>
                <label for="<?php echo $this->get_field_id('min_nights'); ?>"><?php echo LodgixTranslate::translate('Minimum Nights:'); ?></label>
                <select class="widefat" id="<?php echo $this->get_field_id('min_nights'); ?>" name="<?php echo $this->get_field_name('min_nights'); ?>">
                    <?php
                        for ($i = 1; $i < 100; $i++) {
                            $selected = $minNights == $i ? 'selected' : '';
                            echo "<option value='$i' $selected>$i " . LodgixTranslate::translate($i > 1 ? 'nights' : 'night') . "</option>";
                        }
                    ?>
                </select><br>
            </p>
            <p>
                <input id="<?php echo $this->get_field_id('location'); ?>" name="<?php echo $this->get_field_name('location'); ?>" type="checkbox" <?php checked(true, $location); ?>>
                <label for="<?php echo $this->get_field_id('location'); ?>"><?php echo LodgixTranslate::translate('Search by Location'); ?></label>
			</p>
            <p>
                <input id="<?php echo $this->get_field_id('bedrooms'); ?>" name="<?php echo $this->get_field_name('bedrooms'); ?>" type="checkbox" <?php checked(true, $bedrooms); ?>>
                <label for="<?php echo $this->get_field_id('bedrooms'); ?>"><?php echo LodgixTranslate::translate('Search by Bedrooms'); ?></label>
			</p>
            <p>
                <input id="<?php echo $this->get_field_id('price'); ?>" name="<?php echo $this->get_field_name('price'); ?>" type="checkbox" <?php checked(true, $price); ?> onclick="document.getElementById('<?php echo $this->get_field_id('price_details'); ?>').style.display=this.checked?'block':'none'">
                <label for="<?php echo $this->get_field_id('price'); ?>"><?php echo LodgixTranslate::translate('Search by Price'); ?></label>
			</p>
            <div id="<?php echo $this->get_field_id('price_details'); ?>" <?php if (!$price) echo 'style="display:none"' ?>>
                <p>
                    <label for="<?php echo $this->get_field_id('from_price'); ?>"><?php echo LodgixTranslate::translate('From Price:'); ?></label>
                    <input class="widefat" id="<?php echo $this->get_field_id('from_price'); ?>" name="<?php echo $this->get_field_name('from_price'); ?>" type="text" value="<?php echo $fromPrice; ?>"><br>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('to_price'); ?>"><?php echo LodgixTranslate::translate('To Price:'); ?></label>
                    <input class="widefat" id="<?php echo $this->get_field_id('to_price'); ?>" name="<?php echo $this->get_field_name('to_price'); ?>" type="text" value="<?php echo $toPrice; ?>"><br>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('price_increment'); ?>"><?php echo LodgixTranslate::translate('Price Increment:'); ?></label>
                    <input class="widefat" id="<?php echo $this->get_field_id('price_increment'); ?>" name="<?php echo $this->get_field_name('price_increment'); ?>" type="text" value="<?php echo $priceIncrement; ?>"><br>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('currency_symbol'); ?>"><?php echo LodgixTranslate::translate('Currency Symbol:'); ?></label>
                    <input class="widefat" id="<?php echo $this->get_field_id('currency_symbol'); ?>" name="<?php echo $this->get_field_name('currency_symbol'); ?>" type="text" value="<?php echo $currencySymbol; ?>"><br>
                </p>
            </div>
            <p>
                <input id="<?php echo $this->get_field_id('pet_friendly'); ?>" name="<?php echo $this->get_field_name('pet_friendly'); ?>" type="checkbox" <?php checked(true, $petFriendly); ?>>
                <label for="<?php echo $this->get_field_id('pet_friendly'); ?>"><?php echo LodgixTranslate::translate('Search by Pet Friendly'); ?></label>
			</p>
            <p>
                <input id="<?php echo $this->get_field_id('amenities'); ?>" name="<?php echo $this->get_field_name('amenities'); ?>" type="checkbox" <?php checked(true, $amenities); ?>>
                <label for="<?php echo $this->get_field_id('amenities'); ?>"><?php echo LodgixTranslate::translate('Search by Amenities'); ?></label>
			</p>
            <p>
                <input id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>" type="checkbox" <?php checked(true, $tags); ?>>
                <label for="<?php echo $this->get_field_id('tags'); ?>"><?php echo LodgixTranslate::translate('Search by Tags'); ?></label>
			</p>
            <p>
                <input id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>" type="checkbox" <?php checked(true, $name); ?>>
                <label for="<?php echo $this->get_field_id('name'); ?>"><?php echo LodgixTranslate::translate('Search by Name or ID'); ?></label>
			</p>
		<?php
	}

    public function update($new_instance, $old_instance) {
        $instance = Array();
        $instance['title'] = strip_tags($new_instance['title']);
		$instance['button_text'] = strip_tags($new_instance['button_text']);
        $instance['horizontal'] = ($new_instance['horizontal'] == 'on');
        $instance['min_nights'] = strip_tags($new_instance['min_nights']);
        $instance['location'] = ($new_instance['location'] == 'on');
        $instance['bedrooms'] = ($new_instance['bedrooms'] == 'on');
        $instance['price'] = ($new_instance['price'] == 'on');
        $instance['from_price'] = strip_tags($new_instance['from_price']);
        $instance['to_price'] = strip_tags($new_instance['to_price']);
        $instance['price_increment'] = strip_tags($new_instance['price_increment']);
        $instance['currency_symbol'] = strip_tags($new_instance['currency_symbol']);
        $instance['pet_friendly'] = ($new_instance['pet_friendly'] == 'on');
        $instance['amenities'] = ($new_instance['amenities'] == 'on');
        $instance['tags'] = ($new_instance['tags'] == 'on');
        $instance['name'] = ($new_instance['name'] == 'on');
		return $instance;
	}

    public function widget($args, $instance) {
		global $wpdb;

        $pluginPath = plugin_dir_url(plugin_basename(__FILE__));
        $lang = substr(get_locale(), 0, 2);

        $tableProperties = $wpdb->prefix . 'lodgix_properties';
        $tableAmenities = $wpdb->prefix . 'lodgix_searchable_amenities';
        $tableTags = $wpdb->prefix . 'lodgix_tags';

		extract($args);

		$lodgixSettings = get_option('p_lodgix_options');

        $limitBookingDaysAdvance = (int)$wpdb->get_var("SELECT MAX(limit_booking_days_advance) FROM $tableProperties");

        $dateFormat = $lodgixSettings['p_lodgix_date_format'];
        if ($dateFormat == '%m/%d/%Y') {
            $dateFormat = 'mm/dd/yy';
        } else if ($dateFormat == '%d/%m/%Y') {
            $dateFormat = 'dd/mm/yy';
        } else if ($dateFormat == '%m-%d-%Y') {
            $dateFormat = 'mm-dd-yy';
        } else if ($dateFormat == '%d-%m-%Y') {
            $dateFormat = 'dd-mm-yy';
        } else if ($dateFormat == '%d %b %Y') {
            $dateFormat = 'dd M yy';
        } else if ($dateFormat == '%a, %d %b %Y') {
            $dateFormat = 'dd M yy';
        }

        $title = apply_filters('widget_title', empty($instance['title']) ? self::$DEFAULT_SETTINGS['title'] : esc_html($instance['title']));
        $button_text = apply_filters('button_text', empty($instance['button_text']) ? self::$DEFAULT_SETTINGS['button_text'] : esc_html($instance['button_text']));
        $horizontal = array_key_exists('horizontal', $instance) ? $instance['horizontal'] : self::$DEFAULT_SETTINGS['horizontal'];
        $minNights = apply_filters('min_nights', empty($instance['min_nights']) ? self::$DEFAULT_SETTINGS['min_nights'] : intval($instance['min_nights']));
        if ($minNights < 1) {
            $minNights = 1;
        }
        $showLocation = array_key_exists('location', $instance) ? $instance['location'] : self::$DEFAULT_SETTINGS['location'];
        $showBedrooms = array_key_exists('bedrooms', $instance) ? $instance['bedrooms'] : self::$DEFAULT_SETTINGS['bedrooms'];
        $showPrice = array_key_exists('price', $instance) ? $instance['price'] : self::$DEFAULT_SETTINGS['price'];
        $fromPrice = apply_filters('from_price', empty($instance['from_price']) ? self::$DEFAULT_SETTINGS['from_price'] : esc_html($instance['from_price']));
        $toPrice = apply_filters('to_price', empty($instance['to_price']) ? self::$DEFAULT_SETTINGS['to_price'] : esc_html($instance['to_price']));
        $priceIncrement = apply_filters('price_increment', empty($instance['price_increment']) ? self::$DEFAULT_SETTINGS['price_increment'] : esc_html($instance['price_increment']));
        if (!empty($instance['currency_symbol'])) {
            $currencySymbol = esc_html($instance['currency_symbol']);
        } else {
            $properties = $wpdb->get_results("SELECT currency_symbol FROM $tableProperties LIMIT 1");
            if ($properties) {
                $currencySymbol = esc_html($properties[0]->currency_symbol);
            } else {
                $currencySymbol = self::$DEFAULT_SETTINGS['currency_symbol'];
            }
        }
        $currencySymbol = apply_filters('currency_symbol', $currencySymbol);
        $showPetFriendly = array_key_exists('pet_friendly', $instance) ? $instance['pet_friendly'] : self::$DEFAULT_SETTINGS['pet_friendly'];
        $showAmenities = array_key_exists('amenities', $instance) ? $instance['amenities'] : self::$DEFAULT_SETTINGS['amenities'];
        $showTags = array_key_exists('tags', $instance) ? $instance['tags'] : self::$DEFAULT_SETTINGS['tags'];
        $showName = array_key_exists('name', $instance) ? $instance['name'] : self::$DEFAULT_SETTINGS['name'];

        echo $before_widget;

        if (!$horizontal) {
            echo $before_title . LodgixTranslate::translate($title) . $after_title;
        }

        if ($lang != 'en') {
            echo '<script src="' . $pluginPath . 'js/i18n/datepicker-' . $lang. '.js"></script>';
        }

        ?>
            <div class="ldgxRentalSearchWidget <?php echo $horizontal ? 'ldgxRentalSearchWidgetHorizontal' : 'ldgxRentalSearchWidgetVertical'; ?>">
                <form id="ldgxRentalSearchForm" method="post" action="<?php echo get_permalink((int)$lodgixSettings['p_lodgix_search_rentals_page_' . $lang]); ?>">
                    <div class="ldgxRentalSearchContainer">
                        <div class="ldgxRentalSearchDiv ldgxRentalSearchDivArrival">
                            <input type="text" id="ldgxRentalSearchDatepicker" name="lodgix-custom-search-datepicker" value="<?php echo htmlspecialchars($_POST['lodgix-custom-search-datepicker'], ENT_QUOTES); ?>" placeholder="<?php echo LodgixTranslate::translate('Arriving'); ?>" onchange="lodgixRentalSearch()" readonly>
        					<input type="hidden" id="ldgxRentalSearchArrival" name="lodgix-custom-search-arrival" value="<?php echo htmlspecialchars($_POST['lodgix-custom-search-arrival'], ENT_QUOTES); ?>">
                        </div>
                        <div class="ldgxRentalSearchDiv ldgxRentalSearchDivNights">
                            <select id="ldgxRentalSearchNights" name="lodgix-custom-search-nights" onchange="lodgixRentalSearch()">
                                <?php
                                    if (isset($_POST['lodgix-custom-search-nights'])) {
                                        $value = (int)$_POST['lodgix-custom-search-nights'];
                                        for ($i = $minNights; $i < 100; $i++) {
                                            $selected = $value == $i ? 'selected' : '';
                                            echo "<option value='$i' $selected>$i " . LodgixTranslate::translate($i > 1 ? 'nights' : 'night') . "</option>";
                                        }
                                    } else {
                                        echo '<option value="" selected>' . LodgixTranslate::translate('Nights') . '</option>';
                                        for ($i = $minNights; $i < 100; $i++) {
                                            echo "<option value='$i'>$i " . LodgixTranslate::translate($i > 1 ? 'nights' : 'night') . "</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <?php
                            if ($showLocation) {
                                ?>
                                <div class="ldgxRentalSearchDiv ldgxRentalSearchDivArea">
                                    <select id="ldgxRentalSearchArea" name="lodgix-custom-search-area" onchange="lodgixRentalSearch()">
                                        <?php
                                            $value = isset($_POST['lodgix-custom-search-area']) ? $_POST['lodgix-custom-search-area'] : '';
                                            if ($value == '') {
                                                echo '<option value="" selected>' . LodgixTranslate::translate('Location') . '</option>';
                                            }
                                            if ($value == 'ALL_AREAS') {
                                                echo '<option value="ALL_AREAS" selected>' . LodgixTranslate::translate('All Areas') . '</option>';
                                            } else {
                                                echo '<option value="ALL_AREAS">' . LodgixTranslate::translate('All Areas') . '</option>';
                                            }
                                            $categories = (new LodgixServiceCategories())->getAll();
                                            foreach ($categories as $category) {
                                                $selected = $value == $category->category_id ? 'selected' : '';
                                                echo "<option value='$category->category_id' $selected>$category->category_title_long</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <?php
                            }
                            if ($showBedrooms) {
                                ?>
                                <div class="ldgxRentalSearchDiv ldgxRentalSearchDivBedrooms">
                                    <select id="ldgxRentalSearchBedrooms" name="lodgix-custom-search-bedrooms" onchange="lodgixRentalSearch()">
                                        <?php
                                            $minRooms = (int)$wpdb->get_var("SELECT MIN(bedrooms) FROM $tableProperties");
                                            $maxRooms = (int)$wpdb->get_var("SELECT MAX(bedrooms) FROM $tableProperties");
                                            if (isset($_POST['lodgix-custom-search-bedrooms'])) {
                                                $value = $_POST['lodgix-custom-search-bedrooms'];
                                                if ($value == 'ANY') {
                                                    echo '<option value="ANY" selected>' . LodgixTranslate::translate('Any Bedrooms') . '</option>';
                                                    if ($minRooms == 0) {
                                                        echo '<option value="0">' . LodgixTranslate::translate('Studio') . '</option>';
                                                    }
                                                    for ($i = 1; $i <= $maxRooms; $i++) {
                                                        echo "<option value='$i'>$i " . LodgixTranslate::translate($i > 1 ? 'bedrooms' : 'bedroom') . "</option>";
                                                    }
                                                } else {
                                                    echo '<option value="ANY">' . LodgixTranslate::translate('Any Bedrooms') . '</option>';
                                                    if ($minRooms == 0) {
                                                        if ($value == '0') {
                                                            echo '<option value="0" selected>' . LodgixTranslate::translate('Studio') . '</option>';
                                                        } else {
                                                            echo '<option value="0">' . LodgixTranslate::translate('Studio') . '</option>';
                                                        }
                                                    }
                                                    $value = (int)$value;
                                                    for ($i = 1; $i <= $maxRooms; $i++) {
                                                        $selected = $value == $i ? 'selected' : '';
                                                        echo "<option value='$i' $selected>$i " . LodgixTranslate::translate($i > 1 ? 'bedrooms' : 'bedroom') . "</option>";
                                                    }
                                                }
                                            } else {
                                                echo '<option value="" selected>' . LodgixTranslate::translate('Bedrooms') . '</option>';
                                                echo '<option value="ANY">' . LodgixTranslate::translate('Any Bedrooms') . '</option>';
                                                if ($minRooms == 0) {
                                                    echo '<option value="0">' . LodgixTranslate::translate('Studio') . '</option>';
                                                }
                                                for ($i = 1; $i <= $maxRooms; $i++) {
                                                    echo "<option value='$i'>$i " . LodgixTranslate::translate($i > 1 ? 'bedrooms' : 'bedroom') . "</option>";
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                <?php
                            }
                            if ($showPrice) {
                                ?>
                                <div class="ldgxRentalSearchDiv ldgxRentalSearchDivDailyPriceFrom">
                                    <select id="ldgxRentalSearchDailyPriceFrom" name="lodgix-custom-search-daily-price-from" onchange="lodgixRentalSearch()">
                                        <?php
                                            if (isset($_POST['lodgix-custom-search-daily-price-from'])) {
                                                $value = (int)$_POST['lodgix-custom-search-daily-price-from'];
                                                echo '<option value="0" ' . ($value == 0 ? 'selected' : '')
                                                    . '>' . LodgixTranslate::translate('From Any Price') . '</option>';
                                                for ($i = $fromPrice; $i <= $toPrice; $i += $priceIncrement) {
                                                    echo '<option value="' . $i . '" '
                                                        . ($value == $i ? 'selected' : '') . '>' . LodgixTranslate::translate('From')
                                                        . ' ' . $currencySymbol . $i . ' ' . LodgixTranslate::translate('per nt')
                                                        . '</option>';
                                                }
                                            } else {
                                                echo '<option value="" selected>' . LodgixTranslate::translate('Daily Price From')
                                                    . '</option>';
                                                echo '<option value="0">' . LodgixTranslate::translate('From Any Price') . '</option>';
                                                for ($i = $fromPrice; $i <= $toPrice; $i += $priceIncrement) {
                                                    echo '<option value="' . $i . '">' . LodgixTranslate::translate('From') . ' '
                                                        . $currencySymbol . $i . ' ' . LodgixTranslate::translate('per nt') . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="ldgxRentalSearchDiv ldgxRentalSearchDivDailyPriceTo">
                                    <select id="ldgxRentalSearchDailyPriceTo" name="lodgix-custom-search-daily-price-to" onchange="lodgixRentalSearch()">
                                        <?php
                                            if (isset($_POST['lodgix-custom-search-daily-price-to'])) {
                                                $value = (int)$_POST['lodgix-custom-search-daily-price-to'];
                                                echo '<option value="0" ' . ($value == 0 ? 'selected' : '')
                                                    . '>' . LodgixTranslate::translate('To Any Price') . '</option>';
                                                for ($i = $fromPrice; $i <= $toPrice; $i += $priceIncrement) {
                                                    echo '<option value="' . $i . '" '
                                                        . ($value == $i ? 'selected' : '') . '>' . LodgixTranslate::translate('To') . ' '
                                                        . $currencySymbol . $i . ' ' . LodgixTranslate::translate('per nt') . '</option>';
                                                }
                                            } else {
                                                echo '<option value="" selected>' . LodgixTranslate::translate('Daily Price To') . '</option>';
                                                echo '<option value="0">' . LodgixTranslate::translate('To Any Price') . '</option>';
                                                for ($i = $fromPrice; $i <= $toPrice; $i += $priceIncrement) {
                                                    echo '<option value="' . $i . '">' . LodgixTranslate::translate('To') . ' '
                                                        . $currencySymbol . $i . ' ' . LodgixTranslate::translate('per nt') . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                <?php
                            }
                            if ($showPetFriendly || $showAmenities || $showTags) {
                                echo '<div class="ldgxRentalSearchDiv ldgxRentalSearchDivAmenities">';
                                if ($showPetFriendly) {
                                    if (isset($_POST['lodgix-custom-search-pet-friendly']) && $_POST['lodgix-custom-search-pet-friendly'] == 'on') {
                                        $checked = 'checked';
                                    } else {
                                        $checked = '';
                                    }
                                    ?>
                                    <div class="ldgxRentalSearchAmenity">
                                        <input type="checkbox"
                                               id="ldgxRentalSearchPetFriendly"
                                               name="lodgix-custom-search-pet-friendly"
                                               <?php echo $checked; ?>
                                               onclick='lodgixRentalSearch()'>
                                        <label for="ldgxRentalSearchPetFriendly"><?php echo LodgixTranslate::translate('Pet Friendly'); ?></label>
                                    </div>
                                    <?php
                                }
                                if ($showAmenities) {
                                    $values = isset($_POST['lodgix-custom-search-amenity']) ? $_POST['lodgix-custom-search-amenity'] : array();
                                    $values = array_map('stripslashes', $values);
                                    $amenities = $wpdb->get_results("SELECT DISTINCT * FROM $tableAmenities");
                                    $i = 0;
                                    foreach ($amenities as $amenity) {
                                        $amenityName = trim($amenity->description);
                                        $amenityNameTranslated = LodgixTranslate::translateAmenity($amenityName, $lang);
                                        $checked = in_array($amenityName, $values) ? 'checked' : '';
                                        $amenityName = htmlspecialchars($amenityName, ENT_QUOTES);
                                        echo "
                                            <div class='ldgxRentalSearchAmenity'>
                                                <input type='checkbox'
                                                       id='ldgxRentalSearchAmenity$i'
                                                       name='lodgix-custom-search-amenity[]'
                                                       value='$amenityName'
                                                       $checked
                                                       onclick='lodgixRentalSearch()'>
                                                <label for='ldgxRentalSearchAmenity$i'>$amenityNameTranslated</label>
                                            </div>
                                        ";
                                        $i++;
                                    }
                                }
                                if ($showTags) {
                                    $values = isset($_POST['lodgix-custom-search-tag']) ? $_POST['lodgix-custom-search-tag'] : array();
                                    $values = array_map('stripslashes', $values);
                                    $tags = $wpdb->get_results("SELECT * FROM $tableTags ORDER BY tag");
                                    $i = 0;
                                    foreach ($tags as $tag) {
                                        $tagName = trim($tag->tag);
                                        $tagNameTranslated = LodgixTranslate::translate(ucwords($tagName), $lang);
                                        $checked = in_array($tagName, $values) ? 'checked' : '';
                                        $tagName = htmlspecialchars($tagName, ENT_QUOTES);
                                        echo "
                                            <div class='ldgxRentalSearchAmenity'>
                                                <input type='checkbox'
                                                       id='ldgxRentalSearchTag$i'
                                                       name='lodgix-custom-search-tag[]'
                                                       value='$tagName'
                                                       $checked
                                                       onclick='lodgixRentalSearch()'>
                                                <label for='ldgxRentalSearchTag$i'>$tagNameTranslated</label>
                                            </div>
                                        ";
                                        $i++;
                                    }
                                }
                                echo '</div>';
                            }
                            if ($showName) {
                                ?>
                                <div class="ldgxRentalSearchDiv ldgxRentalSearchDivId">
                                    <input type="text" id="ldgxRentalSearchId" name="lodgix-custom-search-id"
                                           placeholder="<?php echo LodgixTranslate::translate('Property Name or ID'); ?>"
                                           onkeyup="lodgixRentalSearch()"
                                           value="<?php echo htmlspecialchars($_POST['lodgix-custom-search-id'], ENT_QUOTES); ?>">
                                </div>
                                <?php
                            }
                        ?>
                        <div class="ldgxRentalSearchDiv ldgxRentalSearchDivSubmit">
                            <button id="ldgxRentalSearchSubmit"><?php echo LodgixTranslate::translate($button_text); ?></button>
                        </div>
                    </div>
                </form>
            </div>
            <script>
                (function($) {
                    'use strict';

                    $.templates({
                        lodgixRentalSearchResultsTooltip: ['<div class="ldgxRentalSearchResultsTooltipContent">{{:numResults}} <?php echo LodgixTranslate::translate('Properties Found'); ?></div>'].join('')
                    });

                    window.lodgixRentalSearchTooltip = new LodgixTooltip();
                    window.lodgixRentalSearchTooltip.el().addClass('ldgxRentalSearchResultsTooltip');

                    function hideTooltip() {
                        window.lodgixRentalSearchTooltip.hide();
                    }

                    $(window).resize(hideTooltip);

                    window.lodgixRentalSearch = function() {
                        if (!$('#ldgxRentalSearchArrival').val()) {
                            var tomorrow = new Date();
                            tomorrow.setDate(tomorrow.getDate() + 1);
                            jQueryLodgix('#ldgxRentalSearchDatepicker').datepicker('setDate', tomorrow);
                        }
                        $('#ldgxRentalSearchNights option[value=""]').remove();
                        $('#ldgxRentalSearchArea option[value=""]').remove();
                        $('#ldgxRentalSearchBedrooms option[value=""]').remove();
                        $('#ldgxRentalSearchDailyPriceFrom option[value=""]').remove();
                        $('#ldgxRentalSearchDailyPriceTo option[value=""]').remove();
                        if (parseInt($('#ldgxRentalSearchDailyPriceFrom').val()) > parseInt($('#ldgxRentalSearchDailyPriceTo').val())) {
                            $('#ldgxRentalSearchDailyPriceTo option[value="0"]').prop('selected', true);
                        }
                        $('#ldgxRentalSearchSubmit').addClass('ldgxRainbowAnimation');
                        $('#ldgxRentalSearchForm').ajaxSubmit({
                            url: '<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php',
                            data: {
                                'action': 'p_lodgix_custom_search'
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response) {
                                    var submitButton = $('.ldgxRentalSearchDivSubmit');
                                    hideTooltip();
                                    window.lodgixRentalSearchTooltip.show({
                                        trigger: submitButton,
                                        template: 'lodgixRentalSearchResultsTooltip',
                                        params: {
                                            numResults: response.num_results || 0
                                        },
                                        touch: true,
                                        onTouch: function() {
                                            window.lodgixRentalSearchTooltip.hide();
                                        },
                                        onBeforeHide: function() {
                                            submitButton.off({
                                                mouseover: hideTooltip,
                                                click: hideTooltip
                                            });
                                            window.lodgixRentalSearchTooltip.el().off({
                                                mouseover: hideTooltip
                                            });
                                        }
                                    });
                                    submitButton.on({
                                        mouseover: hideTooltip,
                                        click: hideTooltip
                                    });
                                    window.lodgixRentalSearchTooltip.el().on({
                                        mouseover: hideTooltip
                                    });

                                    var minNights = <?php echo $minNights ?>;
                                    var selectbox = $('#ldgxRentalSearchNights');
                                    var selected = parseInt(selectbox.val());
                                    if (isNaN(selected) || selected < minNights) {
                                        selected = minNights;
                                    }
                                    var options = [];
                                    for (var i = minNights; i < 100; i++) {
                                        options.push('<option value="');
                                        options.push(i);
                                        options.push('">');
                                        options.push(i);
                                        if (i > 1) {
                                            options.push(' <?php echo LodgixTranslate::translate('nights'); ?>');
                                        } else {
                                            options.push(' <?php echo LodgixTranslate::translate('night'); ?>');
                                        }
                                        options.push('</option>');
                                    }
                                    selectbox.empty().append(options.join(''));
                                    $('#ldgxRentalSearchNights option[value="' + selected + '"]').prop('selected', true);
                                }
                            },
                            complete: function() {
                                $('#ldgxRentalSearchSubmit').removeClass('ldgxRainbowAnimation');
                            }
                        });
                    };

                    var maxDate = null;
                    var limitDays = parseInt('<?php echo $limitBookingDaysAdvance; ?>');
                    if (!isNaN(limitDays) && limitDays > 0) {
                        maxDate = new Date();
                        maxDate.setDate(maxDate.getDate() + limitDays);
                    }

                    $(document).ready(function() {
                        var dateFormat = '<?php echo $dateFormat; ?>';
                        var datepickerEl = jQueryLodgix('#ldgxRentalSearchDatepicker');
                        datepickerEl.datepicker({
                                showOn: 'both',
                                buttonImage: '<?php echo $pluginPath; ?>images/calendar.png',
                                buttonImageOnly: true,
                                dateFormat: dateFormat,
                                altField: '#ldgxRentalSearchArrival',
                                altFormat: 'yy-mm-dd',
                                minDate: 0,
                                maxDate: maxDate,
                                beforeShow: function() {
                                    setTimeout(function() {
                                        jQueryLodgix('#lodgix-datepicker-div').css('z-index', 99999999999999);
                                    }, 0);
                                }
                            }<?php if ($lang != 'en') { echo ', jQueryLodgix.datepicker.regional["' . $lang. '"]'; } ?>)
                            .next('.lodgix-datepicker-trigger').addClass('ldgxRentalSearchDatepickerTrigger');

                        if (maxDate) {
                            var date = jQueryLodgix.datepicker.parseDate(dateFormat, datepickerEl.val());
                            if (date > maxDate) {
                                jQueryLodgix('#ldgxRentalSearchDatepicker').datepicker('setDate', maxDate);
                            }
                        }
                    });

                }(jQLodgix));
            </script>
        <?php

		echo $after_widget;
	}
}

function lodgixRegisterWidgetRentalSearch2() {
    register_widget('LodgixWidgetRentalSearch2');
}

add_action('widgets_init', 'lodgixRegisterWidgetRentalSearch2');

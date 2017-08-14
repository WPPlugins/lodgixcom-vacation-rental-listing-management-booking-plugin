<?php

class Lodgix_Rental_Search_Widget extends WP_Widget {

	function __construct() {
				
		parent::__construct(
			'lodgix_custom_search',
            LodgixTranslate::translate('Lodgix Rental Search (old)'),
			array('description' => LodgixTranslate::translate('Lodgix Rental Search Widget (old)'))
		);		

	}


	function form($instance) {
        if ($instance) {
			$title = esc_attr($instance['title']);
			$amenities = esc_attr($instance['amenities']);
		}
		else {
			$title = 'Lodgix Rental Search';
			$amenities = false;
		}
		?>
			<p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo LodgixTranslate::translate('Title:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('amenites'); ?>"><?php echo LodgixTranslate::translate('Amenities:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('amenities'); ?>" name="<?php echo $this->get_field_name('amenities'); ?>" type="checkbox" <?php checked(true, $amenities); ?> />
			</p>
		<?php
	}

	function update($new_instance, $old_instance) {
		$instance['title'] = strip_tags($new_instance['title']);
		if ($new_instance['amenities'] == 'on')
			$instance['amenities'] = true;
		else {
			$instance['amenities'] = false;
		}
		return $instance;
	}

	function widget($args, $instance) {
		global $wpdb;
				
        $properties_table = $wpdb->prefix . "lodgix_properties";
		$locale = get_locale();
		$sufix = substr($locale,0,2);
		$p_plugin_path = plugin_dir_url(plugin_basename(__FILE__));

		extract($args);
	
		$loptions = get_option('p_lodgix_options'); 
                    
        $title = apply_filters('widget_title', empty($instance['title']) ? LodgixTranslate::translate('Rentals Search') : LodgixTranslate::translate(esc_html($instance['title'])));

        $categoryId = $_POST['lodgix-custom-search-area'];
        $bedrooms_post = $_POST['lodgix-custom-search-bedrooms'];
        $id_post = $_POST['lodgix-custom-search-id'];

        echo $before_widget . $before_title . $title . $after_title;
        echo '<div class="lodgix-search-properties" align="center">';

        $limit_booking_days_advance = (int)$wpdb->get_var("SELECT MAX(limit_booking_days_advance) FROM $properties_table");

        $date_format = $loptions['p_lodgix_date_format'];
        
        if ($date_format == '%m/%d/%Y')
           $date_format = 'mm/dd/yy';
        else if ($date_format == '%d/%m/%Y')
           $date_format = 'dd/mm/yy';
        else if ($date_format == '%m-%d-%Y')
                $date_format = 'mm-dd-yy';
        else if ($date_format == '%d-%m-%Y')
                $date_format = 'dd-mm-yy';                
        else if ($date_format == '%d %b %Y')
                $date_format = 'dd M yy';
    
        if ($sufix!= 'en') {
            echo '<script type="text/javascript" src="' . $p_plugin_path . 'js/i18n/datepicker-' . $sufix. '.js"></script>';
        }

        echo '
            <script>
                function p_lodgix_search_properties() {
                    var amenities = [];
                    var checked = jQueryLodgix(".lodgix-custom-search-amenities:checked");
                    var len = checked.length;
                    if (len) {
                        for (var i = 0; i < len; i++) {
                            amenities.push(checked[i].value);
                        }
                        amenities = "&lodgix-custom-search-amenity[]=" + amenities.join("&lodgix-custom-search-amenity[]=");
                    }
                    jQueryLodgix("#search_results").html("");
                    jQueryLodgix("#lodgix_search_spinner").show();
                    jQueryLodgix.ajax({
                        type: "POST",
                        url: "' .  get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php",
                        data: "action=p_lodgix_custom_search&lodgix-custom-search-area=" + jQueryLodgix("#lodgix-custom-search-area").val() + "&lodgix-custom-search-bedrooms=" + jQueryLodgix("#lodgix-custom-search-bedrooms").val() + "&lodgix-custom-search-id=" + jQueryLodgix("#lodgix-custom-search-id").val() + "&lodgix-custom-search-arrival=" + jQueryLodgix.datepicker.formatDate("yy-mm-dd",jQueryLodgix("#lodgix-custom-search-datepicker").datepicker("getDate")) + "&lodgix-custom-search-nights=" + jQueryLodgix("#lodgix-custom-search-nights").val() + amenities,
                        dataType: "json",
                        success: function(response) {
                            jQueryLodgix("#lodgix_search_spinner").hide();
                            var num_results = response.num_results || 0;
                            jQueryLodgix("#search_results").html(num_results + " ' . LodgixTranslate::translate('Properties Found') . '.");
                            var min_nights = 1;
                            var selectbox = jQueryLodgix("#lodgix-custom-search-nights");
                            var selected = selectbox.val();
                            var options = [];
                            for (var i = min_nights; i < 100; i++) {
                                options.push(\'<option value="\');
                                options.push(i);
                                options.push(\'">\');
                                options.push(i);
                                options.push("</option>");
                            }
                            selectbox.empty().append(options.join("")).val(selected);
                        },
                        failure: function() {
                            jQueryLodgix("#lodgix_search_spinner").hide();
                        }
                    });
                }

                function lodgix_search_before_submit() {
                    var real_date = jQueryLodgix("#lodgix-custom-search-datepicker").datepicker("getDate");
                    real_date = jQueryLodgix.datepicker.formatDate("yy-mm-dd", real_date);
                    jQueryLodgix("#lodgix-custom-search-arrival").val(real_date);
                }

                var maxDate = null;
                var limitDays = parseInt("' . $limit_booking_days_advance . '");
                if (!isNaN(limitDays) && limitDays > 0) {
                    maxDate = new Date();
                    maxDate.setDate(maxDate.getDate() + limitDays);
                }

				jQueryLodgix(document).ready(function() {
                    jQueryLodgix("#lodgix-custom-search-datepicker").datepicker({
                        showOn: "both",
                        buttonImage: "' . $p_plugin_path . 'images/calendar.png",
                        buttonImageOnly: true,
                        dateFormat: "' . $date_format . '",
                        minDate: 0,
                        maxDate: maxDate,
                        beforeShow: function() {
                            setTimeout(function(){
                                jQueryLodgix("#lodgix-datepicker-div").css("z-index", 99999999999999);
                            }, 0);
                        }
                    }' . ($sufix != 'en' ? ', jQueryLodgix.datepicker.regional["' . $sufix. '"]' : '') . ');
                });
            </script>
        ';

        $post_id = (int)$loptions['p_lodgix_search_rentals_page_' . $sufix];

        $post_url = get_permalink($post_id);
        echo '<form name="lodgix_search_form" method="POST" action="' . $post_url .'" onsubmit="javascript:lodgix_search_before_submit();">
                    <div class="lodgix-custom-search-listing" align="left" style="-moz-border-radius: 5px 5px 5px 5px;line-height:20px;">    
                    <table>
                      <tr>
                      <td>
                            <div>'.LodgixTranslate::translate('Arriving').':</div> 			
                            <div style="vertical-align:bottom;"><input id="lodgix-custom-search-datepicker" name="lodgix-custom-search-datepicker" style="width:117px;" onchange="javascript:p_lodgix_search_properties();" readonly></div>
                        </td>
                        <td>&nbsp;
                        </td>
                        <td>
                        <div>'.LodgixTranslate::translate('Nights').':</div>
                        <div><select id="lodgix-custom-search-nights" name="lodgix-custom-search-nights" style="width:54px;" onchange="javascript:p_lodgix_search_properties();">';

        for ($i = 1 ; $i < 100 ; $i++) {
            echo "<option value='" . $i . "'>" . $i . "</option>";
        }
        
        echo '</select>
                        </div>
                        </td>
                        </tr>
                    </table>
                    <div>'.LodgixTranslate::translate('Location').':</div> 
                    <div><select id="lodgix-custom-search-area" style="width:95%" name="lodgix-custom-search-area" onchange="javascript:p_lodgix_search_properties();">
                    <option value="ALL_AREAS">'.LodgixTranslate::translate('All Areas').'</option>';

        $categories = (new LodgixServiceCategories())->getAll();
        foreach ($categories as $category) {
            if ($categoryId == $category->category_id) {
                echo "<option selected value='$category->category_id'>$category->category_title_long</option>";
            } else {
                echo "<option value='$category->category_id'>$category->category_title_long</option>";
            }
        }
            
        echo	'</select></div>
                    <div>'.LodgixTranslate::translate('Bedrooms') .':</div> 
                    <div><select id="lodgix-custom-search-bedrooms" name="lodgix-custom-search-bedrooms" onchange="javascript:p_lodgix_search_properties();">
                    <option value="ANY">Any</option>';
		$min_rooms = (int)$wpdb->get_var("SELECT MIN(bedrooms) FROM " . $properties_table);
		if ($min_rooms == 0)					
            echo '<option value="0">Studio</option>';
        $max_rooms = (int)$wpdb->get_var("SELECT MAX(bedrooms) FROM " . $properties_table);
        for($i = 1 ; $i < ($max_rooms+1) ; $i++)
        {
            
            if ($i == $bedrooms_post)
                echo '<option selected value="'.$i.'">'.LodgixTranslate::translate($i).'</option>';
            else
                echo '<option value="'.$i.'">'.$i.'</option>';
        }
        echo '</select></div>';
        

        if ($instance['amenities']) {
            echo '<div class="lodgix-custom-search-amenities-list">'.LodgixTranslate::translate('Amenities') .':';
            $amenities = $wpdb->get_results("SELECT DISTINCT * FROM " . $wpdb->prefix . "lodgix_searchable_amenities");
            $a = 0;
            foreach($amenities as $amenity) {
                $aux = LodgixTranslate::translateAmenity(trim($amenity->description), $sufix);
                echo '<div><input type="checkbox" class="lodgix-custom-search-amenities" name="lodgix-custom-search-amenity[' . $a . ']" value="' . $amenity->description . '" onclick="javascript:p_lodgix_search_properties();"/> ';
                echo $aux . '</div>';
                $a++;
            }
            echo '</div>';
        }

        echo '<div>'.LodgixTranslate::translate('Search by Property Name or ID') .':</div> 
                    <div><input id="lodgix-custom-search-id" name="lodgix-custom-search-id" style="width:95%" onkeyup="javascript:p_lodgix_search_properties();" value="' . $id_post .  '"></div>
                    <div id="lodgix-custom-search-results" align="center">
                    <div id="lodgix_search_spinner" style="display:none;"><img src="/wp-admin/images/wpspin_light.gif"></div>
                    <div id="search_results">
                    </div>
					<input type="hidden" id="lodgix-custom-search-arrival" name="lodgix-custom-search-arrival" value="">
                    <input type="submit" value="'.LodgixTranslate::translate('Display Results') .'" id="lodgix-custom-search-button">
                    </div>
              </div>';               
        echo '</div></form>';
		
		echo $after_widget;
	}
}

function lodgixRegisterWidgetRentalSearch() {
	register_widget('Lodgix_Rental_Search_Widget');
}

add_action('widgets_init', 'lodgixRegisterWidgetRentalSearch');

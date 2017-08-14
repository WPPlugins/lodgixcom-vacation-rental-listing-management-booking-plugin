<?php

class Lodgix_Featured_Rentals_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'lodgix_featured',
			LodgixTranslate::translate('Lodgix Featured Rentals'),
			array('description' => LodgixTranslate::translate('Lodgix Featured Rentals Widget'))
		);		
	}

	function form($instance) {
		if ($instance) {
			$title = esc_attr($instance['title']);
            $display_properties = esc_attr($instance['display_properties']);
            if (!isset($display_properties) || !is_numeric($display_properties) || $display_properties < 0) {
                $display_properties = 3;
            }
            $rotate = esc_attr($instance['rotate']);

            if (array_key_exists('layout', $instance)) {
                $layout = esc_attr($instance['layout']);
                $display_area = esc_attr($instance['display_area']);
            } else {
                // Legacy widget settings
                $loptions = get_option('p_lodgix_options');
                if (array_key_exists('p_lodgix_display_featured_horizontally', $loptions)) {
                    $layout = $loptions['p_lodgix_display_featured_horizontally'];
                } else {
                    $layout = 0;
                }
                if (array_key_exists('p_lodgix_display_featured', $loptions) && $loptions['p_lodgix_display_featured'] == 'area') {
                    $display_area = true;
                } else {
                    $display_area = false;
                }
            }

		} else {
			$title = 'Featured Rentals';
            $display_properties = 3;
            $rotate = false;
            $layout = 0;
            $display_area = false;
		}

		?>		
			<p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo LodgixTranslate::translate('Title:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br>
			</p>
            <p>
                <label for="<?php echo $this->get_field_id('layout'); ?>"><?php echo LodgixTranslate::translate('Layout:'); ?></label>
                <select class='widefat' id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>">
                    <option value="0"<?php echo $layout == 0 ? 'selected' : ''; ?>>Vertical</option>
                    <option value="1"<?php echo $layout == 1 ? 'selected' : ''; ?>>Horizontal Float Left</option>
                    <option value="2"<?php echo $layout == 2 ? 'selected' : ''; ?>>Horizontal Float Right</option>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('display_properties'); ?>"><?php echo LodgixTranslate::translate('Display Properties:'); ?></label>
                <select class='widefat' id="<?php echo $this->get_field_id('display_properties'); ?>" name="<?php echo $this->get_field_name('display_properties'); ?>">
                    <option value="0"<?php echo $display_properties == 0 ? 'selected' : ''; ?>>All</option>
                    <option value="1"<?php echo $display_properties == 1 ? 'selected' : ''; ?>>1</option>
                    <option value="2"<?php echo $display_properties == 2 ? 'selected' : ''; ?>>2</option>
                    <option value="3"<?php echo $display_properties == 3 ? 'selected' : ''; ?>>3</option>
                    <option value="4"<?php echo $display_properties == 4 ? 'selected' : ''; ?>>4</option>
                    <option value="5"<?php echo $display_properties == 5 ? 'selected' : ''; ?>>5</option>
                    <option value="6"<?php echo $display_properties == 6 ? 'selected' : ''; ?>>6</option>
                    <option value="7"<?php echo $display_properties == 7 ? 'selected' : ''; ?>>7</option>
                    <option value="8"<?php echo $display_properties == 8 ? 'selected' : ''; ?>>8</option>
                    <option value="9"<?php echo $display_properties == 9 ? 'selected' : ''; ?>>9</option>
                    <option value="10"<?php echo $display_properties == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="11"<?php echo $display_properties == 11 ? 'selected' : ''; ?>>11</option>
                    <option value="12"<?php echo $display_properties == 12 ? 'selected' : ''; ?>>12</option>
                    <option value="13"<?php echo $display_properties == 13 ? 'selected' : ''; ?>>13</option>
                    <option value="14"<?php echo $display_properties == 14 ? 'selected' : ''; ?>>14</option>
                    <option value="15"<?php echo $display_properties == 15 ? 'selected' : ''; ?>>15</option>
                    <option value="16"<?php echo $display_properties == 16 ? 'selected' : ''; ?>>16</option>
                    <option value="17"<?php echo $display_properties == 17 ? 'selected' : ''; ?>>17</option>
                    <option value="18"<?php echo $display_properties == 18 ? 'selected' : ''; ?>>18</option>
                    <option value="19"<?php echo $display_properties == 19 ? 'selected' : ''; ?>>19</option>
                    <option value="20"<?php echo $display_properties == 20 ? 'selected' : ''; ?>>20</option>
                </select>
            </p>
            <p>
                <input type="checkbox" id="<?php echo $this->get_field_id('rotate'); ?>" name="<?php echo $this->get_field_name('rotate'); ?>" <?php checked(true, $rotate); ?>>
                <label for="<?php echo $this->get_field_id('rotate'); ?>"><?php echo LodgixTranslate::translate('Rotate'); ?></label><br>
            </p>
            <p>
                <input type="checkbox" id="<?php echo $this->get_field_id('display_area'); ?>" name="<?php echo $this->get_field_name('display_area'); ?>" <?php checked(true, $display_area); ?>>
                <label for="<?php echo $this->get_field_id('display_area'); ?>"><?php echo LodgixTranslate::translate('Show Categories'); ?></label><br>
            </p>
		<?php
	}

	function update($new_instance, $old_instance) {
		$instance['title'] = strip_tags($new_instance['title']);
        $instance['layout'] = strip_tags($new_instance['layout']);
        $instance['display_properties'] = strip_tags($new_instance['display_properties']);
        $instance['rotate'] = $new_instance['rotate'] == 'on' ? true : false;
        $instance['display_city'] = $new_instance['display_city'] == 'on' ? true : false;
        $instance['display_area'] = $new_instance['display_area'] == 'on' ? true : false;
		return $instance;
	}

	function widget($args, $instance) {
		global $wpdb;

        extract($args);
		
        $properties_table = $wpdb->prefix . "lodgix_properties";
        $lang_pages_table = $wpdb->prefix . "lodgix_lang_pages";
		$pages_table = $wpdb->prefix . "lodgix_pages";

		// Each widget can store its own options. We keep strings here.
		$loptions = get_option('p_lodgix_options');
		$title = apply_filters('widget_title', empty($instance['title']) ? LodgixTranslate::translate('Featured Rentals') : LodgixTranslate::translate(esc_html($instance['title'])));

		echo $before_widget . $before_title . $title . $after_title;
		echo '<div class="lodgix-featured-properties" align="center">';

        $sql = 'SELECT ' . $properties_table . '.id AS id,property_id,description,enabled,featured,main_image_thumb,
                bedrooms,bathrooms,proptype,city,post_id FROM ' . $properties_table . '
                LEFT JOIN ' . $pages_table .  ' ON ' . $properties_table . '.id = ' . $pages_table .  '.property_id';

        if (!$loptions['p_lodgix_featured_select_all']) {
            // Show only featured properties
            $sql .= ' WHERE featured=1';
        }

        if (!empty($instance['rotate'])) {
            // Rotate
            $sql .= ' ORDER BY rand()';
        } else {
            $sql .= ' ORDER BY id';
        }

        $limit = $instance['display_properties'];
        if (!isset($limit) || !is_numeric($limit)) {
            $limit = 3;
        }
        if ($limit > 0) {
            // Limit number of displayed properties
            $sql .= ' LIMIT ' . $limit;
        }

        if (array_key_exists('layout', $instance)) {
            $layout = $instance['layout'];
        } elseif (array_key_exists('p_lodgix_display_featured_horizontally', $loptions)) {
            $layout = $loptions['p_lodgix_display_featured_horizontally'];
        } else {
            $layout = 0;
        }
        if ($layout == 1) {
            $position = 'float:left;margin-left:5px;';
        } elseif ($layout == 2) {
            $position = 'float:right;margin-right:5px;';
        } else {
            $position = '';
        }

        if (array_key_exists('display_area', $instance)) {
            $display_area = $instance['display_area'];
        } elseif (array_key_exists('p_lodgix_display_featured', $loptions) && $loptions['p_lodgix_display_featured'] == 'area') {
            $display_area = true;
        } else {
            $display_area = false;
        }

        $properties = $wpdb->get_results($sql);
		foreach($properties as $property) {
			$permalink = get_permalink($property->post_id);

			$location = $property->city;
			if ($property->city) {
                $location = LodgixTranslate::translate('in') .' ' . $location;
            }

			if ($display_area) {
			    $category = (new LodgixServiceProperty($property))->mainCategory('<br>');
                if ($category) {
                    $location = $category->category_title_long;
                }
            }

			$location = '<span class="lodgix-featured-category">' . $location . '</span>';

			if ($_REQUEST['lang'] == "de") {
				$page_id = $wpdb->get_var("SELECT page_id FROM " . $lang_pages_table . " WHERE property_id=" . $property->id);
				$permalink = get_permalink($page_id);
			}
	  
			if ($property->proptype == 'Room type') {
                $proptype = '';
            } else {
                $proptype = ', ' . LodgixTranslate::translate($property->proptype);
            }

			if ($property->bedrooms == 0) {
				$bedrooms = LodgixTranslate::translate('Studio') . ', ';
			} else {
                $bedrooms = $property->bedrooms . ' ' . LodgixTranslate::translate('Bedrm') . ', ';
            }

			echo '<div class="lodgix-featured-listing" style="-moz-border-radius: 5px 5px 5px 5px;' . $position . '">
				  <div class="imgset">
					  <a href="' . $permalink . '">
						  <img alt="View listing" src="' . $property->main_image_thumb . '">
						  <span class="featured-flag"></span>
					  </a>
				  </div>
				  <a class="address-link" href="' . $permalink . '">' . $property->description . '</a>
				  <div class="featured-details">' . $bedrooms . $property->bathrooms . ' ' . LodgixTranslate::translate('Bath') . $proptype . ''
					. $location . '
				  </div>    
				  </div>';
		}
		
		echo '</div>';
		echo $after_widget;
	}
}

function lodgixRegisterWidgetFeatured() {
	register_widget('Lodgix_Featured_Rentals_Widget');
}

add_action('widgets_init', 'lodgixRegisterWidgetFeatured');

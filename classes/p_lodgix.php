<?php

class p_lodgix {

    /**
    * @var string $url The url to this plugin
    */
    var $url = '';
    /**
    * @var string $urlpath The path to this plugin
    */
    var $urlpath = '';
    var $p_plugin_path = NULL;

    var $locale = 'en_US';
    var $sufix = 'en';

    var $page_titles = array();

    var $translator = NULL;

    public function __construct() {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $this->tableProperties = $prefix . LodgixConst::TABLE_PROPERTIES;
        $this->tablePages = $prefix . LodgixConst::TABLE_PAGES;
        $this->tableLangPages = $prefix . LodgixConst::TABLE_LANG_PAGES;
        $this->tableLanguages = $prefix . LodgixConst::TABLE_LANGUAGES;
        $this->tableCategoryPosts = $prefix . LodgixConst::TABLE_CATEGORY_POSTS;

        $this->url = plugins_url(basename(PLUGIN_PATH), PLUGIN_PATH);
        $this->urlpath = plugins_url('', PLUGIN_PATH);

        $this->db = new LodgixServiceDB();
        $this->config = new LodgixServiceConfig();

        //Admin menu
        add_action("admin_menu", array(&$this,"admin_menu_link"));

        //Actions
        add_action('wp_enqueue_scripts', array(&$this, 'load_js_and_css'));
        add_action('admin_enqueue_scripts', array(&$this, 'load_admin_js_and_css'));
        add_action("init", array(&$this,"p_lodgix_init"));
        add_action('wp_head', array(&$this,"p_lodgix_header_code"));
        add_action('wp_ajax_p_lodgix_notify', array(&$this,"p_lodgix_notify"));
        add_action('wp_ajax_nopriv_p_lodgix_notify', array(&$this,"p_lodgix_notify"));
        add_action('wp_ajax_p_lodgix_geturls', array(&$this,"p_lodgix_geturls"));
        add_action('wp_ajax_nopriv_p_lodgix_geturls', array(&$this,"p_lodgix_geturls"));
        add_action('wp_ajax_p_lodgix_check', array(&$this,"p_lodgix_check"));
        add_action('wp_ajax_nopriv_p_lodgix_check', array(&$this,"p_lodgix_check"));
        add_action('wp_ajax_p_lodgix_sort_vr', array(&$this,"p_lodgix_sort_vr"));
        add_action('wp_ajax_nopriv_p_lodgix_sort_vr', array(&$this,"p_lodgix_sort_vr"));

        add_action('wp_ajax_p_lodgix_properties_list', array(&$this, 'p_lodgix_properties_list'));
        add_action('wp_ajax_p_lodgix_toggle_featured', array(&$this,"p_lodgix_toggle_featured"));
        add_action('wp_ajax_p_lodgix_toggle_select_all', array(&$this,"p_lodgix_toggle_select_all"));

        add_action('wp_ajax_p_lodgix_save_settings', array(&$this,"p_lodgix_save_settings"));
        add_action('wp_ajax_p_lodgix_clean_database', array(&$this,"p_lodgix_clean_database"));

        add_action('wp_ajax_p_lodgix_custom_search', array(&$this,"p_lodgix_custom_search"));
        add_action('wp_ajax_nopriv_p_lodgix_custom_search', array(&$this,"p_lodgix_custom_search"));

        add_action("template_redirect", array(&$this,"p_lodgix_template_redirect"));

        add_filter( 'wp_list_pages_excludes', array(&$this,'p_lodgix_remove_pages_from_list'));

        // Menus
        add_filter('wp_get_nav_menu_items',array(&$this,'p_lodgix_nav_menus'),10,3);

        // Content
        add_filter('the_content', array(&$this,'p_lodgix_filter_content'));

        $shortcodes = new LodgixShortcodes();
        add_shortcode('lodgix_availability', array(&$shortcodes, 'availability'));
        add_shortcode('lodgix_vacation_rentals', array(&$shortcodes, 'vacationRentals'));
        add_shortcode('lodgix_search_rentals', array(&$shortcodes, 'searchRentals'));
        add_shortcode('lodgix_category', array(&$shortcodes, 'category'));
        add_shortcode('lodgix_single_property', array(&$shortcodes, 'property'));

        add_filter("gform_pre_render", array(&$this,'p_lodgix_pre_render_function'));
        add_filter("gform_admin_pre_render", array(&$this,'p_lodgix_pre_render_function'));
    }

    function load_js_and_css() {
        $this->load_js_and_css_jquery();
        $this->load_js_and_css_jquery_lodgix();
    }

    function load_js_and_css_jquery() {
        $path = $this->p_plugin_path;

        wp_enqueue_script('lodgix_jquery_latest_js', $path . 'js/jquery-1.11.3.min.js');
        wp_enqueue_script('lodgix_jquery_migrate_js', $path . 'js/jquery-migrate-1.2.1.min.js');
        wp_enqueue_script('lodgix_jquery_form_js', $path . 'js/jquery.form.min.js');
        wp_enqueue_style('lodgix_royalslider_css', $path . 'royalslider/royalslider.css');
        wp_enqueue_style('lodgix_royalslider_skin_css', $path . 'royalslider/skins/default-inverted/rs-default-inverted.css');
        wp_enqueue_script('lodgix_royalslider_js', $path . 'royalslider/jquery.royalslider.min.js');
        wp_enqueue_script('lodgix_jsrender_js', $path . 'js/jsrender.min.js');
        wp_enqueue_style('lodgix_tooltip_css', $path . 'lodgix-tooltip/lodgix-tooltip.min.css');
        wp_enqueue_script('lodgix_tooltip_js', $path . 'lodgix-tooltip/lodgix-tooltip.min.js');
        wp_enqueue_script('lodgix_jquery_confirm_js', $path . 'jquery.confirm/jquery.confirm.min.js');
    }

    function load_js_and_css_jquery_lodgix() {
        $path = $this->p_plugin_path;

        wp_enqueue_script('lodgix_jquery_no_conflict_js', $path . 'js/jquery-no-conflict.js');

        wp_enqueue_script('lodgix_jquery_js', $path . 'js/jquery_lodgix.js');
        wp_enqueue_script('lodgix_jquery_ui_js', $path . 'js/jquery-ui-lodgix.min.js');
        wp_enqueue_style('lodgix_jquery_ui_css', $path . 'css/jquery-ui-1.8.17.custom.css');

        wp_enqueue_script('lodgix_jquery_corner_js', $path . 'js/jquery.corner.js');
        wp_enqueue_script('lodgix_jquery_swf_object_js', $path . 'js/jquery.swfobject.js');
        wp_enqueue_script('lodgix_jquery_lity_js', $path . 'js/jquery.lity.js');
        wp_enqueue_style('lodgix_jquery_lity_css', $path . 'css/jquery.lity.css');

        wp_enqueue_script('lodgix_jquery_responsive_table_js', $path . 'js/jquery.lodgix-responsive-table.js');
        wp_enqueue_script('lodgix_jquery_text_expander_js', $path . 'js/jquery.lodgix-text-expander.js');
        wp_enqueue_script('lodgix_slider_js', $path . 'js/lodgix-slider.js');
        wp_enqueue_script('lodgix_map_js', $path . 'js/lodgix-map.js');

        wp_enqueue_style('lodgix_font_open_sans_css', esc_url_raw('//fonts.googleapis.com/css?family=Open+Sans:400,600,700,600italic,700italic,400italic'));
        wp_enqueue_style('lodgix_font_open_sans_condensed_css', esc_url_raw('//fonts.googleapis.com/css?family=Open+Sans+Condensed:300,300italic,700'));
        wp_enqueue_style('lodgix_common_css', $path . 'css/common.css');
        wp_enqueue_style('lodgix_inventory_common_css', $path . 'css/inventory/common.css');
        wp_enqueue_style('lodgix_inventory_grid1_css', $path . 'css/inventory/grid1.css');
        wp_enqueue_style('lodgix_inventory_rows1_css', $path . 'css/inventory/rows1.css');
        wp_enqueue_style('lodgix_inventory_rows2_css', $path . 'css/inventory/rows2.css');
        wp_enqueue_style('lodgix_property_common_css', $path . 'css/property/common.css');
        wp_enqueue_style('lodgix_widgets_featured_css', $path . 'css/widgets/featured.css');
        wp_enqueue_style('lodgix_widgets_search1_css', $path . 'css/widgets/search1.css');
        wp_enqueue_style('lodgix_widgets_search2_css', $path . 'css/widgets/search2.css');
    }

    function load_admin_js_and_css($hook) {
        if (is_admin() && $hook == 'settings_page_lodgix') {
            $path = $this->p_plugin_path;

            $this->load_js_and_css_jquery();

            wp_enqueue_style('lodgix_bootstrap_css', $path . 'bootstrap/css/bootstrap.min.css');
            wp_enqueue_script('lodgix_bootstrap_js', $path . 'bootstrap/js/bootstrap.min.js');
            wp_enqueue_script('lodgix_bootstrap_tabcollapse', $path . 'bootstrap-tabcollapse/bootstrap-tabcollapse.js');

            $this->load_js_and_css_jquery_lodgix();

            wp_enqueue_script('p_lodgix_jquery_validate_js', $path . 'js/jquery.validate.min.js');

            wp_enqueue_style('p_lodgix_ajax', $path . 'datatables/css/jquery.dataTables.min.css');
            wp_enqueue_script('p_lodgix_ajax', $path . 'datatables/js/jquery.dataTables.js');

            wp_enqueue_script('p_lodgix_script', $path . 'js/lodgix_javascript.js');

            wp_localize_script( 'p_lodgix_script', 'p_lodgix_lang', array(
                'required' => LodgixTranslate::translate('Field is required.'),
                'number' => LodgixTranslate::translate('Please enter a number.'),
                'min' => LodgixTranslate::translate('Please enter a value greater than or equal to 1.'),
            ));

            wp_localize_script('p_lodgix_script', 'p_lodgix_ajax', array(
                'ajaxURL' => admin_url('admin-ajax.php')
            ));

            wp_enqueue_script('lodgix_admin_tab_property_list_js', $path . 'js/admin-tab-property-list.js');
            wp_enqueue_script('lodgix_admin_tab_property_detail_js', $path . 'js/admin-tab-property-detail.js');
        }
    }

    function p_lodgix_set_page_titles() {
        global $wpdb;

        $this->page_titles = array();

        $active_languages = $wpdb->get_results('SELECT * FROM ' . $this->tableLanguages . ' WHERE enabled = 1');
        foreach ($active_languages as $l) {
            unload_textdomain(LodgixTranslate::LOCALIZATION_DOMAIN);
            $mo =  WP_CONTENT_DIR . '/'  . "languages/" .LodgixTranslate::LOCALIZATION_DOMAIN .'-' .$l->locale.".mo";
            if (!load_textdomain(LodgixTranslate::LOCALIZATION_DOMAIN, $mo))
            {
                $mo =  trailingslashit( plugin_dir_path( PLUGIN_PATH )) . "languages/" .LodgixTranslate::LOCALIZATION_DOMAIN .'-' .$l->locale.".mo";
                load_textdomain(LodgixTranslate::LOCALIZATION_DOMAIN, $mo);
            }

            $vacation_rentals =  LodgixTranslate::translate('Vacation Rentals');

            $availability =  LodgixTranslate::translate('Availability');

            $search = LodgixTranslate::translate('Search Rentals');

            $this->page_titles[$l->code] = array(
                'vacation_rentals' => $vacation_rentals,
                'availability' => $availability,
                'search' => $search
            );
       }

       $this->p_lodgix_load_locale();
    }

    function p_lodgix_load_locale() {

        $this->locale = get_locale();
        $this->sufix = substr($this->locale,0,2);

        $mo =  WP_CONTENT_DIR . '/'  . "languages/" .LodgixTranslate::LOCALIZATION_DOMAIN .'-' .$this->locale.".mo";

        if (!load_textdomain(LodgixTranslate::LOCALIZATION_DOMAIN, $mo))
        {
            $mo =  trailingslashit( plugin_dir_path( PLUGIN_PATH )) . "languages/" .LodgixTranslate::LOCALIZATION_DOMAIN .'-' .$this->locale.".mo";
            load_textdomain(LodgixTranslate::LOCALIZATION_DOMAIN, $mo);
        }

    }

    function p_lodgix_pre_render_function($form) {
        global $wpdb;

        //TODO: SELECT FORM ID
        //if($form["id"] != 5)
        //   return $form;

        //Creating drop down item array.
        $items = array();

        //Adding initial blank value.
        //$items[] = array("text" => "Not Selected", "value" => "0");

        //Adding post titles to the items array
        $properties = $wpdb->get_results('SELECT * FROM ' . $this->tableProperties . ' ORDER BY `order`');
        if ($properties)
        {
            foreach($properties as $property)
            {
              $items[] = array("value" => $property->id, "text" => $property->description);
            }
        }

        //Adding items to field id 8. Replace 8 with your actual field id. You can get the field id by looking at the input name in the markup.
        foreach($form["fields"] as &$field)
        if(trim($field["inputName"]) == 'lodgix_property_id'){
            $field["choices"] = $items;
        }

        return $form;
    }

    function p_lodgix_filter_content($content)
    {
        (new LodgixServiceUpgrade($this->config))->upgrade();
        $this->config = new LodgixServiceConfig();

//        if (strrpos($content,'[lodgix vacation_rentals]') > 0)
//            $content = str_replace('[lodgix vacation_rentals]',$this->get_vacation_rentals_content(),$content);
//        if (strrpos($content,'[lodgix availability]') > 0)
//            $content = str_replace('[lodgix availability]',$this->get_availability_page_content(),$content);
//        if (strrpos($content,'[lodgix search_rentals]') > 0)
//            $content = str_replace('[lodgix search_rentals]',$this->get_search_rentals_page_content(),$content);

        // TODO: remove
        if (strrpos($content,'[lodgix area ') !==  false) {
            $content = str_replace($content, $this->getCategoryPageContent($content), $content);
        }

        return $content;
    }

    // TODO: remove
    function getCategoryPageContent($code) {
        global $wpdb;
        preg_match_all('/([\d]+)/', $code, $match);
        $id = (int)$match[0][0];
        if (is_numeric($id)) {
            $categoryId = $wpdb->get_var("SELECT category_id FROM $this->tableCategoryPosts WHERE post_id=$id");
            if ($categoryId) {
                return "[lodgix_category $categoryId]";
            }
        }
        return '';
    }

    function cmp_menu_order($a,$b)
    {
        if ($a->menu_order == $b->menu_order)
            return 0;
        else if ($a->menu_order > $b->menu_order)
            return 1;
        else if ($a->menu_order < $b->menu_order)
            return -1;
    }

    function p_lodgix_nav_menus($items, $menu, $args)
    {
      global $wpdb;

      if (!strrpos($_SERVER['SCRIPT_NAME'],'nav-menus'))
      {

        $pos1 = $this->config->get('p_lodgix_vacation_rentals_page_pos');
        $pos2 = $this->config->get('p_lodgix_availability_page_pos');

        if ($pos1 != '-1')
        {
            foreach($items as $item)
            {
                if ($item->menu_order >= $pos1)
                {
                    $item->menu_order++;
                }
            }
        }
        if ($pos2 != '-1')
        {
            foreach($items as $item)
            {
                if ($item->menu_order >= $pos2)
                {
                    $item->menu_order++;
                }
            }
        }

        if (strpos($this->locale, 'en') !== false)
        {
            if ($pos1 != '-1')
            {
                $post_id = $this->config->get('p_lodgix_vacation_rentals_page_en');
                if ($post_id) {
                    $post = get_post($post_id);
                    if ($post) {
                        $item = wp_setup_nav_menu_item($post, 'post_type');
                        $item->menu_order = $pos1;
                        $items[] = $item;
                    }
                }
            }
            if ($pos2 != '-1')
            {
                $post_id = $this->config->get('p_lodgix_availability_page_en');
                if ($post_id) {
                    $post = get_post($post_id);
                    if ($post) {
                        $item = wp_setup_nav_menu_item($post);
                        $item->menu_order = $pos2;
                        $items[] = $item;
                    }
                }
            }
        }
        else
        {

            if ($pos1 != '-1')
            {
                $post_id = $wpdb->get_var("SELECT page_id FROM " . $this->tableLangPages . " WHERE property_id=-1 AND language_code='" . $this->sufix. "'");
                if ($post_id) {
                    $post = get_post($post_id);
                    if ($post) {
                        $item = wp_setup_nav_menu_item($post);
                        $item->menu_order = $pos1;
                        $items[] = $item;
                    }
                }
            }
            if ($pos2 != '-1')
            {
                $post_id = $wpdb->get_var("SELECT page_id FROM " . $this->tableLangPages . " WHERE property_id=-2 AND language_code='" . $this->sufix. "'");
                if ($post_id) {
                    $post = get_post($post_id);
                    if ($post) {
                        $item = wp_setup_nav_menu_item($post);
                        $item->menu_order = $pos2;
                        $items[] = $item;
                    }
                }
            }
        }

        usort( $items, array(&$this,'cmp_menu_order'));
       }

       return $items;

    }

    function p_lodgix_remove_pages_from_list($excludes) {
        global $wpdb;
        $posts = $wpdb->get_results('SELECT * FROM ' . $this->tablePages);
        foreach($posts as $post)
        {
            if (!$post->enabled)
            {
                array_push($excludes,$post->page_id);
            }
        }
        return $excludes;
    }

    function p_lodgix_toggle_select_all() {
        global $wpdb;
        if ($_POST['checked'] == 'true') $checked = 1; else $checked = 0;

        $this->config->set('p_lodgix_featured_select_all', $checked);
        $this->config->save();

        die(json_encode(array('result' => 'OK')));

    }

    function p_lodgix_toggle_featured() {
        global $wpdb;
        if ($_POST['checked'] == 'true') $checked = 1; else $checked = 0;
        $id = @esc_sql($_POST['id']);

        $sql = 'UPDATE ' . $this->tablePages . ' SET featured=' . $checked . ' WHERE property_id = ' . $id;
        $wpdb->query($sql);
        die(json_encode(array('result' => 'OK')));

    }

    function p_lodgix_properties_list() {
        $inventory = new LodgixInventory($this->config, $this->locale, $this->sufix);
        die($inventory->inventoryJson());
    }

    function p_lodgix_sort_vr() {
        $this->p_lodgix_load_locale();
        $sort = isset($_POST['lodgix-property-list-sort']) ? @esc_sql($_POST['lodgix-property-list-sort']) : '';
        $categoryId = isset($_POST['lodgix-custom-search-area']) ? @esc_sql($_POST['lodgix-custom-search-area']) : '';
        $bedrooms = isset($_POST['lodgix-custom-search-bedrooms']) ? @esc_sql($_POST['lodgix-custom-search-bedrooms']) : null;
        $priceFrom = isset($_POST['lodgix-custom-search-daily-price-from']) ? @esc_sql($_POST['lodgix-custom-search-daily-price-from']) : null;
        $priceTo = isset($_POST['lodgix-custom-search-daily-price-to']) ? @esc_sql($_POST['lodgix-custom-search-daily-price-to']) : null;
        $id = isset($_POST['lodgix-custom-search-id']) ? @esc_sql($_POST['lodgix-custom-search-id']) : '';
        $arrival = isset($_POST['lodgix-custom-search-arrival']) ? @esc_sql($_POST['lodgix-custom-search-arrival']) : '';
        $nights = isset($_POST['lodgix-custom-search-nights']) ? @esc_sql($_POST['lodgix-custom-search-nights']) : '';
        $petFriendly = (isset($_POST['lodgix-custom-search-pet-friendly']) && $_POST['lodgix-custom-search-pet-friendly'] == 'on');
        $amenities = isset($_POST['lodgix-custom-search-amenity']) ? $_POST['lodgix-custom-search-amenity'] : null;
        $tags = isset($_POST['lodgix-custom-search-tag']) ? $_POST['lodgix-custom-search-tag'] : null;
        $inventory = new LodgixInventory($this->config, $this->locale, $this->sufix);
        $content = $inventory->inventoryItems($sort, $categoryId, $bedrooms, $id, $arrival, $nights, $amenities, $priceFrom, $priceTo, $petFriendly, $tags);
        die($content);
    }


    function p_lodgix_init() {
        $this->p_plugin_path = plugin_dir_url(plugin_basename(PLUGIN_PATH));
        $this->p_lodgix_load_locale();

    }

    function p_lodgix_template_redirect()
    {
        global $wp_query;

        if ($wp_query->post->ID && (new LodgixServicePost($this->config))->isLodgixPost($wp_query->post->ID)) {

            if ($this->config->get('p_lodgix_page_template') && $this->config->get('p_lodgix_page_template') != '')
            {
                if ($this->config->get('p_lodgix_page_template') && $this->config->get('p_lodgix_page_template') == 'CUSTOM')
                {
                    $template = WP_CONTENT_DIR . '/' . $this->config->get('p_lodgix_custom_page_template');
                }
                else {
                    $template = TEMPLATEPATH . '/' . $this->config->get('p_lodgix_page_template');
                }

                if (file_exists($template)) {
                    include($template);
                    die();
                }
            }
            else
            {
                $current_theme = get_current_theme();
                if ($this->config->get('p_lodgix_thesis_compatibility'))
                {
                    include('thesis_no_sidebars.php');
                    die();
                }
                else if ($this->config->get('p_lodgix_thesis_2_compatibility'))
                {

                }
                else if ($current_theme  == "FlexSqueeze")
                {

                }
                else if ($current_theme  == "pureVISION")
                {
                    include('purevision_page_template.php');
                    die();
                }
                else
                {
                    include('lodgix_page_template.php');
                    die();
                }
            }
        }
    }

    function p_lodgix_header_code() {
        global $post;
        global $wpdb;

        if (!$post) {
            return;
        }

        $post_id = $post->ID;
        echo "\n".'<!-- Start Lodgix -->'."\n";
        if (!$this->config->get('p_lodgix_thesis_compatibility') && !$this->config->get('p_lodgix_thesis_2_compatibility')) {
            $properties = $wpdb->get_results('SELECT description,description_long,city FROM ' . $this->tableProperties . ' WHERE post_id=' . $post_id);
            if ($properties) {
                $property = $properties[0];
                echo '<meta name="description" content="' . trim(wptexturize($this->truncate_text($property->description_long,150))) . '" />';
                $keywords = $property->description . ', vacation rental, vacation home, vacation, homes, rentals, cottages, condos, holiday';
                if ($property->city != "") {
                    $keywords .= ', ' . $property->city;
                }
                echo '<meta name="keywords" content="' . trim(wptexturize($keywords)) . '" />';
            }
        }

        $css_path = WP_CONTENT_DIR;
        if (file_exists($css_path  . '/lodgix-custom.css')) {
            echo '<link type="text/css" rel="stylesheet" href="' . WP_CONTENT_URL  . '/lodgix-custom.css" />' . "\n";
        }
        ?><script>
            jQueryLodgix(document).ready(function() {
                if (location.hash != '') {
                    location.hash = location.hash;
                }
            });
        </script>
        <?php
        echo '<!-- End Of Lodgix -->'."\n";
    }

      /**
      * @desc Adds the options subpanel
      */
      function admin_menu_link() {
          add_options_page('Lodgix Settings', 'Lodgix Settings', 10, basename(PLUGIN_PATH), array(&$this, 'admin_options_page'));
          add_filter( 'plugin_action_links_' . plugin_basename(PLUGIN_PATH), array(&$this, 'filter_plugin_actions'), 10, 2 );
      }

      /**
      * @desc Adds the Settings link to the plugin activate/deactivate page
      */
      function filter_plugin_actions($links, $file) {
         $settings_link = '<a href="options-general.php?page=' . basename(PLUGIN_PATH) . '">' . LodgixTranslate::translate('Settings') . '</a>';
         array_unshift( $links, $settings_link ); // before other links

         return $links;
      }

    function link_translated_pages()
    {
        global $sitepress,$wpdb;

        $languages = $wpdb->get_results("SELECT * FROM " . $this->tableLanguages . " WHERE enabled = 1 and code <> 'en'");

        if ($languages)
        {

            foreach ($languages as $l)
            {
                $sql = "SELECT * FROM " . $this->tableLangPages . " WHERE language_code = '" . $l->code . "'";

                $posts = $wpdb->get_results($sql);

                foreach($posts as $post)
                {
                    if ($post->property_id == -1)
                    {
                        $post_id = $this->config->get('p_lodgix_vacation_rentals_page_en');
                    }
                    else if ($post->property_id == -2)
                    {
                        $post_id = $this->config->get('p_lodgix_availability_page_en');
                    }
                    else if ($post->property_id == -3)
                    {
                        $post_id = $this->config->get('p_lodgix_search_rentals_page_en');
                    }
                    else
                    {
                        $post_id = 0;
                    }

                    if ($post_id) {
                        $trid = $sitepress->get_element_trid($post_id, 'post_page');
                        $sitepress->set_element_language_details($post->page_id, 'post_page', $trid, $l->code);

                    }
                }
            }
        }
    }

    function set_thesis_2_custom_templates_for_page($page_id) {

        $thesis_skin = get_option('thesis_skin');
        if ($thesis_skin) {
            $class = $thesis_skin['class'];
            $thesis_classic_r_templates = get_option('thesis_classic_r_templates');

            $template = Array('template' => $this->config->get('p_lodgix_thesis_2_template'));

            if ($template != '') {
                add_post_meta($page_id,  '_' . $class, $template, true);
                $meta_values = update_post_meta($page_id,  '_' . $class, $template);
            }
            else {
                delete_post_meta($page_id,  '_' . $class);
            }
        }
    }

    function set_thesis_2_custom_templates() {
        global $wpdb;

        $active_languages = $wpdb->get_results('SELECT * FROM ' . $this->tableLanguages . ' WHERE enabled = 1');
        foreach ($active_languages as $l) {
            $lc = $l->code;
            $this->set_thesis_2_custom_templates_for_page($this->config->get("p_lodgix_vacation_rentals_page_$lc"));
            $this->set_thesis_2_custom_templates_for_page($this->config->get("p_lodgix_availability_page_$lc"));
            $this->set_thesis_2_custom_templates_for_page($this->config->get("p_lodgix_search_rentals_page_$lc"));
        }

        $posts = $wpdb->get_results('SELECT * FROM ' . $this->tablePages);
        foreach($posts as $post)
        {
            $this->set_thesis_2_custom_templates_for_page($post->page_id);
        }

        // TODO: Remove other posts from properties' table
        $posts_de = $wpdb->get_results("SELECT * FROM $this->tableLangPages WHERE property_id > 0");
        foreach($posts_de as $post)
        {
          $this->set_thesis_2_custom_templates_for_page($post->page_id);
        }

        $posts = $wpdb->get_results("SELECT * FROM $this->tableCategoryPosts");
        foreach ($posts as $post) {
            $this->set_thesis_2_custom_templates_for_page($post->post_id);
        }
    }

    function buildAllPages($ownerData, $propertyData) {
        (new LodgixParser($this->config))->processFetchedData($ownerData, $propertyData);
        $this->config = new LodgixServiceConfig();

        $this->buildMainPages();
        $this->link_translated_pages();

        (new LodgixServicePost($this->config))->createOrUpdateAllPages();

        if ($this->config->get('p_lodgix_thesis_2_compatibility')) {
            $this->set_thesis_2_custom_templates();
        }
    }

    // TODO: move to LodgixServicePost
    function buildMainPages() {
        global $wpdb;

        $this->p_lodgix_set_page_titles();

        $languages = $wpdb->get_results("SELECT * FROM $this->tableLanguages WHERE enabled=1");
        if ($languages) {
            foreach ($languages as $l) {
                $lc = $l->code;
                $post_id = $this->config->get("p_lodgix_vacation_rentals_page_$lc");
                if (!$post_id || !get_post($post_id)) {
                    $post = array();
                    if ($lc == 'en') {
                        $policies = (new LodgixServiceProperty(null, $lc))->policies();
                        if ($policies[0]->post_slug_vacation_rentals) {
                            $post['post_name'] = $policies[0]->post_slug_vacation_rentals;
                        }
                    }
                    $post['post_title'] = $this->page_titles[$lc]['vacation_rentals'];
                    $post['menu_order'] = 1;
                    $post['post_status'] = 'publish';
                    $post['post_content'] = '[lodgix_vacation_rentals]';
                    $post['post_author'] = 1;
                    $post['post_type'] = "page";
                    $post_id = wp_insert_post($post);
                    if ($post_id != 0) {
                        $this->config->set("p_lodgix_vacation_rentals_page_$lc", (int)$post_id);
                        if ($lc != 'en') {
                            $sql = "INSERT INTO $this->tableLangPages (page_id,property_id,source_page_id,language_code) VALUES($post_id,-1,NULL,'$lc')";
                            $wpdb->query($sql);
                        }
                    }
                } else {
                    $post = array();
                    if ($lc == 'en') {
                        $policies = (new LodgixServiceProperty(null, $lc))->policies();
                        if ($policies[0]->post_slug_vacation_rentals) {
                            $post['post_name'] = $policies[0]->post_slug_vacation_rentals;
                        }
                    }
                    $post['post_title'] = $this->page_titles[$lc]['vacation_rentals'];
                    $post['post_content'] = '[lodgix_vacation_rentals]';
                    $post['ID'] = $post_id;
                    $post_id = wp_update_post($post);
                    $posts_table = $wpdb->prefix . "posts";
                    $sql = "UPDATE " . $posts_table . " SET post_content='[lodgix_vacation_rentals]' WHERE id=" . $post_id;
                    $wpdb->query($sql);
                }
            }
        }

        $this->config->save();

        if ($this->config->get('p_lodgix_thesis_compatibility') || $this->config->get('p_lodgix_thesis_2_compatibility')) {
            delete_post_meta($this->config->get('p_lodgix_vacation_rentals_page_en'), 'thesis_title');
            delete_post_meta($this->config->get('p_lodgix_vacation_rentals_page_en'), 'thesis_description');
            delete_post_meta($this->config->get('p_lodgix_vacation_rentals_page_en'), 'thesis_keywords');
        }

        $languages = $wpdb->get_results('SELECT * FROM ' . $this->tableLanguages . ' WHERE enabled = 1');
        if ($languages) {
            foreach ($languages as $l) {
                $lc = $l->code;
                $post = array();
                $post['menu_order'] = 2;
                $post['post_title'] =  $this->page_titles[$lc]['availability'];
                $post['post_content'] = '[lodgix_availability]';
                $post['post_status'] = 'publish';
                $post['post_type'] = "page";
                $post_id = $this->config->get("p_lodgix_availability_page_$lc");
                if (!$post_id || !get_post($post_id)) {
                    $post_id = wp_insert_post($post);
                    if ($post_id != 0) {
                        $this->config->set("p_lodgix_availability_page_$lc", (int)$post_id);
                        if ($lc != 'en') {
                            $sql = "INSERT INTO $this->tableLangPages (page_id,property_id,source_page_id,language_code) VALUES($post_id,-2,NULL,'$lc')";
                            $wpdb->query($sql);
                        }
                    }
                } else {
                    $post = array();
                    $post['post_title'] =  $this->page_titles[$lc]['availability'];
                    $post['post_content'] = '[lodgix_availability]';
                    $post['ID'] = $post_id;
                    $post_id = wp_update_post($post);
                    $posts_table = $wpdb->prefix . "posts";
                    $sql = "UPDATE " . $posts_table . " SET post_content='[lodgix_availability]' WHERE id=" . $post_id;
                    $wpdb->query($sql);
                }
            }
        }

        $languages = $wpdb->get_results('SELECT * FROM ' . $this->tableLanguages . ' WHERE enabled = 1');
        if ($languages) {
            foreach ($languages as $l) {
                $lc = $l->code;
                $post = array();
                $post['post_title'] =  $this->page_titles[$lc]['search'];
                $post['post_content'] = '[lodgix_search_rentals]';
                $post['post_status'] = 'publish';
                $post['post_type'] = "page";
                $post_id = $this->config->get("p_lodgix_search_rentals_page_$lc");
                if (!$post_id || !get_post($post_id)) {
                    $post_id = wp_insert_post($post, true);
                    if ($post_id != 0) {
                        $this->config->set("p_lodgix_search_rentals_page_$lc", (int)$post_id);
                        if ($lc != 'en') {
                            $sql = "INSERT INTO $this->tableLangPages (page_id,property_id,source_page_id,language_code) VALUES($post_id,-3,NULL,'$lc')";
                            $wpdb->query($sql);
                        }
                    }
                } else {
                    $post = array();
                    $post['post_title'] =  $this->page_titles[$lc]['search'];
                    $post['post_content'] = '[lodgix_search_rentals]';
                    $post['ID'] = $post_id;
                    $post_id = wp_update_post($post);
                    $posts_table = $wpdb->prefix . "posts";
                    $sql = "UPDATE " . $posts_table . " SET post_content='[lodgix_search_rentals]' WHERE id=" . $post_id;
                    $wpdb->query($sql);
                }
            }
        }

        $this->config->save();
    }

//    function remove_thesis_page($page_id)
//    {
//        if ($this->config->get('p_lodgix_thesis_compatibility') || $this->config->get('p_lodgix_thesis_2_compatibility'))
//        {
//          $thesis_options = get_option('thesis_options');
//          unset($thesis_options->nav['pages'][$page_id]);
//          update_option('thesis_options',$thesis_options);
//        }
//    }

    function p_lodgix_custom_search() {
        $this->p_lodgix_load_locale();

        $arrival = isset($_POST['lodgix-custom-search-arrival']) && $_POST['lodgix-custom-search-arrival'] !== '' ? $_POST['lodgix-custom-search-arrival'] : '';
        $nights = isset($_POST['lodgix-custom-search-nights']) && $_POST['lodgix-custom-search-nights'] !== '' ? $_POST['lodgix-custom-search-nights'] : '1';
        $categoryId = isset($_POST['lodgix-custom-search-area']) && $_POST['lodgix-custom-search-area'] !== '' ? $_POST['lodgix-custom-search-area'] : 'ALL_AREAS';
        $bedrooms = isset($_POST['lodgix-custom-search-bedrooms']) && $_POST['lodgix-custom-search-bedrooms'] !== '' ? $_POST['lodgix-custom-search-bedrooms'] : '0';
        $priceFrom = isset($_POST['lodgix-custom-search-daily-price-from']) ? (int)$_POST['lodgix-custom-search-daily-price-from'] : 0;
        $priceTo = isset($_POST['lodgix-custom-search-daily-price-to']) ? (int)$_POST['lodgix-custom-search-daily-price-to'] : 0;
        $petFriendly = (isset($_POST['lodgix-custom-search-pet-friendly']) && $_POST['lodgix-custom-search-pet-friendly'] == 'on');
        $id = isset($_POST['lodgix-custom-search-id']) && $_POST['lodgix-custom-search-id'] !== '' ? $_POST['lodgix-custom-search-id'] : '';
        $amenities = isset($_POST['lodgix-custom-search-amenity']) ? $_POST['lodgix-custom-search-amenity'] : null;
        $tags = isset($_POST['lodgix-custom-search-tag']) ? $_POST['lodgix-custom-search-tag'] : null;
        $count = (new LodgixServiceProperties($this->config))->countAvailableProperties(
            $arrival,
            $nights,
            $categoryId,
            $bedrooms,
            $priceFrom,
            $priceTo,
            $petFriendly,
            $id,
            $amenities,
            $tags
        );
        die(json_encode(array('num_results' => $count)));
    }

    function reschedule_notify() {
        wp_clear_scheduled_hook('p_lodgix_notify');
        wp_schedule_single_event(time()+900, 'wp_ajax_nopriv_p_lodgix_notify');
    }

    function p_lodgix_notify() {
        ini_set('max_execution_time', 0);

        $this->p_lodgix_build();

        $api = new LodgixApi($this->config->get('p_lodgix_owner_id'), $this->config->get('p_lodgix_api_key'));
        $ownerData = $api->getOwner();
        $propertyData = $api->getProperties();
        if (empty($ownerData) || empty($propertyData)) {
            $this->reschedule_notify();
            die("ERROR");
        }
        if ($ownerData->Error) {
            $this->reschedule_notify();
            die("ERROR");
        }

        $this->buildAllPages($ownerData, $propertyData);

        die("OK");
    }

    function p_lodgix_geturls() {
        header("Content-type: text/xml");
        global $wpdb;
        ini_set('max_execution_time', 0);
        $posts = $wpdb->get_results('SELECT * FROM ' . $this->tablePages);
        print "<Properties>";
        foreach($posts as $post)
        {
            print "<Property>";
            print "<ID>" . $post->property_id . "</ID>";
            print '<URL>' .  htmlentities(urlencode(get_permalink($post->page_id))) . '</URL>';

            $lposts = $wpdb->get_results('SELECT * FROM ' . $this->tableLangPages . ' WHERE property_id=' . $post->property_id);
            foreach($lposts as $lpost)
            {
                print '<URL' . strtoupper($lpost->language_code) . '>' . htmlentities(urlencode(get_permalink($lpost->page_id))) . '</URL' . strtoupper($lpost->language_code) . '>';
            }
            print "</Property>";
        }
        print "</Properties>";

        die("");
    }


    function p_lodgix_check() {
        ini_set('max_execution_time', 0);
        die("PLUGIN_INSTALLED");
    }

    function p_lodgix_build() {
        $this->db->create();
        add_option(LodgixConst::OPTION_DB_VERSION, LodgixConst::DB_VERSION);
    }

    /**
    * Returns Last Index
    **/
    function lastIndexOf($substr, $str){
        if(false !== ($r = strpos(strrev($str), strrev($substr))))
            return strlen($str)-$r-strlen($substr);
        return false;
    }

    /**
    * Truncates text
    **/
    function truncate_text($text,$limit)
    {
        if (strlen($text) < $limit)
            return $text;

        $text = substr ($text, 0,$limit-1);
        $text = substr ($text, 0,$this->lastIndexOf(' ',$text));
        return $text . '.';
    }

    function p_lodgix_save_settings() {
        global $wpdb;

        if (!(current_user_can('manage_options') && $_POST['p_lodgix_save'])) {
            $json = array('result' => 'FAIL', 'msg' => 'Undefined Error!');
            die(json_encode($json));
        }

        ini_set('max_execution_time', 0);

        $this->p_lodgix_build();

        if (!wp_verify_nonce($_POST['_wpnonce'], 'p_lodgix-update-options')) {
            die('Whoops! There was a problem with the data you posted. Please go back and try again.');
        }

        $api = new LodgixApi($_POST['p_lodgix_owner_id'], $_POST['p_lodgix_api_key']);
        $ownerData = $api->getOwner();
        $propertyData = $api->getProperties();
        if (empty($ownerData) || empty($propertyData)) {
            $json = array('result' => 'FAIL', 'msg' => 'Error: It wasn\'t possible to connect to Lodgix. Please try again later.');
            die(json_encode($json));
        }
        if ($ownerData->Error) {
            $json = array('result' => 'FAIL', 'msg' => 'Error: ' . strval($ownerData->Error->Message));
            die(json_encode($json));
        }

        if (
            $this->config->get('p_lodgix_owner_id') != $_POST['p_lodgix_owner_id'] ||
            $this->config->get('p_lodgix_api_key') != $_POST['p_lodgix_api_key'] ||
            $this->config->get('p_lodgix_display_title') != $_POST['p_lodgix_display_title']
        ) {
            (new LodgixServiceInstall($this->config))->cleanAll();
            $this->config = new LodgixServiceConfig();
        }

        $reload = (new LodgixServiceAdmin($this->config, $_POST))->saveConfig();
        $this->config = new LodgixServiceConfig();

        // Save active languages
        $active_languages = array("'en'");
        $languages = $wpdb->get_results("SELECT * FROM $this->tableLanguages");
        if ($languages) {
            foreach ($languages as $l) {
                if (isset($_POST['p_lodgix_contact_url_' . $l->code])) {
                    $this->config->set('p_lodgix_contact_url_' . $l->code, $_POST['p_lodgix_contact_url_' . $l->code]);
                }
                if ($_POST['p_lodgix_generate_' . $l->code] && ($l->code != 'en')) {
                    array_push($active_languages, "'$l->code'");
                }
            }
            $active_languages = implode(',', $active_languages);
            $wpdb->query("UPDATE $this->tableLanguages SET enabled=0 WHERE code NOT IN ($active_languages)");
            $wpdb->query("UPDATE $this->tableLanguages SET enabled=1 WHERE code IN ($active_languages)");
        }
        $this->config->save();

        (new LodgixServicePost($this->config))->deleteInactiveLanguagePosts();

        $this->buildAllPages($ownerData, $propertyData);

        $thesis_2_template_options = Array();
        array_push($thesis_2_template_options,Array('class' => '','title' => 'Default'));

        try {
            $thesis_skin = get_option('thesis_skin');
            if ($thesis_skin) {
                $class = $thesis_skin['class'];
                $thesis_classic_r_templates = get_option('thesis_classic_r_templates');
            }

            if ((is_array($thesis_classic_r_templates) || $thesis_classic_r_templates instanceof Traversable)) {
                foreach ($thesis_classic_r_templates as $key => $value) {
                    $title = ucwords($key);
                        if (0 === strpos($key, 'custom_')) {
                            $title = $value['title'];
                        }
                        array_push($thesis_2_template_options,Array('class' => $key,'title' => $title));
                }
            }
        } catch (SomeException $e) {}

        $json = array('result' => 'OK', 'msg' => 'Success! Your changes were sucessfully saved!');
        if ($reload) {
            $json['result'] = 'RELOAD';
        }
        die(json_encode($json));
    }

    function p_lodgix_clean_database() {
        if (current_user_can('manage_options') && $_POST['p_lodgix_clean']) {
            (new LodgixServiceInstall($this->config))->cleanAll();
            $this->config = new LodgixServiceConfig();
        }
    }

    function admin_options_page() {
        $this->p_lodgix_build();

        (new LodgixServiceUpgrade($this->config))->upgrade();
        $this->config = new LodgixServiceConfig();

        echo (new LodgixAdminSettings($this->config))->page();
    }

}

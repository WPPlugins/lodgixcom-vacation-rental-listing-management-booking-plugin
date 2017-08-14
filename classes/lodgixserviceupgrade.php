<?php

class LodgixServiceUpgrade {

    function __construct($config) {
        global $wpdb;
        $this->db = $wpdb;
        $this->dbPrefix = $this->db->prefix;
        $this->config = $config;
        $this->serviceDb = new LodgixServiceDB();
    }

    public function upgrade() {
        $oldDbVersion = get_option(LodgixConst::OPTION_DB_VERSION);
        if ($oldDbVersion) {
            $oldDbVersion = (float)$oldDbVersion;
            if ($oldDbVersion < (float)LodgixConst::DB_VERSION) {
                $this->upgradeDb($oldDbVersion);
            }
        }
        update_option(LodgixConst::OPTION_DB_VERSION, LodgixConst::DB_VERSION);
    }

    protected function upgradeDb($oldDbVersion) {
        if ($oldDbVersion < 1.2) {
            $this->upgradeDb12();
        }
        if ($oldDbVersion < 1.3) {
            $this->upgradeDb13();
        }
        if ($oldDbVersion < 1.4) {
            $this->upgradeDb14();
        }
        if ($oldDbVersion < 1.5) {
            $this->upgradeDb15();
        }
        if ($oldDbVersion < 1.6) {
            $this->upgradeDb16();
        }
        if ($oldDbVersion < 1.8) {
            $this->upgradeDb18();
        }
        if ($oldDbVersion < 1.9) {
            $this->upgradeDb19();
        }
        if ($oldDbVersion < 2.0) {
            $this->upgradeDb20();
        }
        if ($oldDbVersion < 2.1) {
            $this->upgradeDb21();
        }
        if ($oldDbVersion < 2.2) {
            $this->upgradeDb22();
        }
        if ($oldDbVersion < 2.5) {
            $this->upgradeDb25();
        }
        if ($oldDbVersion < 2.6) {
            $this->upgradeDb26();
        }
        if ($oldDbVersion < 2.7) {
            $this->upgradeDb27();
        }
        if ($oldDbVersion < 2.8) {
            $this->upgradeDb28();
        }
        if ($oldDbVersion < 2.9) {
            $this->upgradeDb29();
        }
        if ($oldDbVersion < 3.0) {
            $this->upgradeDb30();
        }
        if ($oldDbVersion < 3.1) {
            $this->upgradeDb31();
        }
        if ($oldDbVersion < 3.2) {
            $this->upgradeDb32();
        }
        if ($oldDbVersion < 3.3) {
            $this->upgradeDb33();
        }
        if ($oldDbVersion < 3.4) {
            $this->upgradeDb34();
        }
        if ($oldDbVersion < 3.5) {
            $this->upgradeDb35();
        }
        if ($oldDbVersion < 3.6) {
            $this->upgradeDb36();
        }
        if ($oldDbVersion < 3.7) {
            $this->upgradeDb37();
        }
        if ($oldDbVersion < 3.8) {
            $this->upgradeDb38();
        }
    }

    private function upgradeDb12() {
        $pictures_path = WP_CONTENT_DIR.'/lodgix_pictures';
        $this->rrmdir($pictures_path);
    }

    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        $this->rrmdir($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    private function upgradeDb13() {
        $tableProperties = $this->dbPrefix . LodgixConst::TABLE_PROPERTIES;
        $sql = "ALTER TABLE $tableProperties MODIFY COLUMN `bathrooms` float default '0';";
        $this->db->query($sql);
    }

    private function upgradeDb14() {
        $tablePictures = $this->dbPrefix . LodgixConst::TABLE_PICTURES;
        $sql = "ALTER TABLE $tablePictures MODIFY COLUMN `caption` varchar(255) default NULL;";
        $this->db->query($sql);
    }

    private function upgradeDb15() {
        $tableProperties = $this->dbPrefix . LodgixConst::TABLE_PROPERTIES;
        $sql = "ALTER TABLE $tableProperties ADD COLUMN `video_url` text default NULL;";
        $this->db->query($sql);
        $sql = "ALTER TABLE $tableProperties ADD COLUMN `virtual_tour_url` text default NULL;";
        $this->db->query($sql);
    }

    private function upgradeDb16() {
        $tableProperties = $this->dbPrefix . LodgixConst::TABLE_PROPERTIES;
        $sql = "ALTER TABLE $tableProperties ADD COLUMN `beds_text` text default NULL;";
        $this->db->query($sql);
    }

    private function upgradeDb18() {
        $tableLinkRotators = $this->dbPrefix . LodgixConst::TABLE_LINK_ROTATORS;
        if ($this->db->get_var("show tables like '$tableLinkRotators'") != $tableLinkRotators) {
            $sql = "CREATE TABLE $tableLinkRotators (
                        `id` int(11) NOT NULL auto_increment,
                        `url` varchar(255) NOT NULL DEFAULT '',
                        `title` varchar(255) DEFAULT NULL,
                        PRIMARY KEY (`id`)
                );";
            $this->db->query($sql);
        }
    }

    private function upgradeDb19() {
        $tableLangAmenities = $this->dbPrefix . LodgixConst::TABLE_LANG_AMENITIES;
        $this->db->query("ALTER TABLE $tableLangAmenities ADD COLUMN searchable tinyint(1) NOT NULL default 0;");
    }

    private function upgradeDb20() {
        $tableLanguages = $this->dbPrefix . LodgixConst::TABLE_LANGUAGES;
        $tableLangAmenities = $this->dbPrefix . LodgixConst::TABLE_LANG_AMENITIES;
        $tableLangProperties = $this->dbPrefix . LodgixConst::TABLE_LANG_PROPERTIES;

        $this->config->set('p_lodgix_vacation_rentals_page_en', $this->config->get('p_lodgix_vacation_rentals_page'));
        $this->config->set('p_lodgix_contact_url_en', $this->config->get('p_lodgix_contact_url'));
        $this->config->set('p_lodgix_search_rentals_page_en', $this->config->get('p_lodgix_search_rentals_page'));
        $this->config->set('p_lodgix_availability_page_en', $this->config->get('p_lodgix_availability_page'));

        $this->config->set('p_lodgix_areas_pages_en', $this->config->get('p_lodgix_areas_pages'));
        $this->config->deleteKey('p_lodgix_areas_pages');

        if ($this->config->get('p_lodgix_generate_german')) {
            $this->db->query("UPDATE $tableLanguages SET enabled=1 WHERE code = 'de'");
        }

        $this->db->query("ALTER TABLE $tableLangAmenities ADD COLUMN `language_code` varchar(2);");
        $this->db->query("ALTER TABLE $tableLangAmenities CHANGE COLUMN `description_de` `description_translated` varchar(255) DEFAULT NULL;");
        $this->db->query("UPDATE $tableLangAmenities SET language_code = 'de';");

        $this->db->query("ALTER TABLE $tableLangProperties DROP PRIMARY KEY, ADD PRIMARY KEY(`id`,`language_code`);");

        $this->config->save();
        wp_redirect($_SERVER["REQUEST_URI"]);
    }

    private function upgradeDb21() {
        $old = get_option('widget_lodgix_custom_search');
        update_option('old_widget_lodgix_custom_search',$old);

        $old = get_option('widget_lodgix_custom_search_2');
        update_option('old_widget_lodgix_custom_search_2',$old);

        $old = get_option('widget_lodgix_featured');
        update_option('old_widget_lodgix_featured',$old);

        update_option('widget_lodgix_custom_search',array());
        update_option('widget_lodgix_custom_search_2',array());
        update_option('widget_lodgix_featured',array());

        $sidebars = get_option('sidebars_widgets');
        $counter = 20;
        foreach($sidebars as $key => $value) {
            $widget_counter = 0;
            if (is_array($sidebars[$key])) {
                foreach($sidebars[$key] as $widget) {


                    if ($widget == 'rentals-search' || $widget == 'rentals-search-2' || $widget == 'featured-rentals') {

                        if ($widget == 'rentals-search' || $widget == 'rentals-search-2') {

                            $sidebars[$key][$widget_counter] = 'lodgix_custom_search-' . $counter;
                            $old_widget = get_option('old_widget_lodgix_custom_search');
                            if ($widget == 'rentals-search-2') {
                                $old_widget = get_option('old_widget_lodgix_custom_search_2');
                            }

                            $amenities = $old_widget['amenities'];
                            $title = $old_widget['title'];

                            $w = get_option('widget_lodgix_custom_search');
                            $w[$counter] = array(
                                'title' => $title,
                                'amenities' =>  $amenities
                            );
                            update_option('widget_lodgix_custom_search',$w);

                        } else {

                            $sidebars[$key][$widget_counter] = 'lodgix_featured-' . $counter;
                            $old_widget = get_option('old_widget_lodgix_featured');
                            $title = $old_widget['title'];

                            $w = get_option('widget_lodgix_featured');
                            $w[$counter] = array(
                                'title' => $title
                            );

                            update_option('widget_lodgix_featured',$w);

                        }

                        $counter++;
                    }
                    $widget_counter++;
                }
            }

        }

        update_option('sidebars_widgets',$sidebars);
        wp_redirect($_SERVER["REQUEST_URI"]);
    }

    private function upgradeDb22() {
        $this->config->set('p_lodgix_display_weekly_rates', true);
        $this->config->set('p_lodgix_display_monthly_rates', true);
        $this->config->save();
    }

    private function upgradeDb25() {
        if ($this->config->get('p_lodgix_custom_page_template') && $this->config->get('p_lodgix_custom_page_template') != '') {
            $this->config->set('p_lodgix_page_template', 'CUSTOM');
            $this->config->save();
        }
    }

    private function upgradeDb26() {
        $this->config->set('p_lodgix_gmap_zoom_level', 0);
        $this->config->save();
    }

    private function upgradeDb27() {
        $tableAmenities = $this->dbPrefix . LodgixConst::TABLE_AMENITIES;
        $this->db->query("ALTER TABLE $tableAmenities ADD UNIQUE `property_id`(`property_id`, `description`)");
    }

    private function upgradeDb28() {
        $tableReviews = $this->dbPrefix . LodgixConst::TABLE_REVIEWS;
        $this->db->query("ALTER TABLE $tableReviews ADD (stars INTEGER NOT NULL DEFAULT 5, title LONGTEXT NOT NULL DEFAULT '')");
    }

    private function upgradeDb29() {
        $tableProperties = $this->dbPrefix . LodgixConst::TABLE_PROPERTIES;
        $this->db->query("ALTER TABLE $tableProperties ADD (allow_same_day_booking TINYINT NOT NULL DEFAULT 1, limit_booking_days_advance SMALLINT NOT NULL DEFAULT 0)");
    }

    private function upgradeDb30() {
        $tableProperties = $this->dbPrefix . LodgixConst::TABLE_PROPERTIES;
        $this->db->query("ALTER TABLE $tableProperties ADD property_name TEXT");
    }

    private function upgradeDb31() {
        $tableProperties = $this->dbPrefix . LodgixConst::TABLE_PROPERTIES;
        $this->db->query("ALTER TABLE $tableProperties ADD property_title TEXT");
    }

    private function upgradeDb32() {
        $this->db->query("DROP TABLE  " . $this->dbPrefix . "lodgix_rates");
    }

    private function upgradeDb33() {
        $this->db->query("DROP TABLE  " . $this->dbPrefix . "lodgix_rules");
    }

    private function upgradeDb34() {
        $this->serviceDb->createTable(LodgixConst::TABLE_TAGS);
        $this->serviceDb->createTable(LodgixConst::TABLE_PROPERTY_TAGS);
    }

    private function upgradeDb35() {
        $tableProperties = $this->dbPrefix . LodgixConst::TABLE_PROPERTIES;
        $tablePictures = $this->dbPrefix . LodgixConst::TABLE_PICTURES;
        $this->db->query("ALTER TABLE $tableProperties ADD main_image_preview varchar(255) DEFAULT NULL");
        $this->db->query("ALTER TABLE $tablePictures ADD preview_url varchar(255) DEFAULT NULL");
    }

    private function upgradeDb36() {
        $tableProperties = $this->dbPrefix . LodgixConst::TABLE_PROPERTIES;
        $this->serviceDb->createTable(LodgixConst::TABLE_CATEGORIES);
        $this->serviceDb->createTable(LodgixConst::TABLE_PROPERTY_CATEGORIES);
        $this->db->query("ALTER TABLE $tableProperties DROP area");
    }

    private function upgradeDb37() {
        $tablePolicies = $this->dbPrefix . LodgixConst::TABLE_POLICIES;
        $tableProperties = $this->dbPrefix . LodgixConst::TABLE_PROPERTIES;
        $this->db->query("ALTER TABLE $tablePolicies ADD post_slug_vacation_rentals TEXT");
        $this->db->query("ALTER TABLE $tableProperties ADD post_slug TEXT");
    }

    private function upgradeDb38() {
        $tableLanguages = $this->dbPrefix . LodgixConst::TABLE_LANGUAGES;
        $tableCategories = $this->dbPrefix . LodgixConst::TABLE_CATEGORIES;
        $tableCategoryPosts = $this->dbPrefix . LodgixConst::TABLE_CATEGORY_POSTS;

        $this->db->query("ALTER TABLE $tableCategories ADD post_slug TEXT");
        $this->serviceDb->createTable(LodgixConst::TABLE_CATEGORY_POSTS);

        $categories = (new LodgixServiceCategories())->getAll();
        $languages = $this->db->get_results("SELECT * FROM $tableLanguages");
        foreach ($languages as $l) {
            $lc = $l->code;
            $key = "p_lodgix_areas_pages_$lc";
            if ($l->enabled && $categories) {
                $areasPages = unserialize($this->config->get($key));
                if (is_array($areasPages) && count($areasPages) > 0) {
                    foreach ($areasPages as $key => $page) {
                        if ($page->page_id) {
                            $post = get_post($page->page_id);
                            if ($post) {
                                $categoryId = (int)$page->category_id;
                                foreach ($categories as $category) {
                                    if ($categoryId == $category->category_id) {
                                        $this->db->query("
                                            INSERT INTO $tableCategoryPosts (category_id, language_code, post_id)
                                            VALUES($categoryId, '$lc', $post->ID)
                                        ");
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $this->config->deleteKey($key);
        }
        $this->config->save();

    }

}

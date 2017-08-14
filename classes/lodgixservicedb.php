<?php

class LodgixServiceDB {

    private static $FIELD_DEFINITIONS = array(
        LodgixConst::TABLE_PROPERTIES => "
            `id` int(10) unsigned NOT NULL,
            `owner_id` int(10) unsigned,
            `description` varchar(255) default NULL,
            `description_long` text,
            `details` text,
            `main_image` varchar(255) default NULL,
            `main_image_thumb` varchar(255) default NULL,
            `main_image_preview` varchar(255) DEFAULT NULL,
            `address` varchar(500) default NULL,
            `city` varchar(100) default NULL,
            `zip` varchar(10) default NULL,
            `state_code` varchar(2) default NULL,
            `country_code` varchar(2) default NULL,
            `bedrooms` smallint(6) default '0',
            `bathrooms` float default '0',
            `minrate` float default '0',
            `maxrate` float default '0',
            `min_daily_rate` float default '0',
            `min_weekly_rate` float default '0',
            `min_monthly_rate` float default '0',        
            `sleeps` smallint(6) default '0',
            `smoking` tinyint(1) default '0',
            `pets` tinyint(1) default '0',
            `children` tinyint(1) default '0',
            `rates` text,
            `proptype` varchar(255) default NULL,
            `latitude` double default '-1',
            `longitude` double default '-1',
            `contact` varchar(255) default NULL,
            `phone_local` varchar(50) default NULL,
            `phone_free_toll` varchar(50) default NULL,
            `email` varchar(255) default NULL,
            `serving_status` tinyint(1) default '1',
            `deleted` smallint(6) default '0',
            `date_created` timestamp NULL default NULL,
            `date_modified` timestamp NULL default NULL,
            `ts` timestamp NULL default NULL,
            `web_address` varchar(255) default NULL,
            `arrival_times` text,
            `state` varchar(64) default NULL,
            `currency_code` varchar(3) default NULL,
            `currency_symbol` varchar(1) default NULL,
            `display_calendar` tinyint(1) default '1',
            `allow_booking` tinyint(1) default '1',
            `allow_same_day_booking` TINYINT NOT NULL DEFAULT 1,
            `limit_booking_days_advance` SMALLINT NOT NULL DEFAULT 0,
            `check_in` varchar(10) default NULL,
            `check_out` varchar(10) default NULL,
            `post_id` bigint,
            `order` int(10) unsigned NULL,
            `video_url` text default NULL,
            `virtual_tour_url` text default NULL,
            `beds_text` text default NULL,
            `property_name` TEXT,
            `property_title` TEXT,
            `post_slug` TEXT,
            PRIMARY KEY (`id`)
            ",
        LodgixConst::TABLE_TAGS => "
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `tag` varchar(255) NOT NULL default '',
            PRIMARY KEY (`id`),
            UNIQUE KEY `tag` (`tag`)
            ",
        LodgixConst::TABLE_PROPERTY_TAGS => "
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `property_id` int(11) NOT NULL,
            `tag_id` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `property_id` (`property_id`,`tag_id`)
            ",
        LodgixConst::TABLE_CATEGORIES => "
            `id` int NOT NULL AUTO_INCREMENT,
            `category_id` int NOT NULL,
            `parent_category_id` int NOT NULL,
            `title` varchar(255) NOT NULL,
            `post_slug` TEXT,
            PRIMARY KEY (`id`),
            KEY (`category_id`),
            KEY (`parent_category_id`)
            ",
        LodgixConst::TABLE_PROPERTY_CATEGORIES => "
            `id` int NOT NULL AUTO_INCREMENT,
            `property_id` int NOT NULL,
            `category_id` int NOT NULL,
            PRIMARY KEY (`id`),
            KEY (`property_id`),
            KEY (`category_id`)
            ",
        LodgixConst::TABLE_CATEGORY_POSTS => "
            `id` int(11) NOT NULL auto_increment,
            `category_id` int NOT NULL,
            `language_code` char(2) NOT NULL,
            `post_id` bigint NOT NULL,
            PRIMARY KEY (`id`),
            KEY (`category_id`),
            KEY (`language_code`)
            ",
        LodgixConst::TABLE_LANG_PROPERTIES => "
            `id` int(10) unsigned NOT NULL,
            `description` varchar(255) default NULL,
            `description_long` text,
            `details` text,
            `language_code` varchar(2) NOT NULL,
            PRIMARY KEY (`id`,`language_code`)
            ",
        LodgixConst::TABLE_AMENITIES => "
            `id` int(10) NOT NULL auto_increment,
            `property_id` int(11) NOT NULL,
            `description` varchar(255) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `property_id` (`property_id`,`description`)
            ",
        LodgixConst::TABLE_LANG_AMENITIES => "
            `description` varchar(255) NOT NULL DEFAULT '',
            `description_translated` varchar(255) DEFAULT NULL,
            `language_code` varchar(2),
            `searchable` tinyint(1) NOT NULL default 0,
            PRIMARY KEY (`description`)
            ",
        LodgixConst::TABLE_PICTURES => "
            `id` int(11) NOT NULL auto_increment,
            `property_id` int(11) NOT NULL,
            `position` smallint(6) default NULL,
            `caption` varchar(255) default NULL,
            `url` varchar(255) default NULL,
            `thumb_url` varchar(255) default NULL,
            `preview_url` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
            ",
        LodgixConst::TABLE_MERGED_RATES => "
            `id` int(11) NOT NULL auto_increment,
            `property_id` int(11) NOT NULL,
            `from_date` date default NULL,
            `to_date` date default NULL,
            `nightly` decimal(10,2) default NULL,
            `weekend_nightly` decimal(10,2) default NULL,
            `weekly` decimal(10,2) default NULL,
            `monthly` decimal(10,2) default NULL,
            `min_stay` int(11) NOT NULL default '1',
            `name` varchar(128) default NULL,
            PRIMARY KEY (`id`)
            ",
        LodgixConst::TABLE_PAGES => "
            `id` int(11) NOT NULL auto_increment,
            `property_id` int NOT NULL,
            `page_id` bigint NOT NULL,
            `parent_page_id` bigint default NULL,
            `enabled` tinyint(1) NOT NULL default '0',
            `featured` tinyint(1) NOT NULL default '0',        
            PRIMARY KEY (`id`)
            ",
        LodgixConst::TABLE_LANG_PAGES => "
            `id` int(11) NOT NULL auto_increment,
            `property_id` int NOT NULL,
            `page_id` bigint NOT NULL,
            `source_page_id` bigint default NULL,
            `language_code` varchar(2) NOT NULL,
            PRIMARY KEY (`id`)
            ",
        LodgixConst::TABLE_POLICIES => "
            `id` int(11) NOT NULL auto_increment,
            `cancellation_policy` longtext NULL,
            `deposit_policy` longtext NULL,
            `single_unit_helptext` longtext NULL,        
            `multi_unit_helptext` longtext NULL, 
            `language_code` varchar(2) NOT NULL,
            `post_slug_vacation_rentals` TEXT,
            PRIMARY KEY (`id`)
            ",
        LodgixConst::TABLE_TAXES => "
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `property_id` int(11) NOT NULL,
            `type` varchar(32) NOT NULL,
            `title` varchar(255) NOT NULL,
            `value` double NOT NULL,
            `is_flat` tinyint(1) NOT NULL,
            `frequency` varchar(16) NOT NULL,
            PRIMARY KEY (`id`)
            ",
        LodgixConst::TABLE_FEES => "
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `property_id` int(11) NOT NULL,
            `title` varchar(255) NOT NULL,
            `value` double NOT NULL,
            `tax_exempt` tinyint(1) NOT NULL,
            `is_flat` tinyint(1) NOT NULL,
            `type` varchar(32) NOT NULL,
            PRIMARY KEY (`id`)
            ",
        LodgixConst::TABLE_DEPOSITS => "
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `property_id` int(11) NOT NULL,
            `title` varchar(255) NOT NULL,
            `value` double NOT NULL,
            PRIMARY KEY (`id`)
            ",
        LodgixConst::TABLE_REVIEWS => "
            `id` int(11) NOT NULL auto_increment,
            `property_id` int(11) NOT NULL,
            `date` datetime NOT NULL,
            `name` varchar(100) NOT NULL,
            `description` text NOT NULL,
            `language_code` varchar(2) NOT NULL,
            `stars` INTEGER NOT NULL DEFAULT 5,
            `title` LONGTEXT NOT NULL DEFAULT '',
            PRIMARY KEY (`id`)
            ",
        LodgixConst::TABLE_LINK_ROTATORS => "
            `id` int(11) NOT NULL auto_increment,
            `url` varchar(255) NOT NULL DEFAULT '',
            `title` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
            ",
        LodgixConst::TABLE_LANGUAGES => "
            `id` int(11) NOT NULL auto_increment,
            `code` varchar(7) NOT NULL,
            `name` varchar(128) NOT NULL,
            `enabled` BOOL NOT NULL DEFAULT 0,
            `locale` varchar(8) DEFAULT NULL,
            PRIMARY KEY (`id`)
            ",
        LodgixConst::TABLE_SEARCHABLE_AMENITIES => "
            `id` int(11) NOT NULL auto_increment,
            `description` varchar(255) NOT NULL DEFAULT '',
            PRIMARY KEY (`id`)
            "
    );

    private static $FIELD_VALUES = array(
        LodgixConst::TABLE_LANGUAGES => array(
            "'1', 'en', 'English', '1', 'en_US'",
            "'2', 'de', 'German', '1', 'de_DE'",
            "'3', 'fr', 'French', '0', 'fr_FR'",
            "'4', 'es', 'Spanish', '0', 'es_ES'",
            "'5', 'ar', 'Arabic', '0', 'ar'",
            "'6', 'bs', 'Bosnian', '0', ''",
            "'7', 'bg', 'Bulgarian', '0', 'bg_BG'",
            "'8', 'ca', 'Catalan', '0', 'ca'",
            "'9', 'cs', 'Czech', '0', 'cs_CZ'",
            "'10', 'sla', 'Slavic', '0', ''",
            "'11', 'cy', 'Welsh', '0', 'cy'",
            "'12', 'da', 'Danish', '0', 'da_DK'",
            "'13', 'el', 'Greek', '0', 'el'",
            "'14', 'eo', 'Esperanto', '0', 'eo'",
            "'15', 'et', 'Estonian', '0', 'et'",
            "'16', 'eu', 'Basque', '0', 'eu'",
            "'17', 'fa', 'Persian', '0', 'fa_IR'",
            "'18', 'fi', 'Finnish', '0', 'fi_FI'",
            "'19', 'ga', 'Irish', '0', ''",
            "'20', 'he', 'Hebrew', '0', 'he_IL'",
            "'21', 'hi', 'Hindi', '0', ''",
            "'22', 'hr', 'Croatian', '0', 'hr'",
            "'23', 'hu', 'Hungarian', '0', 'hu_HU'",
            "'24', 'hy', 'Armenian', '0', ''",
            "'25', 'id', 'Indonesian', '0', 'id_ID'",
            "'26', 'is', 'Icelandic', '0', 'is_IS'",
            "'27', 'it', 'Italian', '0', 'it_IT'",
            "'28', 'ja', 'Japanese', '0', 'ja'",
            "'29', 'ko', 'Korean', '0', 'ko_KR'",
            "'30', 'ku', 'Kurdish', '0', 'ku'",
            "'31', 'la', 'Latin', '0', ''",
            "'32', 'lv', 'Latvian', '0', 'lv'",
            "'33', 'lt', 'Lithuanian', '0', 'lt'",
            "'34', 'mk', 'Macedonian', '0', 'mk_MK'",
            "'35', 'mt', 'Maltese', '0', ''",
            "'36', 'mo', 'Moldavian', '0', ''",
            "'37', 'mn', 'Mongolian', '0', ''",
            "'38', 'ne', 'Nepali', '0', ''",
            "'39', 'nl', 'Dutch', '0', 'nl_NL'",
            "'40', 'nb', 'Norwegian Bokm�l', '0', 'nb_NO'",
            "'41', 'pa', 'Punjabi', '0', ''",
            "'42', 'pl', 'Polish', '0', 'pl_PL'",
            "'43', 'pt-pt', 'Portuguese, Portugal', '0', 'pt_PT'",
            "'44', 'pt-br', 'Portuguese, Brazil', '0', 'pt_BR'",
            "'45', 'qu', 'Quechua', '0', ''",
            "'46', 'ro', 'Romanian', '0', 'ro_RO'",
            "'47', 'ru', 'Russian', '0', 'ru_RU'",
            "'48', 'sl', 'Slovenian', '0', 'sl_SI'",
            "'49', 'so', 'Somali', '0', ''",
            "'50', 'sq', 'Albanian', '0', ''",
            "'51', 'sr', 'Serbian', '0', 'sr_RS'",
            "'52', 'sv', 'Swedish', '0', 'sv_SE'",
            "'53', 'ta', 'Tamil', '0', ''",
            "'54', 'th', 'Thai', '0', 'th'",
            "'55', 'tr', 'Turkish', '0', 'tr'",
            "'56', 'uk', 'Ukrainian', '0', 'uk_UA'",
            "'57', 'ur', 'Urdu', '0', ''",
            "'58', 'uz', 'Uzbek', '0', 'uz_UZ'",
            "'59', 'vi', 'Vietnamese', '0', 'vi'",
            "'60', 'yi', 'Yiddish', '0', ''",
            "'61', 'zh-hans', 'Chinese (Simplified)', '0', 'zh_CN'",
            "'62', 'zu', 'Zulu', '0', ''",
            "'63', 'zh-hant', 'Chinese (Traditional)', '0', 'zh_TW'",
            "'64', 'ms', 'Malay', '0', 'ms_MY'"
        )
    );

    function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->prefix = $wpdb->prefix;
    }

    function create() {
        $this->createTable(LodgixConst::TABLE_PROPERTIES);
        $this->createTable(LodgixConst::TABLE_TAGS);
        $this->createTable(LodgixConst::TABLE_PROPERTY_TAGS);
        $this->createTable(LodgixConst::TABLE_CATEGORIES);
        $this->createTable(LodgixConst::TABLE_PROPERTY_CATEGORIES);
        $this->createTable(LodgixConst::TABLE_LANG_PROPERTIES);
        $this->createTable(LodgixConst::TABLE_AMENITIES);
        $this->createTable(LodgixConst::TABLE_LANG_AMENITIES);
        $this->createTable(LodgixConst::TABLE_PICTURES);
        $this->createTable(LodgixConst::TABLE_MERGED_RATES);
        $this->createTable(LodgixConst::TABLE_PAGES);
        $this->createTable(LodgixConst::TABLE_LANG_PAGES);
        $this->createTable(LodgixConst::TABLE_POLICIES);
        $this->createTable(LodgixConst::TABLE_TAXES);
        $this->createTable(LodgixConst::TABLE_FEES);
        $this->createTable(LodgixConst::TABLE_DEPOSITS);
        $this->createTable(LodgixConst::TABLE_REVIEWS);
        $this->createTable(LodgixConst::TABLE_LINK_ROTATORS);
        $this->createTable(LodgixConst::TABLE_LANGUAGES);
        $this->createTable(LodgixConst::TABLE_SEARCHABLE_AMENITIES);
    }

    function createTable($table) {
        if (array_key_exists($table, self::$FIELD_DEFINITIONS)) {
            $fieldDefinitions = self::$FIELD_DEFINITIONS[$table];
            $tableName = $this->prefix . $table;
            if ($this->db->get_var("SHOW TABLES LIKE '$tableName'") != $tableName) {
                $this->db->query("CREATE TABLE $tableName ($fieldDefinitions) DEFAULT CHARSET=utf8");
                if (array_key_exists($table, self::$FIELD_VALUES)) {
                    $values = self::$FIELD_VALUES[$table];
                    foreach ($values as $value) {
                        $this->db->query("INSERT INTO $tableName VALUES ($value)");
                    }
                }
            }
        }
    }

    function drop() {
        $this->dropTable(LodgixConst::TABLE_PROPERTIES);
        $this->dropTable(LodgixConst::TABLE_TAGS);
        $this->dropTable(LodgixConst::TABLE_PROPERTY_TAGS);
        $this->dropTable(LodgixConst::TABLE_CATEGORIES);
        $this->dropTable(LodgixConst::TABLE_PROPERTY_CATEGORIES);
        $this->dropTable(LodgixConst::TABLE_LANG_PROPERTIES);
        $this->dropTable(LodgixConst::TABLE_AMENITIES);
        $this->dropTable(LodgixConst::TABLE_LANG_AMENITIES);
        $this->dropTable(LodgixConst::TABLE_PICTURES);
        $this->dropTable(LodgixConst::TABLE_MERGED_RATES);
        $this->dropTable(LodgixConst::TABLE_PAGES);
        $this->dropTable(LodgixConst::TABLE_LANG_PAGES);
        $this->dropTable(LodgixConst::TABLE_POLICIES);
        $this->dropTable(LodgixConst::TABLE_TAXES);
        $this->dropTable(LodgixConst::TABLE_FEES);
        $this->dropTable(LodgixConst::TABLE_DEPOSITS);
        $this->dropTable(LodgixConst::TABLE_REVIEWS);
        $this->dropTable(LodgixConst::TABLE_LINK_ROTATORS);
        $this->dropTable(LodgixConst::TABLE_LANGUAGES);
        $this->dropTable(LodgixConst::TABLE_SEARCHABLE_AMENITIES);
    }

    function dropTable($table) {
        if (array_key_exists($table, self::$FIELD_DEFINITIONS)) {
            $tableName = $this->prefix . $table;
            $this->db->query("DROP TABLE IF EXISTS $tableName");
        }
    }

    function clean() {
        $this->cleanTable(LodgixConst::TABLE_PROPERTIES);
        $this->cleanTable(LodgixConst::TABLE_TAGS);
        $this->cleanTable(LodgixConst::TABLE_PROPERTY_TAGS);
        $this->cleanTable(LodgixConst::TABLE_CATEGORIES);
        $this->cleanTable(LodgixConst::TABLE_PROPERTY_CATEGORIES);
        $this->cleanTable(LodgixConst::TABLE_LANG_PROPERTIES);
        $this->cleanTable(LodgixConst::TABLE_AMENITIES);
        $this->cleanTable(LodgixConst::TABLE_LANG_AMENITIES);
        $this->cleanTable(LodgixConst::TABLE_PICTURES);
        $this->cleanTable(LodgixConst::TABLE_MERGED_RATES);
        $this->cleanTable(LodgixConst::TABLE_PAGES);
        $this->cleanTable(LodgixConst::TABLE_LANG_PAGES);
        $this->cleanTable(LodgixConst::TABLE_POLICIES);
        $this->cleanTable(LodgixConst::TABLE_TAXES);
        $this->cleanTable(LodgixConst::TABLE_FEES);
        $this->cleanTable(LodgixConst::TABLE_DEPOSITS);
        $this->cleanTable(LodgixConst::TABLE_REVIEWS);
    }

    protected function cleanTable($table) {
        if (array_key_exists($table, self::$FIELD_DEFINITIONS)) {
            $tableName = $this->prefix . $table;
            $this->db->query("DELETE FROM $tableName");
        }
    }

}

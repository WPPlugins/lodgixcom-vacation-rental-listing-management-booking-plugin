<?php

class LodgixConst {

    const BASE_URL = 'https://www.lodgix.com';

    const DB_VERSION = '3.8';

    const OPTION_CONFIG = 'p_lodgix_options';
    const OPTION_DB_VERSION = 'p_lodgix_db_version';

    const TABLE_PROPERTIES = 'lodgix_properties';
    const TABLE_TAGS = 'lodgix_tags';
    const TABLE_PROPERTY_TAGS = 'lodgix_property_tags';
    const TABLE_CATEGORIES = 'lodgix_categories';
    const TABLE_PROPERTY_CATEGORIES = 'lodgix_property_categories';
    const TABLE_CATEGORY_POSTS = 'lodgix_category_posts';
    const TABLE_AMENITIES = 'lodgix_amenities';
    const TABLE_MERGED_RATES = 'lodgix_merged_rates';
    const TABLE_PICTURES = 'lodgix_pictures';
    const TABLE_PAGES = 'lodgix_pages';
    const TABLE_LANG_PAGES = 'lodgix_lang_pages';
    const TABLE_LANG_PROPERTIES = 'lodgix_lang_properties';
    const TABLE_LANG_AMENITIES = 'lodgix_lang_amenities';
    const TABLE_WPML_LANGUAGES = 'icl_languages';
    const TABLE_TAXES = 'lodgix_taxes';
    const TABLE_FEES = 'lodgix_fees';
    const TABLE_DEPOSITS = 'lodgix_deposits';
    const TABLE_REVIEWS = 'lodgix_reviews';
    const TABLE_LANGUAGES = 'lodgix_languages';
    const TABLE_LINK_ROTATORS = 'lodgix_link_rotators';
    const TABLE_POLICIES = 'lodgix_policies';
    const TABLE_SEARCHABLE_AMENITIES = 'lodgix_searchable_amenities';

    const IMAGE_640x480 = '640x480';
    const IMAGE_800x600 = '800x600';
    const IMAGE_ORIGINAL = 'original';

    public static $IMAGE_WIDTH = array(
        self::IMAGE_640x480 => 640,
        self::IMAGE_800x600 => 800,
        self::IMAGE_ORIGINAL => 0
    );
    public static $IMAGE_HEIGHT = array(
        self::IMAGE_640x480 => 480,
        self::IMAGE_800x600 => 600,
        self::IMAGE_ORIGINAL => 0
    );

    const ICON_SET_OLD = 'Old';
    const ICON_SET_CIRCLE = 'Circle';
    const ICON_SET_FILLED = 'Filled';
    const ICON_SET_GRADIENT_COLOR = 'Gradient Color';
    const ICON_SET_GRADIENT_GRAY = 'Gradient Gray';
    const ICON_SET_OUTLINED = 'Outlined';
    const ICON_SET_SQUARED_COLOR = 'Squared Color';
    const ICON_SET_SQUARED_GRAY = 'Squared Gray';

    public static $ICON_SET_CLASS = array(
        self::ICON_SET_OLD => 'Old',
        self::ICON_SET_CIRCLE => 'Circle',
        self::ICON_SET_FILLED => 'Filled',
        self::ICON_SET_GRADIENT_COLOR => 'GradientColor',
        self::ICON_SET_GRADIENT_GRAY => 'GradientGray',
        self::ICON_SET_OUTLINED => 'Outlined',
        self::ICON_SET_SQUARED_COLOR => 'SquaredColor',
        self::ICON_SET_SQUARED_GRAY => 'SquaredGray'
    );

    // Singleton
    protected function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

}

<?php

class LodgixTranslate {

    const LOCALIZATION_DOMAIN = 'p_lodgix';

    public static function translate($str) {
        return __($str, self::LOCALIZATION_DOMAIN);
    }

    public static function translateAmenity($str, $language) {
        global $wpdb;
        $table = $wpdb->prefix . LodgixConst::TABLE_LANG_AMENITIES;
        $safeSql = $wpdb->prepare(
            "SELECT description_translated FROM $table WHERE description=%s AND language_code=%s",
            $str,
            $language
        );
        $name = $wpdb->get_var($safeSql);
        if ($name) {
            return $name;
        }
        return $str;
    }

    // Singleton
    protected function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

}

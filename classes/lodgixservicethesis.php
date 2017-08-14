<?php

class LodgixServiceThesis {

    function __construct($postId, $property) {
        $this->postId = $postId;
        $this->property = $property;
    }

    protected static function lastIndexOf($needle, $str) {
        $r = strpos(strrev($str), strrev($needle));
        if ($r !== false) {
            return strlen($str) - $r - strlen($needle);
        }
        return false;
    }

    protected static function truncate($text, $limit) {
        if (strlen($text) > $limit) {
            $text = substr($text, 0,$limit - 1);
            $text = substr($text, 0, self::lastIndexOf(' ', $text));
            $text .= '.';
        }
        return $text;
    }

    protected function setDescription($description) {
        $description = trim(wptexturize(self::truncate($description, 150)));
        add_post_meta($this->postId, 'thesis_description', $description, true);
        update_post_meta($this->postId, 'thesis_description', $description);
    }

    protected function setKeywords($keywords) {
        $keywords = trim(wptexturize($keywords));
        add_post_meta($this->postId, 'thesis_keywords', $keywords, true);
        update_post_meta($this->postId, 'thesis_keywords', $keywords);
    }

    public function setPropertyDescriptionAndKeywords() {
        $this->setDescription($this->property->description_long);
        $keywords =
            $this->property->description .
            ', vacation rental, vacation home, vacation, homes, rentals, cottages, condos, holiday';
        if ($this->property->city != '') {
            $keywords .= ', ' . $this->property->city;
        }
        $this->setKeywords($keywords);
    }

}

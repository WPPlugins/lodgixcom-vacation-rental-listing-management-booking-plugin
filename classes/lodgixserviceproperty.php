<?php

class LodgixServiceProperty {

    function __construct($property, $language='en') {
        global $wpdb;
        $this->db = $wpdb;
        $this->dbPrefix = $this->db->prefix;
        $this->property = $property;
        $this->language = $language;
    }

    function ratesDaily() {
        $table = $this->dbPrefix . LodgixConst::TABLE_MERGED_RATES;
        $propertyId = $this->property->id;
        $low = (int)$this->db->get_var($this->db->prepare("SELECT IFNULL(MIN(nightly),0) FROM $table WHERE property_id=$propertyId", null));
        $lowWeekend = (int)$this->db->get_var($this->db->prepare("SELECT IFNULL(MIN(weekend_nightly),0) FROM $table WHERE property_id=$propertyId", null));
        if ($lowWeekend < $low && $lowWeekend > 0) {
            $low = $lowWeekend;
        }
        $high = (int)$this->db->get_var($this->db->prepare("SELECT IFNULL(MAX(nightly),0) FROM $table WHERE property_id=$propertyId", null));
        $highWeekend = (int)$this->db->get_var($this->db->prepare("SELECT IFNULL(MAX(weekend_nightly),0) FROM $table WHERE property_id=$propertyId", null));
        if ($highWeekend > $high) {
            $high = $highWeekend;
        }
        if ($low > 0) {
            return array(
                $this->property->currency_symbol . $low,
                $this->property->currency_symbol . $high
            );
        }
        return null;
    }

    function ratesWeekly() {
        $table = $this->dbPrefix . LodgixConst::TABLE_MERGED_RATES;
        $propertyId = $this->property->id;
        $low = (int)$this->db->get_var($this->db->prepare("SELECT IFNULL(MIN(weekly),0) FROM $table WHERE property_id=$propertyId", null));
        $high = (int)$this->db->get_var($this->db->prepare("SELECT IFNULL(MAX(weekly),0) FROM $table WHERE property_id=$propertyId", null));
        if ($low > 0) {
            return array(
                $this->property->currency_symbol . $low,
                $this->property->currency_symbol . $high
            );
        }
        return null;
    }

    function ratesMonthly() {
        $table = $this->dbPrefix . LodgixConst::TABLE_MERGED_RATES;
        $propertyId = $this->property->id;
        $low = (int)$this->db->get_var($this->db->prepare("SELECT IFNULL(MIN(monthly),0) FROM $table WHERE property_id=$propertyId", null));
        $high = (int)$this->db->get_var($this->db->prepare("SELECT IFNULL(MAX(monthly),0) FROM $table WHERE property_id=$propertyId", null));
        if ($low > 0) {
            return array(
                $this->property->currency_symbol . $low,
                $this->property->currency_symbol . $high
            );
        }
        return null;
    }

    function ratesMerged() {
        $table = $this->dbPrefix . LodgixConst::TABLE_MERGED_RATES;
        $propertyId = $this->property->id;
        return $this->db->get_results("SELECT * FROM $table WHERE property_id=$propertyId ORDER BY from_date,to_date");
    }

    function taxes() {
        $table = $this->dbPrefix . LodgixConst::TABLE_TAXES;
        $propertyId = $this->property->id;
        return $this->db->get_results("SELECT * FROM $table WHERE property_id=$propertyId");
    }

    function fees() {
        $table = $this->dbPrefix . LodgixConst::TABLE_FEES;
        $propertyId = $this->property->id;
        return $this->db->get_results("SELECT * FROM $table WHERE property_id=$propertyId");
    }

    function deposits() {
        $table = $this->dbPrefix . LodgixConst::TABLE_DEPOSITS;
        $propertyId = $this->property->id;
        return $this->db->get_results("SELECT * FROM $table WHERE property_id=$propertyId");
    }

    function policies() {
        $table = $this->dbPrefix . LodgixConst::TABLE_POLICIES;
        return $this->db->get_results("SELECT * FROM $table WHERE language_code='$this->language'");
    }

    function amenityNames() {
        $tableAmenities = $this->dbPrefix . LodgixConst::TABLE_AMENITIES;
        $propertyId = $this->property->id;
        $amenities = $this->db->get_results("SELECT * FROM $tableAmenities WHERE property_id=$propertyId ORDER BY id");
        $amenityNames = array();
        if (count($amenities) >= 1) {
            foreach($amenities as $amenity) {
                array_push($amenityNames, trim($amenity->description));
            }
        }
        return $amenityNames;
    }

    function reviews() {
        $table = $this->dbPrefix . LodgixConst::TABLE_REVIEWS;
        $propertyId = $this->property->id;
        return $this->db->get_results("SELECT * FROM $table WHERE language_code='$this->language' AND property_id=$propertyId ORDER BY date DESC");
    }

    function photos() {
        $table = $this->dbPrefix . LodgixConst::TABLE_PICTURES;
        $propertyId = $this->property->id;
        return $this->db->get_results("SELECT * FROM $table WHERE property_id=$propertyId ORDER BY position");
    }

    function categories($glue=' - ') {
        $tableCategories = $this->dbPrefix . LodgixConst::TABLE_CATEGORIES;
        $tablePropertyCategories = $this->dbPrefix . LodgixConst::TABLE_PROPERTY_CATEGORIES;
        $propertyId = $this->property->id;
        $glue = $this->db->_real_escape($glue);
        return $this->db->get_results("
            SELECT
                c1.category_id AS category_id,
                c1.title AS category_title,
                IF(c2.title IS NULL, c1.title, CONCAT(c2.title, '$glue', c1.title)) AS category_title_long
            FROM $tablePropertyCategories pc
            LEFT JOIN $tableCategories c1 ON c1.category_id=pc.category_id
            LEFT JOIN $tableCategories c2 ON c1.parent_category_id=c2.category_id
            WHERE pc.property_id=$propertyId
            ORDER BY pc.id
        ");
    }

    function mainCategory($glue=' - ') {
        $categories = $this->categories($glue);
        if (is_array($categories)) {
            return $categories[0];
        }
        return null;
    }

    function bookLink($ownerId, $bookDates) {
        if ($ownerId == 2) {
            $ownerId = 'rosewoodpointe';
        } else if ($ownerId == 13) {
            $ownerId = 'demo_booking_calendar';
        } else {
            // Remove dash and everything after it
            $matches = Array();
            preg_match('/([0-9])+/i', $ownerId, $matches);
            $ownerId = $matches[0];
        }
        $id = $this->property->id;
        return "https://www.lodgix.com/$ownerId/?selected_reservations=$id,$bookDates&adult=1&children=0&external=1";
    }

}

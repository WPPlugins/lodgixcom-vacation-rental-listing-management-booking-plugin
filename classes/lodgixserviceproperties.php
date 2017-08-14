<?php

class LodgixServiceProperties {

    function __construct ($config) {
        global $wpdb;
        $this->db = $wpdb;
        $this->config = $config;
        $this->api = null;
    }

    function getAll () {
        $tp = $this->db->prefix . LodgixConst::TABLE_PROPERTIES;
        return $this->db->get_results("SELECT * FROM $tp");
    }

    function getRandom () {
        $tp = $this->db->prefix . LodgixConst::TABLE_PROPERTIES;
        $tc = $this->db->prefix . LodgixConst::TABLE_CATEGORIES;
        $tpc = $this->db->prefix . LodgixConst::TABLE_PROPERTY_CATEGORIES;
        $rows = $this->db->get_results("
            SELECT
                p.*,
                $tc.category_id AS category_id,
                $tc.title AS category_title,
                IF(c2.title IS NULL, $tc.title, CONCAT(c2.title, ' - ', $tc.title)) AS category_title_long
            FROM $tp p
            LEFT JOIN $tpc pc ON p.id=pc.property_id
            LEFT JOIN $tc ON $tc.category_id=pc.category_id
            LEFT JOIN $tc c2 ON $tc.parent_category_id=c2.category_id
            LIMIT 1
        ");
        if ($rows) {
            return $rows[0];
        }
        return null;
    }

    function getPropertyIdsWithAmenities ($amenities) {
        if ($amenities && is_array($amenities)) {
            $len = count($amenities);
            for ($i = 0; $i < $len; $i++) {
                $amenities[$i] = $this->db->_real_escape($amenities[$i]);
            }
            $aa = join("','", $amenities);
            $ta = $this->db->prefix . LodgixConst::TABLE_AMENITIES;
            $properties = $this->db->get_results("
                SELECT
                    property_id,
                    COUNT(property_id) AS amenities
                FROM $ta
                WHERE description IN ('$aa')
                GROUP BY property_id HAVING amenities=$len
            ");
            $propertyIds = array();
            foreach ($properties as $property) {
                array_push($propertyIds, $property->property_id);
            }
            return $propertyIds;
        }
        return null;
    }

    function countAvailableProperties (
        $arrival='',
        $nights='1',
        $categoryId='ALL_AREAS',
        $bedrooms='0',
        $priceFrom=0,
        $priceTo=0,
        $isPetFriendly=false,
        $propertyNameOrId='',
        $amenities=null,
        $tags=null
    ) {
        $tp = $this->db->prefix . LodgixConst::TABLE_PROPERTIES;
        $tc = $this->db->prefix . LodgixConst::TABLE_CATEGORIES;
        $tpc = $this->db->prefix . LodgixConst::TABLE_PROPERTY_CATEGORIES;

        $arr = $this->filter($propertyNameOrId, $tags, $categoryId, $bedrooms, $priceFrom, $priceTo, $arrival, $nights, $isPetFriendly, $amenities);
        $join = $arr['join'];
        $filter = $arr['filter'];
        $group = $arr['group'];

        $sql = "
            SELECT $tp.id
            FROM $tp
            LEFT JOIN $tpc ON $tp.id=$tpc.property_id
            LEFT JOIN $tc ON $tc.category_id=$tpc.category_id
            $join
            WHERE $filter 1=1 $group
        ";
        $results = $this->db->get_results($sql);
        return count($results);
    }

    function getAvailableProperties (
        $arrival='',
        $nights='1',
        $categoryId='ALL_AREAS',
        $bedrooms='0',
        $priceFrom=0,
        $priceTo=0,
        $isPetFriendly=false,
        $propertyNameOrId='',
        $amenities=null,
        $tags=null,
        $sortBy=''
    )
    {
        $tp = $this->db->prefix . LodgixConst::TABLE_PROPERTIES;
        $tc = $this->db->prefix . LodgixConst::TABLE_CATEGORIES;
        $tpc = $this->db->prefix . LodgixConst::TABLE_PROPERTY_CATEGORIES;

        $arr = $this->filter($propertyNameOrId, $tags, $categoryId, $bedrooms, $priceFrom, $priceTo, $arrival, $nights, $isPetFriendly, $amenities);
        $join = $arr['join'];
        $filter = $arr['filter'];
        $group = $arr['group'];

        $orderBy = '';
        $direction = '';
        if ($sortBy) {
            $orderBy = "$tp." . $this->db->_real_escape($sortBy);
            if ($sortBy == 'pets') {
                $direction = 'DESC';
            }
        }
        if (!$orderBy && $this->config->get('p_lodgix_vacation_rentals_page_design') == 1) {
            $orderBy = "IF($tpc.category_id IS NULL, 1, 0), $tpc.category_id, $tp.description";
        }
        if ($bedrooms != null && $bedrooms != 'ANY') {
            if ($orderBy) {
                $orderBy .= ", $tp.bedrooms";
            } else {
                $orderBy = "$tp.bedrooms";
            }
        }
        if (!$orderBy) {
            $orderBy = "$tp.order";
        }

        $sql = "
            SELECT
                $tp.*,
                $tc.category_id AS category_id,
                $tc.title AS category_title,
                IF(c2.title IS NULL, $tc.title, CONCAT(c2.title, ' - ', $tc.title)) AS category_title_long
            FROM $tp
            LEFT JOIN $tpc ON $tpc.property_id=$tp.id
            LEFT JOIN $tc ON $tc.category_id=$tpc.category_id
            LEFT JOIN $tc c2 ON $tc.parent_category_id=c2.category_id
            $join
            WHERE $filter 1=1 $group
            ORDER BY $orderBy $direction
        ";
        $results = $this->db->get_results($sql);
        return $results;
    }

    private function filter (
        $propertyNameOrId,
        $tags,
        $categoryId,
        $bedrooms,
        $priceFrom,
        $priceTo,
        $arrival,
        $nights,
        $isPetFriendly,
        $amenities
    ) {
        $filter = $this->filterByPropertyId($propertyNameOrId);

        if ($filter == '') {
            $filter = $this->filterByOther($categoryId, $propertyNameOrId, $bedrooms, $priceFrom, $priceTo);
        }

        $filter .= $this->filterByAvailable($arrival, $nights);

        if ($isPetFriendly) {
            $tp = $this->db->prefix . LodgixConst::TABLE_PROPERTIES;
            $filter .= "$tp.pets=1 AND ";
        }

        $filter .= $this->filterByAmenities($amenities);

        $arr = $this->filterByTags($tags);
        $join = $arr['join'];
        $filter .= $arr['filter'];
        $group = $arr['group'];

        return array('join' => $join, 'filter' => $filter, 'group' => $group);
    }

    private function filterByPropertyId ($propertyId) {
        if ($propertyId && preg_match('/^[\d\s,]+$/', $propertyId)) {
            $tp = $this->db->prefix . LodgixConst::TABLE_PROPERTIES;
            $orIds = array();
            $ids = preg_split('/(,|\s)/', $propertyId);
            foreach ($ids as $pid) {
                if ($pid) {
                    $escPid = $this->db->_real_escape($pid);
                    array_push($orIds, "$tp.id='$escPid'");
                }
            }
            if (count($orIds) > 0) {
                // Also search in description in case it was not an ID
                $impIds = implode(' OR ', $orIds);
                $test = $this->db->get_results("SELECT COUNT(*) AS num_results FROM $tp WHERE $impIds");
                if ($test[0]->num_results > 0) {
                    $escId = $this->db->_real_escape(strtolower($propertyId));
                    return "($impIds OR LOWER($tp.description) LIKE '%$escId%') AND ";
                }
            }
        }
        return '';
    }

    private function filterByTags ($tags) {
        if ($tags && is_array($tags)) {
            $tp = $this->db->prefix . LodgixConst::TABLE_PROPERTIES;
            $tt = $this->db->prefix . LodgixConst::TABLE_TAGS;
            $tpt = $this->db->prefix . LodgixConst::TABLE_PROPERTY_TAGS;
            $orTags = array();
            foreach ($tags as $tag) {
                if ($tag) {
                    $escTag = str_replace('\\\\', '', $this->db->_real_escape(strtolower($tag)));
                    array_push($orTags, "$tt.tag='$escTag'");
                }
            }
            $nTags = count($orTags);
            if ($nTags > 0) {
                return array(
                    'join' => "LEFT JOIN $tpt ON $tp.id=$tpt.property_id LEFT JOIN $tt ON $tpt.tag_id=$tt.id",
                    'filter' => "(" . implode(" OR ", $orTags) . ") AND ",
                    'group' => "GROUP BY $tp.id HAVING COUNT($tp.id) >= $nTags"
                );
            }
        }
        return array('join' => '', 'filter' => '', 'group' => '');
    }

    private function filterByOther ($categoryId, $propertyName, $bedrooms, $priceFrom, $priceTo) {
        $tp = $this->db->prefix . LodgixConst::TABLE_PROPERTIES;
        $tc = $this->db->prefix . LodgixConst::TABLE_CATEGORIES;
        $filter = '';
        if ($categoryId != '' && $categoryId != 'ALL_AREAS') {
            $escCategoryId = $this->db->_real_escape($categoryId);
            $filter .= "($tc.category_id='$escCategoryId' OR $tc.parent_category_id='$escCategoryId') AND ";
        }
        if ($propertyName != '') {
            $escCategoryId = $this->db->_real_escape($propertyName);
            $filter .= "LOWER($tp.description) LIKE '%$escCategoryId%' AND ";
        }
        if ($bedrooms != null && $bedrooms != 'ANY') {
            $escBedrooms = $this->db->_real_escape($bedrooms);
            $filter .= "$tp.bedrooms >= '$escBedrooms' AND ";
        }
        $priceFrom = (int)$priceFrom;
        if ($priceFrom > 0) {
            $filter .= "$tp.min_daily_rate >= $priceFrom AND ";
        }
        $priceTo = (int)$priceTo;
        if ($priceTo > 0 && $priceTo >= $priceFrom) {
            $filter .= "$tp.min_daily_rate <= $priceTo AND ";
        }
        return $filter;
    }

    private function filterByAvailable ($arrival, $nights) {
        $api = $this->getApi();
        $available = $api->getAvailableProperties($arrival, $nights);
        if (!$available || $available == 'null') {
            return '1=0 AND ';
        } else if ($available != 'ALL') {
            $tp = $this->db->prefix . LodgixConst::TABLE_PROPERTIES;
            $escAvailable = $this->db->_real_escape($available);
            return "$tp.id IN ($escAvailable) AND ";
        }
        return '';
    }

    private function filterByAmenities ($amenities) {
        $propertyIds = $this->getPropertyIdsWithAmenities($amenities);
        if (is_array($propertyIds)) {
            if (count($propertyIds) > 0) {
                $tp = $this->db->prefix . LodgixConst::TABLE_PROPERTIES;
                $ids = join(',', $propertyIds);
                return "$tp.id IN ($ids) AND ";
            } else {
                return '1=0 AND ';
            }
        }
        return '';
    }

    private function getApi () {
        if (!$this->api) {
            $this->api = new LodgixApi($this->config->get('p_lodgix_owner_id'), $this->config->get('p_lodgix_api_key'));
        }
        return $this->api;
    }

    function getAvailablePropertyIdsAfterRules ($arrival=null, $nights=null) {
        $api = $this->getApi();
        $available = $api->getAvailablePropertiesAfterRules($arrival, $nights);
        if ($available !== 'ALL') {
            return explode(',', $available);
        }
        return null;
    }

}

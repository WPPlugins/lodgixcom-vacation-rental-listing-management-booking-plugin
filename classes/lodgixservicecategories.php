<?php

class LodgixServiceCategories {

    function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $wpdb->prefix . LodgixConst::TABLE_CATEGORIES;
    }

    function getAll() {
        return $this->db->get_results("
            SELECT
                c1.category_id AS category_id,
                c1.title AS category_title,
                IF(c2.title IS NULL, c1.title, CONCAT(c2.title, ' - ', c1.title)) AS category_title_long
            FROM $this->table c1
            LEFT JOIN $this->table c2 ON c1.parent_category_id=c2.category_id
            ORDER BY c1.id
        ");
    }

    function getAllParentFirst() {
        return $this->db->get_results("
            SELECT
                c1.category_id AS category_id,
                c1.title AS category_title,
                IF(c2.title IS NULL, c1.title, CONCAT(c2.title, ' - ', c1.title)) AS category_title_long,
                c2.category_id AS parent_category_id,
                c1.post_slug AS post_slug
            FROM $this->table c1
            LEFT JOIN $this->table c2 ON c1.parent_category_id=c2.category_id
            ORDER BY c2.id, c1.id
        ");
    }

}

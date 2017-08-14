<?php

class LodgixServicePost {

    private $activeLanguages;
    private $inactiveLanguages;

    function __construct($config) {
        global $wpdb;
        $this->db = $wpdb;
        $this->dbPrefix = $this->db->prefix;
        $this->config = $config;
        $this->postCommentStatus = $config->get('p_lodgix_allow_comments') ? 'open' : 'closed';
        $this->postPingStatus = $config->get('p_lodgix_allow_pingback') ? 'open' : 'closed';
    }

    protected function getActiveLanguages() {
        if (!isset($this->activeLanguages)) {
            $tl = $this->dbPrefix . LodgixConst::TABLE_LANGUAGES;
            $this->activeLanguages = $this->db->get_results("SELECT * FROM $tl WHERE enabled=1");
        }
        return $this->activeLanguages;
    }

    protected function getInactiveLanguages() {
        if (!isset($this->inactiveLanguages)) {
            $tl = $this->dbPrefix . LodgixConst::TABLE_LANGUAGES;
            $this->inactiveLanguages = $this->db->get_results("SELECT * FROM $tl WHERE enabled=0");
        }
        return $this->inactiveLanguages;
    }

    protected function createPost($name=null, $title=null, $parentId=null, $content=null, $status=null) {
        $post = array();
        if (!is_null($name)) {
            $post['post_name'] = $name;
        }
        if (!is_null($title)) {
            $post['post_title'] = $title;
        }
        if (!is_null($parentId)) {
            $post['post_parent'] = $parentId;
        }
        if (!is_null($content)) {
            $post['post_content'] = $content;
        }
        if (!is_null($status)) {
            $post['post_status'] = $status;
        }
        $post['post_author'] = 1;
        $post['post_type'] = 'page';
        $post['comment_status'] = $this->postCommentStatus;
        $post['ping_status'] = $this->postPingStatus;
        $postId = wp_insert_post($post);
        return $postId;
    }

    protected function updatePost($postId, $name=null, $title=null, $parentId=null, $content=null, $status=null) {
        if ($postId && get_post($postId)) {
            $post = array();
            $post['ID'] = $postId;
            if (!is_null($name)) {
                $post['post_name'] = $name;
            }
            if (!is_null($title)) {
                $post['post_title'] = $title;
            }
            if (!is_null($parentId)) {
                $post['post_parent'] = $parentId;
            }
            if (!is_null($content)) {
                $post['post_content'] = $content;
            }
            if (!is_null($status)) {
                $post['post_status'] = $status;
            }
            $post['comment_status'] = $this->postCommentStatus;
            $post['ping_status'] = $this->postPingStatus;
            $postId = wp_update_post($post);
            return $postId;
        }
        return 0;
    }

    protected function linkTranslation($basePostId, $translationPostId, $languageCode) {
        global $sitepress;
        $translationId = $sitepress->get_element_trid($basePostId, 'post_page');
        $sitepress->set_element_language_details($translationPostId, 'post_page', $translationId, $languageCode);
    }

    protected function createOrUpdatePropertyPage($property) {
        $tpr = $this->dbPrefix . LodgixConst::TABLE_PROPERTIES;
        $tlpr = $this->dbPrefix . LodgixConst::TABLE_LANG_PROPERTIES;
        $tp = $this->dbPrefix . LodgixConst::TABLE_PAGES;
        $tlp = $this->dbPrefix . LodgixConst::TABLE_LANG_PAGES;

        $propertyId = $property->id;
        $title = $property->description;
        $name = $property->post_slug ? $property->post_slug : sanitize_title_with_dashes($property->property_name, '', 'sav‌​e');
        $content = "[lodgix_single_property $property->id]";

        $basePostId = $property->post_id;
        if ($basePostId && get_post($basePostId)) {
            $basePostId = $this->updatePost($basePostId, $name, $title, null, $content, 'publish');
            if (!$basePostId) {
                return;
            }
        } else {
            $parentPostId = (int)$this->config->get('p_lodgix_vacation_rentals_page_en');
            if (!$parentPostId) {
                return;
            }
            $basePostId = $this->createPost($name, $title, $parentPostId, $content, 'publish');
            if (!$basePostId) {
                return;
            }
            $this->db->query("UPDATE $tpr SET post_id=$basePostId WHERE id=$propertyId");
            $this->db->query("INSERT INTO $tp (page_id,property_id,parent_page_id) VALUES($basePostId,$propertyId,$parentPostId)");
        }

        (new LodgixServiceThesis($basePostId, $property))->setPropertyDescriptionAndKeywords();

        $languages = $this->getActiveLanguages();
        foreach ($languages as $l) {
            $lc = $l->code;
            if ($lc == 'en') {
                continue;
            }
            $translatedTitle = $this->db->get_var("SELECT description FROM $tlpr WHERE id=$propertyId AND language_code='$lc'");
            if (!$translatedTitle) {
                $translatedTitle = $title;
            }
            $translationPostId = $this->db->get_var("SELECT page_id FROM $tlp WHERE property_id=$propertyId AND language_code='$lc'");
            if ($translationPostId && get_post($translationPostId)) {
                $this->updatePost($translationPostId, null, $translatedTitle, null, $content, 'publish');
                if (!$translationPostId) {
                    continue;
                }
            } else {
                $parentPostId = (int)$this->config->get("p_lodgix_vacation_rentals_page_$lc");
                if (!$parentPostId) {
                    continue;
                }
                $translationPostId = $this->createPost(null, $translatedTitle, $parentPostId, $content, 'publish');
                if (!$translationPostId) {
                    continue;
                }
                $this->db->query("INSERT INTO $tlp (page_id,property_id,source_page_id,language_code) VALUES($translationPostId,$propertyId,$translationPostId,'$lc')");
            }

            // TODO: what is this?
            update_post_meta($translationPostId, '_icl_translation', 1);

            $this->linkTranslation($basePostId, $translationPostId, $lc);
        }
    }

    protected function createOrUpdatePropertyPages() {
        $tpr = $this->dbPrefix . LodgixConst::TABLE_PROPERTIES;
        $properties = $this->db->get_results("SELECT * FROM $tpr ORDER BY `order`");
        foreach ($properties as $property) {
            $this->createOrUpdatePropertyPage($property);
        }
    }

    protected function createOrUpdateCategoryPage($category) {
        $tcp = $this->dbPrefix . LodgixConst::TABLE_CATEGORY_POSTS;

        $categoryId = $category->category_id;
        $title = $category->category_title_long;
        $name = $category->post_slug ? $category->post_slug : sanitize_title_with_dashes($title, '', 'sav‌​e');
        $parentCategoryId = $category->parent_category_id;
        $content = "[lodgix_category $categoryId]";

        if ($parentCategoryId) {
            $parentPostId = $this->db->get_var("SELECT post_id FROM $tcp WHERE category_id=$parentCategoryId AND language_code='en'");
            if (!($parentPostId && get_post($parentPostId))) {
                $parentPostId = 0;
            }
        } else {
            $parentPostId = 0;
        }

        $basePostId = $this->db->get_var("SELECT post_id FROM $tcp WHERE category_id=$categoryId AND language_code='en'");
        if ($basePostId && get_post($basePostId)) {
            // Update post
            $basePostId = $this->updatePost($basePostId, $name, $title, $parentPostId, $content, 'publish');
            if (!$basePostId) {
                return;
            }
        } else {
            // Create post
            if ($basePostId) {
                // Delete invalid record
                $this->db->query("DELETE FROM $tcp WHERE category_id=$categoryId AND language_code='en'");
            }
            $basePostId = $this->createPost($name, $title, $parentPostId, $content, 'publish');
            if (!$basePostId) {
                return;
            }
            $this->db->query("INSERT INTO $tcp (category_id,language_code,post_id) VALUES($categoryId,'en',$basePostId)");
        }

        $languages = $this->getActiveLanguages();
        foreach ($languages as $l) {
            $lc = $l->code;
            if ($lc == 'en') {
                continue;
            }

            if ($parentCategoryId) {
                $parentPostId = $this->db->get_var("SELECT post_id FROM $tcp WHERE category_id=$parentCategoryId AND language_code='$lc'");
                if (!($parentPostId && get_post($parentPostId))) {
                    $parentPostId = 0;
                }
            } else {
                $parentPostId = 0;
            }

            $translationPostId = $this->db->get_var("SELECT post_id FROM $tcp WHERE category_id=$categoryId AND language_code='$lc'");
            if ($translationPostId && get_post($translationPostId)) {
                // Update post
                // Don't touch the title until we offer translated category titles
                $translationPostId = $this->updatePost($translationPostId, null, null, $parentPostId, $content, 'publish');
                if (!$translationPostId) {
                    continue;
                }
            } else {
                // Create post
                if ($translationPostId) {
                    // Delete invalid record
                    $this->db->query("DELETE FROM $tcp WHERE category_id=$categoryId AND language_code='$lc'");
                }
                $translationPostId = $this->createPost(null, $title, $parentPostId, $content, 'publish');
                if (!$translationPostId) {
                    return;
                }
                $this->db->query("INSERT INTO $tcp (category_id,language_code,post_id) VALUES($categoryId,'$lc',$translationPostId)");
            }

            $this->linkTranslation($basePostId, $translationPostId, $lc);
        }
    }

    protected function createOrUpdateCategoryPages() {
        $categories = (new LodgixServiceCategories())->getAllParentFirst();
        foreach ($categories as $category) {
            $this->createOrUpdateCategoryPage($category);
        }
    }

    public function createOrUpdateAllPages() {
        $this->createOrUpdateCategoryPages();
        $this->createOrUpdatePropertyPages();
    }

    protected function isPropertyPost($id) {
        $tp = $this->dbPrefix . LodgixConst::TABLE_PAGES;
        if ($this->db->get_var("SELECT page_id FROM $tp WHERE page_id=$id")) {
            return true;
        }
        $tlp = $this->dbPrefix . LodgixConst::TABLE_LANG_PAGES;
        if ($this->db->get_var("SELECT page_id FROM $tlp WHERE page_id=$id")) {
            return true;
        }
        return false;
    }

    protected function isCategoryPost($id) {
        $tcp = $this->dbPrefix . LodgixConst::TABLE_CATEGORY_POSTS;
        if ($this->db->get_var("SELECT post_id FROM $tcp WHERE post_id=$id")) {
            return true;
        }
        return false;
    }

    protected function isOtherPost($id) {
        $languages = $this->getActiveLanguages();
        foreach ($languages as $l) {
            $lc = $l->code;
            if ($this->config->get("p_lodgix_vacation_rentals_page_$lc") == $id) {
                return true;
            }
            if ($this->config->get("p_lodgix_availability_page_$lc") == $id) {
                return true;
            }
            if ($this->config->get("p_lodgix_search_rentals_page_$lc") == $id) {
                return true;
            }
        }
        return false;
    }

    public function isLodgixPost($id) {
        return $this->isPropertyPost($id) || $this->isCategoryPost($id) || $this->isOtherPost($id);
    }

    public function deleteRemovedCategoryPosts() {
        $tcp = $this->dbPrefix . LodgixConst::TABLE_CATEGORY_POSTS;
        $tc = $this->dbPrefix . LodgixConst::TABLE_CATEGORIES;
        $forceDelete = true;
        $posts = $this->db->get_results(
            "SELECT * FROM $tcp cp LEFT JOIN $tc c ON cp.category_id=c.category_id WHERE c.id IS NULL"
        );
        foreach ($posts as $post) {
            wp_delete_post($post->post_id, $forceDelete);
        }
        $this->db->query("DELETE cp FROM $tcp cp LEFT JOIN $tc c ON cp.category_id=c.category_id WHERE c.id IS NULL");
    }

    protected function deleteInactiveLanguageProprtyPosts() {
        $tlp = $this->dbPrefix . LodgixConst::TABLE_LANG_PAGES;
        $forceDelete = true;
        $languages = $this->getInactiveLanguages();
        foreach ($languages as $l) {
            $lc = $l->code;
            $posts = $this->db->get_results(
                "SELECT * FROM $tlp WHERE language_code='$lc' AND property_id > 0"
            );
            foreach ($posts as $post) {
                wp_delete_post($post->page_id, $forceDelete);
            }
            // TODO: Remove other posts from properties' table
            $this->db->query("DELETE FROM $tlp WHERE language_code='$lc' AND property_id > 0");
        }
    }

    protected function deleteInactiveLanguageCategoryPosts() {
        $tcp = $this->dbPrefix . LodgixConst::TABLE_CATEGORY_POSTS;
        $forceDelete = true;
        $languages = $this->getInactiveLanguages();
        foreach ($languages as $l) {
            $lc = $l->code;
            $posts = $this->db->get_results("SELECT * FROM $tcp WHERE language_code='$lc'");
            foreach ($posts as $post) {
                wp_delete_post($post->post_id, $forceDelete);
            }
            $this->db->query("DELETE FROM $tcp WHERE language_code='$lc'");
        }
    }

    protected function deleteInactiveLanguageOtherPosts() {
        $tlp = $this->dbPrefix . LodgixConst::TABLE_LANG_PAGES;
        $forceDelete = true;
        $languages = $this->getInactiveLanguages();
        foreach ($languages as $l) {
            $lc = $l->code;
            wp_delete_post((int)$this->config->get("p_lodgix_vacation_rentals_page_$lc"), $forceDelete);
            wp_delete_post((int)$this->config->get("p_lodgix_availability_page_$lc"), $forceDelete);
            wp_delete_post((int)$this->config->get("p_lodgix_search_rentals_page_$lc"), $forceDelete);
            // TODO: Remove other posts from properties' table
            $this->db->query("DELETE FROM $tlp WHERE language_code='$lc' AND property_id < 0");
        }
    }

    public function deleteInactiveLanguagePosts() {
        $this->deleteInactiveLanguageProprtyPosts();
        $this->deleteInactiveLanguageCategoryPosts();
        $this->deleteInactiveLanguageOtherPosts();
    }

    protected function deleteAllPropertyPosts() {
        $forceDelete = true;

        $tp = $this->dbPrefix . LodgixConst::TABLE_PAGES;
        $posts = $this->db->get_results("SELECT * FROM $tp");
        foreach ($posts as $post) {
            wp_delete_post($post->page_id, $forceDelete);
        }
        $this->db->query("TRUNCATE TABLE $tp");

        $tlp = $this->dbPrefix . LodgixConst::TABLE_LANG_PAGES;
        $posts = $this->db->get_results("SELECT * FROM $tlp");
        foreach ($posts as $post) {
            wp_delete_post($post->page_id, $forceDelete);
        }
        // $this->db->query("TRUNCATE TABLE $tlp");
        // TODO: Remove other posts from properties' table
        $this->db->query("DELETE FROM $tlp WHERE property_id > 0");
    }

    protected function deleteAllCategoryPosts() {
        $tcp = $this->dbPrefix . LodgixConst::TABLE_CATEGORY_POSTS;
        $forceDelete = true;
        $posts = $this->db->get_results("SELECT * FROM $tcp");
        foreach ($posts as $post) {
            wp_delete_post($post->post_id, $forceDelete);
        }
        $this->db->query("TRUNCATE TABLE $tcp");
    }

    protected function deleteAllOtherPosts() {
        $forceDelete = true;
        $languages = $this->getActiveLanguages();
        foreach ($languages as $l) {
            $lc = $l->code;
            wp_delete_post((int)$this->config->get("p_lodgix_vacation_rentals_page_$lc"), $forceDelete);
            wp_delete_post((int)$this->config->get("p_lodgix_availability_page_$lc"), $forceDelete);
            wp_delete_post((int)$this->config->get("p_lodgix_search_rentals_page_$lc"), $forceDelete);
        }
        // TODO: Remove other posts from properties' table
        $tlp = $this->dbPrefix . LodgixConst::TABLE_LANG_PAGES;
        $this->db->query("DELETE FROM $tlp WHERE property_id < 0");
    }

    public function deleteAllPosts() {
        $this->deleteAllPropertyPosts();
        $this->deleteAllCategoryPosts();
        $this->deleteAllOtherPosts();
    }

}

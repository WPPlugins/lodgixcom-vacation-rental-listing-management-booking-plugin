<?php

class LodgixServiceInstall {

    function __construct($config) {
        global $wpdb;
        $this->db = $wpdb;
        $this->dbPrefix = $this->db->prefix;
        $this->config = $config;
    }

    function cleanAll() {
        (new LodgixServicePost($this->config))->deleteAllPosts();
        (new LodgixServiceDB())->clean();
    }

    function deleteAll() {
        (new LodgixServicePost($this->config))->deleteAllPosts();
        (new LodgixServiceDB())->drop();
        $this->config->delete();
        delete_option(LodgixConst::OPTION_DB_VERSION);
    }

}

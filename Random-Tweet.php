<?php
/*
Plugin Name: Random Tweet generator
Author: Luiz Sócrate
Author URI: http://socrate.com.br
*/

global $wpdb;
new RandomTweet($wpdb);

class RandomTweet {
    private $db;

    public function __construct(wpdb $database) {
        $this->db = $database;

        add_action("init", array(&$this, "setup_plugin"));
    }

    public function setup_plugin() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $this->db->random_tweet = $this->db->prefix . "random_tweet";
        $sql = "CREATE TABLE {$this->db->random_tweet} (
          id INT NOT NULL AUTO_INCREMENT,
          technology VARCHAR(255) NOT NULL,
          UNIQUE KEY id (id),
          UNIQUE KEY technology (technology)
        );";

        dbDelta($sql);
    }
}
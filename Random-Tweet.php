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

    function __construct(wpdb $database) {
        $this->db = $database;
    }
}
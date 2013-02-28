<?php
/*
Plugin Name: Random Tweet generator
Author: Luiz Sócrate
Author URI: http://socrate.com.br
*/

global $wpdb;
new RandomTweet($wpdb);

class RandomTweet {
    const NAME = "Random Tweet";
    const SLUG = "randomTweet";
    const CUSTOM_POST_TYPE = 'randomtweet';
    const CSS_EDITOR = "Random-Tweet/css/randomtweet-editor.css";
    const JS_EDITOR = "Random-Tweet/js/randomtweet-editor.js";

    private $db;

    public function __construct(wpdb $database) {
        $this->db = $database;

        add_action("init", array(&$this, "setup_plugin"));
    }

    public function showCharactersCountBox() {
        wp_enqueue_script("randomtweet-editor", plugins_url(self::JS_EDITOR), "jquery", false, true);
        wp_enqueue_style("randomtweet-editor", plugins_url(self::CSS_EDITOR));
        ?>
        <p class="charactercount-count">0</p>
        <?php
    }

    public function setCustomPostTypeMetaboxes() {
        add_meta_box("charcount", "Characters Count", array(&$this, "showCharactersCountBox"), self::CUSTOM_POST_TYPE, "side", "high");
    }

    public function setup_plugin() {
        $args = array(
            "label" => "Random Tweets",
            'labels' => array(
                'singular_name' => 'Random Tweet',
                'add_new_item' => 'Random Tweet',
                'edit_item' => 'Edit Random Tweet'
            ),
            "public" => false,
            "show_ui" => true,
            "menu_position" => 21,
            "supports" => array('title'),
            "register_meta_box_cb" => array(&$this, "setCustomPostTypeMetaboxes")
        );
        register_post_type(self::CUSTOM_POST_TYPE, $args);
    }

    private function getRandomTweets($qty) {
        $args = array(
            'post_type' => self::CUSTOM_POST_TYPE,
            'orderby' => 'rand'
        );

        if ($qty) {
            $args['numberposts'] = (int) $qty;
        }

        $posts = get_posts($args);

        return $posts;
    }
}

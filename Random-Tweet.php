<?php
/*
Plugin Name: Random Tweet generator
Author: Luiz Sócrate
Author URI: http://socrate.com.br
*/

global $wpdb;
new RandomTweet($wpdb);

function random_tweet_button($count = "none", $size = "medium") {
    global $wpdb;
    $rt = new RandomTweet($wpdb);

    return $rt->randomTweetButton($count, $size);
}

class RandomTweet {
    const NAME = "Random Tweet";
    const SLUG = "randomTweet";
    const CUSTOM_POST_TYPE = 'randomtweet';
    const CSS_EDITOR = "Random-Tweet/css/randomtweet-editor.css";
    const JS_EDITOR = "Random-Tweet/js/randomtweet-editor.js";
    const TWITTER_ACCOUNT = 'envisioningtech';
    const SHORTCODE = '{{random-tweet}}';

    private $db;

    public function __construct(wpdb $database) {
        $this->db = $database;

        add_action("init", array(&$this, "setup_plugin"));
        add_filter("the_content", array(&$this, "includeRandomTweet"));
    }

    public function includeRandomTweet($content) {
        if (strpos($content, self::SHORTCODE) !== FALSE) {
            $tweetButton = $this->randomTweetButton();
            $content = str_replace(self::SHORTCODE, $tweetButton, $content);
        }

        return $content;
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

    private function generateMessageForRandomTechnologies(array $technologies) {
        shuffle($technologies);
        $technologies = array_slice($technologies, 0, 2);
        $technologies = implode(' and ', $technologies);
        $year = date('Y');
        $message = sprintf('Envisioning technologies for %d and beyond, including %s.', $year, $technologies);

        return $message;
    }

    public function randomTweetButton($count = "none", $size = "medium") {
        $tweets = $this->getRandomTweets();
        $technologies = array_map(function ($tweet) {
            return $tweet->post_title;
        }, $tweets);

        $message = $this->generateMessageForRandomTechnologies($technologies);
        while (strlen($message) > 140) {
            $message = $this->generateMessageForRandomTechnologies($technologies);
        }

        $lang = get_bloginfo('language');
        $html = sprintf('<a href="https://twitter.com/share" class="twitter-share-button" data-count="%s" data-size="%s" data-lang="%s" data-text="%s" data-related="%s" target="_blank">Tweet</a>', $count, $size, $lang, $message, self::TWITTER_ACCOUNT);
        $html .= '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';

        return $html;
    }
}

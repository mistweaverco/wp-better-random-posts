<?php
/*
Plugin Name: WP Better Random Posts
Version:     1.0.0
Plugin URI:  https://apps.walialu.com/wp-better-random-posts/
Description: Better random posts for WordPress
Author:      Marco Kellershoff
Author URI:  https://about.walialu.com/
License:     MIT License
*/

if (!defined('ABSPATH')) exit;

class WpBetterRandomPosts {

        private static function getImageTagsFromHtmlString($str) {
                $re = '/<img\s.*src="(.*?[^"])".*[^>]>/m';
                preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
                if (isset($matches) && count($matches) > 1 && count($matches[1]) > 1) {

                        return $matches[1][1];
                } else {
                        return null;
                }
        }

        private static function getTeaserImageByContent($postContent) {
                return self::getImageTagsFromHtmlString($postContent);
        }

        private static function getTeaserImageById($postId) {
                $post = get_post($postId);
                $html = apply_filters('the_content', $post->post_content);
                return self::getImageTagsFromHtmlString($html);
        }

        private static function getTeaserImage($id = null, $content = null) {
                if ($content != null) {
                        return self::getTeaserImageByContent($content);
                } else {
                        if ($id == null) {
                                $id = get_the_ID();
                        }
                        return self::getTeaserImageById($id);
                }
        }

        public static function getSimpleOpts($opts = array(), $defaultValues = array(), $defaultFallbackValue = false) {
                foreach ($opts as $key => $value) {
                        if (isset($opts[$key]) == false) {
                                if (isset($defaultValues[$key])) {
                                        $opts[$key] = $defaultValues[$key];
                                } else {
                                        $opts[$key] = $defaultFallbackValue;
                                }
                        }
                }
                return $opts;
        }

        public static function GetRandomPostsData($postCount = 5, $opts = array()) {
                $opts = self::getSimpleOpts($opts, array(
                        'cache' => false,
                        'fetchTeaserImage' => false,
                ));
                $cache = $opts['cache'];
                $fetchTeaserImage = $opts['fetchTeaserImage'];
                $data = array();
                $args = array(
                        'post_type' => 'post',
                        'orderby'   => 'rand',
                        'posts_per_page' => $postCount,
                );
                $the_query = new WP_Query($args);
                if ($the_query->have_posts()) {
                        while ($the_query->have_posts()) {
                                $the_query->the_post();
                                $post_id = get_the_ID();
                                $teaserImage = null;
                                if ($fetchTeaserImage) {
                                        $teaserImage = self::getTeaserImage($post_id);
                                }
                                $data[] = array(
                                        'permalink' => get_permalink(),
                                        'title' => get_the_title(),
                                        'teaserImage' => $teaserImage,
                                );
                        }
                        // Restore original Post Data
                        wp_reset_postdata();
                }
                return $data;
        }

        public static function GetRandomPostsList($postCount = 5, $opts = array()) {
                $opts = self::getSimpleOpts($opts, array(
                        'cls' => 'wp-better-random-posts',
                ));
                $string = '';
                $posts = self::GetRandomPostsData($postCount, $opts);
                if (count($posts) > 0) {
                        $string .= '<ul class="'.$opts['cls'].'">';
                        foreach ($posts as $post) {
                                $string .= '<li><a href="'. $post['permalink'] .'">'. $post['title'] .'</a></li>';
                        }
                        $string .= '</ul>';
                }
                return $string;
        }

}


<?php
/*
Plugin Name: WP Better Random Posts
Version:     3.0.0
Plugin URI:  https://apps.gorilla.moe/wp-better-random-posts/
Description: Better random posts for WordPress
Author:      Marco Kellershoff
Author URI:  https://gorilla.moe/
License:     MIT License
*/

if (!defined('ABSPATH')) {
    exit;
}

class WpBetterRandomPosts
{
    private static function getOpenGraphImagesFromUrl($url)
    {
        $html = new DOMDocument();
        @$html->loadHTML(file_get_contents($url));
        $image_urls = array();
        foreach ($html->getElementsByTagName('meta') as $meta) {
            if ($meta->hasAttribute('property') &&
                strpos($meta->getAttribute('property'), 'og:') === 0) {
                if ($meta->getAttribute('property')=='og:image') {
                    $image_urls[] = $meta->getAttribute('content');
                }
            }
        }
        return $image_urls;
    }
    private static function getImageTagsFromHtmlString($str)
    {
        $re = '/<img\s.*src="(.*?[^"])".*[^>]>/m';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        if (isset($matches) && count($matches) > 1 && count($matches[1]) > 1) {
            return $matches[1][1];
        } else {
            return null;
        }
    }

    private static function getTeaserImageByContent($postContent)
    {
        return self::getImageTagsFromHtmlString($postContent);
    }

    private static function getTeaserImageById($postId)
    {
        $post = get_post($postId);
        $html = apply_filters('the_content', $post->post_content);
        return self::getImageTagsFromHtmlString($html);
    }

    private static function getTeaserImage($id = null, $content = null)
    {
        if ($content != null) {
            return self::getTeaserImageByContent($content);
        } else {
            if ($id == null) {
                $id = get_the_ID();
            }
            return self::getTeaserImageById($id);
        }
    }

    public static function getSimpleOpts($opts = array(), $defaultValues = array())
    {
        foreach ($defaultValues as $key => $value) {
            if (isset($opts[$key]) == false) {
                $opts[$key] = $defaultValues[$key];
            }
        }
        return $opts;
    }

    private static function getCacheKey($postCount, $dataSources)
    {
        $uniqueStr = $postCount . '::' . implode('::', $dataSources);
        $cacheKey = hash('sha256', $uniqueStr);
        return $cacheKey;
    }

    private static function setCachedData($cacheKey, $data)
    {
        $dir = dirname(__FILE__);
        $file = $dir . '/cache/' . $cacheKey . '.json';
        file_put_contents($file, json_encode($data));
    }

    private static function isCacheExpired($cacheKey, $expires)
    {
        $dir = dirname(__FILE__);
        $file = $dir . '/cache/' . $cacheKey . '.json';
        if (file_exists($file)) {
            $lastModif = filemtime($file);
            if ($lastModif > (time()-$expires)) {
                return false;
            }
        }
        return true;
    }

    private static function getCachedData($cacheKey, $expires)
    {
        $dir = dirname(__FILE__);
        $file = $dir . '/cache/' . $cacheKey . '.json';
        if (self::isCacheExpired($cacheKey, $expires) === false) {
            return json_decode(file_get_contents($file), true);
        }
        return false;
    }

    public static function GetRandomPostsData($postCount = 5, $opts = array())
    {
        $opts = self::getSimpleOpts($opts, array(
                        'cache' => false,
                        'dataSources' => array(),
                        'dataSourceTransformers' => array(),
                        'fetchTeaserImage' => false,
                ));
        $cache = $opts['cache'];
        $fetchTeaserImage = $opts['fetchTeaserImage'];
        $cacheKey = self::getCacheKey($postCount, $opts['dataSources']);
        $data = array();
        function getWpData()
        {
            $_data = array();
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
                    $_data[] = array(
                                        'permalink' => get_permalink(),
                                        'title' => get_the_title(),
                                        'teaserImage' => $teaserImage,
                                );
                }
                // Restore original Post Data
                wp_reset_postdata();
            }
            return $_data;
        }
        // no external data sources, just use WordPress posts
        if (count($opts['dataSources']) === 0) {
            $_data = getWpData();
            foreach ($_data as $_dataItem) {
                $data[] = $_dataItem;
            }
        } else { // external data sources, just use these
            foreach ($opts['dataSources'] as $i => $dataSource) {
                // use cache if existent and not expired
                if ($cache && $cachedData = self::getCachedData($cacheKey, $cache)) {
                    foreach ($cachedData as $_dataItem) {
                        $data[] = $_dataItem;
                    }
                } else {
                    if ($dataSource === true) { // true as value will force to use wp posts data
                        $_data = getWpData();
                        foreach ($_data as $_dataItem) {
                            $data[] = $_dataItem;
                        }
                    } else {
                        $_data = $opts['dataSourceTransformers'][$i]($dataSource);
                        foreach ($_data as $_dataItem) {
                            $data[] = $_dataItem;
                        }
                    }
                }
            }
        }
        // fetch teaser images for remote dataSources,
        // if not already present in dataset.
        if ($fetchTeaserImage && count($opts['dataSources']) > 0) {
            foreach ($data as $i => $item) {
                if (!isset($item['teaserImage'])) {
                    $image_urls = self::getOpenGraphImagesFromUrl($item['permalink']);
                    $data[$i]['teaserImage'] = (count($image_urls) > 0) ? $image_urls[0] : '';
                }
            }
        }
        // set cache data
        if ($cache && self::isCacheExpired($cacheKey, $cache)) {
            self::setCachedData($cacheKey, $data);
        }
        // shuffle array elements
        shuffle($data);
        // only a portion of the complete array
        $data = array_slice($data, 0, $postCount);
        return $data;
    }

    public static function GetRandomPostsList($postCount = 5, $opts = array())
    {
        $opts = self::getSimpleOpts($opts, array(
                        'cls' => 'wp-better-random-posts',
                ));
        $string = '';
        $posts = self::GetRandomPostsData($postCount, $opts);
        if (count($posts) > 0) {
            $string .= '<ul class="'.$opts['cls'].'">';
            foreach ($posts as $post) {
                $string .= '<li><a href="'. $post['permalink'] .'">';
                if ($post['teaserImage']) {
                    $string .= '<span class="teaserimage">'
                                                .'<div class="img" style="background-image:url(\'' . $post['teaserImage'] .'\');"></div></span>';
                }
                $string .= '<span class="title">'
                                        . $post['title'] .'</span></a></li>';
            }
            $string .= '</ul>';
        }
        return $string;
    }
}

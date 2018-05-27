WP Better Random Posts
======================

A better WordPress-Plugin for getting random posts.

Installation
------------

Go to your WordPress installation directory,
then change to `wp-content/plugins`.

Then either run (the preferred way):

```bash
git clone \
https://github.com/superevilmegaco/wp-better-random-posts.git \
-b v1.0.0 \
wp-better-random-posts
```

or (no easy updating via `git` or `git submodules`)
download the latest release from [here][releases]
and unzip its contents to `wp-content/plugins/wp-better-random-posts`.

Then head over to `www.your-wordpress-installation-domain.tld/wp-admin/plugins.php`
and activate the plugin.

Usage
-----

There are two public methods:

- `WpBetterRandomPosts::GetRandomPostsList($randomPostCount= 5, $opts = array())`
- `WpBetterRandomPosts::GetRandomPostsData($randomPostCount= 5, $opts = array())`

`GetRandomPostsList()` returns a HTML `<ul>` string.

```php
<?php

echo WpBetterRandomPosts::GetRandomPostsList(10, array(
        'cls' => 'wp-better-random-posts', // CSS-Class-Name for the list
        'fetchTeaserImage' => false, // do not fetch teaser images
        // if set to true, it'll fetch the first image of each post
        'cache' => false, // set to any int, to cache results on disk
        // the value is the time the cache is valid in seconds
        // make sure the `wp-contents/plugin/wp-better-random-posts/cache`
        // directory is writable
));

?>
```

`GetRandomPostsData()` returns an PHP array.

```php
<?php

$data = WpBetterRandomPosts::GetRandomPostsData(10, array(
        'fetchTeaserImage' => false, // do not fetch teaser images
        // if set to true, it'll fetch the first image of each post
        'cache' => false, // set to any int, to cache results on disk
        // the value is the time the cache is valid in seconds
        // make sure the `wp-contents/plugin/wp-better-random-posts/cache`
        // directory is writable
));

print_r($data);

?>
```

[releases]: https://github.com/superevilmegaco/wp-better-random-posts/releases


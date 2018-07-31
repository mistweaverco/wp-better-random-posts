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
-b v2.0.0 \
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

Basic CSS styling
-----------------

Styling is completely up to you.
Here's a basic example on what one could imagine, when using 
`echo WpBetterRandomPosts::GetRandomPostsList();`:

```css
ul.wp-better-random-posts {
        width: 100%;
        max-width:560px;
        text-align: center;
}

.wp-better-random-posts li {
        text-align: left;
        margin: 20px;
        position: relative;
        height: 90px;
        overflow: hidden;
        -webkit-box-shadow: 2px 2px 9px 0 rgba(0,0,0,0.2);
        box-shadow: 2px 2px 9px 0 rgba(0,0,0,0.2);
        vertical-align: middle;
        display:flex;
        flex-wrap:wrap;
        flex-direction:row;
        justify-content:flex-start;
        align-items:stretch;
}

.wp-better-random-posts .title {
        font-size: 2em;
        margin: 15px;
        color: #222222;
        order: 2;
        flex-basis: 100%;
        height: 90px;
        font-weight: bold;
        display: inline-flex;
        width: calc(100% - 120px);
        position: absolute;
}
.wp-better-random-posts .teaserimage {
        order: 1;
        flex-basis: 90px;
}

.wp-better-random-posts .img {
        display: inline-block;
        width: 90px;
        height: 90px;
        background-size: cover;
        background-position: center center;
        box-shadow: inset 0 0 3em rgba(0,0,0,0.12);
}
```

Development
-----------

All linters are run async while editing, while all fixers are run synchronously
before writing the contents of the buffer to disk using [ALE][vim-ale].

### Linters

- [CSSLint][csslint]
- `php -l`


### Fixers

- [Prettier (CSS)][prettier]
- [PHP-CS-Fixer (PHP)][phpcsfixer]



[vim-ale]: https://github.com/w0rp/ale
[csslint]: https://github.com/CSSLint/csslint/wiki/Command-line-interface
[phpcsfixer]: http://cs.sensiolabs.org/#globally-manual
[prettier]: https://prettier.io
[releases]: https://git.superevilmegaco.com/wordpress/wp-better-random-posts/tags


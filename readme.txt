=== WordPress Facebook Like Ranking ===
Contributors: MankinJp
Donate link: 
Tags: plugin, facebook, like, ranking, popular
Requires at least: 2.8
Tested up to: 3.5.1
Stable tag: 1.04
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

With this plugin, you can use a your posts' ranking rated by the number of Facebook like.

== Description ==

= Features =

* **Making a ranking rated by the number of Facebook like**. You can make and use a ranking rated by the number of Facebook like width this plugin. It's good for visitors.

* **Setting in detail**. You can decide how often it will update the raning information and how many posts it will check when it updates the information.

== Installation ==

1. Download the plugin and extract its contents. / プラグインをダウンロード、解凍します。
2. Upload `wp-facebook-like-ranking` to the `/wp-content/plugins/` directory. / `/wp-content/plugins/`ディレクトリにプラグインをアップロードします。
3. Activate the plugin through the 'Plugins' menu in WordPress. / プラグインを有効化します。
4. Create a ranking by pushing a create button through the 'Settings->WP Facebook Like Ranking' menu in WordPres. / 'Settings->WP Facebook Like Ranking'でCreateボタンを押して最初のランキングを生成します。
5. Configure items. / 'Settings->WP Facebook Like Ranking'で細かい設定を行います。
6. Place `<?php if (function_exists('get_like_ranking')) get_like_ranking (); ?>` in your templates. / `<?php if (function_exists('get_like_ranking')) get_like_ranking (); ?>`を、ランキングを出したいところにコピペします。

That's it!

・more about function

`get_like_ranking (int $post_number = 5, bool $post_count = true, array $thumbnail_size = null)`

ex)

`get_like_ranking (10, false, array(20, 20))`

It shows 10 posts and 20px × 20px thumbnail picture without expressing like count.


== Frequently asked questions ==

= I need help with your plugin! What should I do? =

 If you're having problems with the plugin, my suggestion would be try disabling all other plugins.

== Screenshots ==

1. WordPress Facebook Like Ranking on theme's sidebar.
2. WordPress Facebook Like Ranking Stats panel.


== Changelog ==

= 1.04 =
* Change how to create ranking

= 1.03 =
* Fix bug

= 1.02 =
* Make thumbnail image clickable

= 1.01 =
* Fix bug

= 1.0 =
* Public release

== Upgrade notice ==

You better use Wordpress 2.8 at least.

<?php
/*
Plugin Name: WP Facebook Like Ranking
Plugin URI: https://github.com/Mankin/wp-fb-like-ranking
Description: facebookのいいね数に応じた、ブログ記事のランキングを生成します。
Author: Taishi Kato
Version: 1.0
Author URI: http://taishikato.com/blog/
*/

$wplrank = new WpLikeRanking();

class WpLikeRanking {

  public function __construct () {
    if (function_exists('register_activation_hook')) {
      // When This Plugin Become Valid
      register_activation_hook(__FILE__, array(&$this, 'set_likecount_meta'));
    }
    if (function_exists('register_deactivation_hook')) {
      // When This Plugin Become Invalid
      register_deactivation_hook(__FILE__, array(&$this, 'delete_likecount_meta'));
    }
  
    add_action( 'transition_post_status', array(&$this, 'add_userblog_meta'), 10, 3 );
    
    //set Hook
    if ( !wp_next_scheduled( 'wp_fb_like_ranking_event' ) ) {
      $WpFbLikeRankingUpdateFrequency = get_option ('wp_fb_like_ranking_frequency');
      wp_schedule_event(time(), $WpFbLikeRankingUpdateFrequency, 'wp_fb_like_ranking_event');
    }
    // add action
    add_action('wp_fb_like_ranking_event', array(&$this, 'update_fb_like'));
  }

  function add_userblog_meta ( $new_status, $old_status, $post ) {
    if ($new_status == 'publish' AND $old_status != 'publish') {
      global $post;
      add_post_meta($post->ID, 'wp_fb_like_count', 0, true);
    }
  }

  function set_likecount_meta () {  
    // プラグインを有効にしたときの処理を書く
    // Set the options
    update_option ('wp_fb_like_ranking_frequency', 'hourly');
    update_option ('wp_fb_like_ranking_updatePostNumber', 'all'); 
    // Search All Of The Posts
    $lastposts = get_posts('numberposts=0&post_type=post&post_status=');
    foreach($lastposts as $post) {
      setup_postdata($post);
      // get the ID
      $postId = $post->ID;
      // get the permalink
      $permalink = get_permalink($postId);
      $xml = 'http://api.facebook.com/method/fql.query?query=select%20total_count%20from%20link_stat%20where%20url=%22'.$permalink.'%22';
      $result = file_get_contents ($xml);
      $result = simplexml_load_string ($result);
      $likeNumber = $result->link_stat->total_count;
      $likeNumber = (int) $likeNumber;
      // Add Meta Data
      add_post_meta($postId, 'wp_fb_like_count', $likeNumber, true);
    }
  }

  function delete_likecount_meta () {  
    // プラグインを無効にしたときの処理を書く 
    // Search All Of The Posts
    $lastposts = get_posts('numberposts=0&post_type=post&post_status=');
    foreach($lastposts as $post) {
      setup_postdata($post);
      // get the ID
      $postId = $post->ID;

      // Delete Meta Data
      delete_post_meta($postId, 'wp_fb_like_count');
    }
  }

  function update_fb_like () {
    $WpFbLikeRankingUpdatePostNumber = get_option ('wp_fb_like_ranking_updatePostNumber');
    if ($WpFbLikeRankingUpdatePostNumber == 'all') {
      $lastposts = get_posts('numberposts=0&post_type=post&post_status=');
    } else {
      $lastposts = get_posts('numberposts='.$WpFbLikeRankingUpdatePostNumber.'&orderby=post_date&order=DESC');
    }
    foreach($lastposts as $post) {
      setup_postdata($post);
      // get the ID
      $postId = $post->ID;
      // get the permalink
      $permalink = get_permalink($postId);
      // get the number of like
      $xml = 'http://api.facebook.com/method/fql.query?query=select%20total_count%20from%20link_stat%20where%20url=%22'.$permalink.'%22';
      $result = file_get_contents ($xml);
      $result = simplexml_load_string ($result);
      $likeNumber = $result->link_stat->total_count;
      $likeNumber = (int) $likeNumber;

      $preLikeNumber = get_post_meta($postId, 'wp_fb_like_count', true);

      if( $preLikeNumber != $likeNumber ) {
        update_post_meta($postId, 'wp_fb_like_count', $likeNumber, $preLikeNumber);
      }
    }
  }
}

add_action('admin_menu', 'wp_fb_like_ranking_admin_menu');
function wp_fb_like_ranking_admin_menu () {
  add_options_page('WP Facebook Like Ranking', 'WP Facebook Like Ranking', 8, __FILE__, 'wp_fb_like_ranking_edit_setting');
}

// 管理画面設定
function wp_fb_like_ranking_edit_setting () {
  if (isset($_POST['wp_fb_like_ranking_frequency'])) {
    update_option ('wp_fb_like_ranking_frequency', $_POST['wp_fb_like_ranking_frequency']);
  }
  if (isset($_POST['wp_fb_like_ranking_updatePostNumber'])) {
    update_option ('wp_fb_like_ranking_updatePostNumber', $_POST['wp_fb_like_ranking_updatePostNumber']);
  }
  $WpFbLikeRankingFrequency = get_option ('wp_fb_like_ranking_frequency');
  $WpFbLikeRankingUpdatePostNumber = get_option ('wp_fb_like_ranking_updatePostNumber');
  include 'setting.html.php';
}

function get_like_ranking ($number = 5, $like_count = true, $thumbnail_size = null) {
  $number = esc_html($number);
  $rank = get_posts('meta_key=wp_fb_like_count&numberposts='.$number.'&orderby=meta_value_num');
  echo '<ul class="wp-fb-like-ranking">';
  $i = 0;
  foreach($rank as $post) {
    $likeNumberToPost = get_post_meta($post->ID, 'wp_fb_like_count', true);
    if ($likeNumberToPost != 0) {
      $i++;
      if ($like_count == true) {
        if ($thumbnail_size == null) {
          echo '<li><a href="'.$post->guid.'">'.esc_html($post->post_title).'</a> <span class="wp-fb-like-ranking-count">'.$likeNumberToPost.'</span></li>';
        } else {
          echo '<li>'.get_the_post_thumbnail( $post->ID, $thumbnail_size ).'<a href="'.$post->guid.'">'.esc_html($post->post_title).'</a> <span class="wp-fb-like-ranking-count">'.$likeNumberToPost.'</span></li>';
        }
      } else {
        if ($thumbnail_size == null) {
          echo '<li><a href="'.$post->guid.'">'.esc_html($post->post_title).'</a></li>';
        } else {
          echo '<li>'.get_the_post_thumbnail( $post->ID, $thumbnail_size ).'<a href="'.$post->guid.'">'.esc_html($post->post_title).'</a></li>';
        }
      }
    }
  }
  if ($i == 0) echo 'いいねを押されている記事はまだありません';
  echo '</ul>';
  wp_reset_query();
}
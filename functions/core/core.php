<?php
$abb_scripts = array();
$abb_styles = array();
$abb_options = array();

function get_abb_option($key) {
  global $abb_options;

  if (empty($abb_options)) {
    $frontend = get_option('abb_options_frontend');
    if (!is_array($frontend)) $frontend = array();

    $backend = get_option('abb_options_backend');
    if (!is_array($backend)) $backend = array();

    $cpt = get_option('abb_options_cpt');
    if (!is_array($cpt)) $cpt = array();

    $timmy = get_option('abb_options_timmy');

    if (!is_array($timmy)) $timmy = array();

    $tinymce = get_option('abb_options_tinymce');
    if (!is_array($tinymce)) $tinymce = array();

    $vendor = get_option('abb_options_vendor');
    if (!is_array($vendor)) $vendor = array();

    $abb_options = array_merge($frontend, $backend, $timmy, $cpt, $tinymce, $vendor);
  }

  return (isset($abb_options[$key])) ? $abb_options[$key] : false;
}

include_once('acf.php');
include_once('panel.php');
include_once('frontend.php');
include_once('backend.php');
include_once('tinymce.php');

define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true);

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');

add_action('wp_enqueue_scripts', function () {
  global $abb_scripts, $abb_styles;

  wp_deregister_script('jquery');
  wp_dequeue_style('wp-block-library');

  foreach ($abb_styles as $styles) {
  	wp_register_style($styles[0], $styles[1], false, 1, (!empty($styles[2])) ? $styles[2] : 'all');
    wp_enqueue_style($styles[0]);
  }

  foreach ($abb_scripts as $script) {
    $handle = (isset($script[0])) ? $script[0] : 'handle_' . time() . '_' . rand(0, 1024);
    $src = (isset($script[1])) ? $script[1] : '';
    $in_footer = (isset($script[2])) ? $script[2] : false;

    if (!empty($src)) {
      wp_register_script($handle, $src, array(), false, $in_footer);
      wp_enqueue_script($handle);  
    }
  }

  wp_localize_script($handle, 'WP', [
    'template_url' => get_stylesheet_directory_uri(),
    'ajax_url' => admin_url('admin-ajax.php')
  ]);
});

/* remove usless stuff */
add_action('wp_footer', function() {
  wp_dequeue_script('wp-embed');
});

/*
 * Install noticies
 */
add_action('admin_notices', function () {
  global $installNotices;

  if (empty($installNotices)) return;

  echo '<div class="notice notice-warning"><p>Warning some mandatory plugins are missing.</p><ul>';
  
  foreach ($installNotices as $installNotice) {
    $url = (isset($installNotice['url'])) ? $installNotice['url'] : $installNotice['external_url'];
    $target = (!empty($installNotice['external_url'])) ? 'target="_blank"' : '';
    echo '<li><a href="' . $url . '" '.$target.'>' . $installNotice['title'] . '</a></li>';
  }

  echo '</ul></div>';
});

?>

<?php
/*
 * Timber
 */
global $installNotices;
$installNotices = [];
if (!class_exists('Timber')) {
  $installNotices[] = [
    'url' => esc_url(admin_url('/plugin-install.php?s=Timber&tab=search&type=term')),
    'title' => 'Timber'
  ];

  add_filter('template_include', function ($template) {
    return get_stylesheet_directory() . '/no-timber.html';
  });
} else {
  Timber::$dirname = ['interface'];
  Timber::$autoescape = false;
}

/*
 * Debug
 */
if (get_abb_option('debug')) {
  add_filter('body_class', function ($classes) {
    $classes[] =  'debug';

    return $classes;
  });
}

/*
 * Default menu
 */
add_filter('timber/context', function ($context) {
  $context['menus'] = [
    'main' => new \Timber\Menu('main')
  ];

  return $context;
});

if (!class_exists('Timmy\Timmy')) {
  $installNotices[] = [
    'external_url' => 'https://github.com/mindkomm/timmy/releases',
    'title' => 'Timmy'
  ];

  return;
} else {
  new Timmy\Timmy();
}

/*
 * Include
 */

$abb_styles[] = array('abb-styles', get_bloginfo('template_directory') . '/dist/css/style.min.css', false);
$abb_scripts[] = array('abb-scripts', get_bloginfo('template_directory') . '/dist/js/site.min.js');

$i = 0;
$externals_scripts = explode("\n", get_abb_option('externals_scripts'));
if (is_array($externals_scripts)) {
  foreach ($externals_scripts as $external_script) {
    $external_script = str_replace('{template_directory}', get_bloginfo('template_directory'), $external_script);
    $abb_scripts[] = array('external_' . $i, $external_script);

    $i++;
  }
}

$externals_css = explode("\n", get_abb_option('externals_css'));
if (is_array($externals_css)) {
  foreach ($externals_css as $external_css) {
    $external_css = str_replace('{template_directory}', get_bloginfo('template_directory'), $external_css);
    $abb_styles[] = array('external_' . $i, $external_css);

    $i++;
  }
}

/*
 * Disable the Admin Bar
 */
if (!get_abb_option('show_admin_bar')) add_filter('show_admin_bar', '__return_false');

/*
 * Redirect
 */
add_action('init', function () {
  if (!is_user_logged_in() && $_SERVER['REQUEST_URI'] != '/de/jahreszeit/jahreszeit-2021/' && $_SERVER['REQUEST_URI'] != '/saison/saison-2021/' && $GLOBALS['pagenow'] !== 'wp-login.php' && get_abb_option('hide_site')) {
    wp_redirect('/saison/saison-2021/');
    die();
  }
});

/*
 * WPML helpers
 */
function get_the_original_translation_ID($lang = 'fr') {
  return icl_object_id(get_the_ID(), get_post_type(), false, $lang);
}

/* Timber Twig */
function twig_asset($file) {
  return get_bloginfo('template_directory') . '/dist/assets/' . $file;
}

add_filter('timber/twig', function ($twig) {
  // Adding a function.
  $twig->addFunction(new Timber\Twig_Function('asset', 'twig_asset'));

  return $twig;
});

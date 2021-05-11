<?php
/*
 * Timber
 */
if (!class_exists('Timber')) {
  add_action('admin_notices', function() {
    echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
  });

  add_filter('template_include', function( $template ) {
    return get_stylesheet_directory() . '/no-timber.html';
  });

  return;
}

/*
 * Debug
 */
if (get_abb_option('debug')) {
  add_filter('body_class', function($classes) {
    $classes[] =  'debug';

    return $classes;
  });
}

/*
 * Default menu
 */
add_filter('timber/context', function ($context) {
  $context['menus'] = [
    new \Timber\Menu('main')
  ];

  return $context;
});

Timber::$dirname = ['views', 'components'];
Timber::$autoescape = false;

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
if (!get_abb_option('show_admin_bar')) add_filter( 'show_admin_bar', '__return_false' );

/*
 * Redirect
 */
add_action('init', function() {
  if (!is_user_logged_in() && $_SERVER['REQUEST_URI'] != '/coming-soon/' && $GLOBALS['pagenow'] !== 'wp-login.php' && get_abb_option('hide_site')) {
    wp_redirect('/coming-soon/');
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

add_filter('timber/twig', function($twig) {
  // Adding a function.
  $twig->addFunction( new Timber\Twig_Function('asset', 'twig_asset'));
    
  return $twig;
});

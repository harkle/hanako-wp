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
  $context['menu'] = new \Timber\Menu('menu');

  return $context;
});

Timber::$dirname = ['views'];
Timber::$autoescape = false;

/*
 * Include
 */
if (get_abb_option('use_jquery')) $abb_scripts[] = array('jquery', 'https://code.jquery.com/jquery-3.5.1.min.js', false);
if (get_abb_option('use_bootstrap')) $abb_styles[] = array('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css');
if (get_abb_option('use_bootstrap_popper')) $abb_scripts[] = array('bootstrap-popper', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js');
if (get_abb_option('use_bootstrap')) $abb_scripts[] = array('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js');
    
$abb_styles[] = array('abb-styles', get_bloginfo('template_directory') . '/dist/bundle.css', false);
$abb_scripts[] = array('abb-scripts', get_bloginfo('template_directory') . '/dist/bundle.js');

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
  if (!is_user_logged_in() && $GLOBALS['pagenow'] !== 'wp-login.php' && get_abb_option('hide_site')) {
    auth_redirect();
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

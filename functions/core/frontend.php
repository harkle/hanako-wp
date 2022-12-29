<?php
use Timber\ImageHelper;


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
  Timber::$dirname = ['views/twig'];
  Timber::$autoescape = false;
}

/*
 * Dev mode
 */
if (get_abb_option('dev_mode')) {
  add_filter('body_class', function ($classes) {
    $classes[] =  'dev-mode';

    return $classes;
  });
}

/*
 * Auto reload
 */
if (get_abb_option('auto_reload')) {
  add_filter('body_class', function ($classes) {
    $classes[] =  'auto-reload';

    return $classes;
  });
}

/*
 * Error reporting
 */
if (get_abb_option('error_reporting')) {
  $error_reporting = get_abb_option('error_reporting');

  ini_set('display_errors', 1);

  if ($error_reporting == 1) error_reporting(E_ALL);
  if ($error_reporting == 2) error_reporting(E_ERROR);
  if ($error_reporting == 3) error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
  if ($error_reporting == 4) error_reporting(E_ALL ^ E_WARNING ^ E_DEPRECATED);
  if ($error_reporting == 5) error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
} else {
  ini_set('display_errors', 0);
}

/*
 * Timmy
 */
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

$dev_suffix = (get_abb_option('dev_mode') ? '?time=' . date('U') : '');
$abb_styles[] = array('abb-styles', get_bloginfo('template_directory') . '/dist/css/style.min.css' . $dev_suffix, false);
$abb_scripts[] = array('abb-scripts', get_bloginfo('template_directory') . '/dist/js/site.min.js' . $dev_suffix);

$i = 0;
$externals_scripts = explode("\n", get_abb_option('externals_scripts'));
if (is_array($externals_scripts)) {
  foreach ($externals_scripts as $external_script) {
    $external_script = str_replace('{template_directory}', get_bloginfo('template_directory'), $external_script);
    $external_script = str_replace('{dev}', $dev_suffix, $external_script);
    $abb_scripts[] = array('external_' . $i, $external_script);

    $i++;
  }
}

$externals_css = explode("\n", get_abb_option('externals_css'));
if (is_array($externals_css)) {
  foreach ($externals_css as $external_css) {
    $external_css = str_replace('{template_directory}', get_bloginfo('template_directory'), $external_css);
    $external_css = str_replace('{dev}', $dev_suffix, $external_css);
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
  if (!is_user_logged_in() && get_abb_option('hide_site') && $GLOBALS['pagenow'] !== 'wp-login.php') {
    $allowed_urls = explode(',', get_abb_option('allowed_urls'));

    if (!in_array($_SERVER['REQUEST_URI'], $allowed_urls) && $_SERVER['REQUEST_URI'] != get_abb_option('redirect_to')) {
      wp_redirect(get_abb_option('redirect_to'));
      die();
    }
  }
});

/*
 * WPML helpers
 */
function get_the_original_translation_ID($lang = 'fr') {
  return icl_object_id(get_the_ID(), get_post_type(), false, $lang);
}

/* Retrive asset */
function hw_asset($file) {
  return get_bloginfo('template_directory') . '/dist/assets/' . $file;
}

/*
 * Image with lazy loading
 */
function hw_lazy_image($image, $size, $classes = '', $alt = '', $title = '', $data = '') {
  $timber_image = new Timber\Image($image);

  if (empty($image)) return;

  $ratio = $timber_image->aspect() > 0 ? 100 / $timber_image->aspect : 1;

  if (isset($timber_image->sizes[$size])) {
    $ratio = 100 / ($timber_image->sizes[$size]['width'] / $timber_image->sizes[$size]['height']);
  }

  $alt = (!empty($alt)) ? $alt : $timber_image->alt;
  $title = (!empty($title)) ? $alt : $timber_image->title;
  $src = ImageHelper::img_to_webp(get_timber_image_src($timber_image, $size));

  $return  = '<div class="ratio ' . $classes . '" style="--bs-aspect-ratio: ' . $ratio . '%;">';
  $return .= '<img data-hw-src="' . $src . '" class="d-block w-100" title="' . $title . '" alt="' . $alt . '" ' . $data . '>';
  $return .= '</div>';

  return $return;
}

/*
 * Image background with lazy loading
 */
function hw_lazy_background_image($image, $size) {
  $timber_image = new Timber\Image($image);

  $src = ImageHelper::img_to_webp(get_timber_image_src($timber_image, $size));

  return 'data-hw-background-image="' . $src . '"';
}

/*
 * add some useful functions
 */
add_filter('timber/twig', function ($twig) {
  $twig->addFunction(new Timber\Twig_Function('the_permalink', 'get_the_permalink'));

  $twig->addFunction(new Timber\Twig_Function('print_r', 'print_r'));

  $twig->addFunction(new Timber\Twig_Function('asset', 'hw_asset'));

  $twig->addFunction(new Timber\Twig_Function('lazy_image', 'hw_lazy_image'));

  $twig->addFunction(new Timber\Twig_Function('lazy_background_image', 'hw_lazy_background_image'));

  return $twig;
});

/*
 * Fetch templates from models/subfolder
 */
$templateTypes = ['index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date', 'embed', 'home', 'frontpage', 'privacypolicy', 'page', 'paged', 'search', 'single', 'singular', 'attachment'];

foreach ($templateTypes as $templateType) {
  add_filter($templateType . '_template_hierarchy', function ($templates) {
    foreach ($templates as &$template) {
      if (strpos($template, 'odels/') != 1) {
        $template = 'models/' . $template;
      }
    }

    return $templates;
  });
}

/*
 * Load translations
 */
add_action('after_setup_theme', function () {
  load_theme_textdomain('hw-theme', get_template_directory() . '/languages');
});

/*
 * Add defered attribute to style and script tag
 */
if (!is_admin() && $GLOBALS['pagenow'] !== 'wp-login.php') {
  add_filter('style_loader_tag', function ($tag) {
    return str_replace(' href', ' defer href', $tag);
  
    return $tag;
  });

  add_filter('script_loader_tag', function ($tag) {
    return str_replace(' src', ' defer src', $tag);

    return $tag;
  });
}

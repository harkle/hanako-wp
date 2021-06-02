<?php
include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if (!defined('WP_AUTO_UPDATE_CORE')) {
  add_action('admin_notices', function () {

    echo '<div class="error notice"><p>' . __('Merci d\'ajouter l\'instruction suivant au fichier wp-config.php.<pre>define(\'WP_AUTO_UPDATE_CORE\', false);</pre>', 'abb') . '</p></div>';
  });
}

/*
 * image modification
 */
update_option('thumbnail_size_w', 0);
update_option('thumbnail_size_h', 0);
update_option('thumbnail_crop', 1);

update_option('medium_size_w', 0);
update_option('medium_size_h', 0);

update_option('large_size_w', 0);
update_option('large_size_h', 0);

set_post_thumbnail_size(0, 0);

add_filter('jpeg_quality', create_function('', 'return 100;'));

add_filter('big_image_size_threshold', '__return_false');

add_filter('timmy/generate_srcset_sizes', '__return_true');

if (get_abb_option('image_sizes')) {
  add_filter('timmy/sizes', function ($sizes) {
    $options_sizes = json_decode(get_abb_option('image_sizes'), true);

    return (is_array($options_sizes)) ? $options_sizes : [];
  });
}

/*
 * Add editor the privilege to edit theme
 */
$role_object = get_role('editor');
$role_object->add_cap('edit_theme_options');

/*
 * Allow svg upload
 */
add_filter('upload_mimes', function ($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
});

/*
 * Move Yoast to bottom
 */
add_filter('wpseo_metabox_prio', function () {
  return 'low';
});

/*
 * Logo login
 */
add_action('login_head', function () {
  echo '<style type="text/css">.login h1 a {background-image:url(' . get_bloginfo('template_directory') . '/logo-dashboard.png);background-size:218px;width: 218px;height: 40px;}</style>';
});

/*
 * Enable editor style menus
 */
add_editor_style('dist/css/editor-style.min.css');

/*
 * Message on the dashboard
 */
add_action('wp_dashboard_setup', function () {
  wp_add_dashboard_widget('aboutblank_dashboard_widget', '<div style="text-align:center;"><img style="vertical-align: middle; height: 23px;width:190px;" src="' . get_bloginfo('template_directory') . '/logo-dashboard.png" alt=""></div>', function () {
    echo 'Bienvenue sur la plateforme d\'administration du site:<br><strong>' . get_bloginfo('name') . '</strong><br/><br/>About Blank Design Office<br>Ch. des Pépinères 20<br>CH-1020 Renens<br>&nbsp;<br><a href="mailto:lionel@aboutblank.ch">lionel@aboutblank.ch</a><br>+41 21 635 03 22<br>+41 78 718 74 45';
  });
});

remove_action('welcome_panel', 'wp_welcome_panel');

/*
 * Change/Disable the footer text line
 */
add_action('admin_init', function () {
  add_filter('admin_footer_text', '__return_false', 11);
  add_filter('update_footer', function ($content) {
    return '© ' . date('Y') . ' <a target="_blank" href="http://www.aboutblank.ch">About Blank Design Office</a>';
  }, 11);
});

/*
 * Remove WPML metabox
 */
add_action('admin_head', function () {
  $screen = get_current_screen();

  remove_meta_box('icl_div_config', $screen->post_type, 'normal');
});

/*
 * Image linked to files
 */
update_option('image_default_link_type', 'file');

/*
 * Remove french accent on image upload
 */
add_filter('sanitize_file_name', 'remove_accents');

/*
 * Remove update plugin count
 */
add_action('admin_menu', function () {
  global $menu, $submenu;

  $menu[65][0] = 'Plugins';
  $submenu['index.php'][10][0] = 'Updates';
});

/*
 * Hide update notification
 */
add_action('admin_head', function () {
  remove_action('admin_notices', 'update_nag', 3);
}, 1);

/*
 * Remove links in admin bar
 */
add_action('wp_before_admin_bar_render', function () {
  global $wp_admin_bar;

  $wp_admin_bar->remove_menu('wp-logo');
  $wp_admin_bar->remove_menu('comments');
  $wp_admin_bar->remove_menu('itsec_admin_bar_menu');
  $wp_admin_bar->remove_menu('wpseo-menu');

  $current_user = wp_get_current_user();
  if ($current_user->user_login != 'tardyli') $wp_admin_bar->remove_menu('updates');
});

/*
 * Set permalink
 */
add_action('init', function () {
  global $wp_rewrite;

  $wp_rewrite->set_permalink_structure('/%postname%/');
});

/*
 * Remove unnecessary row in admin
 */
add_filter('manage_pages_columns', function ($defaults) {
  unset($defaults['comments']);

  return $defaults;
});

/*
 * Hide update page
 */
add_action('admin_init', function () {
  $current_user = wp_get_current_user();

  if ($current_user->user_login != 'tardyli') remove_submenu_page('index.php', 'update-core.php');
});

/*
 * Remove accents
 */
add_filter('sanitize_file_name', function ($filename) {
  $sanitized_filename = remove_accents($filename);
  $invalid = array(' ' => '-', '%20' => '-', '_' => '-',);
  $sanitized_filename = str_replace(array_keys($invalid), array_values($invalid), $sanitized_filename);
  $sanitized_filename = preg_replace('/[^A-Za-z0-9-\. ]/', '', $sanitized_filename);
  $sanitized_filename = preg_replace('/\.(?=.*\.)/', '', $sanitized_filename);
  $sanitized_filename = preg_replace('/-+/', '-', $sanitized_filename);
  $sanitized_filename = str_replace('-.', '.', $sanitized_filename);
  $sanitized_filename = strtolower($sanitized_filename);

  return $sanitized_filename;
}, 10, 1);

/*
 * Hide CSS
 */
add_action('admin_head', function () {
  $css_queries = explode("\n", get_abb_option('hide_css'));

  echo '<style>';
  $i = 1;

  foreach ($css_queries as $css_query) {
    echo preg_replace("/\r|\n/", "", $css_query);

    if ($i < count($css_queries)) echo ',';
    $i++;
  }

  echo '{ display: none !important; }</style>';
});

/*
 * Enable Theme menus
 */
if (get_abb_option('theme_support_menus')) add_theme_support('menus');

/*
 * Custom Post Types & taxonomies
 */
if (get_abb_option('post_types')) {
  $postTypes = json_decode(get_abb_option('post_types'), true);

  if (is_array($postTypes)) {
    foreach ($postTypes as $postType) {
      register_post_type($postType['name'], $postType['data']);
    }
  }

  $taxnomies = json_decode(get_abb_option('taxnomies'), true);

  if (is_array($taxnomies)) {
    foreach ($taxnomies as $taxnomy) {
      register_taxonomy($taxnomy['name'], $taxnomy['post_type'], $taxnomy['data']);
    }
  }
}

/*
 * ACF option page
 */
if (get_abb_option('add_acf_options')) acf_add_options_page();

/*
 * remove screen options
 */
if (get_abb_option('hide_screen_options')) {
  add_filter('screen_options_show_screen', function () {
    return false;
  });
}

/*
 * remove help options
 */
if (get_abb_option('hide_help')) {
  add_filter('contextual_help', function ($old_help, $screen_id, $screen) {
    $screen->remove_help_tabs();
    return $old_help;
  }, 999, 3);
}

/*
 * Remove meta-box
 */
add_action('admin_menu', function () {
  $metaboxes = explode("\n", get_abb_option('hide_metabox'));
  foreach ($metaboxes as $metabox) {
    $metabox = explode(',', $metabox);
    remove_meta_box($metabox[0], $metabox[1], $metabox[2]);
  }

  $post_types = get_post_types();
  $metaboxes_posttype = explode("\n", get_abb_option('hide_metabox_posttype'));
  foreach ($post_types as $post_type) {
    foreach ($metaboxes_posttype as $metabox) {
      $metabox = explode(',', $metabox);
      remove_meta_box($metabox[0], $post_type, $metabox[1]);
    }
  }
});

/*
 * Site health check
 */
add_filter('site_status_tests', function($tests) {
  unset($tests['direct']['wordpress_version']);
  unset($tests['direct']['plugin_version']);
  unset($tests['direct']['theme_version']);
  unset($tests['direct']['background_updates']);
  
  unset($tests['async']['background_updates']);
  return $tests;
});

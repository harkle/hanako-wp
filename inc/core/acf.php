<?php
add_filter('acf/settings/save_json', function($path) {
    return get_stylesheet_directory() . '/config/acf';
});

add_filter('acf/settings/load_json', function($paths) {
  unset($paths[0]);
    
  $paths[] = get_stylesheet_directory() . '/config/acf';

  return $paths;
});

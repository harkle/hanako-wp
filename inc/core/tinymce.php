<?php
add_filter('acf/fields/wysiwyg/toolbars', function($toolbars) {
  $toolbars_data = json_decode(get_abb_option('toolbars'));

  if (is_array($toolbars_data)) {
    foreach ($toolbars_data as $toolbar) {
      $toolbars[$toolbar->title] = array();
      $toolbars[$toolbar->title][1] = $toolbar->data;
    }
  }

	unset($toolbars['Basic']);

	return $toolbars;
});

add_filter('tiny_mce_before_init', function($init_array) {
  if (get_abb_option('paste_as_text')) $init_array['paste_as_text'] = 'true';
  if (get_abb_option('style_formats')) $init_array['style_formats'] = get_abb_option('style_formats');
  if (get_abb_option('block_formats')) $init_array['block_formats'] = get_abb_option('block_formats');

  return $init_array;
});
?>

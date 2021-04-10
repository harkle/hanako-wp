<?php
  //Body class
  add_filter('body_class', function($classes) {
    global $post;

    if ($post->ID == 1) $classes[] = 'myClass';

    return $classes;
  });

  //menu
  wp_nav_menu(array(
    'container' => false,
    'menu' => 'main',
    'walker' => new Simple_Menu,
    'depth' => 1,
    'items_wrap' => '%3$s',
    'item_class' => ''
  ));

  wp_nav_menu(array(
    'container' => false,
    'menu' => 'main',
    'walker' => new WP_Bootstrap_Navwalker,
    'menu_class' => 'nav navbar-nav',
    'depth' => 1
  ));

  //ACF Repeater
  if (have_rows('parent_field')) {
    while (have_rows('parent_field')) {
      the_row();

      $value = get_sub_field('sub_field');
    }
  }

  //ACF Nested Repeater
  if (have_rows('parent_repeater')) {
    while (have_rows('parent_repeater')) {
      the_row();

  		$parent_title = get_sub_field('parent_title');

  		if (have_rows('child_repeater')) {
  		  while (have_rows('child_repeater')) {
          the_row();

  				$child_title = get_sub_field('child_title');
        }
      }
    }
  }

  // ACF Flexible
  if (have_rows('parent_field')) {
    while (have_rows('parent_field')) {
      the_row();

      $layout = get_row_layout();

  		if ($layout === 'layout_1') {
  			$value = get_sub_field('sub_field_1');
  		} elseif ($layout === 'layout_2') {
  			$value = get_sub_field('sub_field_2');
  		}
    }
  }
?>

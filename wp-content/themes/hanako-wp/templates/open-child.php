<?php
/*
Template Name: Spécial - Ouvrir le 1e enfant
*/
if (have_posts()) {
  while (have_posts()) {
    the_post();

    $children = get_posts([
      'post_type'   => 'page',
      'post_parent' => $post->ID,
      'orderby'     => 'menu_order',
      'order'       => 'asc',
    ]);

    wp_redirect(get_the_permalink($children[0]->ID));
  }
}
?>

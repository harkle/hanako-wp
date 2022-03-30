<?php
/*
 * Populate Timber Context
 */
add_filter('timber/context', function ($context) {
  $context['menus'] = [
    'main' => new \Timber\Menu('main')
  ];

  return $context;
});

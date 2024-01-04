<?php
/*
 * Populate Timber Context
 */
add_filter('timber/context', function ($context) {
  $context['menus'] = [
    'main' => Timber::get_menu('main')
  ];

  return $context;
});

/*
 * Add some useful functions
 */
add_filter('timber/twig/functions', function ($functions) {
  /*$functions['function_name'] = [
    'callable' => 'function_name',
  ];*/

  return $functions;
});

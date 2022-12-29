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

/*
 * Add some useful functions
 */
add_filter('timber/twig', function ($twig) {
  //$twig->addFunction(new Timber\Twig_Function('the_permalink', 'get_the_permalink'));

  return $twig;
});

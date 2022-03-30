<?php
$context = Timber::context();

$context['post'] = new Timber\Post();

Timber::render('pages/templates/page/index.twig', $context);
?>

<?php
$context = Timber::context();

$context['post'] = new Timber\Post();

Timber::render('pages/page/index.twig', $context);
?>

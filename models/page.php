<?php
$context = Timber::context();

$context['post'] = new Timber\Post();

Timber::render('pages/root/page/index.twig', $context);
?>

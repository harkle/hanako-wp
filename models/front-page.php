<?php
$context = Timber::context();

$context['post'] = new Timber\Post();

Timber::render('pages/templates/front-page/index.twig', $context);

<?php
$context = Timber::context();

$context['post'] = new Timber\Post();

Timber::render('pages/front-page/index.twig', $context);

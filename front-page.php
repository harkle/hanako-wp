<?php
$context = Timber::context();

$context['post'] = new Timber\Post();

Timber::render('pages/defaults/front-page/index.twig', $context);

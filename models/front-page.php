<?php
$context = Timber::context();

$context['post'] = new Timber\Post();

Timber::render('pages/root/front-page/index.twig', $context);

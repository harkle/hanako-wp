<?php
$context = Timber::context();

$context['post'] = new Timber\Post();

Timber::render('pages/templates/404/index.twig', $context);

<?php
$context = Timber::context();

$context['post'] = new Timber\Post();

Timber::render('pages/root/404/index.twig', $context);

<?php
$context = Timber::context();

$context['post'] = Timber::get_post();

Timber::render('pages/404/index.twig', $context);

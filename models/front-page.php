<?php
$context = Timber::context();

$context['post'] = Timber::get_post();

Timber::render('pages/front-page/index.twig', $context);

<?php
$context = Timber::context();

$context['post'] = Timber::get_post();

Timber::render('pages/single-post/index.twig', $context);

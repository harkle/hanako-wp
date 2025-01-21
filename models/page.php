<?php
$context = Timber::context();

$context['post'] = Timber::get_post();

Timber::render('pages/page/index.twig', $context);

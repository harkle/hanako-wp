<?php
$context = Timber::context();

$context['posts'] = Timber::get_posts();

Timber::render('pages/archive-post/index.twig', $context);

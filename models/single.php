<?php
$context = Timber::context();

$context['post'] = new Timber\Post();

Timber::render('pages/singles/' . get_post_type() .'.twig', $context);

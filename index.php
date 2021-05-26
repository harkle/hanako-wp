<?php
$context = Timber::context();

$context['posts'] = Timber::get_posts();

Timber::render('archives/' . get_post_type() .'.twig', $context);

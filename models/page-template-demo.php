<?php
/*
Template Name: Demo
*/

$context = Timber::context();

$context['post'] = Timber::get_post();

Timber::render('pages/template-demo/index.twig', $context);

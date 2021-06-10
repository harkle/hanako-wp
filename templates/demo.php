<?php
/*
Template Name: Demo
*/

$context = Timber::context();

$context['post'] = new Timber\Post();

Timber::render('pages/templates/demo.twig', $context);

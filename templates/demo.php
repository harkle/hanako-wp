<?php
/*
Template Name: Demo
*/

$context = Timber::context();

$context['post'] = new Timber\Post();

Timber::render('templates/demo.twig', $context);

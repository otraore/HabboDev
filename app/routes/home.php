<?php

$app->get('/', function() use ($app){
    $app->render('home.twig');
})->name('home');
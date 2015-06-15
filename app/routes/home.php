<?php

$app->get('/', function() use ($app){
    echo $app->lang;
    $app->render('home.twig');
})->name('home');
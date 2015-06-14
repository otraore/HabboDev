<?php

$app->get('/settings', $authenticated(), function() use ($app){
   $app->render('user/settings.twig');
})->name('settings');
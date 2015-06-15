<?php

$app->get('/en', function() use($app){
    $app->setCookie($app->config->get('lang.session'), 'en', time() + (10 * 365 * 24 * 60 * 60));
    $app->redirect($app->urlFor('home'));
});
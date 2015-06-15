<?php

$app->get('/es', function() use($app){
    $app->setCookie($app->config->get('lang.session'), 'es', time() + (10 * 365 * 24 * 60 * 60));
    $app->redirect($app->urlFor('home'));
});
<?php

$app->get('/check', function () use ($app) {
    $app->response->header('Content-Type', 'application/json');
    $type = strtolower(trim($app->request->get('type')));
    $value = trim($app->request->get('value'));
    $output = ['exists' => false];

    if (isset($type) && isset($value) && !empty($type) && !empty($value)) {
        if (in_array($type, ['username', 'email'])) {

            switch ($type) {
                case 'username':
                    $user = (bool)$app->user->where('username', $value)->count();
                    $output['exists'] = $user ? true : false;
                    break;
                case 'email':
                    $email = (bool)$app->user->where('email', $value)->count();
                    $output['exists'] = $email ? true : false;
                    break;
            }
        }
    } else {
        $output['error'] = 'empty arguments';
    }

    echo json_encode($output);

});
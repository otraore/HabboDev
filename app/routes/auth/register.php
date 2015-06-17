<?php

use HabboDev\User\UserPermission;

$app->get('/register', $guest(), function() use ($app){
   $app->render('/auth/register.twig');
})->name('register');

$app->post('/register', $guest(), function() use ($app){

    $request = $app->request();

    $email = $request->post('email');
    $username = $request->post('username');
    $password = $request->post('password');
    $passwordConfirm = $request->post('password_confirm');
    $recaptcha = $request->post('g-recaptcha-response');

    $v = $app->validation;

    $v->validate([
        'email' => [$email, 'required|email|uniqueEmail'],
        'username' => [$username, 'required|alnumDash|max(20)|uniqueUsername'],
        'password' => [$password, 'required|min(6)'],
        'password_confirm' => [$passwordConfirm, 'required|matches(password'],
        'recaptcha' => [$recaptcha, 'verifyCaptcha']
    ]);

    if($v->passes()){
        $identifier = $app->randomlib->generateString(128);

       $user = $app->user->create([
            'email' => $email,
            'username' => $username,
            'password' => $app->hash->password($password),
            'active' => false,
            'active_hash' => $app->hash->hash($identifier)
        ]);

        $user->permissions()->create(UserPermission::$defaults);

        $app->mail->send('email/auth/registered.twig', ['user' => $user, 'identifier' => $identifier], function($message) use($user){
           $message->to($user->email, $user->username);
            $message->subject("Welcome to HabboDev.");
        });

        $app->flash('global', 'You have been registered');
        $app->response->redirect($app->urlFor('home'));
    }

    $app->render('auth/register.twig', [
        'errors' => $v->errors(),
        'request' => $request
    ]);


})->name('register.post');
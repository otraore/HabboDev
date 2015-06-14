<?php

$app->get('/change-password', $authenticated(), function() use($app){
    $app->render('auth/password/change.twig');
})->name('password.change');

$app->post('/change-password', $authenticated(), function() use ($app){

    $request = $app->request;
    $passwordOld = $request->post('password_old');
    $password = $request->post('password');
    $passwordConfirm = $request->post('password_confirm');

    $v = $app->validation;

    $v->validate([
        'password_old' => [$passwordOld, 'required|matchesCurrentPassword'],
        'password' => [$password, 'required|min(6)|differentPassword'],
        'password_confirm' => [$passwordConfirm, 'required|matches(password)']
    ]);

    $user = $app->auth;
    if($v->passes()){
        $user->update([
           'password' => $app->hash->password($password)
        ]);

        $app->mail->send('email/auth/password/changed.twig', [], function($message) use($user) {
            $message->to($user->email, $user->username);
            $message->subject('You changed your password');
        });

        unset($_SESSION[$app->config->get('auth.session')]);

        if($app->getCookie($app->config->get('auth.remember'))){
            $app->auth->removeRememberCredentials();
            $app->deleteCookie($app->config->get('auth.remember'));
        }

        $app->flash('global', "You changed your password, please login with your new password");
        $app->response->redirect($app->urlFor('home'));
    }

    $app->render('auth/password/change.twig', [
        'errors' => $v->errors()
    ]);

})->name('password.change.post');
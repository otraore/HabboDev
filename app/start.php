<?php

use HabboDev\MiddleWare\CsrfMiddleware;
use HabboDev\MiddleWare\LangMiddleware;
use Slim\Slim;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

use Noodlehaus\Config;
use RandomLib\Factory as RandomLib;

use HabboDev\User\User;
use HabboDev\Mail\Mailer;
use HabboDev\Helpers\Hash;
use HabboDev\Validation\Validator;

use HabboDev\MiddleWare\BeforeMiddleWare;


session_cache_limiter(false);
session_start();

ini_set('display_errors', 'On');

define('INC_ROOT', dirname(__DIR__));

require INC_ROOT . '/vendor/autoload.php';

$app = new Slim([
    'mode' => file_get_contents(INC_ROOT . '/mode.php'),
    'view' => new Twig(),
    'templates.path' => INC_ROOT . '/app/views'
]);

$app->add(new BeforeMiddleWare);
$app->add(new CsrfMiddleware);
$app->add(new LangMiddleware());
$app->configureMode($app->config('mode'), function() use ($app){
    $app->config = Config::load(INC_ROOT . "/app/config/{$app->mode}.php");
});

require 'database.php';
require 'filters.php';
require 'routes.php';

$app->auth = false;

$app->container->set('user', function(){
    return new User();
});

$app->container->singleton('hash', function() use ($app){
    return new Hash($app->config);
});

$app->container->singleton('validation', function() use ($app){
    return new Validator($app->user, $app->hash, $app->auth);
});

$app->container->singleton('mail', function() use ($app){
    $mailer = new PHPMailer(true);
    $mailer->Host = $app->config->get('mail.host');
    $mailer->SMTPAuth = $app->config->get('mail.smtp_auth');
    $mailer->SMTPSecure = $app->config->get('mail.smtp_secure');
    $mailer->SMTPDebug = 1;
    $mailer->Port = $app->config->get('mail.port');
    $mailer->Username = $app->config->get('mail.username');
    $mailer->Password = $app->config->get('mail.password');
    $mailer->isHTML($app->config->get('mail.host'));
    $mailer->setFrom('no-reply@habbodev.com', 'no-reply');
    $mailer->XMailer = 'HabboDev Mailer';
    return new Mailer($app->view, $mailer);
});

$app->container->singleton('randomlib', function() {
    $factory = new RandomLib;
    return $factory->getMediumStrengthGenerator();
});

$app->container->set('lang', function() use ($app){
    if(empty($app->getCookie($app->config->get('lang.session')))){
        $app->setCookie($app->config->get('lang.session'), $app->config->get('lang.default'), time() + (10 * 365 * 24 * 60 * 60));
        return $app->config->get('lang.default');
    } else{
       return $app->getCookie($app->config->get('lang.session'));
    }
});

$view = $app->view();

$view->parserOptions = [
  'debug' => $app->config->get('twig.debug')
];

$view->parserExtensions = [
  new TwigExtension()
];

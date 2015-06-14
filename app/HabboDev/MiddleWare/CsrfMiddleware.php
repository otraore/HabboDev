<?php

namespace HabboDev\MiddleWare;

use Exception;
use Slim\Middleware;

class CsrfMiddleware extends Middleware
{
    protected  $key;
    public function call()
    {
        $this->key = $this->app->config->get('csrf.key');
        $this->app->hook('slim.before', [$this, 'check']);
        $this->next->call();
    }

    public function check(){
        if(!isset($_SESSION[$this->key])){
            $_SESSION[$this->key] = $this->app->hash->hash(
                $this->app->randomlib->generateString(128)
            );
        }
        $token = $_SESSION[$this->key];

        if(in_array($this->app->request()->getMethod(), ['POST', 'PUT', 'DELETE'])){
            $submittedToken = $this->app->request()->post($this->key) ?: '';
            if(!$this->app->hash->hashCheck($token, $submittedToken)) {
                throw new Exception('CSRF token mismatch');
            }
        }

        $this->app->view->appendData([
            'csrf_key' => $this->key,
            'csrf_token' => $token
        ]);
    }
}
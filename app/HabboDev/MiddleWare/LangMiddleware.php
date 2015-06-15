<?php

namespace HabboDev\MiddleWare;

use Slim\Middleware;

class LangMiddleware extends Middleware
{
    protected $lang;

    public function call()
    {
        $this->app->hook('slim.before', [$this, 'run']);

        $this->lang = require( INC_ROOT . "/app/HabboDev/MiddleWare/lang/{$this->app->lang}.php");

        $this->next->call();

    }

    public function run()
    {
        $this->app->view->appendData([
            'lang' => $this->lang
        ]);
    }
}
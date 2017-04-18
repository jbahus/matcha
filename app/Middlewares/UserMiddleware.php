<?php

namespace App\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;

class UserMiddleware {

    private $twig;

    public  function  __construct(\Twig_Environment $twig){
        $this->twig = $twig;
    }

    public  function __invoke(Request $request, Response $response, $next){
        $this->twig->addGlobal('user', isset($_SESSION['user']) ? $_SESSION['user'] : []);
        $this->twig->addGlobal('pictures', isset($_SESSION['pictures']) ? $_SESSION['pictures'] : []);
        return $next($request, $response);
    }
}
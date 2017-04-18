<?php

namespace App\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;

class TagsMiddleware {

    private $twig;

    public  function  __construct(\Twig_Environment $twig){
        $this->twig = $twig;
    }

    public  function __invoke(Request $request, Response $response, $next){
//        foreach ($_SESSION['tags'] as $tag) {
//            $this->twig->addGlobal($tag, "OK");
//        }
        return $next($request, $response);
    }
}
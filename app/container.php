<?php
$container = $app->getContainer();

$container['debug'] = function (){
    return false;
};

$container['view'] = function ($container) {
    $dir = dirname(__DIR__);
    $view = new \Slim\Views\Twig($dir . '/app/views', [
//        'cache' => $container->debug ? false : $dir . '/tmp/cache',
        'debug' => $container->debug
    ]);
    if ($container->debug){
        $view->addExtension(new Twig_Extension_Debug());
    }
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    return $view;
};

$container['pdo'] = function(){
    $pdo = new PDO('mysql:host=localhost;dbname=matcha;charset=utf8', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
};
<?php

require "../vendor/autoload.php";

session_start();

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

require('../app/container.php');

$container = $app->getContainer();

$app->add(new \app\Middlewares\FlashMiddleware($container->view->getEnvironment()));
$app->add(new \app\Middlewares\OldMiddleware($container->view->getEnvironment()));
$app->add(new \app\Middlewares\UserMiddleware($container->view->getEnvironment()));
$app->add(new \app\Middlewares\TagsMiddleware($container->view->getEnvironment()));

$app->get('/', \app\Controllers\PagesController::class . ":index")->setName('root');
$app->get('/home', \app\Controllers\PagesController::class . ":home")->setName('home');
$app->post('/home', \app\Controllers\PagesController::class . ":search")->setName('search');

$app->get('/login', \app\Controllers\PagesController::class . ":loginMain")->setName('loginMain');
$app->post('/login', \app\Controllers\PagesController::class . ":login")->setName('login');

$app->get('/logout', \app\Controllers\PagesController::class . ":logout")->setName('logout');

$app->get('/register', \app\Controllers\PagesController::class . ":register")->setName('register');
$app->post('/register/add', \app\Controllers\PagesController::class . ":registerAdd")->setName('register/add');
$app->get('/register/validation/{validation}/{name}', \app\Controllers\PagesController::class . ":validation")->setName('validation');

$app->get('/contact', \app\Controllers\PagesController::class . ":getContact")->setName('contact');

$app->get('/account', \app\Controllers\PagesController::class . ":account")->setName('account');
$app->post('/account/add_picture', \app\Controllers\PagesController::class . ":addPicture")->setName('add_picture');
$app->post('/account/add_tags', \app\Controllers\PagesController::class . ":addTags")->setName('add_tags');
$app->get('/modif_account', \app\Controllers\PagesController::class . ":modif_account")->setName('modif_account');
$app->post('/modif_account/save', \app\Controllers\PagesController::class . ":saveAccount")->setName('modif_account/save');

$app->get('/account/delete/{id}', \app\Controllers\PagesController::class . ':delete_picture')->setName('delete_picture');
$app->get('/account/profile/{id}', \app\Controllers\PagesController::class . ':profile_picture')->setName('profile_picture');

$app->get('/view', \app\Controllers\PagesController::class . ':view_profile')->setName('view_profile');

$app->post('/home/match', \app\Controllers\PagesController::class . ':matched')->setName('match');

$app->run();

<?php

namespace App\Controllers;

use App\Model\Users;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PagesController extends \app\Controllers\Controller
{

    public function index(RequestInterface $request, ResponseInterface $response)
    {
        if (isset($_SESSION['username']))
            return $this->redirect($response, 'home');
        else {
            return $this->redirect($response, 'login');
        }
    }

    public  function logout(RequestInterface $request, ResponseInterface $response){
        unset($_SESSION['user']);
        unset($_SESSION['pictures']);
        unset($_SESSION['tags']);
        return $this->redirect($response, 'login');
    }

    public function home(RequestInterface $request, ResponseInterface $response){
        $view = $this->__get('view');
        $twig = $view->getEnvironment();
        $twig->addGlobal("match", $this->matchUsers());
        $this->render($response, 'pages/home.twig');
    }

    public function search(RequestInterface $request, ResponseInterface $response){
        $params = $request->getParams();
        $users = $this->matchUsers();
        $view = $this->__get('view');
        $twig = $view->getEnvironment();
        $pdo = $this->__get('pdo');
        $sql = $pdo->query("SELECT * from scores");
        $sql->execute();
        $scores = $sql->fetchAll();
        if ($params['distance'])
            $distance = $params['distance'];
        else
            $distance = 50;
        foreach ($users as $key => $user){
            foreach ($scores as $score) {
                if ($user['id'] == $score['id_user']) {
                    if ($this->distance($_SESSION['user']['lat'], $_SESSION['user']['lon'], $user['lat'], $user['lon'], 'K') > $distance || !$this->ageMatch($user['age'], $params['age_min'], $params['age_max'], $params['age_operator']) || !$this->scoreMatch($score['score'], $params['score_min'], $params['score_max'], $params['score_operator']) || !$this->tagsMatch($params['tag'], $user['id'], $params['tag_operator'])) {
                        unset($users[$key]);

                    } else {
                        $count[] = $this->tagsCount($user['id']);
                        $sortScore[] = $score['score'];
                    }
                }
            }
        }
        $users = array_values($users);
        array_multisort($count, SORT_DESC, $sortScore, SORT_DESC, $users);
        $twig->addGlobal("match", $users);
        $this->render($response, 'pages/home.twig');
    }

    function debug_to_console( $data ){
        $output = $data;
        if ( is_array( $output ) )
            $output = implode( ',', $output);

        echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
    }

    public function loginMain(RequestInterface $request, ResponseInterface $response){
        if (isset($_SESSION['user']))
            return $this->redirect($response, 'home');
        $this->render($response, 'pages/login.twig');
    }

    public function scoreNew($user_id)
    {
        $pdo = $this->__get('pdo');
        $sql = $pdo->query('SELECT * FROM scores WHERE id_user = '.$user_id);
        $tmp = $sql->fetch();
        if (!isset($tmp) || empty($tmp)){
            $sql = $pdo->prepare('INSERT INTO scores (`id_user`) VALUES (:user_id)');
            $sql->bindParam(':user_id', $user_id);
            $sql->execute();
        }
    }

    public function login(RequestInterface $request, ResponseInterface $response){
        $params = $request->getParams();
        if (!$params['username']) {
            $errors['username'] = 'Veuillez entrer votre nom d\'utilisateur';
        }
        if (!$params['password']) {
            $errors['password'] = 'Veuillez entrer votre mot de passe';
        }
        if (empty($errors)){
            $user = new \app\Model\Users($this->__get('pdo'), []);
            $info = $user->findByUsername($params['username']);
            $crypted = hash('whirlpool', $params['password']);
            if ($info['username'] == $params['username'] && $info['password'] == $crypted && $info['active'] == 1) {
                $_SESSION['user'] = $user->findByUsername($params['username']);
                $_SESSION['pictures'] = $this->get_dir_filename('pictures/'.$info['username']);
                $this->save_pos($params['lat'], $params['lon'], $params['username']);
                $this->scoreNew($_SESSION['user']['id']);
                unset($_SESSION['user']);
                $_SESSION['user'] = $user->findByUsername($params['username']);
                return $this->redirect($response, 'home');
            }
        }
        $this->render($response, 'pages/login.twig');
    }

    public function getContact(RequestInterface $request, ResponseInterface $response)
    {
        $this->render($response, 'pages/contact.twig');
    }

    public function register(RequestInterface $request, ResponseInterface $response)
    {
        $this->render($response, 'pages/register.twig');
    }

    public function registerAdd(RequestInterface $request, ResponseInterface $response)
    {
        $params = $request->getParams();
        if (!$params['email']) {
            $errors['email'] = 'Votre email n\'est pas valide';
        }
        if (!$params['firstname']) {
            $errors['firstname'] = 'Veuillez entrer votre prenom';
        }
        if (!$params['lastname']) {
            $errors['lastname'] = 'Veuillez entrer votre nom de famille';
        }
        if (!$params['username']) {
            $errors['username'] = 'Veuillez entrer votre nom d\'utilisateur';
        }
        if (!$params['password']) {
            $errors['password'] = 'Veuillez entrer votre mot de passe';
        }
        if (!$params['password_confirm']) {
            $errors['password_confirm'] = 'Veuillez confirmer votre mot de passe';
        }
        if (empty($errors)) {
            $pdo = $this->__get('pdo');
            $user = new \app\Model\Users($pdo, $params);
            if (!$user->findByUsername($params['username'])&& !$user->findByEmail($params['email']) && $params['password'] === $params['password_confirm']) {
                $user->setPassword(hash('whirlpool', $params['password']));
                if ($user->save()){
                    if(($flash['type'] = $this->sendMail($params)) == 'warning')
                        $flash['msg'] = 'Mauvais email';
                    else
                        $flash['msg'] = 'Bravo ! Votre compte à bien été créé. Un email de confirmation vous à été envoyé.';
                }
                else{
                    $flash['type'] = 'danger';
                    $flash['msg'] = 'Oops, quelque chose n\'a pas marché';
                }
            }
            else {
                $flash['type'] = 'warning';
                $flash['msg'] = "L'utilisateur existe déjà." ;
            }
        }
        else{
            $flash['type'] = 'warning';
            $flash['msg'] = 'Il manque quelque chose.';
        }
        $this->flash($flash['msg'], $flash['type']);
        $this->flash($errors, 'errors');
        return $this->redirect($response, 'login');
    }

    public function sendMail($param){
        $link = hash('whirlpool', 'matchajbahus'.$param['username']);
        $to = $param['email'];
        $subject = "Création de compte";
        $message = "Validez votre compte: localhost:4200/register/validation/" . $link ."/" . $param['username'];
        $message = wordwrap($message, 70, "\r\n");
        if (mail($to, $subject, $message))
            return 'success';
        else
            return 'warning';
    }

    public function validation(RequestInterface $request, ResponseInterface $response, $args){
        $user = new \app\Model\Users($this->__get('pdo'), []);
        if ($args['validation'] === hash('whirlpool', 'matchajbahus'.$args['name']))
            $user->alter('active', 1, 'username', $args['name']);
        return $this->redirect($response, 'login');
    }

    public function account(RequestInterface $request, ResponseInterface $response){
        $this->globalTags();
        return $this->render($response, 'pages/account.twig');
    }

    public function modif_account(RequestInterface $request, ResponseInterface $response){
        return $this->render($response, 'pages/modif_account.twig');
    }

    public function addPicture(RequestInterface $request, ResponseInterface $response)
    {
        $uploaddir = 'pictures/' . $_SESSION['user']['username'] . '/';
        if (!file_exists($uploaddir)){
            mkdir($uploaddir);
        }
        $lenght = count($this->get_dir_filename($uploaddir));
        if ($lenght < 5)
            $name = $lenght + 1;
        else{
            $this->flash("Vous avez déjà 5 photos. Supprimez-en une avant de pouvoir en ajouter.", "warning");
            return $this->redirect($response, 'account');
        }
        if (move_uploaded_file($_FILES['picture']['tmp_name'], $uploaddir.$name)){
            $this->flash("Photo ajouté", "success");
        }
        else
            $this->flash("erreur", "warning");
        $_SESSION['pictures'] = $this->get_dir_filename("pictures/".$_SESSION['user']['username'].'/');
        $this->account_check($_SESSION['user']);
        return $this->redirect($response, 'account');
    }

    public function addTags(RequestInterface $request, ResponseInterface $response)
    {
        $params = $request->getParams();
        $pdo = $this->__get('pdo');
        $tags = new \app\Model\Tags($pdo);
        if ($tags->findByUserId($_SESSION['user']['id']))
            $tags->delete($_SESSION['user']['id']);
        $tags->setUserId($_SESSION['user']['id']);
        foreach ($params['tag'] as $tag)
        {
            $tags->setName($tag);
            $ret = $tags->save();
            if (!$ret){
                $this->flash('error', 'warning');
                return $this->redirect($response, 'account');
            }
        }
        $this->account_check($_SESSION['user']);
        $this->flash('Tags modifiés', 'success');
        $sql = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $sql->bindParam(":id", $_SESSION['user']['id']);
        $sql->execute();
        unset($_SESSION['user']);
        $_SESSION['user'] = $sql->fetch();
        return $this->redirect($response, 'account');
    }

    public function saveAccount(RequestInterface $request, ResponseInterface $response){
        $params = $request->getParams();
        $user = new \app\Model\Users($this->__get('pdo'), $params);
        if (empty($params['username']) || empty($params['email'])){
            $this->flash("Votre nom d'utilisateur et email ne peuvent être vide", 'warning');
        }
        else if ($user->modif()) {
            $this->flash("Vos informations ont bien été modifiées", "success");
            $params['password'] = $_SESSION['password'];
            unset($_SESSION['user']);
            $_SESSION['user'] = $user->findByUsername($params['username']);
            $this->account_check($_SESSION['user']);
            $_SESSION['user'] = $user->findByUsername($params['username']);
        }
        else
            $this->flash("Quelque chose ne va pas !", "warning");
        return $this->redirect($response, 'account');
    }

    public function delete_picture(RequestInterface $request, ResponseInterface $response, $args){
        if (unlink("pictures/".$_SESSION['user']['username'].'/'.$args['id'])){
            $this->flash('Photo supprimée !', 'success');
        }
        else
            $this->flash('Impossible de supprimer cette photo', 'warning');
        $_SESSION['pictures'] = $this->get_dir_filename("pictures/".$_SESSION['user']['username'].'/');
        return $this->redirect($response, 'account');
    }

    public function profile_picture(RequestInterface $request, ResponseInterface $response, $args){
        $this->rename_file($args['id'], '1', 'pictures/'.$_SESSION['user']['username'].'/');
        return $this->redirect($response, 'account');
    }

    public function view_profile(RequestInterface $request, ResponseInterface $response, $args)
    {
        $view = $this->__get('view');
        $twig = $view->getEnvironment();
        $user = new \app\Model\Users($this->__get('pdo'));
        $tmp = $user->findById($_GET['id']);
        $ret['account'] = $tmp;
        $p_count = $this->get_dir_filename('pictures/'.$_GET['id']);
        $ret['picture'] = count($p_count);
        $c_tags = new \app\Model\Tags($this->__get('pdo'));
        $tags = $c_tags->findByUserId($_GET['id']);
        $ret['tags'] = $tags;
        $twig->addGlobal('flash', $ret);
        return $this->render($response, 'pages/view_profile.twig');
    }

    public function matched(RequestInterface $request, ResponseInterface $response, $args){
        $params = $request->getParams();
        $pdo = $this->__get('pdo');
        $sql = $pdo->query("SELECT * FROM matching WHERE id1 = ".$_SESSION['user']['id']." AND id2 = ".$params['id']);
        $ret = $sql->execute();
        if ($ret)
            $this->flash('Vous avez deja matché cet utilisateur', 'warning');
        else {
            $sql = $pdo->query("INSERT INTO matching(id1, id2) VALUES (" . $_SESSION['user']['id'] . ", " . $params['id'] . ")");
            $sql->execute();
            $this->flash('Vous avez matché un nouvel utilisateur', 'success');
        }
        return $this->redirect($response, 'home');
    }
}
<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;

class Controller {

    private $container;

    public function __construct($container){
        $this->container = $container;
    }

    public function render_params(ResponseInterface $response, $file, $params){
        $this->container->view->render($response, $file, $params);
    }

    public function render(ResponseInterface $response, $file){
        $this->container->view->render($response, $file);
    }

    public function redirect(ResponseInterface $response, $name)
    {
        return $response->withStatus(302)->withHeader('Location', $this->router->pathFor($name));
    }

    public function __get($name){
        return $this->container->get($name);
    }

    public function flash($message, $type){
        if (!isset($_SESSION['flash'])){
            $_SESSION['flash'] = [];
        }
        return $_SESSION['flash'][$type] = $message;
    }

    public function get_dir_filename($path){
        $arr = scandir($path);
        unset($arr[0]);
        unset($arr[1]);
        return $arr;
    }

    public function rename_file($old, $new, $path){
        $files = $this->get_dir_filename($path);
        foreach ($files as $file)
        {
            if ($new === $file) {
                $tmp = rename($path.$file, $path."tmp");
            }
        }
        if ($tmp) {
            rename($path . $old, $path . $new);
            rename($path . 'tmp', $path . $old);
        }
        else
            rename($path . $old, $path . $new);
        $this->flash('Votre photo de profil a bien été modifiée', 'success');
    }

    public function account_check($user){
        $pdo = $this->__get('pdo');
        if (isset($user['firstname']) && isset($user['lastname']) && isset($user['age']) && $user['gender'] !== 0 && !empty($user['bio']) && !empty($user['firstname']) && !empty($user['lastname']) && !empty($user['age']) && !empty($user['bio']) && count($this->get_dir_filename("pictures/".$user['username']) >= 1)){
            $sql = $pdo->prepare("SELECT * FROM tags WHERE id_user = :id");
            $sql->bindParam(':id', $user['id']);
            $sql->execute();
            if ($sql->fetchAll()) {
                $sql = $pdo->query("SELECT score FROM scores WHERE id_user = ".$_SESSION['user']['id']);
                $score = $sql->fetch();
                if ($score['score'] == 0){
                    $pdo->query('UPDATE scores SET score = 10 WHERE id_user = '.$_SESSION['user']['id']);
                }
                $sql = $pdo->prepare('UPDATE users SET hidden = 1 WHERE id = :id');
                $sql->bindParam(':id', $user['id']);
                return $sql->execute();
            }
        }
        else{
            $sql = $pdo->prepare('UPDATE users SET hidden = 0 WHERE id = :id');
            $sql->bindParam(':id', $user['id']);
            return $sql->execute();
        }
    }

    public function save_pos($lat, $lon, $username){
        $sql = $this->__get('pdo')->prepare("UPDATE users SET `lat` = :lat, `lon` = :lon WHERE username = :username");
        $sql->bindParam(':lon', $lon);
        $sql->bindParam(':lat', $lat);
        $sql->bindParam(':username', $username);
        return $sql->execute();
    }

    function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    function getBySexuality(){
        if ($_SESSION['user']['orientation'] == 1)
            $gender = $_SESSION['user']['gender'];
        else if ($_SESSION['user']['orientation'] == 2){
            if ($_SESSION['user']['gender'] == 1)
                $gender = 2;
            else
                $gender = 1;
        }
        if ($_SESSION['user']['orientation'] == 0) {
            if ($_SESSION['user']['gender'] == 1)
                $query = "SELECT * FROM users WHERE (((gender = 1 AND orientation != 2) OR (gender = 2 AND orientation != 1)) AND id !=".$_SESSION['user']['id']." )";
            elseif ($_SESSION['user']['gender'] == 2) {
                $query = "SELECT * FROM users WHERE (((gender = 1 AND orientation != 1) OR (gender = 2 AND orientation != 2)) AND id !=".$_SESSION['user']['id'].")";
            }
        }
        else
            $query = "SELECT * FROM users WHERE gender = " . $gender." AND id !=".$_SESSION['user']['id'];
        return $query;
    }

    public function tagsCount($id){
        $tagClass = new \app\Model\Tags($this->__get('pdo'));
        $user1 = $tagClass->findByUserId($_SESSION['user']['id']);
        $user2 = $tagClass->findByUserId($id);
        $ret = 0;
        foreach ($user1 as $tag1){
            foreach ($user2 as $tag2){
                if ($tag1['name'] === $tag2['name'])
                    $ret = $ret + 1;
            }
        }
        return $ret;
    }

    public function ageMatch($age, $min, $max, $operator){
        if (!$min)
            return 1;
        if ($operator == '>' && $age < $min)
            return (0);
        if ($operator == '<' && $age > $min)
            return (0);
        if ($operator == '<=>' && ($age < $min || $age > $max)){
            return 0;
        }
        return 1;
    }

    public function scoreMatch($score, $min, $max, $operator){
        if (!$min)
            return 1;
        if ($operator == '>' && $score <= $min)
            return 0;
        if ($operator == '<' && $score >= $min)
            return 0;
        if ($operator == '<=>' && ($score <= $min || $score >= $max)){
            return 0;
        }
        return 1;
    }

    public function matchUsers(){
        if ($_SESSION['user']['hidden'] == 0)
            return;
        $pdo = $this->__get('pdo');
        $sql = $pdo->query($this->getBySexuality());
        $sql->execute();
        $users = $sql->fetchAll();
        $sql = $pdo->query("SELECT * from scores");
        $sql->execute();
        $scores = $sql->fetchAll();
        foreach ($users as $key => $user ){
            foreach ($scores as $score) {
                if ($this->distance($_SESSION['user']['lat'], $_SESSION['user']['lon'], $user['lat'], $user['lon'], 'K') > 50)
                    unset($users[$key]);
                elseif ($user['id'] == $score['id_user']){
                    $count[] = $this->tagsCount($user['id']);
                    $sortScore[] = $score['score'];
                }
            }
        }
        $users = array_values($users);
        array_multisort($count, SORT_DESC, $sortScore, SORT_DESC, $users);
        return ($users);
    }

    public function tagsMatch($tags, $id, $operator){
        if (!$tags)
            return 1;
        $sql = $this->__get('pdo')->query("SELECT name FROM tags WHERE id_user = ".$id);
        $sql->execute();
        $userTags = $sql->fetchAll();
        $count = 2;
        foreach ($tags as $tag){
            if (!$count)
                return 0;
            $count = 0;
            $i = 0;
            while ($userTags[$i]['name']){
                if ($userTags[$i]['name'] == $tag && $operator == 'any')
                    return 1;
                if ($userTags[$i]['name'] == $tag && $operator == 'all')
                    $count = 1;
                $i++;
            }
        }
        if (!$count)
            return (0);
        return 1;
    }

    public function globalTags(){
        $pdo = $this->__get("pdo");
        $sql = $pdo->query("SELECT * FROM tags WHERE id_user = ".$_SESSION['user']['id']);
        $sql->execute();
        $tags = $sql->fetchAll();
        foreach ($tags as $tag){
            $twig = $this->container->view->getEnvironment();
            $twig->addGlobal($tag['name'], "OK");
        }
    }
}
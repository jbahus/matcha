<?php

namespace App\Model;


class Tags{
    private $user_id;
    private $name;
    private $pdo;

    public function __construct($pdo, $array = []){
        $this->pdo = $pdo;
        if (!empty($array)){
            if (array_key_exists("name", $array)){
                $this->name = $array['name'];
            }
            if (array_key_exists("user_id", $array)){
                $this->user_id = $array['user_id'];
            }
        }
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function save(){
        if (isset($this->name) && !empty($this->name) && isset($this->user_id) && !empty($this->user_id)){
            $sql = $this->pdo->prepare('INSERT INTO tags(`id_user`, `name`) VALUES (:user_id, :name)');
            $sql->bindParam(':user_id', $this->user_id);
            $sql->bindParam(':name', $this->name);
            return $sql->execute();
        }
        return false;
    }

    public function findByUserId($user_id){
        $sql = $this->pdo->prepare('SELECT * FROM tags WHERE id_user = :user_id');
        $sql->bindParam(':user_id', $user_id);
        $sql->execute();
        return $sql->fetchAll();
    }

    public function findByName($name){
        $sql = $this->pdo->prepare('SELECT * FROM tags WHERE name = :name');
        $sql->bindParam(':name', $name);
        $sql->execute();
        return $sql->fetch();
    }

    public function findAll(){
        $sql = $this->pdo->query('SELECT * FROM tags');
        $sql->execute();
        return $sql->fetchAll();
    }

    public function modif(){
        $sql = $this->pdo->prepare('UPDATE tags SET `name` = :name WHERE `id_user` = :user_id');
        $sql->bindParam(':name', $this->name);
        $sql->bindParam(':user_id', $this->user_id);
        return $sql->execute();
    }

    public function alter($id){

    }

    public function delete($id){
        $sql = $this->pdo->prepare('DELETE FROM tags WHERE id_user = :id');
        $sql->bindParam(':id', $id);
        return $sql->execute();
    }
}
<?php

namespace App\Model;

class Users{
    private $id;
    private $firstname;
    private $lastname;
    private $age;
    private $email;
    private $username;
    private $password;
    private $confirm;
    private $gender;
    private $active;
    private $hidden;
    private $created;
    private $updated;
    private $token;
    private $bio;
    private $orientation;
    private $pdo;

    public function __construct($pdo, $array = [])
    {
        $this->pdo = $pdo;
        if (!empty($array)){
            if (array_key_exists("id", $array)){
                $this->id = $array['id'];
            }
            if (array_key_exists("firstname", $array)){
                $this->firstname = $array['firstname'];
            }
            if (array_key_exists("lastname", $array)){
                $this->lastname = $array['lastname'];
            }
            if (array_key_exists("age", $array)){
                $this->age = $array['age'];
            }
            if (array_key_exists("email", $array)){
                $this->email = $array['email'];
            }
            if (array_key_exists("username", $array)){
                $this->username = $array['username'];
            }
            if (array_key_exists("password", $array)){
                $this->password = $array['password'];
            }
            if (array_key_exists("confirm", $array)){
                $this->confirm = $array['confirm'];
            }
            if (array_key_exists("gender", $array)){
                $this->gender = $array['gender'];
            }
            if (array_key_exists("active", $array)){
                $this->active = $array['active'];
            }
            if (array_key_exists("hidden", $array)){
                $this->hidden = $array['hidden'];
            }
            if (array_key_exists("created", $array)){
                $this->created = $array['created'];
            }
            if (array_key_exists("updated", $array)){
                $this->updated = $array['updated'];
            }
            if (array_key_exists("token", $array)){
                $this->token = $array['token'];
            }
            if (array_key_exists("bio", $array)){
                $this->bio = $array['bio'];
            }
            if (array_key_exists("orientation", $array)){
                $this->orientation = $array['orientation'];
            }
        }
    }


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function setAge($age)
    {
        $this->age = $age;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getConfirm()
    {
        return $this->confirm;
    }

    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getHidden()
    {
        return $this->hidden;
    }

    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getBio()
    {
        return $this->bio;
    }

    public function setBio($bio)
    {
        $this->bio = $bio;
    }

    public function getOrientation()
    {
        return $this->orientation;
    }

    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;
    }

    static public function encrypted($password){
        return whirlpool($password);
    }

    public function save(){
        if (isset($this->firstname) && !empty($this->firstname) &&
            isset($this->lastname) && !empty($this->lastname) &&
            isset($this->email) && !empty($this->email) &&
            isset($this->username) && !empty($this->username) &&
            isset($this->password) && !empty($this->password)){
            $sql = $this->pdo->prepare('INSERT INTO users(`firstname`, `lastname`, `email`, `username`, `password`) VALUES (:firstname, :lastname, :email, :username, :password)');
            $sql->bindParam(':firstname', $this->firstname);
            $sql->bindParam(':lastname', $this->lastname);
            $sql->bindParam(':email', $this->email);
            $sql->bindParam(':username', $this->username);
            $sql->bindParam(':password', $this->password);
            return $sql->execute();
        }
        return -2;
    }

    public function findById($id){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $sql->bindParam(':id', $id);
        $sql->execute();
        return $sql->fetch();
    }

    public function findByFirstname($firstname){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE firstname = :firstname');
        $sql->bindParam(':firstname', $firstname);
        $sql->execute();
        return $sql->fetch();
    }

    public function findByLastname($lastname){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE lastname = :lastname');
        $sql->bindParam(':lastname', $lastname);
        $sql->execute();
        return $sql->fetch();
    }

    public function findByAge($age){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE age = :age');
        $sql->bindParam(':age', $age);
        $sql->execute();
        return $sql->fetch();
    }

    public function findByEmail($email){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $sql->bindParam(':email', $email);
        $sql->execute();
        return $sql->fetch();
    }

    public function findByUsername($username){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
        $sql->bindParam(':username', $username);
        $sql->execute();
        return $sql->fetch();
    }

    public function findByPassword($password){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE password = :password');
        $sql->bindParam(':password', $password);
        $sql->execute();
        return $sql->fetch();
    }

    public function findByConfirm($confirm){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE confirm = :confirm');
        $sql->bindParam(':confirm', $confirm);
        $sql->execute();
        return $sql->fetch();
    }

    public function findByGender($gender_id){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE gender_id = :gender_id');
        $sql->bindParam(':gender_id', $gender_id);
        $sql->execute();
        return $sql->fetch();
    }

    public function findByActive($active){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE active = :active');
        $sql->bindParam(':active', $active);
        $sql->execute();
        return $sql->fetch();
    }

    public function findByActiveUsername($username, $active){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE active = :active');
        $sql->bindParam(':active', $active);
        $sql->execute();
        return $sql->fetch();
    }

    public function findByHidden($hidden){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE hidden = :hidden');
        $sql->bindParam(':hidden', $hidden);
        $sql->execute();
        return $sql->fetch();
    }

    public function findByCreated($created){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE created = :created');
        $sql->bindParam(':created', $created);
        $sql->execute();
        return $sql->fetch();
    }

    public function findByUpdated($updated){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE updated = :updated');
        $sql->bindParam(':updated', $updated);
        $sql->execute();
        return $sql->fetch();
    }

    public function findByToken($token){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE token = :token');
        $sql->bindParam(':token', $token);
        $sql->execute();
        return $sql->fetch();
    }

    public function findByBio($bio){
        $sql = $this->pdo->prepare('SELECT * FROM users WHERE bio = :bio');
        $sql->bindParam(':bio', $bio);
        $sql->execute();
        return $sql->fetch();
    }

    public function findAll(){
        $sql = $this->pdo->query('SELECT * FROM users');
        $sql->execute();
        return $sql->fetchAll();
    }

    public function modif(){
        if (!$this->age)
            $this->age = null;
        $sql = $this->pdo->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, age = :age, email = :email, username = :username, gender = :gender, orientation = :orientation, bio = :bio WHERE id = :id");
        $sql->bindParam(':firstname', $this->firstname);
        $sql->bindParam(':lastname', $this->lastname);
        $sql->bindParam(':age', $this->age);
        $sql->bindParam(':email', $this->email);
        $sql->bindParam(':username', $this->username);
        $sql->bindParam(':gender', $this->gender);
        $sql->bindParam(':orientation', $this->orientation);
        $sql->bindParam(':bio', $this->bio);
        $sql->bindParam(':id', $_SESSION['user']['id']);
        return $sql->execute();
    }

    public function alter($col1, $value, $col2, $id){
        $sql = $this->pdo->prepare('UPDATE users SET '.$col1.' = :valu WHERE '.$col2.' = :id');
        $sql->bindParam(':valu', $value);
        $sql->bindParam(':id', $id);
        return $sql->execute();
    }


    public function delete($id){
        $sql = $this->pdo->prepare('DELETE * FROM users WHERE id = :id');
        $sql->bindParam(':id', $id);
        return $sql->execute();
    }
}
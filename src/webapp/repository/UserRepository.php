<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Phone;
use tdt4237\webapp\models\Email;
use tdt4237\webapp\models\NullUser;
use tdt4237\webapp\models\User;
use tdt4237\webapp\Auth;

class UserRepository
{
    const INSERT_QUERY   = "INSERT INTO users(user, pass, first_name, last_name, phone, company, isadmin) VALUES('%s', '%s', '%s' , '%s' , '%s', '%s', '%s')";
    const UPDATE_QUERY   = "UPDATE users SET email='%s', first_name='%s', last_name='%s', isadmin='%s', phone ='%s' , company ='%s' WHERE id='%s'";
    const FIND_BY_NAME   = "SELECT * FROM users WHERE user='%s'";
    const DELETE_BY_NAME = "DELETE FROM users WHERE user='%s'";
    const SELECT_ALL     = "SELECT * FROM users";
    const FIND_FULL_NAME   = "SELECT * FROM users WHERE user='%s'";

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function makeUserFromRow(array $row)
    {
        $user = new User($row['user'], $row['pass'], $row['first_name'], $row['last_name'], $row['phone'], $row['company']);
        $user->setUserId($row['id']);
        $user->setFirstName($row['first_name']);
        $user->setLastName($row['last_name']);
        $user->setPhone($row['phone']);
        $user->setCompany($row['company']);
        $user->setIsAdmin($row['isadmin']);

        if (!empty($row['email'])) {
            $user->setEmail(new Email($row['email']));
        }

        if (!empty($row['phone'])) {
            $user->setPhone(new Phone($row['phone']));
        }

        return $user;
    }

    public function getNameByUsername($username)
    {
        $query = sprintf(self::FIND_FULL_NAME, $username);

        $result = $this->pdo->query($query, PDO::FETCH_ASSOC);
        $row = $result->fetch();
        $name = $row['first_name'] + " " + $row['last_name'];
        return $name;
    }

    public function findByUser($username)
    {
        $query  = sprintf(self::FIND_BY_NAME, $username);
        $result = $this->pdo->query($query, PDO::FETCH_ASSOC);
        $row = $result->fetch();
        
        if ($row === false) {
            return false;
        }

        return $this->makeUserFromRow($row);
    }

    public function deleteByUsername($username)
    {
         if (! Auth::isAdmin()) {
            $this->app->flash('info', "You must be administrator delete users.");
            $this->app->redirect('/');
            return;
        }
        return $this->pdo->exec(
            sprintf(self::DELETE_BY_NAME, $username)
        );
    }

    public function all()
    {
        $rows = $this->pdo->query(self::SELECT_ALL);
        
        if ($rows === false) {
            return [];
            throw new \Exception('PDO error in all()');
        }

        return array_map([$this, 'makeUserFromRow'], $rows->fetchAll());
    }

    public function save(User $user)
    {
        if ($user->getUserId() === null) {
            return $this->saveNewUser($user);
        }

        $this->saveExistingUser($user);
    }

    public function saveNewUser(User $user)
    {
        $query = sprintf(
            self::INSERT_QUERY, $user->getUsername(), $user->getHash(), $user->getFirstName(), $user->getLastName(), $user->getPhone(), $user->getCompany(), $user->isAdmin()
        );

        return $this->pdo->exec($query);
    }

    public function saveExistingUser(User $user)
    {
        $query = sprintf(
            self::UPDATE_QUERY, $user->getEmail(), $user->getFirstName(), $user->getLastName(), $user->isAdmin(), $user->getPhone(), $user->getCompany(), $user->getUserId()
        );

        return $this->pdo->exec($query);
    }

}

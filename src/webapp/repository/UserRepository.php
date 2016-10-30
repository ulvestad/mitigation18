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
    const INSERT_QUERY   = "INSERT INTO users(user, pass, first_name, last_name, phone, company, isadmin, lastLoginAttempt) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
    const UPDATE_QUERY   = "UPDATE users SET email = ?, first_name = ?, last_name = ?, isadmin = ?, phone = ? , company = ? WHERE id = ?";
    const FIND_BY_NAME   = "SELECT * FROM users WHERE user = ?";
    const DELETE_BY_NAME = "DELETE FROM users WHERE user = ?";
    const SELECT_ALL     = "SELECT * FROM users";
    const FIND_FULL_NAME   = "SELECT * FROM users WHERE user = ?";
    const UPDATE_LAST_LOGIN_ATTEMPT   = "UPDATE users SET lastLoginAttempt = ? WHERE user = ?";
    const GET_LAST_LOGIN_ATTEMPT   = "SELECT lastLoginAttempt FROM users WHERE user = ?";

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
        $query = self::FIND_FULL_NAME;
        $result = $this->pdo->prepare($query);
        $result->execute(array($username));
        $row = $result->fetch();
        $name = $row['first_name'] + " " + $row['last_name'];
        return $name;
    }

    public function findByUser($username)
    {
        $query = self::FIND_BY_NAME;
        $result = $this->pdo->prepare($query);
        $result->execute(array($username));
        $row = $result->fetch();

        if ($row === false) {
            return false;
        }

        return $this->makeUserFromRow($row);
    }

    public function deleteByUsername($username)
    {
        $query = self::DELETE_BY_NAME;
        $result = $this->pdo->prepare($query);
        return $result->execute(array($username));
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
        $query = self::INSERT_QUERY;
        $result = $this->pdo->prepare($query);
        $values = [$user->getUsername(), $user->getHash(), $user->getFirstName(), $user->getLastName(), $user->getPhone(), $user->getCompany(), $user->isAdmin(), 0];
        return $result->execute($values);
    }

    public function saveExistingUser(User $user)
    {
        $query = self::UPDATE_QUERY;
        $result = $this->pdo->prepare($query);
        $values = [$user->getEmail(), $user->getFirstName(), $user->getLastName(), $user->isAdmin(), $user->getPhone(), $user->getCompany(), $user->getUserId()];
        return $result->execute($values);
    }

    public function getLastLoginAttempt($username)
    {
        $query = self::GET_LAST_LOGIN_ATTEMPT;
        $result = $this->pdo->prepare($query);
        $result->execute(array($username));
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row['lastLoginAttempt'];

    }

    public function updateLastLoginAttempt($username)
    {
        $query = self::UPDATE_LAST_LOGIN_ATTEMPT;
        $result = $this->pdo->prepare($query);
        $d = new \DateTime();
        $d->add(new \DateInterval('PT10S'));
        $d = $d->format('U');
        $values = [$d, $username];
        return $result->execute($values);
    }

}

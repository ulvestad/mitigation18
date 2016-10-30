<?php

namespace tdt4237\webapp;

use Exception;
use tdt4237\webapp\Hash;
use tdt4237\webapp\repository\UserRepository;

class Auth
{

    /**
     * @var Hash
     */
    private $hash;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository, Hash $hash)
    {
        $this->userRepository = $userRepository;
        $this->hash           = $hash;
    }

    public function checkCredentials($username, $password)
    {
        $d = new \DateTime();
        $d = $d->format('U');
        print_r($this->userRepository->getLastLoginAttempt($username));
        if ($this->userRepository->getLastLoginAttempt($username) > $d) {
            return 2;
        }

        if ($this->hash->check($password, $user->getHash())) {
          return 0;
        } else {
          $this->userRepository->updateLastLoginAttempt($username);
          return 1;
        }

        $user = $this->userRepository->findByUser($username);
        if ($user === false) {
            return 1;
        }
    }

    /**
     * Check if is logged in.
     */
    public function check()
    {
        session_regenerate_id();
        return isset($_SESSION['user']);
    }

    public function getUsername() {
        if(isset($_SESSION['user'])){
        return $_SESSION['user'];
        }
    }

    /**
     * Check if the person is a guest.
     */
    public function guest()
    {
        return $this->check() === false;
    }

    /**
     * Get currently logged in user.
     */
    public function user()
    {
        if ($this->check()) {
            return $this->userRepository->findByUser($_SESSION['user']);
        }

        throw new Exception('Not logged in but called Auth::user() anyway');
    }

    /**
     * Is currently logged in user admin?
     */
    public function isAdmin()
    {
      if(isset($_SESSION['user'])) {
      $user = $this->userRepository->findByUser($_SESSION['user']);
        if($user !== null) {
          return $user->isAdmin();
        }
      }
    }

    public function logout()
    {
        if(!$this->guest()) {
            session_destroy();
        }
    }

}

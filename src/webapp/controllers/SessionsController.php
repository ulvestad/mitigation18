<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\repository\UserRepository;

class SessionsController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function newSession()
    {
        if ($this->auth->check()) {
            $username = $this->auth->user()->getUsername();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
            return;
        }

        $this->render('sessions/new.twig', []);
    }

    public function create()
    {
        $request = $this->app->request;
        $user    = ($request->post('user'));
        $pass    = ($request->post('pass'));

        $loginStatus = $this->auth->checkCredentials($user, $pass);

        if ($loginStatus === 0) {
            $_SESSION['user'] = $user;
            $this->app->flash('info', "You are now successfully logged in as $user.");
            $this->app->redirect('/');
            return;
        } elseif ($loginStatus === 1) {
          $this->app->flashNow('error', 'Incorrect user/pass combination.');
          $this->render('sessions/new.twig', []);
        } elseif ($loginStatus === 2) {
          $this->app->flashNow('error', 'Please wait 10seconds before trying again.');
          $this->render('sessions/new.twig', []);
        }

    }

    public function destroy()
    {
        $this->auth->logout();
        $this->app->redirect('http://www.ntnu.no/');
    }
}

<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;

class LoginController extends Controller 
{

    public function signin() {

        $flash = '';

        if(!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }


        $this->render('signin', [
            'flash' => $flash
        ]);
    }

    public function signinAction() {
        
        $email = filter_input(INPUT_POST, 'email');
        $password = filter_input(INPUT_POST, 'password');

        if($email && $password) {

            $token = UserHandler::verifyLogin($email, $password);

            if($token) {
                $_SESSION['token'] = $token;
                $this->redirect('/');
            } else {
                $_SESSION['flash'] = 'E-mail e/ou senha não conferem!';
                $_SESSION['message'] = 'error';
                $this->redirect('/login');
            }

        } else {
            $this->redirect('/login');
        }
    }

    public function signup() {

        $flash = '';

        if(!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }


        $this->render('signup', [
            'flash' => $flash
        ]);
    }

    public function signupAction() {
        $name = ucwords(filter_input(INPUT_POST, 'name'));
        $email = strtolower(filter_input(INPUT_POST, 'email'));
        $birthdate = filter_input(INPUT_POST, 'birthdate');
        $password = filter_input(INPUT_POST, 'password');
        
        if($name && $email && $birthdate && $password) {

            $birthdate = explode('/', $birthdate);
            if(count($birthdate) != 3) {
                $_SESSION['flash'] = 'Data de nascimento inválida!';
                $_SESSION['message'] = 'error';
                $this->redirect('/cadastro');
            }

            $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];

            if(strtotime($birthdate) === false) {
                $_SESSION['flash'] = 'Data de nascimento inválida!';
                $_SESSION['message'] = 'error';
                $this->redirect('/cadastro');
            }

            if(UserHandler::emailExists($email) === false) {
                $token = UserHandler::addUser($name, $email, $birthdate, $password);
                $_SESSION['token'] = $token;

                $this->redirect('/');
            } else {
                $_SESSION['flash'] = 'E-mail já cadastrado';
                $_SESSION['message'] = 'error';
                $this->redirect('/cadastro');
            }

        } else {
            $this->redirect('/cadastro');
        }
    }

    public function loggout() {
        $_SESSION['token'] = '';

        $this->redirect('/login');
    }

}
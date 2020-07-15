<?php
namespace src\controllers;

use \core\Controller;
use \src\models\User;
use \src\handlers\UserHandler;


class ConfigController extends Controller {

    private $loggedUser;

    public function __construct() {

        $this->loggedUser = UserHandler::checkLogin();

        if($this->loggedUser === false) {
            $this->redirect('/login');
        }
    }

    public function index() {

        $flash = '';

        if(!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }

        $this->render('config', [
            'loggedUser' => $this->loggedUser,
            'flash' => $flash
            ]
        );
    }

    public function updateAction($id) {
        
        $id = $this->loggedUser->id;
        $name = ucwords(filter_input(INPUT_POST, 'name'));
        $birthdate = filter_input(INPUT_POST, 'birthdate');
        $password = filter_input(INPUT_POST, 'password');
        $password_confirmation = filter_input(INPUT_POST, 'password-confirmation');
        $email = filter_input(INPUT_POST, 'email');
        $work = filter_input(INPUT_POST, 'work');
        $city = filter_input(INPUT_POST, 'city');

        // Verificar se tem algum campo está preenchido
        if(!empty($name) && 
            !empty($birthdate) && 
            !empty($password) && 
            !empty($email) && 
            !empty($work) && 
            !empty($city)) {
                $this->redirect('/config');
            }

        // Update Name
        if(!empty($name)) {
            UserHandler::updateName($id, $name);
        }

        // Update Birthdate
        if(!empty($birthdate)) {
            $birthdate = explode('/', $birthdate);
            if(count($birthdate) != 3) {
                $_SESSION['flash'] = 'Data de nascimento inválida!';
                $_SESSION['message'] = 'error';
                $this->redirect('/config');
            }

            $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];

            if(strtotime($birthdate) === false) {
                $_SESSION['flash'] = 'Data de nascimento inválida!';
                $_SESSION['message'] = 'error';
                $this->redirect('/config');
            }

            UserHandler::updateBirthdate($id, $birthdate);
        }

        // Update password
        if(!empty($password) && !empty($password_confirmation)) {
            if($password != $password_confirmation) {
                $_SESSION['flash'] = 'As senhas não correspondem';
                $_SESSION['message'] = 'error';
                $this->redirect('/config');
            } else {
                UserHandler::updatePassword($id, $password);
            }
        }

        // Update E-mail
        if(!empty($email)) {
            if(UserHandler::emailExists($email) === false) {

                UserHandler::updateEmail($id, $email);
            } else {
                $_SESSION['flash'] = 'E-mail já cadastrado';
                $_SESSION['message'] = 'error';
            }
        }

        // Update Work
        if(!empty($work)) {
            UserHandler::updateWork($id, $work);
        }

        // Update City
        if(!empty($city)) {
            UserHandler::updateCity($id, $city);
        }

        // Update Avatar
        if(isset($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])) {

            $newAvatar = $_FILES['avatar'];

            // Tipos de imagem
            $jpeg = 'image/jpeg';
            $jpg = 'image/jpg';
            $png = 'image/png';

            if(in_array($newAvatar['type'], [$jpeg, $jpg, $png])) {
                $avatarName = $this->cutImage($newAvatar, 200, 200, 'media/avatars');
                UserHandler::updateAvatar($id, $avatarName);
            }
        }

        // Update Cover
        if(isset($_FILES['cover']) && !empty($_FILES['cover']['tmp_name'])) {

            $newCover = $_FILES['cover'];

            // Tipos de imagem
            $jpeg = 'image/jpeg';
            $jpg = 'image/jpg';
            $png = 'image/png';

            if(in_array($newCover['type'], [$jpeg, $jpg, $png])) {
                $coverName = $this->cutImage($newCover, 850, 310, 'media/covers');
                UserHandler::updateCover($id, $coverName);
            }
        }

        // Retorno
        $_SESSION['flash'] = 'Cadastro atualizado com sucesso';
        $_SESSION['message'] = 'success';
        $this->redirect('/config');
    }

    private function cutImage($file, $w, $h, $folder) {
        list($widthOrig, $heightOrig) = getimagesize($file['tmp_name']);
        $ratio = $widthOrig / $heightOrig;

        $newWidth= $w;
        $newHeight = $newWidth / $ratio;

        if($newHeight < $h) {
            $newHeight = $h;
            $newWidth = $newHeight * $ratio;
        }

        $x = $w - $newWidth;
        $y = $h - $newHeight;

        $x = $x < 0 ? $x / 2 : $x;
        $y = $y < 0 ? $y / 2 : $y;

        $finalImage = imagecreatetruecolor($w, $h);
        switch ($file['type']) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($file['tmp_name']);
                break;

            case 'image/png':
                $image = imagecreatefrompng($file['tmp_name']);
                break;
        }

        imagecopyresampled(
            $finalImage, $image,
            $x, $y, 0, 0,
            $newWidth, $newHeight, $widthOrig, $heightOrig
        );

        $fileName = md5(time().rand(0, 9999)).'.jpg';

        imagejpeg($finalImage, $folder.'/'.$fileName);

        echo $fileName;
        return $fileName;
    }

}
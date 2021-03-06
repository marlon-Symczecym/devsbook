<?php
namespace src\handlers;

use \src\models\User;
use \src\models\UserRelation;
use \src\handlers\PostHandler;

class UserHandler 
{

    public static function checkLogin() {

        if(!empty($_SESSION['token'])) {

            $token = $_SESSION['token'];

            $data = User::select()->where('token', $token)->one();

            if(count($data) > 0) {

                $loggedUser = New User();
                $loggedUser->id = $data['id'];
                $loggedUser->name = strtolower($data['name']);
                $loggedUser->avatar = $data['avatar'];
                $loggedUser->email = $data['email'];
                $loggedUser->birthdate = $data['birthdate'];
                $loggedUser->work = $data['work'];
                $loggedUser->city = $data['city'];


                return $loggedUser;
            }
        }

        return false;
        
    }

    public static function verifyLogin($email, $password) {

        $user = User::select()->where('email', $email)->one();

        if($user) {
            if(password_verify($password, $user['password'])) {
                $token = md5(time().range(0, 9999).time());

                User::update()
                    ->set('token', $token)
                    ->where('email', $email)
                ->execute();

                return $token;
            }
        } 

        return false;

    }

    public static function idExists($id) {

        $user = User::select()->where('id', $id)->one();
        
        return $user ? true : false;
    }

    public static function emailExists($email) {

        $user = User::select()->where('email', $email)->one();
        
        return $user ? true : false;
    }

    public static function getUser($id, $full = false) {

        $data = User::select()->where('id', $id)->one();

        if($data) {
            $user = new User();
            $user->id = $data['id'];
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->birthdate = $data['birthdate'];
            $user->city = $data['city'];
            $user->work = $data['work'];
            $user->avatar = $data['avatar'];
            $user->cover = $data['cover'];

            if($full) {
                $user->followers = [];
                $user->following = [];
                $user->photos = [];

                // Followers
                $followers = UserRelation::select()->where('user_to', $id)->get();

                foreach ($followers as $follower) {

                    $userData = User::select()->where('id', $follower['user_from'])->one();

                    $newUser = new User();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->followers[] = $newUser;
                }

                // Following
                $following = UserRelation::select()->where('user_from', $id)->get();

                foreach ($following as $follower) {

                    $userData = User::select()->where('id', $follower['user_to'])->one();

                    $newUser = new User();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->following[] = $newUser;
                }
                // Photos
                $user->photos = PostHandler::getPhotosFrom($id);
            }

            return $user;
        }

        return false;
        
    }

    public static function addUser($name, $email, $birthdate, $password) {

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = md5(time().range(0, 9999).time());

        User::insert([
            'name' => $name,
            'email' => $email,
            'birthdate' => $birthdate,
            'password' => $hash,
            'token' => $token
        ])->execute();

        return $token;
    }

    public static function isFollowing($from, $to) {

        $data = UserRelation::select()
            ->where('user_from', $from)
            ->where('user_to', $to)
        ->one();

        if($data) {
            return true;
        }

        return false;
    }

    public static function follow($from, $to) {
        UserRelation::insert([
            'user_from' => $from,
            'user_to' => $to
        ])->execute();
    }

    public static function unfollow($from, $to) {
        UserRelation::delete()
            ->where('user_from', $from)
            ->where('user_to', $to)
        ->execute();
    }

    public static function searchUser($term) {
        $users = [];
        $data = User::select()->where('name', 'like', '%'.$term.'%')->get();

        if($data) {
            foreach($data as $user) {
                $newUser = new User();
                $newUser->id = $user['id'];
                $newUser->name = $user['name'];
                $newUser->avatar = $user['avatar'];

                $users[] = $newUser;
            }
        }

        return $users;
    }

    public static function updatePassword($id, $password) {
        
        $hash = password_hash($password, PASSWORD_DEFAULT);

        User::update()
            ->set('password', $hash,)
            ->where('id', $id)
        ->execute();
    }

    public static function updateName($id, $name) {
        User::update()
            ->set('name', $name)
            ->where('id', $id)
        ->execute();
    }

    public static function updateBirthdate($id, $birthdate) {
        User::update()
            ->set('birthdate', $birthdate)
            ->where('id', $id)
        ->execute();
    }

    public static function updateEmail($id, $email) {
        User::update()
            ->set('email', $email)
            ->where('id', $id)
        ->execute();
    }

    public static function updateCity($id, $city) {
        User::update()
            ->set('city', $city)
            ->where('id', $id)
        ->execute();
    }

    public static function updateWork($id, $work) {
        User::update()
            ->set('work', $work)
            ->where('id', $id)
        ->execute();
    }

    public static function updateAvatar($id, $avatarName) {
        User::update()
            ->set('avatar', $avatarName)
            ->where('id', $id)
        ->execute();
    }

    public static function updateCover($id, $coverName) {
        User::update()
            ->set('cover', $coverName)
            ->where('id', $id)
        ->execute();
    }

}
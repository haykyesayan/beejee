<?php

namespace App\Controllers;
use \Core\BaseController;
use \Core\View;
use \App\Models\User;
/**
 * Home controller
 *
 * PHP version 7.0
 */
class AdminController extends BaseController
{

    /**
     * Show the tasks page
     *
     * @return void
     */
    public function loginAction()
    {
        $error_message = '';
        $referer = $_SERVER['HTTP_REFERER'];
        if (isset($_POST['submited'])) {
            $referer = isset($_POST['refferer']) ? $_POST['refferer']:$_SERVER['HTTP_REFERER'];

            $validator = $this->validator(['login','password'],$_POST);
            if (!$validator['valid']) {
                $error_message=implode(" , ".$validator['not_valid_fields'])." is required";
            }
            $login = htmlspecialchars($validator['fields']['login']);
            $password = htmlspecialchars($validator['fields']['password']);
            $user = User::login($login,$this->encryptPassword($password));
            if (!$user) {
                $error_message=" incorrect login password ";
                
            }else{
                $_SESSION['admin_user_id'] = $user['id'];
                header("Location: ".$referer); 

            }
        }
        View::renderTemplate('Admin/login.html',['referer'=>$referer,'error_message'=>$error_message]);
    }
    public function logoutAction()
    {
        unset($_SESSION['admin_user_id']);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        
    }

    /**
     * Encrypt password
     *
     * @param string $password not encrypted password
     * @return string encrypted password
     */
    private function encryptPassword($password){
        return md5(md5($password));
    }
}

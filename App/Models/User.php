<?php

namespace App\Models;
use \Core\BaseModel;
use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends BaseModel
{

    /**
     * Get all the users as an associative array
     * @param string $userName username(login) of user
     * @param string $password encrypted password of user
     *
     * @return array of user
     */
    public static function login($userName,$password)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT id FROM users WHERE username=:username and password=:password');
            $stmt->bindParam(":username",$userName,PDO::PARAM_STR);
            $stmt->bindParam(":password",$password,PDO::PARAM_STR);
        if (!$stmt->execute()) {
            return False;
        }
        if ($stmt->rowCount()==0) {
            return False;
            
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all the users as an associative array
     * @param string $userName username(login) of user
     * @param string $password encrypted password of user
     *
     * @return True if task is edited, False if not
     */
    public static function createUser($userName,$password)
    {
        $db = static::getDB();
        $stmt = $db->prepare('INSERT INTO `users`(`username`, `password`) VALUES (:username,:password)');
            $stmt->bindParam(":username",$username,PDO::PARAM_STR);
            $stmt->bindParam(":password",$password,PDO::PARAM_STR);

        if (!$stmt->execute()) {
            return False;
        }
        return True;
    }
}

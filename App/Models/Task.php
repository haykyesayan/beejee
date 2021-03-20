<?php

namespace App\Models;
use \Core\BaseModel;
use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Task extends BaseModel
{

    /**
     * Get all taskes
     *
     * @param int $page page number 
     * @param string $order_by in witch column yu wont to order by, it can be user_name, email, task, done, edited
     * @param bool $desc if True, order by type is DESC else ASC 
     * @param int $count_per_page count of tasks witch will be returned
     *
     * @return array of tasks
     */
    public static function getTasks($page=1,$order_by='id',$desc=1,$count_per_page = 3)
    {

        $db = static::getDB();

        $sqlForSelectTasksCount = "SELECT count(id) as COUNT from tasks";
        $stmt = $db->query($sqlForSelectTasksCount);
        $count = $stmt->fetch()['COUNT'];

        $sqlForSelectTasks = "SELECT id,user_name, email, task, done, edited FROM tasks ORDER BY $order_by ";
        if ($desc) {
            $sqlForSelectTasks .='DESC ';
        }else{
            $sqlForSelectTasks .='ASC ';   
        }
        $sqlForSelectTasks .= "LIMIT ".($page-1)*$count_per_page." , ".$count_per_page;
        $stmt = $db->query($sqlForSelectTasks);
        $tasks = [
            'count'=>$count,
            'page'=>$page,
            'count_per_page'=>$count_per_page,
            'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC),
        ];
        return $tasks;
    }


    public static function getTask($id)
    {

        $db = static::getDB();

   
        $sqlForSelectTasks = "SELECT id,user_name, email, task, done, edited FROM tasks WHERE id=:id";
        $stmt = $db->prepare($sqlForSelectTasks);
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        if (!$stmt->execute()) {
            return false;
        }
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    /**
     * add new Task
     *
     * @param string $name user name
     * @param string $email email
     * @param string $task task discription
     *
     * @return array of task if task is added, False if not
     */

    public static function addTask($name,$email,$task)
    {
        
        $db = static::getDB();
        $name = htmlspecialchars($name);
        $email = htmlspecialchars($email);
        $task = htmlspecialchars($task);

        $stmt = $db->prepare("INSERT INTO `tasks`(`user_name`, `email`, `task`) VALUES (:user_name,:email,:task)");
            $stmt->bindParam(':user_name',$name,PDO::PARAM_STR);
            $stmt->bindParam(':email',$email,PDO::PARAM_STR);
            $stmt->bindParam(':task',$task,PDO::PARAM_STR);
        if (!$stmt->execute()) {
            return ['id'=>$db->lastInsertId(),'user_name'=>$user_name,'email'=>$email,'task'=>$task];
        }
        return True;

    }

    /**
     * edit task notes
     *
     * @param int $taskId task id witch you wont to edit
     * @param string $task new task description
     *
     * @return True if task is edited, False if not
     */
    public static function editTask($taskId,$task)
    {
        
        $db = static::getDB();
        $stmt = $db->prepare("SELECT id FROM tasks WHERE id=:id");
            $stmt->bindParam(':id',$taskId,PDO::PARAM_INT);
        if (!$stmt->execute()) {
            return False;
        }
        if (!$stmt->rowCount()) {
            return False;
        }
   
        $stmt = $db->prepare("UPDATE `tasks` SET `task`=:task,`edited`=1 WHERE `id`=:id");
            $stmt->bindParam(':task',$task,PDO::PARAM_STR);
            $stmt->bindParam(':id',$taskId,PDO::PARAM_INT);
        if (!$stmt->execute()) {
            return False;
        }
        return True;
    }


    /**
     * edit task notes
     *
     * @param int $taskId task id witch you wont to edit
     *
     * @return True if task is edited, False if not
     */

    public static function setTaskIsDone($taskId)
    {
        
        $db = static::getDB();
        $stmt = $db->prepare("SELECT id FROM tasks WHERE id=:id");
            $stmt->bindParam(':id',$taskId,PDO::PARAM_INT);
        if (!$stmt->execute()) {
            return False;
        }
        if (!$stmt->rowCount()) {
            return False;
        }
   
        $taskId=intval($taskId);
        $stmt = $db->prepare("UPDATE `tasks` SET `done`=1 WHERE `id`=:id");
            $stmt->bindParam(':id',$taskId,PDO::PARAM_INT);
        if (!$stmt->execute()) {
            return False;
        }
        return True;
    }


}

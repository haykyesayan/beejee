<?php

namespace App\Controllers;
use \Core\BaseController;
use \Core\View;
use \App\Models\Task;
/**
 * Home controller
 *
 * PHP version 7.0
 */
class TaskController extends BaseController
{

    /**
     * Show the tasks page
     *
     * @return void
     */
    public function indexAction()
    {
        $page = isset($_GET['page']) ? intval($_GET['page']) > 0 ? intval($_GET['page']):1:1;
        $order_by = isset($_GET['order_by']) ? in_array($_GET['order_by'],['user_name', 'email', 'done', 'edited']) ? $_GET['order_by'] : 'id' : 'id';
        $desc = !isset($_GET['desc']) ? 1:($_GET['desc']==1 ? 1:0);

    	$tasks = Task::getTasks($page,$order_by,$desc);
        View::renderTemplate('Task/list.html',
        	[
        		'tasks'=>$tasks['data'],
                'count'=>$tasks['count'],
                'start_number'=>($tasks['page']-1)*$tasks['count_per_page']+1,
                'end_number'=>$tasks['page']*$tasks['count_per_page'] > $tasks['count'] ? $tasks['count']:$tasks['page']*$tasks['count_per_page'],
                'nearbyPagesLimit'=>3,
        		'nbPages'=>ceil($tasks['count']/$tasks['count_per_page']),
        		'currentPage'=>$tasks['page'],
                'order_by'=>$order_by,
                'desc'=>$desc,
        		'is_admin'=>isset($_SESSION['admin_user_id']) ? 1:0
        	]);
    }

	/**
     * add new task 
     * this function use $_POST data
     * @return json, { 'success' : 1 if task added, 0 if not, 'error_message': if have errors }
     */
    public function createAction()
    {
    	$error_message = '';
        $referer = $_SERVER['HTTP_REFERER'];

    	if(isset($_POST['submited'])){
            $referer = isset($_POST['referer']) ? $_POST['referer']:$_SERVER['HTTP_REFERER'];

	    	$validator = $this->validator(['name','email','task'],$_POST);
	    	if (!$validator['valid']) {
	    		$error_message = implode(" , ",$validator['not_valid_fields'])." is required";
	    	}else{
		    	$name = htmlspecialchars($validator['fields']['name']);
		    	$email = htmlspecialchars($validator['fields']['email']);
		    	$task = htmlspecialchars($validator['fields']['task']);

		    	if($task = Task::addTask($name,$email,$task)){
		    		header("Location: ".$referer); //TODO

		    	}
		    	$error_message = 'error with db';
	    	}
    	}

        View::renderTemplate('Task/add.html',['error_message'=>$error_message,'referer'=>$referer]);

    }

	/**
     * edit task 
     * this function use $_POST data, and only admin users can edit task
     * @return 
     */
    public function editAction(){
        if (!isset($_SESSION['admin_user_id'])) {
            exit('no permission');
        }
        if (!isset($_GET['id'])) {
            return False;
        }
        $message = '';
        $referer = $_SERVER['HTTP_REFERER'];
        if (isset($_POST['submited'])) {
            $referer = isset($_POST['referer']) ? $_POST['referer']:$_SERVER['HTTP_REFERER'];
        	if (!isset($_SESSION['admin_user_id'])) {
        		exit('no permission');
        	}

        	$validator = $this->validator(['task'],$_POST);
        	if (!$validator['valid']) {
        		exit(json_encode(['success'=>0,'error_message'=>implode($validator['not_valid_fields'])." is required"]));
        	}
        	$task = htmlspecialchars($validator['fields']['task']);
        	$taskId = intval($_GET['id']);
        	if(Task::editTask($taskId,$task)){
                header('Location: ' . $referer);
        	}
        }

        $taskId = intval($_GET['id']);
        $task = Task::getTask($taskId);
        View::renderTemplate('Task/edit.html',['task'=>$task['task'],'user_name'=>$task['user_name'],'email'=>$task['email'],'id'=>$_GET['id'],'message'=>$message,'referer'=>$referer]);
    }

    /**
     * set task done 
     * only admin users can set task done
     * @return 
     */
    public function doneAction(){
    	if (!isset($_SESSION['admin_user_id'])) {
    		exit('no permission');
    	}
    	$taskId = intval($_GET['id']);
    	if(Task::setTaskIsDone($taskId)){
            header('Location: ' . $_SERVER['HTTP_REFERER']);
    	}
    	exit('0');
    }
}

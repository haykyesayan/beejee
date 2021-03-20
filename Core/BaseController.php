<?php

namespace Core;

/**
 * Base controller
 *
 * PHP version 7.0
 */
abstract class BaseController
{

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];

    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }

    /**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name  Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     */
    public function __call($name, $args)
    {
        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], $args);
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Validator function, check if isset required fields in array
     *
     * @param array $fields  Parameters from the route
     * @param array $source array when we search fields
     * @return void
     */
    protected function validator($fields,$source){
        $valid = true;
        $notValidFields = [];
        $valid_fields = [];
        foreach ($fields as $field) {
            if (!isset($source[$field])) {
                $valid = false;
                $notValidFields[] = $field;
            }elseif($source[$field] == ''){
                $valid = false;
                $notValidFields[] = $field;
            }else{
                $valid_fields[$field] = $source[$field];
            }
        }
        if ($valid) {
            return ['valid'=>1,'fields'=>$valid_fields];

        }else{
            return ['valid'=>0,'not_valid_fields'=>$notValidFields];
        }

    }

    
}

<?php
/**
* 在处理请求之前(进入业务controller之前)做的预处理，一般在这里做一些登录校验、权限校验等
*
*/
Class Pre_handle_hook{

    public $swoole_obj = null;
    public $swoole_request = null;
    public $swoole_response = null;
    public $route = null;
    
    public function __construct($swoole_obj,$swoole_request,$swoole_response,$route)
    {
        $this->swoole_obj = $swoole_obj;
        $this->swoole_request = $swoole_request;
        $this->swoole_response = $swoole_response;
        $this->route = $route;
    }
    
    public function run()
    {
        /*
        //向顶级控制器注入变量
        $INSTTANCE = Controller::get_instance();
        $INSTTANCE->user_info = $user_info;
        */
        return true;
    }
    
}
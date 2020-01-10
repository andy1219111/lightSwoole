<?php
/**
* 在处理完请求后要做的一些操作
*
*/
Class After_handle_hook{

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

        return true;
    }
    
}
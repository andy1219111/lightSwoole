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
        if($this->route['class'] == 'User' && $this->route['method'] == 'login'
        || $this->route['class'] == 'Parking_fee' && $this->route['method'] == 'pay_callback'
        || $this->route['directory'] == 'open'
        )
        {
            return true;
        }
        //如果不是登录接口  需要进行token校验
        $token = isset($this->swoole_request->post['token']) ? $this->swoole_request->post['token'] : '';

        if(empty($token))
        {
            $response = ['code'=>101,'message'=>'非法访问'];
            $this->swoole_response->end(json_encode($response));
            return false;
        }

        $user_info = $this->check_token($token);
        if(!$user_info)
        {
            $response = ['code'=>101,'message'=>'非法访问'];
            $this->swoole_response->end(json_encode($response));
            return false;
        }
        $INSTTANCE = Controller::get_instance();
        $INSTTANCE->user_info = $user_info;
        return true;
    }
    
}
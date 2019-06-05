<?php

/**
 * 顶级控制器
 *
 * @author aaron
 * @version 2018-5-14
 *
 */
class Controller{

	//swoole_server_request 对象  包含所有的请求和响应信息
	public $request = NULL;
	
	//swoole_server_response 对象  包含所有的响应操作
	public $response = NULL;
	
	//swoole 实例
	public $swoole_obj = NULL;
	//请求序列号
    public $serial_no = '';
    
	//日志类对象
    public $loger = '';
    
    private static $instance;
    
	public function __construct($swoole_obj,$request,$response)
	{
		self::$instance =& $this;
        $this->swoole_obj = $swoole_obj;
		$this->request = $request;
		$this->response = $response;
		$this->loger = load_class('Log');
        
        //生成请求序列号
        $this->serial_no = $this->generate_serial_no();
        //记录请求信息
        $request_data = '';
        $request_data .= 'post:' . (isset($this->request->post) ? json_encode($this->request->post) : '');
        $request_data .= 'get:' . (isset($this->request->get) ? json_encode($this->request->get) : '');
        $request_data .= 'post-raw:' . $this->request->rawContent();
        $this->loger->write_log('INFO', 
                                $this->serial_no . 
                                '-[' . $this->request->server['request_uri'] . ']-' . 
                                $this->request->server['request_method'] . 
                                '(' . (isset($this->request->header['user-agent']) ? $this->request->header['user-agent'] : '') . '):' . 
                                $request_data
                            );
	}
    
    /**
     * 单例模式 获取对象实例
     *
     * @author aaron
     * @version 2018-05-14
     *
     */
    public static function &get_instance()
    {
        return self::$instance;
    }
    
    /**
     * 生成请求序列号
     *
     * @author aaron
     * @version 2018-05-14
     *
     */
    function generate_serial_no()
    {
        //自动以生成请求序列号的方式  serial_no = date('YmdHis') . mt_rand(10000, 99999)
        return date('YmdHis') . strval(mt_rand(10000, 99999));
    }
    
    /**
     * 向client端响应数据
     *
     * @author aaron
     * @version 2018-05-14
     *
     */
    function ajax_response($data)
    {
        $this->loger->write_log('INFO', 
                                $this->serial_no
                                . '-[' . $this->request->server['request_uri'] . ']-OUTPUT:'
                                . json_encode($data)
                            );
        $this->response->header('Content-Type: ', 'application/json');
        $this->response->end(json_encode($data));
    }

    /**
     * 验证token并得到用户详情
     *
     * @author aaron
     * @version 2018-05-16
     *
     */
    function check_token($token)
    {
        $key = config_item('token_key') . $token;
        $redis_operator = new Redis_operator(config_item('token_redis'));
        $redis_operator->select(config_item('token_redis')['dbId']);
        $user_info = $redis_operator->get($key);
        
        if(empty($user_info))
        {
            return false;
        }
        return json_decode($user_info, true);
    }

}
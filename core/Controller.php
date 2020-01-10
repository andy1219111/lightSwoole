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

    /**
     * 加载模板 显示html页面
     *
     * @param string $view_path 模板路径
     * @param array $params 要导入到模板中的变量
     * @return void
     */
    function view($view_path,$params = [],$use_layout = false)
    {
        //如果模板路径中没有后缀名  补充上
        if(substr(trim($view_path),-4) != '.php'){
            $view_path .= '.php'; 
        }
        $view_file = ROOT_PATH . 'application/view/' . $view_path;
        if(!file_exists($view_file)){
            $this->loger->write_log('ERROR','template fileis not exist:' . $view_path);
            $this->response->end('template fileis not exist');
        }
        //将标量导出 提供给模板使用
        if(!empty($params)){
            extract($params);
        }
        //开启缓冲区
        ob_start();
        include $view_file;
        $buffer = ob_get_clean();
        //使用布局模板
        if($use_layout){
            $new_param['content'] = $buffer;
            extract($new_param);
            include ROOT_PATH . 'application/view/layout.php';
            $buffer = ob_get_clean();
            unset($new_param);
        }
        //结束并清空缓冲区
        @ob_end_clean();
        $this->response->end($buffer);
        unset($buffer,$params);
    }
}
<?php
// 入口文件
define('ROOT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
//获取环境变量
$env = getenv('LIGHT_SWOOLE_ENV');
$env = $env ? $env : 'testing';
define('ENVIRONMENT', $env);

include 'autoload.php';

/**
* 服务入口 server类
*
* @param string $item 配置参数名称
* @param string $config_file 配置文件路径
*
*/
class Light_swoole_server{
	
	//swoole实例
	private $serv = null;
	private $loger = null;
	
	/**
	* 构造函数 创建swoole对象 并启动服务
	*
	*
	*/
	public function __construct($port = 9523,$worker_num = 4,$task_worker_num = 4)
	{
		$this->loger = load_class('Log');

        $table = new swoole_table(10000);
        $table->column('arm_push_info', swoole_table::TYPE_STRING, 10000);
        $table->create();
        
		$this->serv = new swoole_http_server("0.0.0.0", $port);
		$this->serv->set(array(
							'worker_num' => $worker_num,
							//开启守护进程模式
							'daemonize' => false,
							'max_request' => 2000,
							'dispatch_mode' => 2,
							'debug_mode'=> 1,
							'task_worker_num' => $task_worker_num,
							'log_file'=>config_item('log_config')['log_path'] . 'log-' . date('Y-m-d') . '.log'
							)
						);
		$this->serv->on('Start', array($this, 'onStart'));
        //$this->serv->on('Connect', array($this, 'onConnect'));
        //$this->serv->on('Receive', array($this, 'onReceive'));
		$this->serv->on('request', array($this, 'onRequest'));
        $this->serv->on('Close', array($this, 'onClose'));
        $this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
        // bind callback
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));
        
        $this->serv->table = $table;
		//启用swoole进程
        $this->serv->start();
        
	}

	/**
	* swoole启动时的回调方法
	*
	* @param swoole_server $serv 当前的swoole_server实例
	*/
	public function onStart($serv) 
	{
		$this->loger->write_log('info',"The server started. \n");
	}
	
	/**
	* 当有客户端连接时的回调
	*
	* @param swoole_server $serv 当前的swoole_server实例
	* @param swoole_server $fd 当前连接的客户端的连接的文件描述符，发送数据/关闭连接时需要此参数
	*/
	public function onConnect( $serv, $fd, $from_id ) 
	{
		$this->loger->write_log('info',"Client {$fd} connect \n");
    }
	
	/**
	* swoole服务器接收到消息时的回调
	*
	* @param swoole_server $serv 当前的swoole_server实例
	* @param int $fd 当前连接的客户端的连接的文件描述符，发送数据/关闭连接时需要此参数
	* @param int $from_id $from_id来自那个Reactor线程
	*/
	public function onReceive( swoole_server $serv, $fd, $from_id, $data ) 
	{
		$this->loger->write_log('info',"Get Message From Client {$fd}:{$data}\n");
        $commands = array('reload','shutdown','version','stats');
		//去除回车键
		$data = trim($data);

		//是否是有效的命令
		if(!in_array($data,$commands))
		{
			$serv->send($fd,"command not found \n");
		}
        switch($data)
		{
			case 'reload':
				$serv->send($fd,"Execute " . $data . " operation ...\n");
				$serv->reload();
				break;
			case 'shutdown':
				$serv->send($fd,"Execute " . $data . " operation ...\n");
				$serv->shutdown();
				break;
			case 'version':
				$version = swoole_version();
				$serv->send($fd,$version . "\n");
				break;
			case 'stats':
				$serv->send($fd,"Execute stats  operation ...\n");
				$status = $serv->stats();
				$serv->send($fd,json_encode($status) . "\n");
				break;
		}
    }
	/**
	* 在worker进程/task进程启动时发生
	*
	* @param swoole_server $serv 当前的swoole_server实例
	* @param int $fd 当前连接的客户端的连接的文件描述符，发送数据/关闭连接时需要此参数
	* @param int $from_id $from_id来自那个Reactor线程
	*/
	function onWorkerStart(swoole_server $serv, $worker_id)
	{
		$this->loger->write_log('info',"Work_id " . $worker_id . " started \n");
	}
    
	/**
	* 接收到任务时的回调函数 任务执行函数
	*
	* @param swoole_server $serv 当前的swoole_server实例
	* @param int $task_id 任务id  由swoole自动生成
	* @param int $from_id $from_id来自那个worker进程
	* @param mixed $data 是任务的内容
	*/
	public function onTask($serv, $task_id, $from_id, $data) 
	{
        $this->loger->write_log('INFO', 'Excute task ' . $task_id . ', from ' . $from_id . ',task data:' . json_encode($data));
        
    	return "Task {$task_id}'s result";
    }
    
	/**
	 * Parse REQUEST_URI
	 *
	 * Will parse REQUEST_URI and automatically detect the URI from it,
	 * while fixing the query string if necessary.
	 *
	 * @return	string
	 */
	protected function _parse_request_uri($request)
	{
        // parse_url() returns false if no host is present, but the path or query string
		$uri = parse_url('http://dummy' . $request->server['request_uri']);
		//$query = !empty($request->server['query_string']) ? $request->server['query_string'] : '';
       
		$uri = isset($uri['path']) ? $uri['path'] : '';
		simple_log('new request:' . $request->server['request_uri']);
		if (isset($request->server['script_name'][0]))
		{
			if (strpos($uri, $request->server['script_name']) === 0)
			{
				$uri = (string) substr($uri, strlen($request->server['script_name']));
			}
			elseif (strpos($uri, dirname($request->server['script_name'])) === 0)
			{
				$uri = (string) substr($uri, strlen(dirname($request->server['script_name'])));
			}
		}

		// Do some final cleaning of the URI and return it
		$uri_arr = explode('/',remove_relative_directory($uri));
        if(count($uri_arr) >2)
        {
            if(is_dir(ROOT_PATH . 'application/controller/' . $uri_arr[0]))
            {
                $route['directory'] = $uri_arr[0];
                $route['class'] = isset($uri_arr[1]) ? ($uri_arr[1] == '' ? 'Index' : ucfirst( $uri_arr[1])) : 'Index';
                $route['method'] = isset($uri_arr[2]) ? ($uri_arr[2] == '' ? 'index' : $uri_arr[2]) : 'index';
            }
            else
            {
                $route['directory'] = '';
                $route['class'] = isset($uri_arr[0]) ? ($uri_arr[0] == '' ? 'Index' : ucfirst( $uri_arr[0])) : 'Index';
                $route['method'] = isset($uri_arr[1]) ? ($uri_arr[1] == '' ? 'index' : $uri_arr[1]) : 'index';
            } 
        }
        else
        {
            $route['directory'] = '';
            $route['class'] = isset($uri_arr[0]) ? ($uri_arr[0] == '' ? 'Index' : ucfirst( $uri_arr[0])) : 'Index';
            $route['method'] = isset($uri_arr[1]) ? ($uri_arr[1] == '' ? 'index' : $uri_arr[1]) : 'index';
        }
		
		return $route;
	}
	
	/**
	* 当接收到请求时处理请求
	*
	* @param swoole_server $request 请求对象
	* @param swoole_http_response $response 响应对象
	* @author aaron
	*/
	function onRequest(swoole_http_request $request, swoole_http_response $response)
	{
        //hook埋点 on_request  在刚收到请求且未进行路由解析之前
        if(file_exists(ROOT_PATH . '/hooks/On_request_hook.php'))
        {
            $on_request_hook = new On_request_hook($this->serv, $request, $response);
            if(!$on_request_hook->run())
            {
                return;
            }
        }
        
        //路由
		$route = $this->_parse_request_uri($request);
		$this->loger->write_log('INFO', 'router:' . json_encode($route));
        $dir = $route['directory'] == '' ? '' : $route['directory'] . '/';
		if (!file_exists(ROOT_PATH.'/application/controller/' . $dir . $route['class'].'.php'))
		{
			$response->status(404);
			$response->end('not found');
		}
		else
		{
			require_once(ROOT_PATH . '/application/controller/' . $dir . $route['class'] . '.php');
            if(!class_exists($route['class']))
            {
                $response->status(404);
                $response->end('not found');
                return;
            }
			$controller = new $route['class']($this->serv,$request,$response);
            if(!method_exists($controller, $route['method']))
            {
                $response->status(404);
                $response->end('not found');
                return;
            }
            //hook埋点 Pre_handle 在执行请求之前执行
            if(file_exists(ROOT_PATH . '/hooks/Pre_handle_hook.php'))
            {
                $pre_handle_hook = new Pre_handle_hook($this->serv, 
                                                    $request, 
                                                    $response, 
                                                    $route
                                                );
                if(!$pre_handle_hook->run())
                {
                    return;
                }
            }
        
			call_user_func_array(array(&$controller, $route['method']),array());
            
            //hook埋点 在处理完请求后执行
            if(file_exists(ROOT_PATH . '/hooks/After_handle_hook.php'))
            {
                $after_handle_hook = new After_handle_hook($this->serv, $request, $response, $route);
                $after_handle_hook->run();
            }
		}
	}
	/**
	* TCP客户端连接关闭后，在worker进程中回调此函数
	*
	* @param swoole_server $serv 当前的swoole_server实例
	* @param int $fd 当前连接的客户端的连接的文件描述符，发送数据/关闭连接时需要此参数
	* @param int $from_id $from_id来自那个Reactor线程
	*/
	public function onClose( $serv, $fd, $from_id ) 
	{
		$this->loger->write_log('info',"Client {$fd} close connection\n");
    }

	//当任务结束时执行的操作 onTask方法会把执行结果传递给该方法
	public function onFinish($serv,$task_id,$data) 
	{
    	$this->loger->write_log('info',"Task {$task_id} finish\n");
    	$this->loger->write_log('info',"Result: {$data}\n");
    }
    
}


//配置信息
$port = config_item('port');
$worker_num = config_item('worker_num');
$task_worker_num = config_item('task_worker_num');
//端口
$port = !empty($port) && is_numeric($port) ? $port : 9523;
//启动worker数量
$worker_num = !empty($worker_num) && is_numeric($worker_num) ? $worker_num : 4;
//任务进程数量
$task_worker_num = !empty($task_worker_num) && is_numeric($task_worker_num) ? $task_worker_num : 4;
$serv = new Light_swoole_server($port,$worker_num,$task_worker_num);


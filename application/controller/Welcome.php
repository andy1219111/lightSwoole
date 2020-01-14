<?php
/**
 * 这是一个控制器的例子
 *
 * @author aaron
 * @version 2016-10-17
 *
 */
class Welcome extends Controller{
	
	/**
	 * 控制器构造函数
	 *
	 * @param swoole_server $swoole_obj swoole_http_server的实例
	 * @param swoole_http_request $request swoole http request的对象  包含了请求相关信息
	 * @param swoole_http_response $response swoole http response 响应对象
	 */
	function __construct($swoole_obj,$request,$response)
	{
		parent::__construct($swoole_obj,$request,$response);
	}
	
	/**
     * 这是一个action的例子，当url中不存在action部分时，会执行这个方法
     *
     * @author aaron
     * @version 2017-10-17
     *
     */
	function index(){
		
		$data['param'] = '欢迎您使用LightSwoole,';
		$this->view('index.php',$data);
		//发送响应
		//$this->response->end('welcome to use LightSwoole');
	}
}
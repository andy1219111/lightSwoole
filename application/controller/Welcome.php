<?php
/**
 * 这是一个控制器的例子
 *
 * @author aaron
 * @version 2016-10-17
 *
 */
class Welcome {

	//swoole_server_request 对象  包含所有的请求和响应信息
	public $request = NULL;
	
	//swoole_server_response 对象  包含所有的响应操作
	public $response = NULL;
	
	//swoole 实例
	public $swoole_obj = NULL;
	
	function __construct($swoole_obj,$request,$response)
	{
		$this->swoole_obj = $swoole_obj;
		$this->request = $request;
		$this->response = $response;
	}
	
	/**
     * 这是一个action的例子，当url中不存在action部分时，会执行这个方法
     *
     * @author aaron
     * @version 2017-10-17
     *
     */
	function index(){
		//获取get参数
		$this->request->get['hello'];
		//获取post参数
		$this->request->post['hello'];
		//获取server环境变量
		$this->request->server['hello'];
		
		//发送响应
		$this->response->end('This is your first program whith lightSwoole...');
	}
  
}
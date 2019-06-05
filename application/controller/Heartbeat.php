<?php
/**
 * 心跳检测
 *
 * @author aaron
 * @version 2016-10-17
 *
 */
class Heartbeat {

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
     * 接收心跳检测请求
     *
     * @author aaron
     * @version 2017-10-17
     *
     */
    function test()
    {
        $this->response->end('ok');
    } 
}
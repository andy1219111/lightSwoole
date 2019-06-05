<?php

class Rest{		
	/*
	 * curl get请求方式
	 * 
	 * @param string url 调用API的请求地址
	 * @return array
	 */
	public function curlGet($url,$header=array(),$userpwd = '',$proxy = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if(!empty($proxy))
		{
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
		}
		if(!empty($header)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}
		if(!empty($userpwd)) {
			curl_setopt($ch, CURLOPT_USERPWD, $userpwd);
		}
		//curl_setopt($ch, CURLOPT_TIMEOUT, 15);//设置curl允许执行的最长秒数
		$response = curl_exec($ch);	
		$result = curl_errno($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $total_time = curl_getinfo($ch,CURLINFO_TOTAL_TIME);
		curl_close($ch);
		return array($result,$response,$status,$contentType,$header_size,$total_time);
	}
	
	/*
	 * curl post请求方式
	 * 
	 * @param string url 调用API的请求地址
	 * @param string data 发送内容
	 * @param array header 发送内容类型
	 * @return array
	 */
	public function curlPost($url,$data,$header = array(),$proxy = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if(!empty($proxy))
		{
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
		}
		//如果存在请求header
		if(!empty($header))
		{
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}
		else
		{
			curl_setopt($ch, CURLOPT_HEADER, false);
		}

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);//设置curl允许执行的最长秒数
		$response = curl_exec($ch);
		$result = curl_errno($ch);
		$status = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $total_time = curl_getinfo($ch,CURLINFO_TOTAL_TIME);
		curl_close($ch);
		return array($result,$response,$status,$contentType,$header_size,$total_time);
	}
	
	/*
	 * curl delete请求方式
	 *
	 * @param string url 调用API的请求地址
	 * @return array
	 */
	public function curlDelete($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		//curl_setopt($ch, CURLOPT_TIMEOUT, 15);//设置curl允许执行的最长秒数
		$response = curl_exec($ch);
		$result = curl_errno($ch);
        $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $total_time = curl_getinfo($ch,CURLINFO_TOTAL_TIME);
		curl_close($ch);
		return array($result,$response,$status,$contentType,$header_size,$total_time);
	}
	
	/*
	 * curlInit 
	 * 
	 * @param string url 连接地址
	 * @param string query_array 发送内容
	 * @param string method 请求的方法
	 * @param array format 发送内容类型
	 * @return array
	 */
	function curlInit( $url = '', $query_array = array(), $method = "GET", $post_data = "", $format = '',$userpwd = '',$proxy = null){
		$header = array();
		if(!empty($format))
		{
			$header[] = "Content-Type: $format";
		}
		
		if(!empty($query_array)){
			$query = http_build_query($query_array);		// Build array to url query
			$url = $url.'?'.$query;
		}
		log_message('INFO',"accessUrl:$url");
		$method = strtolower($method);
		switch ($method){
			case 'post':
				$res = $this->curlPost($url,$post_data,$header,$proxy);
				break;
			case 'get':
				$res = $this->curlGet($url,$header,$userpwd,$proxy);
				break;
			case 'delete':
				$res = $this->curlDelete($url);
				break;
			default:
				break;
		}
        //请求所耗费的时长 记录大于3s的请求
        if($res[5] >= 3)
        {
            log_message('DEBUG',"Request api [" . $url . "] cost long:" . $res[5]);
        }
		return $res;
	}
}
?>
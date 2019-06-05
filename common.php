<?php

/**
* 得到配置参数
*
* @param string $item 配置参数名称
* @param string $config_file 配置文件路径
*
*/
function config_item($item,$config_file = 'config.php')
{
	static $config_param = [];
	
    
	if(isset($config_param[$item]))
	{
		return $config_param[$item];
	}
    $config_file = ROOT_PATH . '/config/' . ENVIRONMENT . '/' . $config_file;
	//不存在该参数 且未提供配置文件
	if(!file_exists($config_file))
	{
		echo 'The config file is not exist:' .  $config_file . "\n";
        return NULL;
	}
	//加载配置文件
	require($config_file);
	if ( ! isset($config) OR ! is_array($config))
	{
		return NULL;
	}
	$config_param = array_merge($config_param,$config);
	if(isset($config_param[$item]))
	{
		return $config_param[$item];
	}
	return NULL;
}

/**
* 加载指定的类库
*
* @param string $class 类名
* @author aaron
*
*/
function &load_class($class,$init_param = '')
{
	static $_classes = array();

	// Does the class exist?  If so, we're done...
	if (isset($_classes[$class]))
	{
		return $_classes[$class];
	}
	//是否存在初始化参数
	if(!empty($init_param))
	{
		$instance = new $class($init_param);
	}
	else
	{
		$instance = new $class();
	}

	$_classes[$class] = $instance;
	return $_classes[$class];
}

/**
* 加载模型类
*
* @param string $class 类名
* @author aaron
*
*/
function load_model($model_name, $db_config)
{
    static $loaded_models = [];
    if(isset($loaded_models[$model_name]))
    {
        return $loaded_models[$model_name];
    }
    
    $real_model_name = ucfirst($model_name) . '_model';
    if(!file_exists(ROOT_PATH . '/application/model/' . $real_model_name . '.php'))
    {
        simple_log('Model ' . $real_model_name . ' is not exists!');
        return false;
    }
    
    require_once(ROOT_PATH . '/application/model/' . $real_model_name . '.php');
    $loaded_models[$model_name] = new $real_model_name($db_config);
    return  $loaded_models[$model_name];
}

	/**
	 * 测试是否可写
	 *
	 * is_writable() returns TRUE on Windows servers when you really can't write to
	 * the file, based on the read-only attribute. is_writable() is also unreliable
	 * on Unix servers if safe_mode is on.
	 *
	 * @link	https://bugs.php.net/bug.php?id=54709
	 * @param	string
	 * @return	bool
	 */
	function is_really_writable($file)
	{
		// If we're on a Unix server with safe_mode off we call is_writable
		if (DIRECTORY_SEPARATOR === '/' && (is_php('5.4') OR ! ini_get('safe_mode')))
		{
			return is_writable($file);
		}

		/* For Windows servers and safe_mode "on" installations we'll actually
		 * write a file then read it. Bah...
		 */
		if (is_dir($file))
		{
			$file = rtrim($file, '/').'/'.md5(mt_rand());
			if (($fp = @fopen($file, 'ab')) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			@chmod($file, 0777);
			@unlink($file);
			return TRUE;
		}
		elseif ( ! is_file($file) OR ($fp = @fopen($file, 'ab')) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}
	
	/**
	 * Determines if the current version of PHP is equal to or greater than the supplied value
	 *
	 * @param	string
	 * @return	bool	TRUE if the current version is $version or higher
	 */
	function is_php($version)
	{
		static $_is_php;
		$version = (string) $version;

		if ( ! isset($_is_php[$version]))
		{
			$_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
		}

		return $_is_php[$version];
	}
	
	/**
	 * 记录日志
	 *
	 * We use this as a simple mechanism to access the logging
	 * class and send messages to be logged.
	 *
	 * @param	string	the error level: 'error', 'debug' or 'info'
	 * @param	string	the error message
	 * @return	void
	 */
	function log_message($level, $message)
	{
		static $_log;

		if ($_log === NULL)
		{
			// references cannot be directly assigned to static variables, so we use an array
			$_log[0] =& load_class('Log');
		}

		$_log[0]->write_log($level, $message);
	}
	/**
	* 将秒换算成时分秒
	*
	* @param int $seconds
	* @author aaron
	* @return string 
	*
	*/
	function second2hour($seconds = 0)
	{
		$seconds = (int)$seconds;
		if($seconds == 0)
		{
			return 0;
		}
		//hour
		$hour = floor($seconds / 3600);
		
		$minutes_seconds = $seconds % 3600;
		$minutes = floor($minutes_seconds / 60);
		$left_seconds = $seconds % 60;
		$duration = '';
		if($hour != 0)
		{
			$duration .= $hour . '时';
		}
		if($minutes != 0)
		{
			$duration .= $minutes . '分';
		}
		if($left_seconds != 0)
		{
			$duration .= $left_seconds . '秒';
		}
		return $duration;
	}
	

    /**
	* 直接将输出信息打印出来
	*
	* @param int $seconds
	* @param string  $level 日志级别  FATAL ERROR DEBUG INFO 
	* @author aaron
	* @return string 
	*
	*/
    function simple_log($log_str = '', $level = 'DEBUG')
    {
        echo date('Y-m-d H:i:s') . '[' . $level . ']-' . $log_str . "\n";
    }
	/**
	* 随机字符串
	*
	* @param int $seconds
	* @author aaron
	* @return string 
	*
	*/
	function random_string($type = 'alnum', $len = 8)
	{
		switch ($type)
		{
			case 'basic':
				return mt_rand();
			case 'alnum':
			case 'numeric':
			case 'nozero':
			case 'alpha':
				switch ($type)
				{
					case 'alpha':
						$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'alnum':
						$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'numeric':
						$pool = '0123456789';
						break;
					case 'nozero':
						$pool = '123456789';
						break;
				}
				return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
			case 'unique': // todo: remove in 3.1+
			case 'md5':
				return md5(uniqid(mt_rand()));
			case 'encrypt': // todo: remove in 3.1+
			case 'sha1':
				return sha1(uniqid(mt_rand(), TRUE));
		}
	}
	/**
	* 服务器请求失败报警
	*
	* @param int $seconds
	* @author aaron
	* @return string 
	*
	*/
	function alert_msg($service_id, $msg)
	{
		$rpc_client = load_class('Rest');
		$alert_url = config_item('alert_url');
		
		$res = $rpc_client->curlInit($alert_url,array(),'POST',array('id'=>$service_id,'msg'=>$msg));
		if($res[0] = 0 && $res[2] == 200)
		{
			return true;
		}
		else
		{
			simple_log('request ' . $alert_url . ' failed!');
		}
	
	}
    
	/**
    * curl 请求二次封装
    * @param string $url 请求url
    * @param array $param 请求参数  key-》value
    * @param array $query_array 查询字符串，会附加到url中
    * @param string $mime mime类型  一般可不用加
    * @param boolean $is_decode 是否对响应结果进行返解
    * @return string
    */
    function http_request($url,$param,$method = 'post',$query_array = array(),$mime = '',$is_decode = false)
    {
        $rest = load_class('Rest');
        
        $response = $rest->curlInit($url,$query_array,$method,$param,$mime);
        if($response[0] == 0 && $response[2] == 200)
        {
            if($is_decode)
            {
                $result = json_decode($response[1],true);
                return $result;
            }
            return $response[1];
        }
        return false;
    }
    
	function remove_relative_directory($uri)
	{
		$uris = array();
		$tok = strtok($uri, '/');
		while ($tok !== FALSE)
		{
			if (( ! empty($tok) OR $tok === '0') && $tok !== '..')
			{
				$uris[] = $tok;
			}
			$tok = strtok('/');
		}

		return implode('/', $uris);
	}
	
	
	/**
     *
     * 将简单数组转化为简单的xml
     * @param string $data  要进行转化的数组
     * @param string $tag   要使用的标签
     * @example
     * $arr = array(
        'rtxAccount'=>'aaron','ipAddr'=>'192.168.0.12',
        'conferenceList'=>array('conference'=>
                            array(
                                array('conferenceId'=>1212,'conferenceTitle'=>'quanshi 444','smeAccount'=>'bingxu.dong@quanshi.com'),
                                array('conferenceId'=>454,'conferenceTitle'=>'quanshi meetting','smeAccount'=>'bingxu.dong@quanshi.com'),
                                array('conferenceId'=>6767,'conferenceTitle'=>'quanshi meetting','smeAccount'=>'bingxu.dong@quanshi.com'),
                                array('conferenceId'=>232323,'conferenceTitle'=>'quanshi uuu','smeAccount'=>'bingxu.dong@quanshi.com'),
                                array('conferenceId'=>8989,'conferenceTitle'=>'quanshi meetting','smeAccount'=>'bingxu.dong@quanshi.com'),
                                array('conferenceId'=>1234343212,'conferenceTitle'=>'quanshi meetting','smeAccount'=>'bingxu.dong@quanshi.com')
                                )
                            )
        
                                
        );
        转化为：
        <rtxAccount>aaron</rtxAccount>
        <ipAddr>192.168.0.12</ipAddr>
        <conferenceList>
            <conference>
                <conferenceId>1212</conferenceId>
                <conferenceTitle>quanshi 444</conferenceTitle>
                <smeAccount>bingxu.dong@quanshi.com</smeAccount>
            </conference>
            <conference>
                <conferenceId>454</conferenceId>
                <conferenceTitle>quanshi meetting</conferenceTitle>
                <smeAccount>bingxu.dong@quanshi.com</smeAccount>
            </conference>
            <conference>
                <conferenceId>6767</conferenceId>
                <conferenceTitle>quanshi meetting</conferenceTitle>
                <smeAccount>bingxu.dong@quanshi.com</smeAccount>
            </conference>
            <conference>
                <conferenceId>232323</conferenceId>
                <conferenceTitle>quanshi uuu</conferenceTitle>
                <smeAccount>bingxu.dong@quanshi.com</smeAccount>
            </conference>
            <conference>
                <conferenceId>8989</conferenceId>
                <conferenceTitle>quanshi meetting</conferenceTitle>
                <smeAccount>bingxu.dong@quanshi.com</smeAccount>
            </conference>
            <conference>
                <conferenceId>1234343212</conferenceId>
                <conferenceTitle>quanshi meetting</conferenceTitle>
                <smeAccount>bingxu.dong@quanshi.com</smeAccount>
            </conference>
        </conferenceList>
     */
    function array2xml($data,$tag = '')
    {
        $xml = '';
        
        foreach($data as $key => $value)
        {
            if(is_numeric($key))
            {
                if(is_array($value))
                {
                    $xml .= "<$tag>";
                    $xml .= array2xml($value);
                    $xml .="</$tag>";
                }
                else
                {
                    $xml .= "<$tag>$value</$tag>";
                }    
            }
            else
            {
                if(is_array($value))
                {
                    $keys = array_keys($value);
                    if(is_numeric($keys[0]))
                    {
                        $xml .=array2xml($value,$key);
                    }
                    else
                    {
                        $xml .= "<$key>";
                        $xml .=array2xml($value);
                        $xml .= "</$key>";
                    }
                    
                }
                else
                {
                    $xml .= "<$key>$value</$key>";
                }
            }
        }
        return $xml;
    }

    /**
	 * 
	 * 将简单的xml转化成关联数组
	 * @param string $xmlString  xml字符串
	 * @example
	 * <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <RTXConferenceReqDTO>
            <conferenceTitle>IT交流会</conferenceTitle>
            <startTime>2011-12-19 12:00:00</startTime>
            <rtxAccount>andy1111111</rtxAccount>
            <ipAddr>192.168.1.56</ipAddr>
            <duration>120</duration>
            <conferenceType>1</conferenceType>
            <invitees>
                <invitee>
                    <rtxAccount>被邀请人1的RTX账号</rtxAccount>
                    <tel>被邀请人1电话号码</tel>
                </invitee>
                <invitee>
                    <rtxAccount>被邀请人2的RTX账号</rtxAccount>
                    <tel>被邀请人2电话号码</tel>
                </invitee>
            </invitees>
        </RTXConferenceReqDTO>
        转化之后的关联数组：
        Array
        (
            [conferenceTitle] => IT交流会
            [startTime] => 2011-12-19 12:00:00
            [rtxAccount] => andy1111111
            [ipAddr] => 192.168.1.56
            [duration] => 120
            [conferenceType] => 1
            [invitees] => Array
                (
                    [invitee] => Array
                        (
                            [0] => Array
                                (
                                    [rtxAccount] => 被邀请人1的RTX账号
                                    [tel] => 被邀请人1电话号码
                                )

                            [1] => Array
                                (
                                    [rtxAccount] => 被邀请人2的RTX账号
                                    [tel] => 被邀请人2电话号码
                                )

                        )

                )

        )
	 */
	function xml2array($xmlString = '')
	{
		$targetArray = array();
		$xmlObject = simplexml_load_string($xmlString);
		$mixArray = (array)$xmlObject;
		foreach($mixArray as $key => $value)
		{
			if(is_string($value))
			{
				$targetArray[$key] = $value;
			}
			if(is_object($value))
			{
				$targetArray[$key] = xml2array($value->asXML());
			}
			if(is_array($value))
			{
				foreach($value as $zkey => $zvalue)
				{
					if(is_numeric($zkey))
					{
						if(is_string($zvalue))
                        {
                            $targetArray[$key][] = $zvalue;
                        }
                        else
                        {
                            $targetArray[$key][] = xml2array($zvalue->asXML());
                        }
                        
					}
					if(is_string($zkey))
					{
						if(is_string($zvalue))
                        {
                            $targetArray[$key][$zkey] = $zvalue;
                        }
                        else
                        {
                            $targetArray[$key][$zkey] = xml2array($zvalue->asXML());
                        }
                        
					}
				}
			}
		}
		return $targetArray;
		
	}
    
if ( ! function_exists('write_file'))
{
	/**
	 * Write File
	 *
	 * Writes data to the file specified in the path.
	 * Creates a new file if non-existent.
	 *
	 * @param	string	$path	File path
	 * @param	string	$data	Data to write
	 * @param	string	$mode	fopen() mode (default: 'wb')
	 * @return	bool
	 */
	function write_file($path, $data, $mode = 'wb')
	{
		if ( ! $fp = @fopen($path, $mode))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);

		for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, substr($data, $written))) === FALSE)
			{
				break;
			}
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return is_int($result);
	}
}
    
    /**
	 * 根据车牌号 获取车牌号中的数字，从而推算出该车的进出记录存在哪个表中
	 *
	 *
	 * @param	string	$vpl	车牌号
	 * @return	int
	 */
    function match_table($vpl)
    {
        //得到数字
        $numbers = [];
        preg_match_all('/[0-9]/', $vpl, $numbers);
        if(empty($numbers) || empty($numbers[0]))
        {
            return 'odd';
        }
        
        $num = intval(implode('', $numbers[0]));
        return $num % 100;
    }
    
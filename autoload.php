<?php
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

require(ROOT_PATH . 'common.php');
// 注册自动加载方法
spl_autoload_register('my_loader');

function my_loader($class_name)
{
	if(file_exists(ROOT_PATH . $class_name . '.php'))
	{
		include ROOT_PATH . $class_name . '.php';
		return;
	}
	
	if(file_exists(ROOT_PATH . 'lib/' . $class_name . '.php'))
	{
		include ROOT_PATH . 'lib/' . $class_name . '.php';
        return;
	}
    if(file_exists(ROOT_PATH . 'core/' . $class_name . '.php'))
    {
        include ROOT_PATH . 'core/' . $class_name . '.php';
        return;
    }
    //自动加载钩子
    if(file_exists(ROOT_PATH . 'hooks/' . $class_name . '.php'))
    {
        include ROOT_PATH . 'hooks/' . $class_name . '.php';
        return;
    }
}
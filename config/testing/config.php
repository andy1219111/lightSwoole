<?php
//端口配置
$config['port'] = 9523;
//接收请求的worker数量
$config['worker_num'] = 6;
//任务进程数量
$config['task_worker_num'] = 4;

//config_cloud数据库配置
$config['db'] = array(
                                'dsn'=>'mysql:host=127.0.0.1;port=4000;dbname=config_cloud',
                                'username'=>'config',
                                'password'=>'RmUyNLd95sZr1fcbouRxLXuKa',
                                'charset'=>'utf8',
                            );
                         
//进出车数据rabbitMQ相关配置
$config['rabbitmq_param'] = array('host'=>'127.0.0.1',
									'port'=>'5672',
									'login' => 'guest',
									'password' => 'guest',
									'vhost'=>'/',
									'exchange_name'=>'arm_msg_broadcast_test',
									'route_key'=>'arm_msg_broadcast_test',
									'queue_name'=>'ipk_monitor'
								);
                          
/*
	log_threshold
|	0 = Disables logging, Error logging TURNED OFF
|	1 = Error Messages (including PHP errors)
|	2 = Debug Messages
|	3 = Informational Messages
|	4 = All Messages
*/
/*
$config['log_config'] = array('log_path'=>'/home/data2/logs/eis_server/',
							'log_file_extension'=>'log',
							'log_threshold'=>4
							);
*/
$config['log_config'] = array('log_path'=>'/home/data1/dongbingxu/IPK_API/log/',
							'log_file_extension'=>'log',
							'log_threshold'=>4,
							'log_max_files'=>10
							);

//公共数据缓存
$config['public_data_redis'] = array(
	'host' => '127.0.0.1',
	'port' => '16379',
	'auth' => ''
);
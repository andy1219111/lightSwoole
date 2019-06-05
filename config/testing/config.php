<?php
//端口配置
$config['port'] = 9523;
//接收请求的worker数量
$config['worker_num'] = 6;
//任务进程数量
$config['task_worker_num'] = 4;

//config_cloud数据库配置
$config['config_cloud_db'] = array(
                                'dsn'=>'mysql:host=127.0.0.1;port=4000;dbname=config_cloud',
                                'username'=>'config',
                                'password'=>'RmUyNLd95sZr1fcbouRxLXuKa',
                                'charset'=>'utf8',
                            );
//IOP数据库配置
$config['iop_db'] = array('dsn'=>'mysql:host=127.0.0.1;port=4000;dbname=iop',
								'username'=>'iop',
								'password'=>'XQtCnHF3H9ZSS2xPAWxd9eI4q7VbPE',
								'charset'=>'utf8',
							);
//I2P数据库配置
$config['i2p_db'] = array('dsn'=>'mysql:host=127.0.0.1;port=4000;dbname=i2p',
								'username'=>'i2p',
								'password'=>'COjVxg1E8e1HP9KP2PYktMye1',
								'charset'=>'utf8',
							);
//park_cloud数据库配置
$config['park_cloud_db'] = array('dsn'=>'mysql:host=127.0.0.1;port=4000;dbname=park_cloud_new',
								'username'=>'reader',
								'password'=>'VVkCk9yd9TSjKRa',
								'charset'=>'utf8',
							);
//inside_cars数据库配置
$config['inside_cars_db'] = array('dsn'=>'mysql:host=127.0.0.1;port=4000;dbname=inside_cars',
								'username'=>'insidecars',
								'password'=>'AgLxFhn23480df1qZWYt47NUjLDpVQRG',
								'charset'=>'utf8',
							);
//ebills数据库配置
$config['ebills_db'] = array('dsn'=>'mysql:host=127.0.0.1;port=4000;dbname=ebills',
								'username'=>'reader',
								'password'=>'VVkCk9yd9TSjKRa',
								'charset'=>'utf8',
							); 
//IRS数据库配置
$config['irs_db'] = array('dsn'=>'mysql:host=127.0.0.1;port=4000;dbname=irs',
								'username'=>'irs',
								'password'=>'XQtCnHF3H9ZSS2xPAWxd9eI4q7VbPE',
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
//支付信息队列
$config['pay_rabbitmq_param'] = array(
                                'host'=>'127.0.0.1',
                                'port'=>'5672',
                                'login' => 'guest',
                                'password' => 'guest',
                                'vhost'=>'/',
                                'exchange_name'=>'bill_collector_test',
                                'route_key'=>'bill_collector_test',
                                'queue_name'=>'ipk_api_payment',
                            );

//pad_agent队列
$config['ipk_arm_websocket_rabbitmq_param'] = array('host'=>'127.0.0.1',
									'port'=>'5672',
									'login' => 'guest',
									'password' => 'guest',
									'vhost'=>'/',
									'exchange_name'=>'ipk_arm_websocket',
									'route_key'=>'ipk_arm_websocket',
									'queue_name'=>'arm_websocket_monitor'
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
                            
//主动报警系统接口
$config['alert_url'] = 'http://10.168.245.106:8092/api/alert/report';
$config['service_id'] = '28';

//smtp服务器配置
$config['smtp_server'] = 'smtp.263.net';
$config['smtp_port'] = '25';
$config['smtp_username'] = 'support@parkingwang.com';
$config['smtp_password'] = 'asd888';
$config['smtp_from'] = 'support@parkingwang.com';
$config['smtp_from_name'] = '停车王';


//token键值
$config['token_key'] = 'IOP:parkeeper_token:';

//token 缓存配置
$config['token_redis'] = array(
	'host' => '127.0.0.1',
	'port' => '16379',
    'dbId' => 11,
	'auth' => ''
);

//授权类型配置
$config['auth_type'] = array('v3'=>array(
                                    '1'   => '白名单',
                                    '2'   => '黑名单',
                                    '3'   => '授权收费一',
                                    '4'   => '授权收费二',
                                    '5'   => '月租车',
                                    '6'   => '商户优惠车',
                                    '7'   => '协议单位优惠车',
                                    '8'   => '员工车',
                                    '9'   => '免费车',
                                    '10'  => '内部车',
                                    '11'  => '储值车',
                                    '12'  => '周期封顶车',
                                    '13'  => '储值周期封顶车',
                                    '14'  => '储时车',
                                    '15'  => '军警车'
                                    ),
                                'v2'=>array(
                                    '1'=>'月租车',
                                    '2'=>'商户优惠车',
                                    '3'=>'协议单位优惠车',
                                    '6'=>'内部车',
                                    '4'=>'员工车',
                                    '5'=>'免费车',
                                    '98'=>'授权收费一',
                                    '99'=>'授权收费二',
                                    '100'=>'储值车',
                                    '101'=>'周期封顶车',
                                    '102'=>'储值周期封顶车',
                                    '103'=>'储时车'	
                                )
                            );
//支付宝和微信付款码的前两位配置
$config['pay_code_flag'] = [
                            'wechat'=>['10','11','12','13','14','15'],
                            'alipay'=>['25','26','27','28','29','30']
                            ];
//加解密key
$config['secret_key'] = '@parkingwang.com';

//base url
$config['base_url'] = 'http://116.62.132.145:9523';
//进出车图片域名配置
$config['inout_img_domain'] = 'http://img.parkingwang.com';



// ARM 收费类型参考
$config['arm_pay_type'] = array(
	0 => '短时免费',
	1 => '现金',
	2 => '后支付',
	3 => 'POS机',
	4 => '在线支付',
	5 => '纸质小时券',
	6 => '电子小时券',
	7 => '授权车免费',
	8 => '手动免费开闸',
	9 => '自动抬杆时间放行',
	10 => '储值划扣',
	11 => '封顶放行',
	12 => '提前支付预留时间放行',
	13 => '会员积分支付',
	14 => '公交卡支付',
	15 => '储时划扣',
	16 => '电子优惠券金额券',
	17 => '电子优惠券免次券',
	18 => '电子优惠券时段免费券',
	19 => '纸质优惠券金额券',
	20 => '权益卡次数',
	21 => '电子折扣券',
	22 => '纸质折扣券',
	23 => '权益卡金额',
	24 => '权益卡时间',
	47 => '电子通用金额券',
	48 => '微信',
	49 => '支付宝',
	50 => '银联',
	51 => '纸质次数券',
	52 => '计费免费',
	53 => '第三方微信',
	54 => '第三方支付宝',
	55 => '第三方其他支付',
	56 => '第三方金额积分支付',
	57 => '第三方金额优惠券支付',
    58 => '折扣金额',
    59 => '线上代金券',
    60 => '停车王钱包',
    61 => 'ETC',
    62 => '翼支付',
    63 => '财付通',
    64 => 'applePay',
    65 => '快钱支付',
    66 => '易付宝',
    67 => '云网支付'
);

//金额优惠项目
$config['discount_channels'] = array(
    '16' => '电子金额券',
    '19' => '纸质金额券',
    '21' => '电子折扣券',
    '22' => '纸质折扣券',
    '47' => '通用金额券',
    '13' => '积分抵扣',
    '56' => '第三方积分抵扣',//第三方积分
    '57' => '第三方优惠券',
    '59' => '停车王代金券',
    '58' => '折扣金额'
);
//时间段优惠项目
$config['duration_discount_channels'] = array(
    '6' => '电子小时券',
    '5' => '纸质小时券',
    '18' => '电子时段券',
    '24' => '时间权益卡'
);

//次数类优惠券
$config['time_discount_channels'] = array(
    '17' => '电子次数券',
    '20' => '次数权益卡',
    '51' => '纸质次数券',
);

//IPK 收费类型配置
$config['ipk_charge_types'] = array(
    '20' => '现金',
    '38' => '支付宝',
    '18' => '微信',
    '8' => '免费开闸'
);
//对讲服务器配置
$config['intercom_server'] = 'www.parkingwang.net:50802';
//对讲服务器密码
$config['intercom_password'] = 'irain#0818';
//ebills/ipay 的大支付类型配置
$config['ipay_channel'] = [
                        '1'=>'现金',
                        '2'=>'停车王钱包',
                        '3'=>'微信',
                        '4'=>'支付宝',
                        '5'=>'银联',
                        '6'=>'停车卡',
                        '7'=>'招行一网通',
                        '8'=>'建行龙支付',
                        '9'=>'中信银行',
                        '10'=>'建行-西部集团清算平台',
                        '11'=>'独墅湖',
                        '12'=>'少林寺',
                        '13'=>'蜂鸟',
                        '14'=>'云闪付',
                        '15'=>'深圳建行',
                        '16'=>'银联UPARK平台',
                        '98'=>'开放平台',
                        '99'=>'第三方',
                        '101'=>'齐鲁高速积分',
                        '102'=>'民生智家',
                        '103'=>'杭州建行无感',
                        '104'=>'招行智慧停车',
                        '105'=>'杭州共停(中行)',
                        '106'=>'苏奥中心'
                        ];
## lightSwoole

-------

[TOC]

#### 缘起
如果你需要一款框架，专注于API开发，如果你厌倦了laravel、thinkPHP、CodeIgniter等框架的繁冗，如果你想花很少的时间去学习这个框架，那么请使用lightSwoole吧。
lightSwoole是一个基于swoole的`swoole_http_server`实现的http框架。

#### 约束
- php版本5.6+
- swoole版本1.9+

#### 框架目录结构
1. `application` 应用程序目录，如果你用过codeIgniter、laravel等框架的话肯定对这个目录很熟悉。这个目录就是应用程序目录，以后你写的业务代码大都会放在这里，这下面主要包含了两个目录：
	1).  `controller`，这里存放所有的控制器
	2).  `model`，这里存放所有的模型类，原则上，一个模型应该对应一张物理表
2. `config`,配置文件目录
3. `core`，框架核心目录，目前里面只有两个文件`Controller.php`模型和`Model.php`，分别为顶级控制器文件和顶级模型文件，application下所有的文件都继承自这两个文件，如果你想增加公共的方法，可在这两个类中扩充。
4.  `hooks`，程序钩子存放目录，目前埋点了两个钩子：“On_request_hook”，刚接到请求是执行的钩子；“Pre_handle_hook”，在处理请求之前(进入业务controller之前)做的预处理，一般在这里做一些登录校验、权限校验等。
5.  `lib`，类库目录，所有用到的第三方的、自己开发的类库都放到这里。

#### 配置
所有的框架，在使用前，都需要先从配置开始。在lightSwoole中，配置文件放在`config`目录下，有'production'和'testing'两个目录，代表着两个运行环境的配置，你可以根据自己的需要增加配置组或者重命名。框架运行起来后，会从环境变量中读取环境配置参数.
- 在linux中设置环境变量：
```
# 编辑/etc/profile文件
vim /etc/profile
#在文件最后加入 'export LIGHT_SWOOLE_ENV=testing'
export LIGHT_SWOOLE_ENV=testing
#保存文件，执行
source /etc/profile
#查看是否设置成功
echo $LIGHT_SWOOLE_ENV
```
- 添加自己的配置项
你可以在原有配置文件中增加自己的配置项，也可以根据配置文件的用途新增配置文件。在配置文件中增加`$config['配置项key']`即可。

- 在程序中读取配置参数
在配置文件'redis.php'中，有如下配置信息:
```
//redis配置信息
$config['redis1'] = array(
	'host' => '127.0.0.1',
	'port' => '16379',
	'auth' => ''
);
```
在程序中获取配置,`config_item($config_key,[$config_file = 'config.php'])`方法需要两个参数，第一个参数为配置项的键值，第二个为配置文件的文件名，默认从`config.php`中读取。
```
$redis_config = config_item('redis1', 'redis.php');
```

#### 控制器
如你之间使用MVC框架的经验类似，controller是整个业务框架中最重要的部分，控制器的名字和URL中urI部分一一对应(后面的版本中会引入route，但仍然保证uri和controller是有对应关系的)。举个栗子：你的请求的URL为`http://myapi.com/my_project/hello_world`,与此URL对应的控制器为`controller/My_project.php`中的`hello_world`方法。
所有的controller必须继承自顶级`Controller`，该文件在`core/Controller.php`.
在获取请求参数、发送响应时，需要用到swoole的[`swoole_http_request`](https://wiki.swoole.com/wiki/page/328.html)类和[`swoole_http_response`](https://wiki.swoole.com/wiki/page/329.html)类，这两个类的用法请去swoole官方网站了解。控制器的栗子：
```
/**
 * 这是一个控制器的例子
 *
 * @author aaron
 * @version 2016-10-17
 *
 */
class Welcome extends Controller{

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
```
#### 模型
模型就是和数据库打交道的类，建议一个模型类对应数据库的一张表。为了降低框架的学习成本，以及更高的执行效率，lightSwoole没有引入ORM框架。在lightSwoole中，每一个model必须继承自`Model`顶级模型，该模型位于`core/Model.php`中。如果你没有特殊需要，你新建的模型中不需要新建任何方法，所有有关数据库操作的方法将继承自Model顶级模型类。
在controller中引用model使用`load_model`方法，该方法接收两个参数：模型名称以及该模型对应的数据库配置值。
- 在controller中引用model：
```
 $model = load_model('example_model', 'db1');
```
然后，就可以在controller中使用model的方法了。

- 一个model的栗子：
```
/**
* 一个关于模型的例子
*
*
*/
Class Example_model extends Model{

    public $table_name = 'example';
    
    
    /**
    * 构造函数
    * @param db_config string  数据库配置
    */
    public function __construct($db_config)
    {
        parent::__construct($db_config);
        
    }
    
    /**
    * 执行sql语句 
    *
    */
    function query($where,$order_by = '',$limit = 1)
    {
        $sql = <<<SQL
            SELECT 
                C.park_code,
                C.depot_id,
                username,
                C.cardno,
                detail_type,
                phone,
                validbtime,
                validetime 
            FROM
                t_card_info C 
            INNER JOIN 
                t_authcars A 
            ON 
                C.park_code = A.park_code AND C.cardno = A.cardno
                  
            WHERE 
                $where order by $order_by limit $limit
SQL;
        $stmt = $this->dbo->query($sql);
        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 
}
```
- 数据库配置存放于config/config.php中，数据库的配置支持读写分离，你只需要在配置中增加‘r’（读数据库），‘w’（写数据库）即可；你也可以不配置主从，参考'db2'配置，你可以在这里添加任意个你自己的数据库配置信息：
```
//数据库配置1，配置了读写分离
$config['db1'] = [
					'w'=>[
							'dsn'=>'mysql:host=127.0.0.1;port=3307;dbname=huoxiaoyuan',
							'username'=>'huoxiaoyuan',
							'password'=>'PrEdd4bnfFdRwAHP',
							'charset'=>'utf8',
							//使用持久化连接
							'is_persistent'=>true,
						],
					'r'=>[
						'dsn'=>'mysql:host=127.0.0.1;port=3307;dbname=huoxiaoyuan',
						'username'=>'huoxiaoyuan',
						'password'=>'PrEdd4bnfFdRwAHP',
						'charset'=>'utf8',
						//使用持久化连接
						'is_persistent'=>true,
							//访问权重
							//'weight'=>1,
						]
				];
//数据库配置2，未使用读写分离配置
$config['db2'] = [
                    'dsn'=>'mysql:host=127.0.0.1;port=3307;dbname=huoxiaoyuan',
                    'username'=>'huoxiaoyuan',
                    'password'=>'PrEdd4bnfFdRwAHP',
                    'charset'=>'utf8',
                    //使用持久化连接
                    'is_persistent'=>true,
				];
```


#### 类库
待补充

#### 钩子

待补充


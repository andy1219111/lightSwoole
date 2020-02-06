<?php

/**
 * 数据库连接对象管理器
 * 
 * @author aaron
 * @version 1.0.0
 */
class DBOManager
{

    private $db_config = [];

    //存放数据库连接对象的容器 读写分离
    private $connections = ['w' => null, 'r' => null];

    static $instances = [];

    /**
     * 得到数据库连接管理器对象
     *
     * @param array $cofnig 包含数据库配置的数组
     * @return void
     */
    public static function get_instance($config_key)
    {
        //数据库配置项的key，每个配置创建一个实例，保存在一个键值数组中
        if(isset(self::$instances[$config_key]) && self::$instances[$config_key] instanceof self){
            return self::$instances[$config_key];
        }
        self::$instances[$config_key] = new self();
        //根据配置项 得到具体的配置参数 进行DBO对象的初始化
        self::$instances[$config_key]->init(config_item($config_key));
        return self::$instances[$config_key];
    }

    
    /**
     * 初始化数据路连接对象
     *
     * @param array $cofnig 包含数据库配置的数组
     * @return void
     */
    private function init($config)
    {
        $this->db_config = $config;
        if (!isset($config['w'])) {
            $this->connections['w'] = new DBO($config);
            $this->connections['r'] = $this->connections['w'];
        } else {
            //进行读写分离的对象构建
            $this->connections['w'] = new DBO($config['w']);
            $this->connections['r'] = new DBO($config['r']);
        }
    }

    /**
     * 从表中查询符合条件的一条数据
     *
     * @param string $table
     * @param array $param
     * @return array
     */
    function select_one($table,array $param,$fields = '*')
    {
        $param['limit'] = 1;
        $results = $this->connections['r']->select($table,$param,$fields);
        if(empty($results)){
            return [];
        }
        return $results[0];
    }

    /**
     * 进行select操作
     *
     * @param string $table
     * @param array $param
     * @return array
     */
    function select($table,array $param,$fields = '*')
    {
       return $this->connections['r']->select($table,$param,$fields);
    }

    /**
     * 进行select操作
     *
     * @param string $table
     * @param array $param
     * @return array
     */
    function count($table,$where)
    {
       return $this->connections['r']->count($table,$where);
    }

    /**
     * 执行更新操作
     *
     * @param string $table
     * @param array $data
     * @param mixed $where
     * @return int  影响的行数
     */
    function update($table,$data,$where)
    {
        return $this->connections['w']->update($table,$data,$where);
    }

    /**
     * 执行插入操作
     *
     * @param string $table
     * @param array $data
     * @return int  影响的行数
     */
    function insert($table,$data)
    {
        return $this->connections['w']->insert($table,$data);
    }
        
    /**
     * 执行删除操作
     *
     * @param string $table
     * @param mixed $where
     * @return int  影响的行数
     */
    function delete($table,$where)
    {
        return $this->connections['w']->delete($table,$where);
    }

    /**
     * 执行替换操作
     *
     * @param string $table
     * @param array $data
     * @return int  影响的行数
     */
    function replace($table,$data)
    {
        return $this->connections['w']->replace($table,$data);
    }

    /**
     * 执行插入或更新操作
     *
     * @param string $table
     * @param array $add_values
     * @param array $update_values
     * @return int 影响的行数
     */
    function duplicate_insert($table, $add_values, $update_values)
    {
        return $this->connections['w']->duplicate_insert($table, $add_values, $update_values);
    }

    /**
     * 执行自定义的sql语句 用于执行较复杂的sql
     *
     * @param string $sql
     * @return mixed 查询得到的数据或影响的行数
     */
    function query($sql)
    {
        $sql = trim($sql);
		if (strpos($sql, 'select') === 0 || strpos($sql, 'SELECT') === 0) {
			return $this->connections['r']->query($sql);
		} else {
			return $this->connections['w']->exec($sql);
		}
    }

    /**
     * 开启事务
     *
     * @return void
     */
    function begin_transaction()
    {
        $this->connections['w']->begin_transaction();
    }

    /**
     * 提交事务
     *
     * @return void
     */
    function commit()
    {
        $this->connections['w']->commit();
    }

    /**
     * 回滚事务
     *
     * @return void
     */
    function roll_back()
    {
        $this->connections['w']->roll_back();
    }

}

<?php

/**
 * 数据库连接对象管理器
 * 
 * @author aaron
 * @version 1.0.0
 */
class DBOManager
{

    private $db_config = null;

    //存放数据库连接对象的容器 读写分离
    private $connections = ['w' => null, 'r' => null];

    private $instance = null;

    /**
     * 得到数据库连接管理器对象
     *
     * @param array $cofnig 包含数据库配置的数组
     * @return void
     */
    public static function get_instance($cofnig)
    {
        if($this->instance instanceof self){
            return $this->instance;
        }
        $this->instance = new self();
        $this->instance->init($cofnig);
        return $this->instance;
    }

    
    /**
     * 初始化数据路连接对象
     *
     * @param array $cofnig 包含数据库配置的数组
     * @return void
     */
    private function init($cofnig)
    {
        $this->db_config = $cofnig;
        if (!isset($cofnig['w'])) {
            $this->connections['w'] = new DBO($cofnig);
            $this->connections['r'] = $this->connections['w'];
        } else {
            //进行读写分离的对象构建
            $this->connections['w'] = new DBO($cofnig['w']);
            $this->connections['r'] = new DBO($cofnig['r']);
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
        return $this->connections['w']->replace($table, $add_values, $update_values);
    }

}

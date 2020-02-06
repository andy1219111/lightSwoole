<?php
/**
* 顶级model类  所有的model都继承自改model
*
*
*/
Class Model {

    public $table_name = '';
    
    //数据库连接对象管理器的实例 在DBO类之外封装了一层，用于实现读写分离
    public $dbo = NULL;
    
    /**
    * 构造函数
    *
    * @param string $config_key 数据库配置的键值，一般用于区分是哪个数据库配置，用于配置文件中有多套数据配置的情况
    * @return void
    */
    public function __construct($config_key)
    {
        $this->dbo = DBOManager::get_instance($config_key); 
    }
    
    /**
    * get 方法
    *
    */
    public function __get($key)
    {
        $_controller = Controller::get_instance();
        return $_controller->$key;
    }
    
    /**
    * 获取一条符合条件的信息
    *
    * @param array fields 要查询的字段信息
    * @param string where 查询条件
    * @param string order_by 
    * @param int limit  
    * @return array
    *
    */
    function getone($where = '', $order_by = '', $fields = '*')
    {
        if($where != '')
        {
            $param['where'] = $where;
        }
        
        if($order_by != '')
        {
            $param['order'] = $order_by;
        }
        return $this->dbo->select_one($this->table_name, $param, $fields);
    }
    
    /**
    * 获取一条符合条件的信息
    *
    * @param array fields 要查询的字段信息
    * @param string where 查询条件
    * @param string order_by 
    * @param int limit  
    * @return array
    *
    */
    function get($where, $order_by = '', $limit = 0, $fields = '*')
    {
        $param['where'] = $where;
        if($limit !== 0)
        {
            $param['limit'] = $limit;
        }
        if($order_by != '')
        {
            $param['order'] = $order_by;
        }
        $data = $this->dbo->select($this->table_name, $param, $fields);
        return $data;
    }
    
    /**
    * 插入记录
    *
    * @param array data 要插入表中的数据
    * @return array
    *
    */
    function count($where = '')
    {
        return $this->dbo->count($this->table_name, $where);
    }
    
    /**
    * 插入记录
    *
    * @param array data 要插入表中的数据
    * @return array
    *
    */
    function insert($data)
    {
        return $this->dbo->insert($this->table_name, $data);
    }
    
    /**
    * 插入或者覆盖
    *
    * @param array data 要插入表中的数据
    * @return array
    *
    */
    function replace($data)
    {
        return $this->dbo->replace($this->table_name, $data);
    }
    
    /**
    * 删除记录
    *
    * @param array where 包含删除条件的数组
    * @return array
    *
    */
    function delete($where)
    {
        return $this->dbo->delete($this->table_name, $where);
    }
    
    /**
    * 更新记录
    *
    * @param array data 包含了更新字段的数组
    * @param array where 包含了更新条件的字符串数组
    * @return int  影响的行数
    *
    */
    function update($data, $where)
    {
        return $this->dbo->update($this->table_name, $data, $where);
    }
    
    
    /**
    * duplicate insert有相应记录更新，否则插入
    *
    * @param array add_values 插入字段数组
    * @param array update_values 更新字段数组
    * @return int  影响的行数
    *
    */
    function duplicate_insert($add_values, $update_values)
    {
        return $this->dbo->duplicate_insert($this->table_name, $add_values, $update_values);
    }

}
<?php
/**
* 顶级model类  所有的model都继承自改model
*
*
*/
Class Model {

    public $table_name = '';
    
    //数据库连接对象的实例
    public $dbo = NULL;
    
    /**
    * 构造函数
    *
    */
    public function __construct($db_config)
    {
        $this->dbo = new DBO(config_item($db_config)); 
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
    function getone($fields, $where = '', $order_by = '')
    {
        if($where != '')
        {
            $param['where'] = $where;
        }
        
        if($order_by != '')
        {
            $param['order'] = $order_by;
        }
        $param['limit'] = 1;
        $data = $this->dbo->select($this->table_name, $fields, $param);
        
        if(empty($data))
        {
            return [];
        }
        return $data[0];
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
    function get($fields, $where, $order_by = '', $limit = 0)
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
        $data = $this->dbo->select($this->table_name, $fields, $param);
        if(empty($data))
        {
            return [];
        }
        return $data;
    }
    
    /**
    * 插入记录
    *
    * @param array data 要插入表中的数据
    * @return array
    *
    */
    function get_count($where_str = '')
    {
        return $this->dbo->total($this->table_name, $where_str);
    }
    
    /**
    * 插入记录
    *
    * @param array data 要插入表中的数据
    * @return array
    *
    */
    function add($data)
    {
        return $this->dbo->add($this->table_name, $data);
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
    * @param array where_array 包含删除条件的数组
    * @return array
    *
    */
    function delete($where_array)
    {
        return $this->dbo->delete($this->table_name, $where_array);
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
        return $this->dbo->update($this->table_name, $where, $data);
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
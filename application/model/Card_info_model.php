<?php
/**
* 授权信息模型
*
*
*/
Class User_model extends Model{

    public $table_name = 't_user';
    
    
    /**
    * 构造函数
    *
    */
    public function __construct($db_config)
    {
        parent::__construct($db_config);
        
    }
 
}
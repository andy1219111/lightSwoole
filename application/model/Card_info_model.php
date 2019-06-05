<?php
/**
* 授权信息模型
*
*
*/
Class Card_info_model extends Model{

    public $table_name = 't_card_info';
    
    
    /**
    * 构造函数
    *
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
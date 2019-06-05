<?php
/**
* 数据库处理类
* 
* @author aaron
*
*/
class DBO{
	//pdo对象
	private $_pdo = null;
	//语句集对象
	private $_stmt = null;
	
	private $dsn = null;
	
	private $db_user = null;
	
	private $is_persistent = null;
	
	private $db_password = null;
	private $driver_option = array();
	
	
	//构造函数
	function __construct($db_config,$is_persistent = FALSE) {
		try {
			//$db_config = config_item('db_config');
			$this->driver_option = array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES ' . $db_config['charset']);
			if($is_persistent)
			{
				$this->driver_option[PDO::ATTR_PERSISTENT] = TRUE;
			}
			$this->dsn = $db_config['dsn'];
			$this->db_user = $db_config['username'];
			$this->db_password = $db_config['password'];
			
			$this->_pdo = new PDO($this->dsn, $this->db_user, $this->db_password, $this->driver_option);
			$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES , false);
			//echo 'database connected.' . "\n";
		} catch (PDOException $e) {
			print_r($e->getMessage());
		}
	}
	//重连数据库
	public function reconnect()
	{
		try
		{
			$this->_pdo = NULL;
			$this->_pdo = new PDO($this->dsn, $this->db_user, $this->db_password, $this->driver_option);
			$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES , false);
		}
		catch(PDOException $e)
		{
			print_r($e->getMessage());
		}
		
	}
	//新增
	public function add($_table, Array $_addData) {
		$_addFields = array();
		$_addValues = array();
		$param_tag = array();
		foreach ($_addData as $_key=>$_value) {
			$_addFields[] = $_key;
			$_addValues[] = $_value;
			$param_tag[] = '?';
			
		}
		$_addFields = implode(',', $_addFields);
		$param_str= implode(',', $param_tag);
		//$_addValues = implode("','", $_addValues);
		//$_sql = "INSERT INTO $_tables[0] ($_addFields) VALUES ('$_addValues')";
		$_sql = "INSERT INTO $_table ($_addFields) VALUES ($param_str)";
		return $this->execute($_sql,$_addValues)->rowCount();
	}
    
	//新增或替换
	public function replace($_table, Array $_addData) {
		$_addFields = array();
		$_addValues = array();
		$param_tag = array();
		foreach ($_addData as $_key=>$_value) {
			$_addFields[] = $_key;
			$_addValues[] = $_value;
			$param_tag[] = '?';
			
		}
		$_addFields = implode(',', $_addFields);
		$param_str= implode(',', $param_tag);
		//$_addValues = implode("','", $_addValues);
		//$_sql = "INSERT INTO $_tables[0] ($_addFields) VALUES ('$_addValues')";
		$_sql = "REPLACE INTO $_table ($_addFields) VALUES ($param_str)";
		return $this->execute($_sql,$_addValues)->rowCount();
	}
	//修改
	public function update($_table, Array $_param, Array $_updateData) {
		$_where = $_setData = '';
		foreach ($_param as $_key=>$_value) {
			$_where .= $_value.' AND ';
		}
		$_where = 'WHERE '.substr($_where, 0, -4);
		foreach ($_updateData as $_key=>$_value) {
			/*
			if (Validate::isArray($_value)) {
				$_setData .= "$_key=$_value[0],";
			} else {
			*/
			$_setData .= "$_key='$_value',";
			
			//}
		}
		$_setData = substr($_setData, 0, -1);
		$_sql = "UPDATE $_table SET $_setData $_where";
		return $this->execute($_sql)->rowCount();
	}
	
	//验证一条数据
	public function isOne($_tables, Array $_param) {
		$_where = '';
		foreach ($_param as $_key=>$_value) {
			$_where .=$_value.' AND ';
		}
		$_where = 'WHERE '.substr($_where, 0, -4);
		$_sql = "SELECT id FROM $_tables[0] $_where LIMIT 1";
		return $this->execute($_sql)->rowCount();
	}
	
	//删除
	public function delete($_table, Array $_param) {
		$_where = '';
		foreach ($_param as $_key=>$_value) {
			$_where .= $_value.' AND ';
		}
		$_where = 'WHERE '.substr($_where, 0, -4);
		$_sql = "DELETE FROM $_table $_where";
		return $this->execute($_sql)->rowCount();
	}
	
	//查询
	public function select($_table, Array $_fileld, Array $_param = array()) {
		$_limit = $_order = $_where = $_like = '';
		if (is_array($_param) && !empty($_param)) {
			$_limit = isset($_param['limit']) ? 'LIMIT '.$_param['limit'] : '';
			$_order = isset($_param['order']) ? 'ORDER BY '.$_param['order'] : '';
			if (isset($_param['where'])) {
				
				$_where = 'WHERE ' . $_param['where'];
			}
		
		}
		$_selectFields = implode(',', $_fileld);
		//$_table = isset($_tables[1]) ? $_tables[0].','.$_tables[1] : $_tables[0];
		$_sql = "SELECT $_selectFields FROM $_table $_where $_order $_limit";
		$this->_stmt = $this->execute($_sql);
		$_result = array();
		while ($_objs = $this->_stmt->fetch(PDO::FETCH_ASSOC)) {
			$_result[] = $_objs;
		}
		return $_result;
	}
    //duplicate 插入，不存在则插入，存在则更新
    function duplicate_insert($table, $add_values, $update_values)
    {
        $_addFields = array();
		$_addValues = array();
		$param_tag = array();
		$update_fields = array();
		foreach ($add_values as $_key=>$_value) {
			$_addFields[] = $_key;
			$_addValues[] = $_value;
			$param_tag[] = '?';
			
		}
        foreach($update_values as $_key=>$_value)
        {
            $update_fields[] = "$_key=$_value";
        }
		$_addFields = implode(',', $_addFields);
		$param_str = implode(',', $param_tag);
		$update_str = implode(',', $update_fields);
		$_sql = "INSERT INTO $table ($_addFields) VALUES($param_str) on duplicate key update $update_str";
		return $this->execute($_sql,$_addValues)->rowCount();
    }
	
	//总记录
	public function total($_table, $where = '') {
		if ($where != '') {
			
			$where = 'WHERE ' . $where;
		}
		$_sql = "SELECT COUNT(*) as count FROM $_table $where";
		$this->_stmt = $this->execute($_sql);
		return $this->_stmt->fetchObject()->count;
	}
	
	//得到下一个ID
	public function nextId($_tables) {
		$_sql = "SHOW TABLE STATUS LIKE '$_tables[0]'";
		$this->_stmt = $this->execute($_sql);
		return $this->_stmt->fetchObject()->Auto_increment;
	}


	//执行SQL
	public function execute($_sql,$param = array()) {
		try {
			$this->_stmt = $this->_pdo->prepare($_sql);
			if(!empty($param))
			{
				$this->_stmt->execute($param);
			}
			else
			{
				$this->_stmt->execute();
			}
			
		} catch (PDOException  $e) {
			
			simple_log('SQL语句：'.$_sql.' 错误信息：'.$e->getMessage(), 'ERROR');
			$err_info = $this->_pdo->errorInfo();
			//数据库连接断开  重连后重新查询
			if(!empty($err_info) && $err_info[0] == 'HY000')
			{
				simple_log("开始重连数据库...", 'DEBUG');
				$this->reconnect();
				simple_log("重连数据库成功，重试执行sql语句...", 'DEBUG');
				$this->_stmt = $this->_pdo->prepare($_sql);
				if(!empty($param))
				{
					$this->_stmt->execute($param);
				}
				else
				{
					$this->_stmt->execute();
				}
			}
		}
		return $this->_stmt;
	}
    
    //执行原始的sql查询
    function query($sql)
    {
        $sql = trim($sql);
        if(strpos($sql, 'select') === 0 || strpos($sql, 'SELECT') === 0)
        {
            return $this->_pdo->query($sql);
        }
        else
        {
            return $this->_pdo->exec($sql);
        }
    }
	//关闭连接
	function close()
	{
		if($this->_stmt)
		{
			$this->_stmt->closeCursor();
			$this->_stmt == null;
			//echo 'Close statement Cursor.' . "\n";
		}
		if($this->_pdo)
		{
			$this->_pdo == null;
			//echo 'Close pdo connection.' . "\n";
		}
	}
}

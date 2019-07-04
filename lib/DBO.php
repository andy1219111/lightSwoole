<?php

/**
 * 数据库处理类
 * 
 * @author aaron
 *
 */
class DBO
{
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
	function __construct($db_config, $is_persistent = FALSE)
	{
		try {
			//$db_config = config_item('db_config');
			$this->driver_option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $db_config['charset']);
			if ($is_persistent) {
				$this->driver_option[PDO::ATTR_PERSISTENT] = TRUE;
			}
			$this->is_persistent = $is_persistent;
			$this->dsn = $db_config['dsn'];
			$this->db_user = $db_config['username'];
			$this->db_password = $db_config['password'];

			$this->_pdo = new PDO($this->dsn, $this->db_user, $this->db_password, $this->driver_option);
			$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		} catch (PDOException $e) {
			print_r($e->getMessage());
		}
	}

	/**
	 * 重连数据库
	 *
	 * @return void
	 */
	public function reconnect()
	{
		try {
			$this->_pdo = NULL;
			$this->_pdo = new PDO($this->dsn, $this->db_user, $this->db_password, $this->driver_option);
			$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		} catch (PDOException $e) {
			print_r($e->getMessage());
		}
	}
	
	/**
	 * 插入数据
	 *
	 * @param string $_table
	 * @param array $_addData
	 * @return int
	 */
	public function add($_table, array $_addData)
	{
		$_addFields = array();
		$_addValues = array();
		$param_tag = array();
		foreach ($_addData as $_key => $_value) {
			$_addFields[] = $_key;
			$_addValues[] = $_value;
			$param_tag[] = '?';
		}
		$_addFields = implode(',', $_addFields);
		$param_str = implode(',', $param_tag);
		$_sql = "INSERT INTO $_table ($_addFields) VALUES ($param_str)";
		return $this->execute($_sql, $_addValues)->rowCount();
	}

	/**
	 * 插入数据或替换数据
	 *
	 * @param string $_table
	 * @param array $_addData
	 * @return int
	 */
	public function replace($_table, array $_addData)
	{
		$_addFields = array();
		$_addValues = array();
		$param_tag = array();
		foreach ($_addData as $_key => $_value) {
			$_addFields[] = $_key;
			$_addValues[] = $_value;
			$param_tag[] = '?';
		}
		$_addFields = implode(',', $_addFields);
		$param_str = implode(',', $param_tag);
		$_sql = "REPLACE INTO $_table ($_addFields) VALUES ($param_str)";
		return $this->execute($_sql, $_addValues)->rowCount();
	}

	/**
	 * 组装查询条件
	 *
	 * @param mixed $where 包含查询条件的字符串或数组。支持字符串、键值对数组和字符串数组
	 * @return string 返回组装好的查询条件
	 */
	private function handle_where($where)
	{
		if (is_string($where)) {
			return $where;
		}
		$where_string = '';
		if (is_array($where)) {
			//判断是否是键值对数组
			if (array_keys($where) !== range(0, count($where) - 1)) {
				foreach ($where as $field => $value) {
					$where_string .= $field . '="' . $value . '" AND ';
				}
			} else {
				foreach ($where as $value) {
					$where_string .= $value . ' AND ';
				}
			}
		}
		$where_string = substr($where_string, 0, -5);
		return $where_string;
	}

	/**
	 * 更新数据
	 *
	 * @param string $_table
	 * @param mixed $where 字符串或包含条件语句的数组
	 * @param array $_updateData 要更新的数据的键值数组
	 * @return int 更新数据的条数
	 */
	public function update($_table, $where, array $_updateData)
	{
		$_setData = '';
		$_where = 'WHERE ' . $this->handle_where($where);
		foreach ($_updateData as $_key => $_value) {
			$_setData .= "$_key='$_value',";
		}
		$_setData = substr($_setData, 0, -1);
		$_sql = "UPDATE $_table SET $_setData $_where";
		return $this->execute($_sql)->rowCount();
	}

	/**
	 * 验证指定条件的数据是否存在
	 *
	 * @param [string] $_table 表名
	 * @param [mixed] $where 查询条件，字符串或包含查询条件的数组
	 * @return int
	 */
	public function is_exist($_table, $where)
	{
		$_where = 'WHERE ' . $this->handle_where($where);
		$_sql = "SELECT id FROM $_table $_where LIMIT 1";
		return $this->execute($_sql)->rowCount();
	}

	//删除
	public function delete($_table, $where)
	{
		$_where = 'WHERE ' . $this->handle_where($where);
		$_sql = "DELETE FROM $_table $_where";
		return $this->execute($_sql)->rowCount();
	}

	/**
	 * 执行select操作
	 *
	 * @param string $_table
	 * @param array $_fileld
	 * @param array $_param
	 * @return array
	 */
	public function select($_table, array $_fileld, array $_param = array())
	{
		$_limit = $_order = $_where = $_like = '';
		if (is_array($_param) && !empty($_param)) {
			$_limit = isset($_param['limit']) ? 'LIMIT ' . $_param['limit'] : '';
			$_order = isset($_param['order']) ? 'ORDER BY ' . $_param['order'] : '';
			if (isset($_param['where'])) {
				$_where = 'WHERE ' . $this->handle_where($_param['where']);
			}
		}
		$_selectFields = implode(',', $_fileld);
		$_sql = "SELECT $_selectFields FROM $_table $_where $_order $_limit";
		$this->_stmt = $this->execute($_sql);
		$_result = array();
		while ($_objs = $this->_stmt->fetch(PDO::FETCH_ASSOC)) {
			$_result[] = $_objs;
		}
		return $_result;
	}

	/**
	 * duplicate 插入，不存在则插入，存在则更新
	 *
	 * @param [type] $table
	 * @param [type] $add_values
	 * @param [type] $update_values
	 * @return void
	 */
	function duplicate_insert($table, $add_values, $update_values)
	{
		$_addFields = array();
		$_addValues = array();
		$param_tag = array();
		$update_fields = array();
		foreach ($add_values as $_key => $_value) {
			$_addFields[] = $_key;
			$_addValues[] = $_value;
			$param_tag[] = '?';
		}
		foreach ($update_values as $_key => $_value) {
			$update_fields[] = "$_key=$_value";
		}
		$_addFields = implode(',', $_addFields);
		$param_str = implode(',', $param_tag);
		$update_str = implode(',', $update_fields);
		$_sql = "INSERT INTO $table ($_addFields) VALUES($param_str) on duplicate key update $update_str";
		return $this->execute($_sql, $_addValues)->rowCount();
	}

	/**
	 * 查询符合条件的记录总数量
	 *
	 * @param string $_table
	 * @param mixed $where
	 * @return void
	 */
	public function total($_table, $where = '')
	{
		if ($where != '') {
			$where = 'WHERE ' . $this->handle_where($where);
		}
		$_sql = "SELECT COUNT(*) as count FROM $_table $where";
		$this->_stmt = $this->execute($_sql);
		return $this->_stmt->fetchObject()->count;
	}

	//得到下一个ID
	public function next_id($_table)
	{
		$_sql = "SHOW TABLE STATUS LIKE '$_table'";
		$this->_stmt = $this->execute($_sql);
		return $this->_stmt->fetchObject()->Auto_increment;
	}

	/**
	 * 执行sql语句
	 *
	 * @param string $_sql
	 * @param array $param
	 * @return void
	 */
	public function execute($_sql, $param = array())
	{
		try {
			$this->_stmt = $this->_pdo->prepare($_sql);
			if (!empty($param)) {
				$this->_stmt->execute($param);
			} else {
				$this->_stmt->execute();
			}
		} catch (PDOException  $e) {

			simple_log('SQL语句：' . $_sql . ' 错误信息：' . $e->getMessage(), 'ERROR');
			$err_info = $this->_pdo->errorInfo();
			//数据库连接断开  重连后重新查询
			if (!empty($err_info) && $err_info[0] == 'HY000') {
				simple_log("开始重连数据库...", 'DEBUG');
				$this->reconnect();
				simple_log("重连数据库成功，重试执行sql语句...", 'DEBUG');
				$this->_stmt = $this->_pdo->prepare($_sql);
				if (!empty($param)) {
					$this->_stmt->execute($param);
				} else {
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
		if (strpos($sql, 'select') === 0 || strpos($sql, 'SELECT') === 0) {
			return $this->_pdo->query($sql);
		} else {
			return $this->_pdo->exec($sql);
		}
	}
	//关闭连接
	function close()
	{
		if ($this->_stmt) {
			$this->_stmt->closeCursor();
			$this->_stmt == null;
			//echo 'Close statement Cursor.' . "\n";
		}
		if ($this->_pdo) {
			$this->_pdo == null;
			//echo 'Close pdo connection.' . "\n";
		}
	}
}

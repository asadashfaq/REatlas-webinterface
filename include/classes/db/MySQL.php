<?php

class MySQL extends Db
{
	/**
	 * @see DbCore::connect()
	 */
	public function connect()
	{
		if (!defined('_PS_MYSQL_REAL_ESCAPE_STRING_'))
			define('_PS_MYSQL_REAL_ESCAPE_STRING_', function_exists('mysql_real_escape_string'));

		if (!$this->link = mysql_connect($this->server, $this->user, $this->password))
			throw new RADatabaseException(Tools::displayError('Link to database cannot be established.'));

		if (!$this->set_db($this->database))
			throw new RADatabaseException(Tools::displayError('The database selection cannot be made.'));

		// UTF-8 support
		if (!mysql_query('SET NAMES \'utf8\'', $this->link))
			throw new RADatabaseException(Tools::displayError('PrestaShop Fatal error: no utf-8 support. Please check your server configuration.'));

		return $this->link;
	}

	/**
	 * @see DbCore::disconnect()
	 */
	public function disconnect()
	{
		mysql_close($this->link);
	}

	/**
	 * @see DbCore::_query()
	 */
	protected function _query($sql)
	{
		return mysql_query($sql, $this->link);
	}

	/**
	 * @see DbCore::nextRow()
	 */
	public function nextRow($result = false)
	{
		return mysql_fetch_assoc($result ? $result : $this->result);
	}

	/**
	 * @see DbCore::_numRows()
	 */
	protected function _numRows($result)
	{
		return mysql_num_rows($result);
	}

	/**
	 * @see DbCore::Insert_ID()
	 */
	public function Insert_ID()
	{
		return mysql_insert_id($this->link);
	}

	/**
	 * @see DbCore::Affected_Rows()
	 */
	public function Affected_Rows()
	{
		return mysql_affected_rows($this->link);
	}

	/**
	 * @see DbCore::getMsgError()
	 */
	public function getMsgError($query = false)
	{
		return mysql_error($this->link);
	}

	/**
	 * @see DbCore::getNumberError()
	 */
	public function getNumberError()
	{
		return mysql_errno($this->link);
	}

	/**
	 * @see DbCore::getVersion()
	 */
	public function getVersion()
	{
		return mysql_get_server_info($this->link);
	}

	/**
	 * @see DbCore::_escape()
	 */
	public function _escape($str)
	{
		return _PS_MYSQL_REAL_ESCAPE_STRING_ ? mysql_real_escape_string($str, $this->link) : addslashes($str);
	}

	/**
	 * @see DbCore::set_db()
	 */
	public function set_db($db_name)
	{
		return mysql_select_db($db_name, $this->link);
	}

	/**
	 * @see Db::hasTableWithSamePrefix()
	 */
	public static function hasTableWithSamePrefix($server, $user, $pwd, $db, $prefix)
	{
		if (!$link = @mysql_connect($server, $user, $pwd, true))
			return false;
		if (!@mysql_select_db($db, $link))
			return false;

		$sql = 'SHOW TABLES LIKE \''.$prefix.'%\'';
		$result = mysql_query($sql);
		return (bool)@mysql_fetch_assoc($result);
	}

	/**
	 * @see Db::checkConnection()
	 */
	public static function tryToConnect($server, $user, $pwd, $db, $newDbLink = true, $engine = null, $timeout = 5)
	{
		ini_set('mysql.connect_timeout', $timeout);
		if (!$link = @mysql_connect($server, $user, $pwd, $newDbLink))
			return 1;
		if (!@mysql_select_db($db, $link))
			return 2;

		if (strtolower($engine) == 'innodb')
		{
			$sql = 'SHOW VARIABLES WHERE Variable_name = \'have_innodb\'';
			$result = mysql_query($sql);
			if (!$result)
				return 4;
			$row = mysql_fetch_assoc($result);
			if (!$row || strtolower($row['Value']) != 'yes')
				return 4;
		}
		@mysql_close($link);
		return 0;
	}

	/**
	 * @see Db::checkEncoding()
	 */
	static public function tryUTF8($server, $user, $pwd)
	{
		$link = @mysql_connect($server, $user, $pwd);
		if (!mysql_query('SET NAMES \'utf8\'', $link))
			$ret = false;
		else
			$ret = true;
		@mysql_close($link);
		return $ret;
	}
}

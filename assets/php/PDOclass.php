<?php

class DBquery{

	private $db_host = 'localhost';
	private $db_name = 'ikki';
	private $db_username = 'root';
	private $db_password = 'root';

	private $_conn;
	private $_query;
	private $_stmt;
	private $_where = '';
	private $_join = '';
	private $_prepareArgs = array();

	/**====================================================================
	 * Public functions
	 ====================================================================*/

	/**
	 * Make any kind of query
	 * @param string $query / A user-provided query
	 * @return array / The returned rows from the query
	 */
	public function query($query)
	{
		$this->_connect();
		$this->_query = filter_var($query, FILTER_SANITIZE_STRING);
		$this->_prepareQuery();

		$result = $this->_bindResults();
		return $result;
	}

	/**
	 * A SELECT * query
	 * @param string $tableName / The name of the table to work with
	 * @param int $numRows / The number of rows to return
	 * @return array / The returned rows from the select query
	 */
	public function selectAll($tableName, $numRows = NULL)
	{
		$this->_connect();
		$this->_query = "SELECT * from $tableName";
		$this->_buildQuery($numRows);
		$this->_prepareQuery();

		$result = $this->_bindResults();
		return $result;
	}

	/**
	 * A SELECT query with column arguments
	 * @param string $tableName / The name of the table to work with
	 * @param array $cols / The columns to select
	 * @param int $numRows / The number of rows to return
	 * @return array / The returned rows from the select query
	 */
	public function select($tableName, $cols, $numRows = NULL)
	{
		$this->_connect();
		$this->_query = "SELECT ";
		$this->_buildQuery($numRows, $cols, $tableName);
		$this->_prepareQuery();

		$result = $this->_bindResults();
		return $result;
	}

	/**
	 * An INSERT query
	 * @param string $tableName / The name of the table to work with
	 * @param array $insertData / The information to insert into the table
	 * @return boolean / Boolean indicating whether the insert query was completed
	 */
	public function insert($tableName, $insertData)
	{
		$this->_connect();
		$this->_query = "INSERT into $tableName ";
		$this->_buildQuery(NULL, $insertData);
		$this->_prepareQuery();

		if ($this->_stmt->rowCount()) {
			return true;
		}
	}

	/**
	 * An UPDATE query
	 * Be sure to call the 'where' method first
	 * @param string $tableName / The name of the table to work with
	 * @param array $updateData / The information to be updated
	 * @return boolean / Boolean indicating whether the update query was completed
	 */
	public function update($tableName, $updateData)
	{
		$this->_connect();
		$this->_query = "UPDATE $tableName SET ";
		$this->_buildQuery(NULL, $updateData);
		$this->_prepareQuery();

		if ($this->_stmt->rowCount()) {
			return true;
		}
	}

	/**
	 * A DELETE query
	 * Be sure to call the 'where' method first
	 * @param string $tableName / The name of the table to work with
	 */
	public function delete($tableName)
	{
		$this->_connect();
		$this->_query = "DELETE FROM $tableName";
		$this->_buildQuery();
		$this->_prepareQuery();

		if ($this->_stmt->rowCount()) {
			return true;
		}
	}

	/**
	 * Allows the user to specify a WHERE statement
	 * @param array/string $args / The arguments of the WHERE option
	 * @param string $value / The value of the argument when using a single WHERE option
	 */
	public function where($args, $value = NULL)
	{
		if (empty($this->_where)) {
			$this->_where = ' WHERE ';
		} else {
			$this->_where .= ' AND ';
		}
		if (gettype($args) === 'array') {
			$keys = array_keys($args);
			if (count($keys)===1){
				$this->_where .= $keys[0] . " = :w_$keys[0]";
				$this->_prepareArgs["w_$keys[0]"] = $args[$keys[0]];
			} else {
				$this->_where .= '(' . $keys[0] . " = :w_$keys[0] ";
				$this->_where .= $args[$keys[1]];
				$this->_where .= ' ' . $keys[2] . " = :w_$keys[2])";

				$this->_prepareArgs["w_$keys[0]"] = $args[$keys[0]];
				$this->_prepareArgs["w_$keys[2]"] = $args[$keys[2]];
			}
		} else {
			$this->_where .= $args . " = :w_$args";
			$this->_prepareArgs["w_$args"] = $value;
		}
	}

	/**
	 * Allows the user to make a JOIN
	 */
	public function join($tableName, $on)
	{
		$this->_join = " INNER JOIN $tableName on $on[0] = $on[1]";
	}

	/**====================================================================
	 * Private functions
	 ====================================================================*/

	/**
	 * Connect to the database
	 * @return boolean / Whether the connection was successful or not
	 */
	private function _connect()

	{
		try {
		    $this->_conn = new PDO("mysql:host=$this->db_host;dbname=$this->db_name", $this->db_username, $this->db_password);
		    $this->_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
		    echo 'ERROR: ' . $e->getMessage();
		    return false;
		}
		return true;
	}

	/**
	 * Build the query
	 */
	private function _buildQuery($numRows = NULL, $tableData = false, $tableName = NULL)
	{
		// Did the user provide table data
		$hasTableData = null;
		if (gettype($tableData) === 'array') {
			$hasTableData = true;
		}

		if ($hasTableData) {
			// Is it a SELECT
			$sel = strpos($this->_query, 'SELECT');
			if ($sel !== false) {
				$c = count($tableData);
				foreach ($tableData as $key => $value) {
					$this->_query .= $value;
					$c>1 ? $this->_query .= ', ' : $this->_query .= ' ';
					$c--;
				}
				$this->_query .= 'FROM ' . $tableName;
			}

			// Is is an INSERT
			$ins = strpos($this->_query, 'INSERT');
			if ($ins !== false) {
				$keys = array_keys($tableData);
				$this->_query .= '(' . implode($keys, ', ') . ')';
				$this->_query .= ' VALUES (';
				$c = count($tableData);
				foreach ($tableData as $key => $value) {
					$this->_query .= ":$key";
					$c>1 ? $this->_query .= ', ' : $this->_query .= ')';
					$c--;
					$this->_prepareArgs[$key] = $value;
				}
			}

			// Is an UPDATE
			$upd = strpos($this->_query, 'UPDATE');
			if ($upd !== false) {
				$c = count($tableData);
				foreach ($tableData as $key => $value) {
					$this->_query .= $key . " = :$key";
					$c>1 ? $this->_query .= ', ' : $this->_query .= ' ';
					$c--;
					$this->_prepareArgs[$key] = $value;
				}
			}
		}

		// Did the user set a JOIN
		if (!empty($this->_join)) {
			$this->_query .= $this->_join;
		}

		// Did the user set a WHERE
		if (!empty($this->_where)) {
			$this->_query .= $this->_where;
		}

		// Did the user set a LIMIT
		if (isset($numRows)) {
			$this->_query .= " LIMIT " . (int) $numRows;
		}
	}

	/**
	 * Bind the results
	 * @return array $result / The return results of the query
	 */
	private function _bindResults()
	{
		$result = $this->_stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	/**
	 * Prepare and execute the query
	 */
	private function _prepareQuery()
	{
		$this->_stmt = $this->_conn->prepare($this->_query);
		$this->_stmt->execute($this->_prepareArgs);
	}
}

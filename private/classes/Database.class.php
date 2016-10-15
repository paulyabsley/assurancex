<?php

class Database {

	private static $_db;  // singleton connection object
	
	private function __construct() {}  // disallow creating a new object of the class with new Database()
	
	private function __clone() {}  // disallow cloning the class

	/**
	 * Get the instance of the PDO connection
	 * @return DB  PDO connection
	 */
	public static function get_instance() {
		if (static::$_db === NULL) {
			$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
			static::$_db = new PDO($dsn, DB_USER, DB_PASS);

			// Raise exceptions when a database exception occurs
			static::$_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		return static::$_db;
	}

	/**
	 * Format param
	 * @param string $param
	 * @return string
	 */
	protected static function fparam($param) {
		$param = strtolower($param);
		$param = str_replace('(', '_', $param);
		$param = str_replace(')', '', $param);
		return $param;
	}

	/**
	 * Create table
	 * @param string $sql
	 * @return mixed string or null
	 */
	public static function create_table($sql) {
		try {
			$db = self::get_instance();
			$db->exec($sql);
			return 'Table created';
		} catch (PDOException $e) {
			Utilities::pdo_caught($e);
		}
	}

	/**
	 * Find row by id
	 * @param string $table
	 * @param int $row
	 * @param array $columns
	 * @return array
	 */
	public static function find_row_by_id($table, $row, $columns = '') {
		if (isset($table) && isset($row)) {
			try {
				$db = self::get_instance();
				$cols = '';
				if (!empty($columns)) {
					foreach ($columns as $column) {
						$cols .= $column . ', ';
					}
					$cols = substr($cols, 0, -2);
				} else {
					$cols = '*';
				}
				$sql = "SELECT $cols FROM $table WHERE id = :id LIMIT 1";
				$sth = $db->prepare($sql);
				$sth->bindParam(':id', $row, PDO::PARAM_INT);
				$sth->execute();
			} catch (PDOException $e) {
				Utilities::pdo_caught($e, $sql);
			}
			$result = $sth->fetch(PDO::FETCH_ASSOC);
			$sth = null;
			if ($result) {
				return $result;
			}
		}
	}

	/**
	 * Select by sql
	 * @param string $sql
	 * @param array $params
	 * @return array
	 */
	public static function select_by_sql($sql, $params = []) {
		try {
			$db = self::get_instance();
			$sth = $db->prepare($sql);
			if (!empty($params)) {
				foreach ($params as $placeholder => $value) {
					$sth->bindValue($placeholder, $value);
				}
			}
			$sth->execute();
		} catch (PDOException $e) {
			Utilities::pdo_caught($e, $sql);
		}
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		$sth = null;
		return ($result) ? $result : false;
	}

	/**
	 * General SQL select method
	 * @param string $columns
	 * @param string $table
	 * @param mixed $where key value pairs of columns and column values _or_ string
	 * @param string $orderd_by
	 * @return mixed $result object when successful or false
	 */
	public static function select($columns, $table, $where = '', $order = '') {
		try {
			// Create placeholders for prepared statement
			$cols_placeholders = " ";
			$values = [];
			// SQL
			$sql = 'SELECT ' . $columns . ' FROM ' . $table;
			if (!empty($where)) {
				if (is_array($where)) { // Array: single or multiple where clauses
					foreach ($where as $column => $value) {
						$cols_placeholders .= $column . ' = :' . self::fparam($column) . ' AND ';
						$values[$column] = $value;
					}
					$cols_placeholders = substr($cols_placeholders, 0, -5);
					$sql .= ' WHERE ' . $cols_placeholders;
				} else { // String: custom where clause
					$sql .= ' WHERE ' . $where;
				}
			}
			if (!empty($order)) {
				$sql .= ' ORDER BY ' . $order;
			}
			// Prepare statement
			$db = self::get_instance();
			$sth = $db->prepare($sql);
			// Bind Values
			if (!empty($values)) {
				foreach ($values as $param => $value) {
					$sth->bindValue(self::fparam($param), $value);
				}
			}
			// Execute
			$sth->execute();
		} catch(PDOException $e) {
			Utilities::pdo_caught($e, $sql);
		}
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		$sth = null;
		return ($result) ? $result : false;
	}

	/**
	 * Insert row
	 * @param string $table
	 * @param array $insert key/value pairs of columns and vals
	 * @return array
	 */
	public static function insert_row($table, $insert) {
		if (isset($table) && isset($insert)) {
			$columns = $placeholders = '';
			foreach ($insert as $column => $value) {
				$columns .= $column . ', ';
				$placeholders .= ':' . $column . ', ';
			}
			$columns = substr($columns, 0, -2);
			$placeholders = substr($placeholders, 0, -2);
			$sql = "INSERT INTO $table (";
			$sql .= $columns;
			$sql .= ") VALUES (";
			$sql .= $placeholders;
			$sql .= ")";
			try {
				$db = self::get_instance();
				$sth = $db->prepare($sql);
				foreach ($insert as $column => $value) {
					$param = ':' . $column;
					$sth->bindValue($param, $value, PDO::PARAM_STR);
				}
				$sth->execute();
			} catch (PDOException $e) {
				Utilities::pdo_caught($e, $sql);
			}
			$rows_added = $sth->rowCount();
			$sth = null;
			return $rows_added;
		}
	}

	/**
	 * Get last inserted row
	 * @return int
	 */
	public static function last_inserted_row() {
		$db = self::get_instance();
		$row = $db->lastInsertId();
		return $row;
	}

	/**
	 * Update row
	 * @param string $table
	 * @param array $update key/value pairs of columns and vals
	 * @param string $id
	 * @return array
	 */
	public static function update_row_by_id($table, $update, $id) {
		if (isset($table) && isset($update)) {
			$cols_placeholders = " ";
			foreach ($update as $column => $value) {
				$cols_placeholders .= $column . ' = :' . $column . ', ';
			}
			$cols_placeholders = substr($cols_placeholders, 0, -2);
			$sql = "UPDATE $table SET ";
			$sql .= $cols_placeholders;
			$sql .= " WHERE id = :id";
			try {
				$db = self::get_instance();
				$sth = $db->prepare($sql);
				foreach ($update as $column => $value) {
					$param = ':' . $column;
					$sth->bindValue($param, $value, PDO::PARAM_STR);
				}
				$sth->bindParam(':id', $id, PDO::PARAM_INT);
				$sth->execute();
			} catch (PDOException $e) {
				Utilities::pdo_caught($e, $sql);
			}
			$rows_updated = $sth->rowCount();
			$sth = null;
			return $rows_updated;
		}
	}

}

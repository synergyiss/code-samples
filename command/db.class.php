<?php
/*
Copyright 2023
United States Golf Association
Paul Niebuhr

db.class.php

This program is the data access layer for the Command system.

This class should be inherited by dal classes for specific tables.

*/

class Db_class
{

	public $db = NULL;        // database connection
	protected $statement = NULL; // current statement
	protected $row = NULL;       // current row 

	public $last_error = "";

	public $field_definitions = array();


	public $table = NULL;       // table being managed

	// used to generate a dropdown list of key values

	public $id = NULL;          // primary key of table
	public $name = NULL;        // name field  

	public $parent_id = NULL;          // primary key of parent table
	public $parent_id_to_add = 0;  // parent id to be used for adding records


	public $field_values = array();


	public function __construct()
	{

		try {
			$this->db = null;
			$this->db = new PDO(DB_CONNECTION, DB_USER, DB_DBPASSWORD, array(PDO::ATTR_PERSISTENT => true));
		} catch (PDOException $e) {
			//print "Error!: " . $e->getMessage() . "<br/>";  don't want to show connection information
			print "Error!: Connection Failed.";
			die();
		}
		$this->db->exec('SET CHARACTER SET utf8');
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->statement = NULL;
	}

	public function __destruct()
	{
		$this->db = null;
	}

	public function beginTransaction()
	{
		$this->db->beginTransaction();
	}

	public function checkConnection($reconnect = false)
	{

		if ($this->db === null || $reconnect) {
			// reconnect
			error_log("reconnecting");
			$this->__construct();
		}
	}

	public function commit()
	{
		try {
			$this->db->commit();
		} catch (PDOException $e) {
		}
	}

	/*
	function date_to_db
	
	Format date to proper format for inserting into database.
	*/

	public function date_to_db($d)
	{

		if ($d == "0000-00-00") {
			$output = "NULL";
		} else if ($d > "") {
			try {
				$newd = new DateTime($d);
				$newd = $newd->format("'Y-m-d H:i:s'");
				$output = "$newd";
			} catch (Exception $e) {

				error_log("date exception: $d - " . $e->getMessage());
				$output = "NULL";
			}
		} else {
			$output = "NULL";
		}

		return $output;
	}

	// date to db - no quotes

	public function date_to_db_nq($d)
	{

		if ($d == "0000-00-00") {
			$output = "NULL";
		} else if ($d > "") {

			try {
				$newd = new DateTime($d);
				$newd = $newd->format("Y-m-d H:i:s");
				$output = "$newd";
			} catch (Exception $e) {

				error_log("date exception: $d - " . $e->getMessage());
				$output = "NULL";
			}
		} else {
			$output = "NULL";
		}

		return $output;
	}

	// date to db that only converts date portion.

	public function date_only_to_db($d)
	{

		if ($d > "") {
			try {
				$newd = new DateTime($d);
				$newd = $newd->format("'Y-m-d'");
				$output = "$newd";
			} catch (Exception $e) {

				error_log("date exception: $d - " . $e->getMessage());
				$output = "NULL";
			}
		} else {
			$output = "NULL";
		}

		return $output;
	}

	public function time_to_db($d)
	{

		if ($d > "") {

			$datetime = strtotime($d);
			$output = "'" . date("0000-00-00 H:i:s", $datetime) . "'";
		} else {
			$output = "NULL";
		}

		return $output;
	}

	/*
   
   Format a date coming from the database to human readable.
   
   */

	public function db_to_date($d)
	{


		$system_date_format = get_system_variable("date_format", "m/d/Y");
		if (is_null($d))
			$output = NULL;
		else if ($d == "" || $d == "0000-00-00")
			$output = "";
		else {

			try {
				$newd = new DateTime($d);
				$newd = $newd->format($system_date_format);
				$output = "$newd";
			} catch (Exception $e) {

				error_log("date exception: $d - " . $e->getMessage());
				$output = "NULL";
			}
		}


		return $output;
	}

	public function db_to_time($d)
	{

		$system_date_format = get_system_variable("date_format", "g:iA");
		if (is_null($d))
			$output = NULL;
		else if ($d == "" || $d == "00:00:00")
			$output = "";
		else {
			$datetime = strtotime($d);
			$output = date("g:iA", $datetime);
		}
		return $output;
	}

	public function db_to_date_time($d)
	{

		$system_date_format = get_system_variable("date_time_format", "m/d/Y g:iA");
		if (is_null($d))
			$output = NULL;
		else if ($d == "" || $d == "00:00:00")
			$output = "";
		else {
			$datetime = strtotime($d);
			$output = date("g:iA", $datetime);
		}
		return $output;
	}

	// not used in production
	public function current_statement()
	{
		if ($this->statement == NULL) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/*
	
	function: prepare_field
	
	$value - field value
	
	$field_type - Command data type to be converted and prepared for database.
	
	*/

	public function prepare_field($value, $field_type)
	{
		$output = $value;

		switch ($field_type) {
			case "s":
				$output = $this->db->quote($value);
				break;
			case "z":                        // serialized value
				$output = $this->db->quote(serialize(($value)));
				break;
			case "d":
				$output = $this->date_to_db($value);
				break;
			case "t":
				$output = $this->time_to_db($value);
				break;
			case "n":

				if ($value == "") $value = 0;
				if ($value === "on") $value = 1;
				if ($value === "off") $value = 0;
				$output = $value;
				break;
			case "p":
				if ($value == "") $value = null;
				else $value = "GeomFromText('POINT($value)')";
				$output = $value;
				break;
		}

		return $output;
	}

	/*
	 * CONSTRUCT_SELECT
	* Required: values  Array of field names and values.
	* e.g.  $values=array('prefix_name'=>'Mr.'); 
	*
	*       $field_definitions are part of the class that
	*       extended the db_class
	*
	*  Important: This function will ignore keys that have blank values
	*             which is useful when passing in parameters from
	*             a search screen when someone doesn't enter
	*             a search value for certain fields they don't want
	*             to search on.
	*/
	public function construct_select($values = null, $operator = null)
	{

		$where = "";

		foreach ($values as $key => $value) {

			if (strlen($value) > 0) {

				if (isset($operator[$key]))
					$op = $operator[$key];
				else
					$op = "=";

				if (!isset($this->field_definitions[$key])) {
					//  error_log ("Key not defined.  Table: " . $this->table . "- key:".$key);
				} else {

					if ($where > "") $where .= " and ";


					switch ($this->field_definitions[$key]['type']) {
						case "s":
							if (strpos($value, '%') === FALSE)
								$where .= "(" . $key . " $op " . $this->db->quote($value) . ")";
							else
								$where .= "(" . $key . " LIKE " . $this->db->quote($value) . ")";
							break;
						case "d":
							// check date format

							//						$value=date("Y-m-d",strtotime($value));

							try {
								$newd = new DateTime($d);
								$newd = $newd->format("Y-m-d");
								$value = "$newd";
							} catch (Exception $e) {

								error_log("date exception: $d - " . $e->getMessage());
								$value = "NULL";
							}

							$where .= "(" . $key . " $op '" . $value . "')";
							break;
						case "n":
							$where .= "(" . $key . " $op " . $value . ")";
							break;
					}
				}
				//error_log($where);         

			}
		}
		//	error_log($where);
		return $where;
	} // end of construct_select


	/*
	 * CONSTRUCT_SELECT_EXACT
	* Required: values  Array of field names and values.
	* e.g.  $values=array('prefix_name'=>'Mr.'); 
	*
	*       $field_definitions are part of the class that
	*       extended the db_class
	*
	*  Important: This function DOES NOT ignore keys that have blank values
	*/

	public function construct_select_exact($values = null, $operator = null)
	{

		$where = "";
		//print "db_class";
		//error_log("table-".$this->table);		
		foreach ($values as $key => $value) {

			//error_log( $key."-".$value);
			//error_log( "operator:".$key."-".$operator["key"]);

			if (isset($operator[$key]))
				$op = $operator[$key];
			else
				$op = "=";

			if ($where > "") $where .= " and ";

			if (!isset($this->field_definitions[$key]['type'])) {
				//  error_log ("Type not defined.  Table: " . $this->table . "- key:".$key);
			}


			switch ($this->field_definitions[$key]['type']) {
				case "s":
					//error_log(print_r($value,true));
					if (strpos($value, '%') === FALSE)
						$where .= "(" . $key . " $op " . $this->db->quote($value) . ")";
					else
						$where .= "(" . $key . " LIKE " . $this->db->quote($value) . ")";
					break;
				case "z":
					if (strpos($value, '%') === FALSE)
						$where .= "(" . $key . " $op " . $this->db->quote($value) . ")";
					else
						$where .= "(" . $key . " LIKE " . $this->db->quote($value) . ")";
					break;
				case "d":
					$where .= "(" . $key . " $op '" . $value . "')";
					break;
				case "n":
					$where .= "(" . $key . " $op " . $value . ")";
					break;
			}
		}
		//	error_log("where:".$where);
		return $where;
	} // end of construct_select_exact


	/*
	 * UPDATE
	* Required: values  Array of field names and values.
	* e.g.  $values=array('prefix_name'=>'Mr.'); 
	*           $field_definitions
	*           $where  Valid SQL where clause.  pass * to affect entire database
	*           $limit  Valid SQL limit phrase.  pass * to have no limit       
	*/
	public function update($values = null, $where = null, $limit = "1")
	{

		$this->last_error = "starting update ";
		$this->checkConnection();

		if ($values == null || $this->table == null || $where == null) {
			$this->last_error = "Some value is null values: $values table: " . $this->table . " where $where";
			return false;
		}


		$update = 'UPDATE `' . $this->table . "` SET ";

		$fields = "";

		foreach ($this->field_definitions as $key => $value) {
			//error_log("key: " . $key . " value: " . $values[$key]);  

			if (isset($values[$key])) {

				if ($fields > "") {
					$fields .= ",";
				}
				$fields .= $key . "=";
				if (isset($values[$key])) {
					$fields .= $this->prepare_field($values[$key], $this->field_definitions[$key]['type']);
				} else
					$fields .= $this->prepare_field('', $this->field_definitions[$key]['type']);
			}
		}

		if ($fields > "") {
			$update .= $fields;

			if ($where != "*")
				$update .= " WHERE " . $where;

			if ($limit != "*")
				$update .= " LIMIT " . $limit;

			//    print "<br>update: $update<br>";
			//	 error_log($update);
			$this->last_error = "";
			try {
				$upd = $this->db->prepare($update);
				$upd->execute();

				return TRUE;
			} catch (PDOException $e) {
				$this->last_error = $e->getMessage() . '<pre>' . $e->getTraceAsString() . '</pre>';
				$this->last_error .= " SQL: " . $update;
				error_log("Update Error. " . $this->last_error);
				return FALSE;
			}
		} // end if fields>""


		$this->last_error = "No fields to update";
		return TRUE;
	} // end of update


	public function sql($sql)
	{
		// error_log("DAL SQL ." . $sql);
		$this->checkConnection();

		$success = FALSE;
		$this->last_error = "";


		try {
			$this->statement = $this->db->prepare($sql);
			$success = $this->statement->execute();
		} catch (PDOException $e) {
			error_log("SQL Error." . $sql);
			$this->last_error = $e->getMessage() . '<pre>' . $e->getTraceAsString() . '</pre>';
			error_log($this->last_error);
			return FALSE;
		}

		return $success;
	}  // end of sql

	/*
	 * INSERT
	* Required: values  Array of field names and values.
	* e.g.  $values=array('prefix_name'=>'Mr.');  
	*/
	public function insert($values = null)
	{

		if ($values == null || $this->table == null)
			return false;

		$this->checkConnection();

		$insert = 'INSERT INTO `' . $this->table . '`';

		$rows = "";
		$vals = "";




		foreach ($this->field_definitions as $key => $value) {
			//error_log("key: " . $key . " value: " . $values[$key] . " prepared: ".$this->prepare_field($values[$key],$this->field_definitions[$key]['type']));  
			if (isset($values[$key])) {
				// on an insert, do not set the primary key to zero
				if ((($this->id == $key) && ($values[$key] > 0)) || $this->id != $key) {
					if ($rows > "") {
						$rows .= ",";
						$vals .= ",";
					}
					$rows .= $key;
					$vals .= $this->prepare_field($values[$key], $this->field_definitions[$key]['type']);
				}
			}
		}


		$insert .= ' (' . $rows . ')';

		$insert .= ' VALUES (' . $vals . ')';

		//     error_log("insert: $insert");

		$this->last_error = "";
		try {
			$ins = $this->db->prepare($insert);
			$ins->execute();

			// check to see if either a primary key was passed in or one was
			// generated from an auto increment.
			if ($this->id > "") {
				if ($this->db->lastInsertId() == 0) {
					// no auto increment.  see if one was passed in.
					if ($values[$this->id] > 0) {
						return TRUE;
					} else {
						return FALSE;
					}
				} else {
					return TRUE;
				}
			}
		} catch (PDOException $e) {
			error_log("Insert Error." . $insert);
			$this->last_error = $e->getMessage() . '<pre>' . $e->getTraceAsString() . '</pre>';
			return FALSE;
		}
	} // end of insert


	public function select($fields = '*', $where = null, $order_by = null, $limit = null)
	{

		$this->checkConnection();
		$select = 'SELECT ' . $fields . ' FROM `' . $this->table . '`';

		if ($where != null)
			$select .= " WHERE " . $where;

		if ($order_by != null)
			$select .= " ORDER BY " . $order_by;

		if ($limit != null)
			$select .= " LIMIT " . $limit;

		$select .= ";";

		//error_log($select);   
		// clear rows                                                 
		//		$this->numResults = null;

		$retries = 2;

		while ($retries > 0) {

			try {
				$this->statement = $this->db->prepare($select);
				return $this->statement->execute();
			} catch (PDOException $e) {
				if ($retries > 0) {
					$this->checkconnection(true); // force reconnect
					error_log("db.class error " . $e->getMessage() . '<pre>' . $e->getTraceAsString() . '</pre>');
				} else {
					$this->last_error = $e->getMessage() . '<pre>' . $e->getTraceAsString() . '</pre>';
					return FALSE;
				}
			}
			$retries--;
		} // end while


	}  // end of select

	// delete

	public function delete($where = null, $limit = 1)
	{


		$delete = 'DELETE FROM `' . $this->table . '`';

		if ($where != null)
			$delete .= " WHERE " . $where;

		if ($limit != null)
			$delete .= " LIMIT " . $limit;

		$delete .= ";";

		$this->last_error = "";
		try {
			$this->statement = $this->db->prepare($delete);
			return $this->statement->execute();
		} catch (PDOException $e) {
			$this->last_error = $e->getMessage() . '<pre>' . $e->getTraceAsString() . '</pre>';
			return FALSE;
		}
	}  // end of delete

	// get_id_name_array

	public function get_id_name_array($where = null, $order = null)
	{
		$my_array = array();
		//error_log("getarray:".$this->table." name:" . $this->name);    
		$get = "select * from `" . $this->table . '`';

		if (!is_null($where))
			$get .= " where " . $where;

		if (!is_null($order))
			$get .= " order by " . $order;
		else
			$get .= " order by " . $this->name;
		//error_log($get);

		try {
			$this->statement = $this->db->prepare($get);
			$this->statement->execute();
			while ($row = $this->statement->fetch()) {
				//error_log($row[$this->id].'='.$row[$this->name]);
				$my_array[$row[$this->id]] = $row[$this->name];
			}

			return $my_array;
		} catch (PDOException $e) {
			$this->last_error = $e->getMessage() . '<pre>' . $e->getTraceAsString() . '</pre>';
			return FALSE;
		}
	}

	// get_name_array

	public function get_name_array($where = null, $order = null)
	{
		$my_array = array();
		//error_log("getarray");    
		$get = "select * from `" . $this->table . '`';

		if (!is_null($where))
			$get .= " where " . $where;

		if (!is_null($order))
			$get .= " order by " . $order;
		else
			$get .= " order by " . $this->name;

		try {
			$this->statement = $this->db->prepare($get);
			$this->statement->execute();
			while ($row = $this->statement->fetch()) {
				//error_log($row[$this->id].'='.$row[$this->name]);
				$my_array[$row[$this->name]] = $row[$this->name];
			}

			return $my_array;
		} catch (PDOException $e) {
			$this->last_error = $e->getMessage() . '<pre>' . $e->getTraceAsString() . '</pre>';
			return FALSE;
		}
	}

	public function row_count()
	{
		return $this->statement->rowCount();
	}

	public function fetchObject()
	{
		//error_log("table:".$this->table);		
		if ($this->row = $this->statement->fetchObject()) return TRUE;
		else return FALSE;
	}

	public function fetch()
	{
		//error_log("table:".$this->table);		
		if ($this->row = $this->statement->fetch()) return TRUE;
		else return FALSE;
	}

	public function lastInsertId()
	{
		return ($this->db->lastInsertId());
	}

	public function get_row()
	{
		return $this->row;
	}

	public function get_field($field)
	{
		if (!$this->current_statement()) {
			if ($field == $this->parent_id)
				return $this->parent_id_to_add;
			else
				return $this->get_default($field);
		} else {

			/*		
if ($field=="registration_start_date") {
$field="xyz";
if (is_null($this->row->{$field})) error_log("null"); else error_log("not null");
}

*/
			//error_log("table:".$this->table."-".$field."-value:".$this->row->{$field});

			//error_log("db.class:".print_r($this->row,true));

			if (isset($this->field_definitions[$field])) {
				// valid field

				if (isset($this->row->{$field}) && !is_null($this->row->{$field}))
					$value = $this->row->{$field};
				else
					return NULL;
			} else {
				// check to see if field is part of a join or sql statement and not in the defining.
				if (isset($this->row->{$field})) {

					$value = $this->row->{$field};
				} else {
					//							error_log("get_field error. table:".$this->table."-".$field);
					//							error_log(print_r($this->row,true));
					$value = "";
				}
			}
			/*				
        if (isset($this->row->{$field}) && !is_null($this->row->{$field})) {
				   $value=$this->row->{$field};
				} else if (isset($this->field_definitions->{$field}) && is_null($this->row->{$field})) {
				   return NULL;
				} else {
							error_log("get_field error. table:".$this->table."-".$field);
							$value="";
				}
*/
			return $value;
		}
	}

	public function get_options($field)
	{
		if (isset($this->field_definitions[$field]['options'])) {
			return $this->field_definitions[$field]['options'];
		} else {
			return array();
		}
	} // end get_options

	public function last_error()
	{
		return $this->last_error;
	}

	public function get_default($field)
	{
		return $this->field_definitions[$field]['default'];
	}  // end of get_default


	// returns array of column names

	public function get_columns()
	{
		$total_column = $this->statement->columnCount();
		//		    $column[] = $total_column;
		//		return $total_column;  this works
		for ($counter = 0; $counter <= $total_column; $counter++) {
			$meta = $this->statement->getColumnMeta($counter);
			$column[] = $meta['name'];
			//		    $column[] = print_r($meta,true);
		}
		return $column;
	}
}  // End of Contact_class

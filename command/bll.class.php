<?php

/*
Copyright 2023
United States Golf Association
Paul Niebuhr

bll.class.php

This program is the business logic layer for the Command system.

This class should be inherited by the a class to manage the business logic for 
a specific table.

*/

require_once get_file("add_change_log_dal.class.php");

class Bll_class
{

	public $dal = null;

	public $last_error = null;

	public $sql = null;

	public $select = null;

	public $where = null;

	public $order_by = null;

	public $limit = null;

	public $page = null;

	public $rows_per_page = null;

	public $rows_no_limit = 0;  // select done with no limit set

	public $id_to_add = 0;  // id to be used for adding records

	public $last_inserted_id = 0;  // the last id inserted by the insert function of this classs

	/************************************
	
		function: merge
		
		Merge will move all records associated with the from cid to the to cid
		
		this may be overriden by child class if special processing is required, e.g. resetting primary flags.
	
	 *************************************/
	public function merge($from_cid, $to_cid)
	{
		// limit to 1000 records
		if ($this->update(array("cid" => $to_cid), "cid=" . $from_cid, 1000)) return true;
		else return false;
	}  // end merge

	// this function will add a record to the add/change log


	public function add_change_log($action, $id, $parent_id = 0)
	{

		$add_change_log_dal = new Add_change_log_dal_class;

		$uid = $_SESSION['uid'];

		$today = date("Y/m/d H:i");

		$values = array(
			'table_name' => $this->dal->table,
			'id' => $id,
			'parent_id' => $parent_id,
			'db_action' => $action,
			'change_date' => $today,
			'uid' => $uid,

		);
		$add_change_log_dal->insert($values);

		return;
	}  // end of add_change_log

	// this function will typically be overridden

	public function validate_data($values = null)
	{
		$my_return["error_message"] = "";
		$my_return["error_fields"] = "";

		return $my_return;
	}  // end of validate_data

	public function prepare_field($value, $field_type)
	{
		return $this->dal->prepare_field($value, $field_type);
	}
	public function show_where()
	{
		return $this->where;
	}
	public function get_columns()
	{
		return $this->dal->get_columns();
	}

	public function set_parent_id($id)
	{
		return $this->dal->parent_id = $id;
	}

	public function set_sql($sql)
	{
		return $this->sql = $sql;
	}

	public function beginTransaction()
	{
		return $this->dal->beginTransaction();
	}

	public function commit()
	{
		return $this->dal->commit();
	}

	public function get_options($field)
	{
		return $this->dal->get_options($field);
	}

	public function set_order_by($order_by)
	{
		return $this->order_by = $order_by;
	}

	public function set_limit($limit)
	{
		return $this->limit = $limit;
	}


	/*
	 * set_where
	 *    
	* Mandatory:   where  Valid where clause
	* 
	*/

	public function set_where($where = null)
	{
		$this->where = $where;
	}  // end of set_where

	/*
	 * construct_select
	 *    
	* Mandatory:   values  Array of keys and values to be used in select 
	* 
	*/

	public function construct_select($values = null, $operator = null)
	{
		$this->where = $this->dal->construct_select($values, $operator);
	}  // end of construct_select


	/*
	 * construct_select
	 *    
	* Mandatory:   values  Array of keys and values to be used in select 
	* 
	*/

	public function construct_select_exact($values = null, $operator = null)
	{
		$this->where = $this->dal->construct_select_exact($values, $operator);
	}  // end of construct_select


	/*
	 * sql - directly executes an sql statement against the database.
	*/

	public function sql($sql = null)
	{
		$this->sql = $sql;
		$my_return = $this->dal->sql($sql);
		$this->last_error = $this->dal->last_error;
		return $my_return;
	}  // end of sql


	/*
	 * set_page_length
	 *    
	* Mandatory:   $rows_per_page 
	* 
	*/

	public function set_page_length($rows_per_page = null)
	{

		if ($rows_per_page == null) return false;

		if (isset($_SESSION['user_rows_per_page'])) {
			if ($_SESSION['user_rows_per_page'] > 0) {
				$rows_per_page = $_SESSION['user_rows_per_page'];
			}
		}

		$this->rows_per_page = $rows_per_page;
	}  // end of set_page_length

	/*
	 * get_sql_page_of_rows
	 *    
	* Optional:   $page (default 1) 
	* 
	*/

	public function get_sql_page_of_rows($page = 1)
	{
		$start_row = ($page - 1) * $this->rows_per_page;
		$limit = $start_row . "," . $this->rows_per_page;
		$this->dal->sql($this->sql . " limit " . $limit);
	}  // end of get_sql_page_of_rows


	/*
	 * get_page_of_rows
	 *    
	* Optional:   $page (default 1) 
	* 
	*/

	public function get_page_of_rows($page = 1)
	{
		$start_row = ($page - 1) * $this->rows_per_page;
		$limit = $start_row . "," . $this->rows_per_page;
		$this->dal->select("*", $this->where, $this->order_by, $limit);
	}  // end of get_page_of_rows

	/*
	 * row_count
	 *    
	*/

	public function row_count()
	{
		return $this->dal->row_count();
	}  // end of row_count


	/*
	 * $get_sql_rows
	 *    
	*/

	public function get_sql_rows()
	{
		$this->dal->sql($this->sql);
	}  // end of get_sql_rows

	/*
	 * $get_rows
	 *    
	*/

	public function get_rows()
	{
		$this->dal->select("*", $this->where, $this->order_by, $this->limit);
	}  // end of get_rows

	public function get_row()
	{
		$this->dal->get_row();
	}  // end of get_rows

	/*
	 * fetchObject
	 *    
	*/

	public function fetchObject()
	{
		return $this->dal->fetchObject();
	}  // end of fetchObject

	/*
	 * fetch
	 *    
	*/

	public function fetch()
	{
		return $this->dal->fetch();
	}  // end of fetch


	public function get_field($field)
	{

		return $this->dal->get_field($field);
	}

	public function get_field_length($field)
	{
		if (is_null($this->dal))
			return "";
		else {
			$f = $this->dal->field_definitions;
			//			error_log("Definitions: $field ".$this->dal->field_definitions[$field]["length"]);
			if (isset($f[$field]))
				return $this->dal->field_definitions[$field]["length"];
			else
				return "";
		}
	}

	public function db_to_date_time($d)
	{
		return $this->dal->db_to_date_time($d);
	}  // end of db_to_date_time

	public function db_to_date($d)
	{
		return $this->dal->db_to_date($d);
	}  // end of db_to_date

	public function db_to_time($d)
	{
		return $this->dal->db_to_time($d);
	}  // end of db_to_time

	public function date_to_db($d)
	{
		return $this->dal->date_to_db($d);
	}  // end of date_to_db

	public function date_to_db_nq($d)
	{
		return $this->dal->date_to_db_nq($d);
	}  // end of date_to_db

	public function time_to_db($d)
	{
		return $this->dal->time_to_db($d);
	}  // end of time_to_db

	/*
	 * INSERT
	* Required: values  Array of field names and values.
	* e.g.  $values=array('prefix_name'=>'Mr.');  
	*/
	public function insert($values = null)
	{

		$this->last_inserted_id = 0;

		if ($values == null)
			return FALSE;

		$success = $this->dal->insert($values);

		$this->last_inserted_id = $this->dal->lastInsertId();

		return $success;
	} // end of insert

	/*
	 * 
	 
	* Required: values  Array of field names and values.
	* e.g.  $values=array('prefix_name'=>'Mr.'); 
	*           $where  Valid SQL where clause.  pass * to affect entire database
	*           $limit  Valid SQL limit phrase.  pass * to have no limit       
	*/
	public function update($values = null, $where = null, $limit = "1")
	{

		if ($values == null || $where == null)
			return FALSE;

		return $this->dal->update($values, $where, $limit);
	} // end of update

	/*
	 * DELETE
	* Optional:   where   Valid SQL where clause 
	* Optional:   limit   Integer limit of number of records to get 
	* 
	*/
	public function delete($where = null, $limit = 1)
	{
		return $this->dal->delete($where, $limit);
	}  // end of delete


	/*
	 * SELECT
	* Optional:   fields  String of comma separated fields to be selected.  Defaults to all fields 
	* Optional:   where   Valid SQL where clause 
	* Optional:   order_by   Valid SQL order by clause 
	* Optional:   limit   Integer limit of number of records to get 
	* 
	*/
	public function select($fields = '*', $where = null, $order_by = null, $limit = null)
	{
		//error_log("checking");
		//error_log(print_r($this->dal,true));
		if (is_null($this->dal)) error_log("is null");
		else error_log("is not null");

		//error_log("fields: $fields  where: $where  order_by: $order_by  limit: $limit");
		$result = $this->dal->select($fields, $where, $order_by, $limit);

		return $result;
	}  // end of select

	public function last_error()
	{
		return $this->dal->last_error;
	}

	public function lastInsertId()
	{

		return $this->last_inserted_id;
	}

	public function set_parent_id_to_add($parent_id_to_add = 0)
	{
		$this->dal->parent_id_to_add = $parent_id_to_add;
	}

	/*
	 * GET_ID_NAME_ARRAY
	*/
	public function get_id_name_array($where = null, $order = null)
	{

		return $this->dal->get_id_name_array($where, $order);
	} // end of get_id_name_array

	/*
	 * GET_NAME_ARRAY
	*/
	public function get_name_array($where = null, $order = null)
	{

		return $this->dal->get_name_array($where, $order);
	} // end of get_id_name_array

	/*
	 * get_field_definition
	*/
	public function get_field_definition($field_name)
	{

		return $this->dal->field_definitions[$field_name];
	} // end of get_field_definition

	/*
	 * set_field_default
	*/
	public function set_field_default($field_name, $default)
	{

		$this->dal->field_definitions[$field_name]["default"] = $default;
	} // end of set_field_default

	/*
	 * set_field_class
	*/
	public function set_field_class($field_name, $class = "")
	{

		$this->dal->field_definitions[$field_name]["class"] = $class;
	} // end of set_field_class



}

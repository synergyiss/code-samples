<?php
/*
Copyright 2023
United States Golf Association
Paul Niebuhr

contact_individual_bll.class.php

This program is the business logic layer for the Command system
contact_individual table.

*/

require_once BLL_CLASS_FILE;
require_once CONTACT_INDIVIDUAL_DAL_CLASS_FILE;

class Contact_individual_bll_class extends Bll_class
{

	public function __construct()
	{
		$this->dal = new Contact_individual_dal_class;
	}  // end construct()

	/*
		overrides parent function update	 
	*/
	public function update($values = null, $where = null, $limit = "1", $allow_push = 1)
	{
		require_once get_file("handicap_contact_bll.class.php");

		// check to see if handicap service should be updated

		$my_handicap_contact_bll = new Handicap_contact_bll_class;

		if (isset($values["cid"]) && $allow_push) if ($values["cid"] > 0) {
			$my_handicap_contact_bll->contact_individual_updated($values["cid"], $values);
		}

		$success = parent::update($values, $where, $limit);
		return $success;
	}

	/*
	add_or_update - this function will search for the primary key passed in by the
		$req parameter and determine if the record should be updated or a new
		record added.
	*/

	function add_or_update(&$req)
	{

		require_once(get_file("contact_dal.class.php"));
		$my_contact_dal = new Contact_dal_class;

		if ($req["cid"] > 0) {
			$my_return["add_flag"] = false;
			if ($this->update($req, "cid=" . $req["cid"]))
				if ($my_contact_dal->update($req, "cid=" . $req["cid"])) {
					$my_return["success"] = true;
					$my_return["cid"] = $req["cid"];

					// write to log

					$this->add_change_log(1, $req['cid']);
				}
		} else {
			unset($req["cid"]);
			if ($my_contact_dal->insert($req)) {
				$req["cid"] = $my_contact_dal->lastInsertId();
				if ($this->insert($req)) {

					// write to log

					$this->add_change_log(0, $req['cid']);
				} else {
					$req["error"] = "Contact Individual Save Error. " . $this->last_error;
					error_log("Contact Individual Save Error. " . $this->last_error);
				}
			} else {
				$req["error"] = "Contact Save Error. " . $my_contact_dal->last_error;
				error_log("Contact Save Error. " . $my_contact_dal->last_error);
			}
		}


		return $req["cid"];
	}  // end add_or_update

	/*
	get_id_name_array - this function will return an array with the contents
		of the table that can be used in a dropdown selection list.

	$where - sql where clause
	$order - sql order clause
	*/

	public function get_id_name_array($where = null, $order = null)
	{
		$my_array = array("0" => "Choose");

		$sql = "select ci.cid,last_name,first_name,middle_name,suffix_name from `contact_individual` as ci
    inner join contact as c on c.cid=ci.cid
    where contact_status='A' order by last_name,first_name,suffix_name";

		$this->sql($sql);

		while ($this->fetchObject()) {
			$my_array[$this->get_field("cid")] = $this->get_field("last_name") . ", " . $this->get_field("first_name") . " " . $this->get_field("middle_name") . " " . $this->get_field("suffix_name");
		}

		return $my_array;
	}

	public function calc_age()
	{

		$date = new DateTime($this->get_field("birth_date"));
		$now = new DateTime();
		$interval = $now->diff($date);
		$age = $interval->y;
		if ($now->format("Y") < $date->format("Y")) $age = -$age;
		return $age;
	}
}  // Contact_individual_bll_class

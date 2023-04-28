<?php

/*
	ghin_bll.class.php


	copyright 2023, United States Golf Association, Paul Niebuhr, Cynergy Networks, LLC.

	This class consumes the REST API from the
	United States Golf Association GHIN system.


*/

/*
https://app.swaggerhub.com/apis-docs/GHIN/Admin/1.0#/
*/
require_once get_file("contact_individual_bll.class.php");
require_once get_file("contact_club_bll.class.php");
require_once get_file("address_bll.class.php");
require_once get_file("email_contact_bll.class.php");
require_once get_file("handicap_bll.class.php");
require_once get_file("association_bll.class.php");
require_once get_file("message/message_bll.class.php", "module", "message");


class Ghin_bll_class
{


	public $ghin_error;
	public $ghin_http_code;
	public $success;

	// v. 0.9 api

	private $ghin_user = VASP_USER;
	private $ghin_password = VASP_PASSWORD;

	// v. 1 api

	private $app_key;
	private $app_secret;

	private $debug;


	public $token;
	private $token_expiry;

	public function __construct()
	{
		$api_version = get_system_variable("api_version", .9);
		if ($api_version == 1) {
			$this->api_key = get_system_variable("app_key", "");
			$this->app_secret = get_system_variable("app_secret", "");
		}
		$this->debug = get_system_variable("debug", 0);

		$this->ghin_error = "";
		$this->ghin_http_code = 0;
		$this->success = 0;

		$this->get_token();
	}  // end construct()


	/*
	 * get_ghin_status
	 
	 the way this should work is the usga command system return a 1 if everything is ok.  otherwise, return an error.  right now bypassing everything because
	 it needs to do a check on the url to determine what to send back.
	*/


	public function get_ghin_status()
	{

		//		if (URL_ADDRESS=='https://usga.commandsystem.org/command/')

		return 1;
		$ghin_status = get_system_variable("ghin_status", 2);
		if ($ghin_status != 2)
			$ghin_status;

		$ghin_status = 1;

		// get ghin status from usga.commandsystem.org

		$url = "https://usga.commandsystem.org/command/";
		$query = "page=api&operation=getghinstatus&ajax=1&un=ghpinterface&pw=ghp1";
		//$url="http://usga.commandsystem.org/command/";

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		curl_setopt($ch, CURLOPT_POST, 6);

		$data = curl_exec($ch);
		$info = curl_getinfo($ch);
		$this->ghin_http_code = $info['http_code'];
		$this->ghin_error = $data;

		if ($info['http_code'] == 0) {
			error_log("http_code is zero.  dns? data: $data");
			//			flush();
			$ghin_status = 0;
			$ghin_status = ("http_code is zero.  dns? data: $data");
		} else if ($info['http_code'] == 500) {
			error_log("GHIN_bll curl error httpcode: " . $info['http_code'] . " data: $data");
			error_log("Postfields: $query");
			//			flush();
			$ghin_status = 500;
		} else if ($info['http_code'] != 200) {
			error_log("GHIN_bll curl error httpcode: " . $info['http_code'] . " data: $data retries: $retries");
			error_log("Postfields: $query");
			//			flush();
			$ghin_status = 200;
			// return ERROR;
		} else
			$ghin_status = $data;

		return $ghin_status;
	}



	/*
	 * get_token
	*/

	public function get_token()
	{
		$ch = curl_init();

		$email = "paul@synergyinnovativesystems.com";
		$password = "Cynergy1!";
		$remember_me = "true";

		$query = "email=$email&password=$password&remember_me=$remember_me";

		$query = json_encode(array("user" => array("email" => $email, "password" => $password, "remember_me" => $remember_me)));


		curl_setopt($ch, CURLOPT_URL, "https://api.ghin.com/api/v1/users/login.json");

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'accept: application/json',
			'Content-Type: application/json',
		));

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_ENCODING, '');

		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		curl_setopt($ch, CURLOPT_POST, 6);
		//    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$result = curl_exec($ch);

		$info = curl_getinfo($ch);
		$ghin_http_code = $info['http_code'];

		//		return($ghin_http_code);

		//return(print_r($result,true));		

		$success = true;
		$r = json_decode($result);
		if (isset($r->token)) {
			$this->token = $r->token;
		} else {
			$success = false;
		}

		curl_close($ch);
		return $success;
		//********************** done
		$ch = curl_init();


		$url = "https://api.ghin.com/api/v1/associations/1/billing_report/logs.json";
		$query = "";

		curl_setopt($ch, CURLOPT_URL, "$url?$query");
		//		curl_setopt($ch, CURLOPT_URL, "http://cynergynetworks.com");


		$authorization = "Authorization: Bearer $token";

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			$authorization,
		));


		//		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_ENCODING, '');

		//	  		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		//	  		curl_setopt($ch, CURLOPT_POST, 6);

		$result = curl_exec($ch);

		$info = curl_getinfo($ch);
		$ghin_http_code = $info['http_code'];

		curl_close($ch);

		$my_return = "<textarea rows=500 cols=500>" . $token . "\n\nresult:" . print_r($result, true) . "\n\ninfo:" . print_r($info, true) .
			"postfields: " . print_r($query, true) . "</textarea>";


		$result = json_decode($result, true);
		$club_billing_report_logs = $result['club_billing_report_logs'];
		$my_return = "";
		foreach ($club_billing_report_logs as $club_billing_report_log) {
			$my_return .= $club_billing_report_log['id'] . ' ' .
				$club_billing_report_log['report_type'] . ' ' .
				$club_billing_report_log['initial_billing_revision_date'] . ' ' .
				$club_billing_report_log['status'] . ' ' .
				$club_billing_report_log['url'] . ' ' .
				"<br>";
		}

		return $my_return;

		return $token;


		$authorization = "Authorization: Bearer $token";
		curl_setopt($ch, CURLOPT_URL, "https://apis.usga.org/api/v1/clubs/1000026/golfersroster");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			$authorization,
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, false);
		$result = curl_exec($ch);

		$info = curl_getinfo($ch);
		$ghin_http_code = $info['http_code'];
		$ghin_http_code = print_r($info, true);

		curl_close($ch);

		return $result;

		$r = json_decode($result);



		return json_decode($result);
	} // end get_token

	public function ghin_fix_zip($zip)
	{
		$zip = trim($zip);
		if (strlen($zip) > 5) {
			$zip_array = explode("-", $zip);
			$zip = substr($zip_array[0], 0, 5);
		}
		return $zip;
	}

	//	funtion header_callback(char *buffer,   size_t size,   size_t nitems,   void *userdata) {
	// https://curl.haxx.se/libcurl/c/CURLOPT_HEADERFUNCTION.html

	public function header_callback(&$buffer, $size, $nitems, &$userdata)
	{
		return 1;
	}

	public function fix_golfer_array(&$golfer)
	{

		for ($i = 0; $i < sizeof($golfer); $i++) {

			if (!isset($golfer[$i]["ghinnumber"])) $golfer[$i]["ghinnumber"] = "";
			if (!isset($golfer[$i]["prefix"])) $golfer[$i]["prefix"] = "";
			if (!isset($golfer[$i]["firstname"])) $golfer[$i]["firstname"] = "";
			if (!isset($golfer[$i]["middlename"])) $golfer[$i]["middlename"] = "";
			if (!isset($golfer[$i]["lastname"])) $golfer[$i]["lastname"] = "";
			if (!isset($golfer[$i]["suffix"])) $golfer[$i]["suffix"] = "";
			if (!isset($golfer[$i]["gender"])) $golfer[$i]["gender"] = "";
			if (!isset($golfer[$i]["dateofbirth"])) $golfer[$i]["dateofbirth"] = "";
			if (!isset($golfer[$i]["email"])) $golfer[$i]["email"] = "";
			if (!isset($golfer[$i]["address1"])) $golfer[$i]["address1"] = "";
			if (!isset($golfer[$i]["address2"])) $golfer[$i]["address2"] = "";
			if (!isset($golfer[$i]["city"])) $golfer[$i]["city"] = "";
			if (!isset($golfer[$i]["state"])) $golfer[$i]["state"] = "";
			if (!isset($golfer[$i]["zip"])) $golfer[$i]["zip"] = "";
			if (!isset($golfer[$i]["membertype"])) $golfer[$i]["membertype"] = "";
			if (!isset($golfer[$i]["statusdate"])) $golfer[$i]["statusdate"] = "";
			if (!isset($golfer[$i]["club"])) $golfer[$i]["club"] = "";
			if (!isset($golfer[$i]["clubid"])) $golfer[$i]["clubid"] = "";
			if (!isset($golfer[$i]["holes"])) $golfer[$i]["holes"] = "";
			if (!isset($golfer[$i]["gender"])) $golfer[$i]["gender"] = "";
			if (!isset($golfer[$i]["hivalue"])) $golfer[$i]["hivalue"] = "";
			if (!isset($golfer[$i]["hidisplay"])) $golfer[$i]["hidisplay"] = "";
			if (!isset($golfer[$i]["assoc"])) $golfer[$i]["assoc"] = "";
			if (!isset($golfer[$i]["active"])) $golfer[$i]["active"] = "";
		}
	}

	/*
	ghin_query_2020

	$function - REST function to be called.
	$myquery - parameters added to the url
	$json_data - parameters for REST call
	$custom_request - if request should be other than POST
	*/

	public function ghin_query_2020($function, $myquery = "", $json_data = null, $custom_request = null, $retries = 15)
	{


		$ghin_error = "";
		$ghin_http_code = 0;
		$token = $this->token;


		if ($myquery > "") $myquery = $this->urlencodeall($myquery);



		if ($this->debug)
			error_log("starting ghp call $function");



		$query = $myquery;

		if ($query > "")
			$url = "https://api.ghin.com/api/v1/$function.json/?$query";
		else
			$url = "https://api.ghin.com/api/v1/$function.json";

		$success = false;
		$this->success = false;

		while (!$success and $retries > 0) {

			$ch = curl_init();
			if ($this->debug)
				error_log("after curl_init");

			curl_setopt($ch, CURLOPT_URL, "$url");


			$authorization = "Authorization: Bearer $token";

			if (!is_null($json_data)) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					$authorization,
					'accept: application/json',
					'Content-Type: application/json',
				));
				if (!is_null($custom_request)) {
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $custom_request);
				} else
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

				error_log("custom_request: $custom_request  json_data: $json_data");
			} else {
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					$authorization,
				));
			}

			if (!is_null($custom_request)) {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $custom_request);
			}


			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 120);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_ENCODING, '');



			if ($this->debug)
				error_log("before exed");

			if ($this->debug) {
				error_log($url);
			}

			$data = curl_exec($ch);

			if ($this->debug) {
				error_log("after exed");
			}




			$info = curl_getinfo($ch);
			$this->ghin_http_code = $info['http_code'];
			$this->ghin_error = $data;

			if ($info['http_code'] == 0) {
				error_log("http_code is zero.  dns? retries: $retries");
				$retries--;
			} else if ($info['http_code'] == 400) {
				error_log("GHIN_bll curl error httpcode: " . $info['http_code'] . " data: $data");
				error_log("Postfields: $query");
				$this->ghin_error = $data;
				$retries = 0;
				return false;
			} else if ($info['http_code'] == 403) { // forbidden
				error_log("GHIN_bll curl error httpcode: " . $info['http_code'] . " data: $data");
				error_log("Postfields: $query");
				$this->ghin_error = $data;
				$retries = 0;
				return false;
			} else if ($info['http_code'] == 404) {
				error_log("GHIN_bll curl error httpcode: " . $info['http_code'] . " data: $data");
				error_log("Postfields: $query");
				$this->ghin_error = $data;
				$retries = 0;
				return false;
			} else if ($info['http_code'] == 500) {
				error_log("GHIN_bll curl error httpcode: " . $info['http_code'] . " data: $data");
				error_log("Postfields: $query");
				$this->error = $data;
				$retries = 0;
				return false;
			} else if ($info['http_code'] != 200) {
				error_log("GHIN_bll curl error httpcode: " . $info['http_code'] . " data: $data retries: $retries");
				error_log("Postfields: $query");
				$retries--;
				sleep(5);
				// return ERROR;
			} else if ($data === false) {
				if (curl_errno($ch) > 0) {
					error_log("GHIN_bll curl error: " . curl_error($ch));
					$retries--;
					sleep(5);
					// return ERROR;
				} else {
					error_log("GHIN_bll curl returned no data.  Continuing");
				}
			} else {

				// check for ghin error message

				if (strpos(strtolower($data), "service unavailable") > 0) {
					error_log("ghin_bll: service unavailable");
					$success = false;
					sleep(5);
					$retries--;
				} else {
					$success = true;
				}

				if ($this->debug)
					error_log("GHIN_bll curl success: $success");
				//						error_log("GHIN_bll curl success: $success - data: ".substr($data,0,500));			
			}
			curl_close($ch);
			unset($ch);
		} // end while
		//
		$this->success = $success;
		// the following line makes the log files really big.
		//error_log("ghin_data: ".print_r($data,true));			

		if (!$success) {
			error_log("GHIN_bll curl failed.");
			return "Error";
			// return ERROR;
		} else {
			return $data;
		}
	} // end ghin_query_2020


	/*
	 * find_billing_report
	 
	 searches for a specific report.  returns the url
	 
	 
	 
	*/


	public function find_billing_report($report_type, $initial_billing_revision_date, $last_billing_revision_dates = "", $current_billing_revision_date = "", $id = "")
	{


		if ($id == "") $id = PRIMARY_ASSOCIATION;
		if ($last_billing_revision_dates == "") $last_billing_revision_dates = $initial_billing_revision_date;
		if ($current_billing_revision_date == "") $current_billing_revision_date = $initial_billing_revision_date;

		$last_billing_revision_dates_array = explode(",", $last_billing_revision_dates);
		$last_billing_revision_date = $last_billing_revision_dates_array[sizeof($last_billing_revision_dates_array) - 1];

		$function = "associations/$id/billing_report/logs";
		$query = "";

		$result = $this->ghin_query_2020($function, $query);
		$result = json_decode($result, true);

		$url_found = "";

		$club_billing_report_logs = $result['club_billing_report_logs'];

		if ($this->success) {

			foreach ($club_billing_report_logs as $club_billing_report_log) {

				error_log(print_r($club_billing_report_log, true));


				error_log($club_billing_report_log['initial_billing_revision_date'] . '-' .
					$club_billing_report_log['last_billing_revision_date'] . '-' .
					$club_billing_report_log['current_billing_revision_date'] . '-' .
					$initial_billing_revision_date . "-$report_type");
				if (
					(strtotime($club_billing_report_log['initial_billing_revision_date']) == strtotime($initial_billing_revision_date) &&
						$club_billing_report_log['report_type'] == $report_type
						&& $report_type == 'initial') ||
					(strtotime($club_billing_report_log['initial_billing_revision_date']) == strtotime($initial_billing_revision_date) &&
						strtotime($club_billing_report_log['last_billing_revision_date']) == strtotime($last_billing_revision_date) &&
						strtotime($club_billing_report_log['current_billing_revision_date']) == strtotime($current_billing_revision_date) &&
						$club_billing_report_log['report_type'] == $report_type
						&& $report_type == 'incremental')
				) {

					$run_found = true;
					if ($club_billing_report_log['status'] == "ready") {
						$url_found = $club_billing_report_log['url'];
					} else {
						$url_found = 'pending';
					}



					break;  // don't need to go further
				} // end if
			} // end foreach
		} // end if success

		return $url_found;
	} // end find_billing_report

	public function billing_report_run($report_type, $initial_billing_revision_date, $last_billing_revision_dates = "", $current_billing_revision_date = "", $id = "")
	{


		if ($id == "") $id = PRIMARY_ASSOCIATION;
		if ($last_billing_revision_dates == "") $last_billing_revision_dates = $initial_billing_revision_date;
		if ($current_billing_revision_date == "") $current_billing_revision_date = $initial_billing_revision_date;

		$query = "report_type=$report_type&initial_billing_revision_date=$initial_billing_revision_date&last_billing_revision_dates=$last_billing_revision_dates&current_billing_revision_date=$current_billing_revision_date";
		$function = "associations/$id/billing_report/run";
		$result = $this->ghin_query_2020($function, $query);

		if ($this->success) {
			$result = json_decode($result, true);
			$result = $result['message'];
		} else { // end if success
			$result = "fail";
		}

		return $result;
	} // end find_billing_report

	public function clubs($status = "Active", $id = "")
	{


		if ($id == "") $id = PRIMARY_ASSOCIATION;

		$per_page = 100;
		$page = 1;
		$per_page = 100;

		$clubs = array();

		$done = false;

		$max = 100;

		while (!$done && $max > 0) {
			//error_log("max: $max");
			$max--;
			$query = "per_page=$per_page&page=$page&association_id=$id";
			$function = "associations/$id/clubs";
			$result = $this->ghin_query_2020($function, $query);

			if ($this->success) {
				//error_log("success");
				$result = json_decode($result, true);
				$result = $result['clubs'];
				//error_log("size: ".sizeof($result));
				if (sizeof($result) > 0) {
					$clubs = array_merge($clubs, $result);
				} else {
					$done = true;
				}
			} else { // end if success
				$result = "fail";
				$done = true;
			}
			$page++;
		} // end while

		return $clubs;
	} // end clubs

	public function clubs_get_club($ghin_id, $id = "")
	{

		$query = "";
		if ($id == "") $id = PRIMARY_ASSOCIATION;


		$function = "associations/$id/clubs/$ghin_id";
		$result = $this->ghin_query_2020($function, $query);

		$result = json_decode($result, true);

		return $result;
	} // end clubs_get_club

	public function facilities($facility_id)
	{

		$query = "";
		$function = "facilities/$facility_id";
		$facilities = json_decode($this->ghin_query_2020($function, $query), true);

		return $facilities;
	} // end facilities


	public function courses($course_id)
	{

		$query = "";
		$function = "courses/$course_id";
		$courses = json_decode($this->ghin_query_2020($function, $query), true);

		return $courses;
	} // end courses


	public function courses_search($country = "", $state = "", $name = "", $facility_id = 0)
	{

		$function = "courses/search";
		$query = "";
		if ($country > "") {
			$query .= "country=$country";
		}
		if ($country > "") {
			if ($query > "") $query .= "&";
			$query .= "state=$state";
		}
		if ($name > "") {
			if ($query > "") $query .= "&";
			$query = "name=$name";
		}
		if ($facility_id > 0) {
			if ($query > "") $query .= "&";
			$query .= "facility_id=$facility_id";
		}

		$result = $this->ghin_query_2020($function, $query);
		$courses = json_decode($result, true);

		$courses = $courses['courses'];

		$keys = array_keys($courses);

		if ($keys[0] <> "0") {
			$courses = array("0" => $courses);
		}

		return $courses;
	}

	public function facility_home_courses($club_id)
	{

		$function = "clubs/$club_id/facility_home_courses";
		$query = "";

		$result = $this->ghin_query_2020($function, $query);
		$result = json_decode($result, true);
		return $result;
	}

	public function facility_home_courses_delete($club_id, $facility_id)
	{

		$function = "clubs/$club_id/facility_home_courses/$facility_id";
		$query = "";
		$custom_request = "DELETE";

		$result = $this->ghin_query_2020($function, $query, null, $custom_request);
		$result = json_decode($result, true);
		return $result;
	}

	public function golfers_search($golfer_id = null, $last_name = null, $first_name = null, $club_id = null, $state = null, $status = "All", $local_number = null, $association_id = PRIMARY_ASSOCIATION, $include_archived = 0, $email = null)
	{

		// this only gets the first 100 because ghin pages the results.  if we need more than 100 results will need to update this function

		$function = "golfers/search";

		$query = "";
		if (strtolower($association_id) != "all")
			$query .= "association_id=$association_id";

		$query .= "&per_page=100";
		$query .= "&page=1";
		$query .= "&sorting_criteria=id";
		$query .= "&order=ASC";

		if (!is_null($golfer_id))
			$query .= "&golfer_id=$golfer_id";

		if (!is_null($last_name))
			$query .= "&last_name=$last_name";

		if (!is_null($email))
			$query .= "&email=$email";

		if (!is_null($first_name))
			$query .= "&first_name=$first_name";

		if (!is_null($club_id))
			$query .= "&club_id=$club_id";

		if (!is_null($state))
			$query .= "&state=$state";

		if (!is_null($local_number))
			$query .= "&local_number=$local_number";

		if (strtolower($status) == "all") {
			//			$golfer2020 = json_decode($this->ghin_query_2020($function,$query."&status=Active"),true);
			//			$golfer2020_i = json_decode($this->ghin_query_2020($function,$query."&status=Inactive"),true);
			//			$result["golfers"]=array_merge($golfer2020['golfers'],$golfer2020_i['golfers']);
			$result = $this->ghin_query_2020($function, $query);
			$result = json_decode($result, true);
		} else {
			$result = $this->ghin_query_2020($function, $query . "&status=$status");
			$result = json_decode($result, true);
		}

		if (!$include_archived) {
			if (isset($result['golfers'])) {
				$new_result = array();
				foreach ($result["golfers"] as $one_item) {
					if (strtolower($one_item['status']) != "archived") {
						$new_result[] = $one_item;
					}
				}
			}

			$result['golfers'] = $new_result;
		}


		return $result;
	}

	public function golfers_global_email_search($email)
	{

		// this only gets the first 100 because ghin pages the results.  if we need more than 100 results will need to update this function

		$function = "golfers";

		$query = "";

		$query .= "&per_page=100";
		$query .= "&page=1";
		$query .= "&from_ghin=false";
		$query .= "&global_search=true";
		$query .= "&search=$email";
		$query .= "&order=ASC";

		$result = $this->ghin_query_2020($function, $query);
		$result = json_decode($result, true);


		return $result;
	}

	public function golfers_global_name_email_search($email, $last_name)
	{
		$golfer2020 =  $this->golfers_global_email_search($email);
		if (!$this->success)
			return array();
		else {
			$new_golfers = array();
			foreach ($golfer2020['golfers'] as $golfer) {
				if (trim(strtolower($golfer['last_name'])) == trim(strtolower($last_name)))
					$new_golfers[] = $golfer;
			}
		}
		$golfer2020['golfers'] = $new_golfers;
		return $golfer2020;
	}

	public function golfers_inactivate($golfer_ids = array(), $club_id, $from_date = null)
	{

		$function = "clubs/$club_id/golfers/inactivate";

		if (!is_null($from_date))
			$query = json_encode(array("golfer_ids" => $golfer_ids, "from_date" => date("Y-m-d", strtotime($from_date))));
		else
			$query = json_encode(array("golfer_ids" => $golfer_ids));
		//		$query=addslashes($query);
		//error_log("query: $query");

		$result = $this->ghin_query_2020($function, "", $query);
		$result = json_decode($result, true);
		return $result;
	}

	public function golfer_clear_inactivate($golfer_id, $club_id)
	{

		$result = $this->golfers_club_affiliations($golfer_id);

		$club_affiliations_id = 0;
		$use_in_mail = "";

		$club_affiliations = $result['club_affiliations'];

		//error_log(print_r($club_affiliations,true));

		foreach ($club_affiliations as $club_affiliation) {
			//error_log(print_r($club_affiliation,true));
			if ($club_affiliation['club_id'] == $club_id) {
				$club_affiliations_id = $club_affiliation['id'];
				$use_in_mail = $club_affiliation['use_in_mail'];
			}
		}
		//error_log($club_affiliations_id);		
		if ($club_affiliations_id > 0) {

			$function = "golfers/$golfer_id/club_affiliations/$club_affiliations_id";

			$custom_request = "PATCH";

			$json_data = json_encode(array("club_affiliation" => array("inactive_date" => "", "inactive_flag" => "", "use_in_mail" => $use_in_mail)));

			$result = $this->ghin_query_2020($function, "", $json_data, $custom_request);

			$result = json_decode($result, true);
		}

		return $club_affiliations_id;
	}


	public function golfers_club_affiliations($golfer_id)
	{

		$function = "golfers/$golfer_id/club_affiliations";

		$result = $this->ghin_query_2020($function);

		$result = json_decode($result, true);
		return $result;
	}

	public function clubs_golfers_create(
		$club_id,
		$cid,
		$om_ghin_type,
		$guardian_handicap_number = "",
		$guardian_first_name = "",
		$guardian_last_name = "",
		$guardian_relationship = "",
		$guardian_relationship_other = "",
		$email = ""
	) {

		$this->success = 0;

		$membership_types = $this->clubs_membership_types($club_id);

		$membership_types = $membership_types['membership_types'];

		$membership_id = 0;
		foreach ($membership_types as $membership_type) {
			if ($membership_type['code'] == $om_ghin_type) $membership_id = $membership_type['id'];
		}

		if ($membership_id == 0) {
			$this->ghin_error .= "Membership type ($om_ghin_type) not found for this club, $om_ghin_type";
			return "";
		}


		$this->ghin_error = "";

		$my_contact_individual_bll = new contact_individual_bll_class;
		$my_address_bll = new address_bll_class;
		$my_email_contact_bll = new email_contact_bll_class;

		$handicap_number = "";

		if ($cid > 0) {
			// get contact info
			$my_contact_individual_bll->construct_select(array("cid" => $cid));
			$my_contact_individual_bll->get_rows();
			$my_contact_individual_bll->fetchObject();
			//error_log("here: cid: $cid first_name: ".	$my_contact_individual_bll->get_field("first_name"));	

			// get primary address

			$sql = "select * from address where cid=$cid order by primary_address desc";

			$my_address_bll->sql($sql);
			$my_address_bll->fetchObject();

			// get primary email

			$sql = "select * from email_contact where cid=$cid order by primary_email desc";

			$my_email_contact_bll->sql($sql);
			$my_email_contact_bll->fetchObject();


			$query  = "";

			$zip = $this->ghin_fix_zip($my_address_bll->get_field("zip"));



			$function = "clubs/$club_id/golfers";


			$country = $my_address_bll->get_field("country");

			if ($country == "" || $country == "US") $country = "USA";

			$state = strtolower($my_address_bll->get_field("state"));
			if (
				$state == "bc" ||
				$state == "on" ||
				$state == "qu"
			) $country = "CAN";


			//error_log("function: $function  country: $country");

			if (
				$my_contact_individual_bll->calc_age() > 12 &&
				trim($my_email_contact_bll->get_field("email")) > ""
			) {
				$query = (array(
					"golfer" => array(
						"first_name" => $my_contact_individual_bll->get_field("first_name"),
						"middle_name" => $my_contact_individual_bll->get_field("middle_name"),
						"last_name" => $my_contact_individual_bll->get_field("last_name"),
						"email" => trim($my_email_contact_bll->get_field("email")),
					),
					"force" => "true",
					"membership_code" => array("id" => $membership_id),
				)
				);
			} else {
				$query = (array(
					"golfer" => array(
						"first_name" => $my_contact_individual_bll->get_field("first_name"),
						"middle_name" => $my_contact_individual_bll->get_field("middle_name"),
						"last_name" => $my_contact_individual_bll->get_field("last_name"),
					),

					"force" => "true",
					"membership_code" => array("id" => $membership_id),
				));
			}

			if ($my_address_bll->get_field("street1") > "" && $my_address_bll->get_field("street2") > "" && $my_address_bll->get_field("city") > "" && $my_address_bll->get_field("state") > "") {
				$query["primary_address"] = array(
					"street_1" => $my_address_bll->get_field("street1"),
					"street_2" => $my_address_bll->get_field("street2"),
					"city" => $my_address_bll->get_field("city"),
					"state" => $my_address_bll->get_field("state"),
					"zip" => $my_address_bll->get_field("zip"),
					"country" => $country,
				);
			}

			if ($my_contact_individual_bll->get_field("birth_date") > "") {
				$query["golfer"]["date_of_birth"] = date("Y-m-d", strtotime($my_contact_individual_bll->get_field("birth_date")));
			}

			if ($my_contact_individual_bll->get_field("gender") > "") {
				$query["golfer"]["gender"] = $my_contact_individual_bll->get_field("gender");
			}




			if ($guardian_last_name > "") {
				// look for email address of guardian

				$result = $this->guardians_golbal_search_email($email);
				if (isset($result['guardians'][0]['golfer_id'])) {
					$guardian_handicap_number = $result['guardians'][0]['golfer_id'];
				} else {

					$query["guardian"] = array(
						"first_name" => $guardian_first_name,
						"last_name" => $guardian_last_name,
						"email" => $email,
						"relationship" => $guardian_relationship,
					);
				}
			}

			if ($guardian_handicap_number > "") {
				$query["guardian"] = array(
					"golfer_id" => $guardian_handicap_number,
					"relationship" => $guardian_relationship,
				);
			}

			if ($guardian_relationship == "Other") {
				$query["guardian"]["other_relationship"] = $guardian_relationship_other;
			}

			$query = json_encode($query);
			if ($this->debug)
				error_log("query: $query");

			$result = $this->ghin_query_2020($function, "", $query);

			if ($this->debug)
				error_log("result: $result");

			$result = json_decode($result, true);

			if (isset($result['golfers']['id']))
				$handicap_number = $result['golfers']['id'];
			else
				$handicap_number = "";

			if (!$this->success) {
				//			$this->ghin_error.=" $guardian_first_name $guardian_last_name $guardian_email $relationship $other_relationship $guardian_handicap_number";
				error_log(print_r($this->ghin_error, true));
			}



			return $handicap_number;
		} else { // end if cid>0
			return "";
		}
	} // end clubs_golfers_create


	// Global Search for a golfer

	public function golfers($email)
	{

		$page = 1;
		$per_page = 100;

		$golfers = array();

		$done = false;

		$max = 100;

		while (!$done && $max > 0) {

			$max--;
			$query = "per_page=$per_page&page=$page";
			$query .= "&search=$email";
			$query .= "&global_search=true";
			$query .= "&order=ASC";
			$function = "golfers";
			$result = $this->ghin_query_2020($function, $query);

			if ($this->success) {
				$result = json_decode($result, true);
				$result = $result['golfers'];
				if (sizeof($result) > 0) {
					$golfers = array_merge($golfers, $result);
				} else {
					$done = true;
				}
			} else { // end if success
				$result = "fail";
				$done = true;
			}
			$page++;
		} // end while

		return $golfers;
	}  // end clubs_golfers

	// Search for a golfer

	public function clubs_golfers($club_id, $status = "Active", $ghinNumber = "", $is_merged = "")
	{

		$page = 1;
		$per_page = 100;

		$golfers = array();

		$done = false;

		$max = 100;

		while (!$done && $max > 0) {

			$max--;
			$query = "per_page=$per_page&page=$page";
			$query .= "&status=$status";
			$query .= "&sorting_criteria=id";
			$query .= "&order=ASC";
			if ($is_merged > "") {
				$query .= "&is_merged=$is_merged";
			}
			$function = "clubs/$club_id/golfers";
			$result = $this->ghin_query_2020($function, $query);

			if ($this->success) {
				$result = json_decode($result, true);
				$result = $result['golfers'];
				if (sizeof($result) > 0) {
					$golfers = array_merge($golfers, $result);
				} else {
					$done = true;
				}
			} else { // end if success
				$result = "fail";
				$done = true;
			}
			$page++;
		} // end while

		return $golfers;
	}  // end clubs_golfers

	// get individual golfer informaton

	public function clubs_golfer($club_id, $golfer_id)
	{

		$function = "clubs/$club_id/golfers/$golfer_id";

		$result = $this->ghin_query_2020($function);

		$result = json_decode($result, true);
		return $result;
	} // end clubs_golfer

	public function clubs_golfer_handicap_display($club_id, $golfer_id)
	{

		$function = "clubs/$club_id/golfers/$golfer_id/handicap_display";

		$result = $this->ghin_query_2020($function);

		$result = json_decode($result, true);
		return $result;
	} // end clubs_golfer

	public function golfers_handicap_history($golfer_id, $date_begin = "", $date_end = "")
	{

		if ($date_begin == "") {
			$date_begin = date("Y-m-d", time());
		} else {
			$date_begin = date("Y-m-d", strtotime($date_begin));
		}

		if ($date_end == "") {
			$date_end = date("Y-m-d", time());
		} else {
			$date_begin = date("Y-m-d", strtotime($date_end));
		}

		$function = "golfers/$golfer_id/handicap_history";

		$query = "date_begin=$date_begin";
		$query .= "&date_end=$date_end";
		$query .= "&rev_count=1";

		$result = $this->ghin_query_2020($function, $query);

		$result = json_decode($result, true);
		return $result;
	} // end clubs_golfer

	public function golfer_club_affiliations($handicap_number)
	{

		$function = "golfers/$handicap_number/club_affiliations";

		$query = "";

		$result = $this->ghin_query_2020($function, $query);

		$result = json_decode($result, true);
		return $result;
	} // end golfer_club_affiliations

	public function get_golfer_club_affiliation_id($handicap_number, $ghin_id)
	{

		$affiliations = ($this->golfer_club_affiliations($handicap_number));

		$club_affiliation_id = 0;
		$use_in_mail = "";

		$result = array();

		if (!is_null($affiliations)) {
			foreach ($affiliations["club_affiliations"] as $affiliation) {
				if ($affiliation["club_id"] == $ghin_id) {
					$result = $affiliation;
				}
			}
		}

		return $result;
	} // end clubs_golfer

	public function golfer_club_affiliations_update($handicap_number, $ghin_id, $status = null, $inactive_date = null, $inactive_flag = null, $status_date = null)
	{

		// there were problems with just doing one call to update a golfer's status.  You can't just change the status.
		// you have to also use the inactive_date and inactive_flag with today's date to get the status to change.
		//error_log("golfer_club_affiliations_update");	

		$result = 0;
		$this->success = 0;

		$club_affiliation_id_array =  $this->get_golfer_club_affiliation_id($handicap_number, $ghin_id);

		if (!isset($club_affiliation_id_array["id"])) {
			$this->ghin_error = "club affiliation not found hc: $handicap_number, club: $ghin_id";
			$this->success = 0;
			error_log("club affiliation not found hc: $handicap_number, club: $ghin_id");
			return null;
		}

		//error_log("golfer_club_affiliations_update1");	

		$club_affiliation_id = $club_affiliation_id_array["id"];
		$use_in_mail = $club_affiliation_id_array["use_in_mail"];
		$current_inactive_flag = $club_affiliation_id_array["inactive_flag"];
		$current_inactive_date = $club_affiliation_id_array["inactive_date"];


		//error_log("golfer_club_affiliations_update2 $club_affiliation_id");	
		//print "club_affiliation_id: $club_affiliation_id  use_in_mail: $use_in_mail current_inactive_flag: $current_inactive_flag current_inactive_date:$current_inactive_date";
		//return 0;

		if ($club_affiliation_id == 0) return 0;

		$function = "golfers/$handicap_number/club_affiliations/$club_affiliation_id";

		$custom_request = "PATCH";

		$json_array = array();

		$query = "";

		$today = date("Y-m-d");

		// first set status

		if (!is_null($status)) {
			if ($status == "Active") {
				$json_array["membership_active"] = true;
				/*
				$json_array["status"]=$status;
				$json_array["inactive_date"]=$today;
				$json_array["inactive_flag"]="Activate";
				$json_array["membership_active"]="true";
				$json_array=array("club_affiliation"=>$json_array);
				$json_data=json_encode($json_array);
				$result=$this->ghin_query_2020($function,"",$json_data,$custom_request);
				if (!$this->success) return null;
*/
			} else if ($status == "Inactive") {
				$json_array["membership_active"] = false;
				/*
				$json_array["status"]=$status;
				$json_array["inactive_date"]=$today;
				$json_array["inactive_flag"]="Inactivate";
				$json_array["membership_active"]="false";
				$json_array=array("club_affiliation"=>$json_array);
				$json_data=json_encode($json_array);
				$result=$this->ghin_query_2020($function,"",$json_data,$custom_request);
				if (!$this->success) return null;
*/
			}

			//			$result=json_decode($result,true);
		}

		//		$json_array=array();

		//		if (is_null($use_in_mail)) $use_in_mail="true";

		if (!is_null($inactive_date))
			$json_array["inactive_date"] = date("Y-m-d", strtotime($inactive_date));
		//		else
		//			$json_array["inactive_date"]=$current_inactive_date;

		if (!is_null($inactive_flag))
			$json_array["inactive_flag"] = $inactive_flag;
		//		else
		//			$json_array["inactive_flag"]=$current_inactive_flag;

		if (!is_null($status_date))
			$json_array["status_date"] = $status_date;

		$json_array = array("club_affiliation" => $json_array);
		/*
		if ($json_array["club_affiliation"]["inactive_flag"]=="Inactivate" && strtotime($json_array["club_affiliation"]["inactive_date"])>time()) {
			$json_data=json_encode($json_array);
			$result=$this->ghin_query_2020($function,"",$json_data,$custom_request);
			$result=json_decode($result,true);
		}
*/
		$json_data = json_encode($json_array);
		$result = $this->ghin_query_2020($function, "", $json_data, $custom_request);
		$result = json_decode($result, true);

		//error_log("updating inactive date $function,,$json_data,$custom_request");

		//		return $query;
		//		return "club_affiliation_id:$club_affiliation_id handicap_number: $handicap_number ghin_id:$ghin_id  status:$status  inactivate_date:$inactivate_date  inactive_flag:$inactive_flag  status_date:$status_date";
		//print "club_affiliation_id: $club_affiliation_id - json_data: $json_data";			

		return $result;
	} // end golfer_club_affiliations_update

	function golfer_get_inactivate_date_2020($handicap_number, $ghin_id)
	{
		$inactive_date = "";
		$golfer_club_affiliation =  $this->get_golfer_club_affiliation_id($handicap_number, $ghin_id, 0);
		if (is_null($golfer_club_affiliation))
			$inactive_date = "";
		else {
			$inactive_date = "";
			if (isset($golfer_club_affiliation['inactive_flag'])) {
				if ($golfer_club_affiliation['inactive_flag'] == 'Inactivate')
					$inactive_date = $golfer_club_affiliation['inactive_date'];
			}
		}
		return $inactive_date;
	}

	public function clubs_golfers_update_membership_code($club_id, $handicap_number, $membership_code)
	{

		$membership_types = $this->clubs_membership_types($club_id);

		$membership_types = $membership_types['membership_types'];

		$membership_id = 0;
		foreach ($membership_types as $membership_type) {
			if ($membership_type['code'] == $membership_code) $membership_id = $membership_type['id'];
		}

		if ($membership_id == 0) {
			$this->success = 0;
			$this->ghin_error .= "Membership type not found for this club, $membership_code";
			return null;
		}

		$query = "";

		$json_array = array(
			"membership_code" => array("id" => $membership_id)
		);


		$json_data = json_encode($json_array);


		$function = "clubs/$club_id/golfers/$handicap_number";

		$custom_request = "PATCH";

		$result = $this->ghin_query_2020($function, "", $json_data, $custom_request);

		$result = json_decode($result, true);

		return $result;
	} // end clubs_golfers_update_membership_code

	public function clubs_golfers_update_address($club_id, $handicap_number, $street_1, $street_2, $city, $state, $zip, $country = "USA")
	{

		$query = "";

		if ($handicap_number == "" || $street_1 == "" || $state == "" || $zip == "") return;

		if ($country == "" || $country == "US") $country = "USA";

		$json_array = array(
			"primary_address" => array(
				"street_1" => $street_1,
				"street_2" => $street_2,
				"city" => $city,
				"state" => $state,
				"zip" => $zip,
				"country" => $country,
			)
		);


		$json_data = json_encode($json_array);


		$function = "clubs/$club_id/golfers/$handicap_number";

		$custom_request = "PATCH";

		$result = $this->ghin_query_2020($function, "", $json_data, $custom_request);

		$result = json_decode($result, true);

		return $result;
	} // end clubs_golfers_update_address

	public function clubs_golfers_update_phone($club_id, $handicap_number, $number)
	{

		$query = "";

		if ($club_id == "" || $handicap_number == "" || $number == "") {
			$this->success = 0;
			$this->ghin_error = "Missing information.";
			return null;
		}

		$json_array = array("golfer" => array(
			"phone_number" => $number,
		));


		$json_data = json_encode($json_array);


		$function = "clubs/$club_id/golfers/$handicap_number";

		$custom_request = "PATCH";

		$result = $this->ghin_query_2020($function, "", $json_data, $custom_request);

		$result = json_decode($result, true);

		return $result;
	} // end clubs_golfers_update_phone

	public function clubs_golfers_update_dob($club_id, $handicap_number, $date_of_birth)
	{

		$query = "";

		if ($date_of_birth == "") return;


		$json_array = array("golfer" => array("date_of_birth" => $date_of_birth));

		$json_data = json_encode($json_array);

		$function = "clubs/$club_id/golfers/$handicap_number";

		$custom_request = "PATCH";

		$result = $this->ghin_query_2020($function, "", $json_data, $custom_request);

		$result = json_decode($result, true);

		return $result;
	} // end clubs_golfers_update_dob

	public function clubs_golfers_update_name($club_id, $handicap_number, $prefix_name, $first_name, $middle_name, $last_name, $suffix_name)
	{

		$query = "";

		$prefix_name = trim($prefix_name);
		$first_name = trim($first_name);
		$middle_name = trim($middle_name);
		$last_name = trim($last_name);
		$suffix_name = trim($suffix_name);

		$success = false;
		if ($handicap_number == "" || $first_name == "" || $last_name == "") {
			$this->success = 0;
			$this->ghin_error .= " Command missing required handicap_number:$handicap_number first_name:$first_name last_name:$last_name";
			error_log($this->ghin_error);
			return null;
		}

		$json_array = array(
			"golfer" => array(
				"prefix" => $prefix_name,
				"first_name" => $first_name,
				"middle_name" => $middle_name,
				"last_name" => $last_name,
				"suffix" => $suffix_name,
			)
		);

		$json_data = json_encode($json_array);

		$function = "clubs/$club_id/golfers/$handicap_number";

		$custom_request = "PATCH";
		//error_log(1);
		$result = $this->ghin_query_2020($function, "", $json_data, $custom_request);
		//error_log(2);



		$result = json_decode($result, true);
		//error_log(print_r($result,true));
		return $result;
	} // end clubs_golfers_update_email

	public function clubs_golfers_update_email($club_id, $handicap_number, $email)
	{

		$query = "";

		if ($email == "") {
			$this->success = 0;
			$this->ghin_error = "Command Invalid Email Sent";
			return null;
		}

		$json_array = array("golfer" => array("email" => $email));

		$json_data = json_encode($json_array);

		$function = "clubs/$club_id/golfers/$handicap_number";

		$custom_request = "PATCH";

		$result = $this->ghin_query_2020($function, "", $json_data, $custom_request);

		$result = json_decode($result, true);

		return $result;
	} // end clubs_golfers_update_email

	function clubs_membership_types($ghin_id)
	{
		$inactive_date = "";
		$function = "clubs/$ghin_id/membership_types";

		$result = $this->ghin_query_2020($function);

		$result = json_decode($result, true);
		return $result;
	} // end clubs_membership_types

	public function golfer_add_2020(
		$ghin_id,
		$cid,
		$om_ghin_type,
		$guardian_handicap_number = "",
		$guardian_first_name = "",
		$guardian_last_name = "",
		$guardian_relationship = "",
		$guardian_relationship_other = "",
		$email = ""
	) {
		$my_handicap_bll = new handicap_bll_class;
		$my_association_bll = new association_bll_class;

		$this->success = 0;

		// look up handicap service

		$my_handicap_bll->construct_select(array("handicap_name" => HANDICAP_SERVICE));
		$my_handicap_bll->get_rows();  // get all rows to get count

		$my_handicap_bll->sql("select * from handicap where handicap_name='" . HANDICAP_SERVICE . "'");  // get all rows to get count

		// write_log("info","u","select * from handicap where handicap_name='".HANDICAP_SERVICE."'");

		if ($my_handicap_bll->fetchObject()) {
			$hid = $my_handicap_bll->get_field("hid");
		}

		// get primary association asid


		$my_association_bll->construct_select(array("association_number" => PRIMARY_ASSOCIATION));
		$my_association_bll->get_rows();  // get all rows to get count

		if ($my_association_bll->fetchObject()) {
			$asid = $my_association_bll->get_field("asid");
		}

		//error_log("asid:".$asid);

		unset($golfer);


		$result = $this->clubs_golfers_create($ghin_id, $cid, $om_ghin_type, $guardian_handicap_number, $guardian_first_name, $guardian_last_name, $guardian_relationship, $guardian_relationship_other, $email);

		if ($result == "") {
			$result = "Error. " . $this->ghin_error;
			$this->success = 0;
		}

		return $result;
	} // end golfer_add_2020


	public function fix_golfer_array_2020(&$golfer)
	{

		for ($i = 0; $i < sizeof($golfer); $i++) {
			if (!isset($golfer[$i]["ghin"])) $golfer[$i]["ghin"] = "";
			if (!isset($golfer[$i]["id"])) $golfer[$i]["id"] = $golfer[$i]["ghin"];
			if ($golfer[$i]["ghin"] == "") $golfer[$i]["ghin"] = $golfer[$i]["id"];

			if (!isset($golfer[$i]["prefix"])) $golfer[$i]["prefix"] = "";
			if (!isset($golfer[$i]["first_name"])) $golfer[$i]["first_name"] = "";
			if (!isset($golfer[$i]["middle_name"])) $golfer[$i]["middle_name"] = "";
			if (!isset($golfer[$i]["last_name"])) $golfer[$i]["last_name"] = "";
			if (!isset($golfer[$i]["suffix"])) $golfer[$i]["suffix"] = "";
			if (!isset($golfer[$i]["gender"])) $golfer[$i]["gender"] = "";
			if (!isset($golfer[$i]["date_of_birth"])) $golfer[$i]["date_of_birth"] = "";
			if (!isset($golfer[$i]["email"])) $golfer[$i]["email"] = "";
			if (!isset($golfer[$i]["primary_address"]["street_1"])) $golfer[$i]["primary_address"]["street_1"] = "";
			if (!isset($golfer[$i]["primary_address"]["street_2"])) $golfer[$i]["primary_address"]["street_2"] = "";
			if (!isset($golfer[$i]["primary_address"]["city"])) $golfer[$i]["primary_address"]["city"] = "";
			if (!isset($golfer[$i]["primary_address"]["state"])) $golfer[$i]["primary_address"]["state"] = "";
			if (!isset($golfer[$i]["primary_address"]["zip"])) $golfer[$i]["primary_address"]["zip"] = "";
			if (!isset($golfer[$i]["service"])) $golfer[$i]["service"] = ""; // not used
			if (!isset($golfer[$i]["hi_display"])) $golfer[$i]["hi_display"] = "";
			if (!isset($golfer[$i]["low_hi_value"])) $golfer[$i]["low_hi_value"] = "";
			if (!isset($golfer[$i]["lowhi"])) $golfer[$i]["lowhi"] = ""; // not used
			if (!isset($golfer[$i]["low_hi_display"])) $golfer[$i]["low_hi_display"] = "";
			if (!isset($golfer[$i]["membershippaidtime"])) $golfer[$i]["membershippaidtime"] = ""; // not used


		}
	}

	// warning, clears inactivate date
	// only sets om_ghin_type when adding to a club.

	public function clubs_golfers_activate(
		$club_id,
		$handicap_number,
		$om_ghin_type,
		$guardian_handicap_number = "",
		$guardian_first_name = "",
		$guardian_last_name = "",
		$guardian_relationship = "",
		$guardian_relationship_other = "",
		$email = ""
	) {


		//error_log("activate1");
		$guardian_error = "";

		if ($guardian_last_name > "" || $guardian_handicap_number > "") {
			$this->golfers_guardians_create($handicap_number, $guardian_relationship, $guardian_relationship_other, $guardian_first_name, $guardian_last_name, $email, $guardian_handicap_number);
			if (!$this->success) $guardian_error = "guardian ghin error code: " . $this->ghin_http_code . " message: " . $this->ghin_error;
		} else {
			if ($email == "") {
				// get email adddress
				$my_email_contact_bll = new email_contact_bll_class;
				$my_handicap_contact_bll = new handicap_contact_bll_class;
				$my_handicap_contact_bll->construct_select(array("handicap_number" => $handicap_number));
				$my_handicap_contact_bll->get_rows();
				$my_handicap_contact_bll->fetchObject();
				$cid = $my_handicap_contact_bll->get_field("cid");
				if ($my_email_contact_bll->get_email($cid))
					$email = $my_email_contact_bll->get_field("email");
				// moved to below				$this->clubs_golfers_update_email($club_id,$handicap_number,$email);
			}
		}

		if ($email > "") {
			$this->clubs_golfers_update_email($club_id, $handicap_number, $email);
		}

		//error_log("activate2");

		// check to see if golfer is already active

		$result = $this->golfer_get_club_status($handicap_number, $club_id);
		//error_log("activate3");

		if (!$this->success) {
			error_log("not in club");
			return "";
		}

		if ($result == "Active") {
			error_log("aready active");
			return "Already Active";
		}


		//error_log("activate4");
		$query = "";
		/*

this is using the batch activate. may not be reliable

		$json_array=array("golfer_ids"=>array($handicap_number));

		$json_data=json_encode($json_array);

		$function="clubs/$club_id/golfers/activate";

		$custom_request="POST";

		$result=$this->ghin_query_2020($function,"",$json_data,$custom_request);
*/

		if ($result == "Inactive") {


			$this->golfer_club_affiliations_update($handicap_number, $club_id, "Active");

			$result = json_decode($result, true);

			if (!$this->success || $guardian_error > "")
				$this->ghin_error .= "$guardian_error-$guardian_first_name-$guardian_last_name-$email-$guardian_relationship-$guardian_relationship_other-$guardian_handicap_number";
			/*
	from old batch activate
	
			if (isset($result['unsuccessfully_activated_golfers'])) {
				if ($result['unsuccessfully_activated_golfers']>0 || $result['activated_golfers_who_need_further_review']>0) {
					$this->success=0;
					$this->ghin_error.=" ".print_r($result,true);
				}
			}
	error_log("ghin_activate_result: ".print_r($result,true));
	*/

			// Double check golfer is active

			$result = $this->golfer_get_club_status($handicap_number, $club_id);

			if (!$this->success) {
				return "";
			}

			if ($result != "Active") {
				error_log("naaa: Not active after activate");

				// try again
				/*
	from old batch update
				$result=$this->ghin_query_2020($function,"",$json_data,$custom_request);
		
				$result=json_decode($result,true);
	*/

				$this->golfer_club_affiliations_update($handicap_number, $club_id, "Active");

				if (!$this->success || $guardian_error > "")
					$this->ghin_error .= "$guardian_error-$guardian_first_name-$guardian_last_name-$email-$guardian_relationship-$guardian_relationship_other-$guardian_handicap_number";

				if (isset($result['unsuccessfully_activated_golfers'])) {
					if ($result['unsuccessfully_activated_golfers'] > 0 || $result['activated_golfers_who_need_further_review'] > 0) {
						$this->success = 0;
						$this->ghin_error .= " " . print_r($result, true);
					}
				}

				// triple check

				$result = $this->golfer_get_club_status($handicap_number, $club_id);

				if (!$this->success) {
					return "";
				}

				if ($result != "Active") {
					error_log("naaa: Not active after activate2");
					$this->success = 0;
					$this->ghin_error .= " second failure " . print_r($result, true);
					error_log($this->ghin_error);
				}
			} else {
				error_log("naaa: activation was successful");
			}
		} else {
			// add to club
			$result = $this->golfers_add_to_club(
				$club_id,
				$handicap_number,
				$om_ghin_type,
				$guardian_handicap_number,
				$guardian_first_name,
				$guardian_last_name,
				$guardian_relationship,
				$guardian_relationship_other,
				$email
			);
		}

		return $result;
	} // end clubs_golfers_activate

	public function clubs_golfers_inactivate($club_id, $handicap_number)
	{

		$query = "";

		$this->golfer_club_affiliations_update($handicap_number, $club_id, "Inactive");

		/*
old batch method
		$json_array=array("golfer_ids"=>array($handicap_number));

		$json_data=json_encode($json_array);

		$function="clubs/$club_id/golfers/inactivate";

		$custom_request="POST";

		$result=$this->ghin_query_2020($function,"",$json_data,$custom_request);

		$result=json_decode($result,true);

		return $result;		
*/
	} // end clubs_golfers_inactivate

	public function association_status_changes($last_ghin_time)
	{

		$query = "";

		$query = "start_time=$last_ghin_time";

		$function = "associations/" . PRIMARY_ASSOCIATION . "/status_changes";

		$result = $this->ghin_query_2020($function, $query);

		$result = json_decode($result, true);

		return $result;
	} // end association_status_changes

	public function association_address_changes($last_ghin_time)
	{

		$query = "";

		$query = "start_time=$last_ghin_time";

		$function = "associations/" . PRIMARY_ASSOCIATION . "/address_changes";

		$result = $this->ghin_query_2020($function, $query);

		$result = json_decode($result, true);

		return $result;
	} // end association_address_changes

	public function association_email_changes($last_ghin_time)
	{

		$query = "";

		$query = "start_time=$last_ghin_time";

		$function = "associations/" . PRIMARY_ASSOCIATION . "/email_changes";

		$result = $this->ghin_query_2020($function, $query);

		$result = json_decode($result, true);

		return $result;
	} // end association_email_changes

	public function golfers_add_to_club(
		$club_id,
		$handicap_number,
		$om_ghin_type,
		$guardian_handicap_number = "",
		$guardian_first_name = "",
		$guardian_last_name = "",
		$guardian_relationship = "",
		$guardian_relationship_other = "",
		$email = ""
	) {

		if ($email == "") {
			// get email adddress
			$my_email_contact_bll = new email_contact_bll_class;
			$my_handicap_bll = new handicap_bll_class;

			//			$my_handicap_bll->construct_select(array("handicap_number"=>$handicap_number));
			//			$my_handicap_bll->get_rows();
			$sql = "select * from handicap_contact where handicap_number='$handicap_number'";
			$my_handicap_bll->sql($sql);

			$my_handicap_bll->fetchObject();

			$cid = $my_handicap_bll->get_field("cid");

			//error_log("cid: $cid handicap_number: $handicap_number");
			$my_email_contact_bll->get_email($cid);

			$email = $my_email_contact_bll->get_field("email");
			//error_log("email: $email");

		}

		$query = "";

		// check for guardian information

		$guardian_error = "";
		if ($guardian_last_name > "" || $guardian_handicap_number > "") {
			$this->golfers_guardians_create($handicap_number, $guardian_relationship, $guardian_relationship_other, $guardian_first_name, $guardian_last_name, $email, $guardian_handicap_number);
			if (!$this->success) $guardian_error = "guardian ghin error code: " . $this->ghin_http_code . " message: " . $this->ghin_error;
		}
		// xxx
		$membership_types = $this->clubs_membership_types($club_id);

		$membership_types = $membership_types['membership_types'];

		$membership_id = 0;
		foreach ($membership_types as $membership_type) {
			if ($membership_type['code'] == $om_ghin_type) $membership_id = $membership_type['id'];
		}

		if ($membership_id == 0) {
			$this->ghin_error .= "Membership type ($om_ghin_type) not found for this club, $om_ghin_type";
			return "";
		}


		$json_array = array(
			"golfer_club" => array("club_id" => $club_id, "golfer_id" => $handicap_number),
			"golfer" => array("email" => $email),
			"membership_code" => array("id" => $membership_id)
		);

		$json_data = json_encode($json_array);

		$function = "golfers/add_to_club";

		$custom_request = "POST";

		$result = $this->ghin_query_2020($function, "", $json_data, $custom_request);

		if (!$this->success || $guardian_error > "")
			$this->ghin_error .= "$guardian_error-$guardian_first_name-$guardian_last_name-$email-$relationship-$other_relationship-$guardian_handicap_number";

		$result = json_decode($result, true);

		return $result;
	} // end clubs_golfers_inactivate

	public function golfer_get_club_membership_type($club_id, $golfer_id)
	{
		$function = "clubs/$club_id/golfers/$golfer_id/membership_types";

		$result = $this->ghin_query_2020($function);

		$result = json_decode($result, true);
		if ($this->success)
			return $result["membership_type"]["code"];
		else
			return "";
	} // end golfer_get_club_membership_type

	public function golfer_get_club_ghin_id($handicap_number)
	{
		$my_contact_individual_bll = new contact_individual_bll_class;
		$ghin_id = "";

		$query = "select ghin_id,handicap_number,ccr_status from contact_club_relation as ccr
		inner join contact_club as cc on cc.cid=ccr.club_cid
		where handicap_number='$handicap_number'
		order by ccr_status;";

		$my_contact_individual_bll->sql($query);
		if ($my_contact_individual_bll->fetchObject()) {
			$ghin_id = $my_contact_individual_bll->get_field('ghin_id');
		}


		return $ghin_id;
	} // end golfer_get_club_ghin_id

	public function get_club_ghin_id($club_number)
	{
		$my_contact_club_bll = new contact_club_bll_class;
		$ghin_id = "";

		$query = "select ghin_id from contact_club where club_number=$club_number order by ghin_id desc;";

		$my_contact_club_bll->sql($query);
		if ($my_contact_club_bll->fetchObject()) {
			$ghin_id = $my_contact_club_bll->get_field('ghin_id');
		}


		return $ghin_id;
	} // end golfer_get_club_ghin_id

	public function golfer_set_inactivate($handicap_number, $ghin_id, $inactivate_date)
	{
		//error_log("golfer_set_inactivate($handicap_number,$ghin_id,$inactivate_date)");
		$result = $this->golfer_club_affiliations_update($handicap_number, $ghin_id, null, $inactivate_date, "Inactivate");
		return $result;
	} // end golfer_get_club_ghin_id

	public function golfer_get_club_status($handicap_number, $ghin_id)
	{
		$club_status = "";
		$golfer =  $this->get_golfer_club_affiliation_id($handicap_number, $ghin_id);
		if (isset($golfer['id'])) {
			$club_status = $golfer['status'];
		}
		error_log(print_r($golfer, true));
		return $club_status;
	}

	public function golfers_merge($merged_id, $merger_id)
	{

		$query = "";

		$json_array = array("merger_id" => array($merger_id), "merged_id" => array($merged_id));

		$json_data = json_encode($json_array);

		$function = "golfers/merge";

		$custom_request = "POST";

		$result = $this->ghin_query_2020($function, "", $json_data, $custom_request);

		$result = json_decode($result, true);

		return $result;
	} // end clubs_golfers_inactivate

	public function golfers_logs($handicap_number, $page = 1)
	{

		$query = "page=$page";

		$function = "golfers/$handicap_number/logs";

		$result = $this->ghin_query_2020($function, $query);

		$result = json_decode($result, true);

		return $result;
	} // end association_email_changes

	public function golfers_guardians_create(
		$handicap_number,
		$relationship,
		$other_relationship,
		$guardian_first_name = null,
		$guardian_last_name = null,
		$guardian_email = null,
		$guardian_handicap_number = null
	) {

		$query = "";

		if ($guardian_email > "") {
			$result = $this->guardians_golbal_search_email($email);
			if (isset($result['guardians'][0]['golfer_id'])) {
				$guardian_handicap_number = $result['guardians'][0]['golfer_id'];
			}
		}

		if ($guardian_handicap_number > "") {
			$json_array = array("guardian" => array(
				"golfer_id" => $guardian_handicap_number,
				"relationship" => $relationship,
				"other_relationship" => $other_relationship,
			));
		} else {
			$json_array = array("guardian" => array(
				"first_name" => $guardian_first_name,
				"last_name" => $guardian_last_name,
				"email" => $guardian_email,
				"relationship" => $relationship,
				"other_relationship" => $other_relationship,
			));
		}

		$json_data = json_encode($json_array);

		$function = "golfers/$handicap_number/guardians";

		$custom_request = "POST";

		$result = $this->ghin_query_2020($function, "", $json_data, $custom_request);

		if (!$this->success)
			$this->ghin_error .= " $guardian_first_name $guardian_last_name $guardian_email $relationship $other_relationship $guardian_handicap_number";

		$result = json_decode($result, true);

		return $result;
	} // end clubs_golfers_inactivate

	public function golfers_guardians_get($handicap_number)
	{

		$query = "";

		$function = "golfers/$handicap_number/guardians";

		$result = $this->ghin_query_2020($function, $query);

		$result = json_decode($result, true);

		return $result;
	} // end association_email_changes

	public function guardians_golbal_search_email($email = null)
	{

		// this only gets the first 100 because ghin pages the results.  if we need more than 100 results will need to update this function

		if (is_null($email) || $email == "") {
			$this->ghin_error = "Email Required";
			$this->success = 0;
			return array();
		}

		$function = "guardians";

		$query = "";
		$query .= "&per_page=100";
		$query .= "&page=1";
		$query .= "&sorting_criteria=id";
		$query .= "&global_search=false";
		$query .= "&order=ASC";
		$query .= "&search=$email";

		$result = $this->ghin_query_2020($function, $query);
		$result = json_decode($result, true);


		return $result;
	}
}  // Ghin_bll_class

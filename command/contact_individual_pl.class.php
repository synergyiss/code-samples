<?php
/*
Copyright 2023
United States Golf Association
Paul Niebuhr

contact_individual_pll.class.php

This program is the presentation layer for the Command system
contact_individual table.

*/
require_once $_SESSION['command_root'] . "/app/settings/config.inc.php";
require_once CONTACT_INDIVIDUAL_BLL_CLASS_FILE;
require_once CONTACT_BLL_CLASS_FILE;
require_once ADDRESS_BLL_CLASS_FILE;
require_once CONTACT_CLUB_BLL_CLASS_FILE;
require_once CONTACT_CLUB_RELATION_BLL_CLASS_FILE;
require_once REGION_BLL_CLASS_FILE;
require_once CLUB_FACILITY_RELATION_BLL_CLASS_FILE;
require_once MODULE_PL_CLASS_FILE;

require_once CONTACT_CLUB_PL_CLASS_FILE;

require_once get_file("handicap_bll.class.php");
require_once get_file("handicap_contact_bll.class.php");
require_once get_file("association_contact_bll.class.php");
require_once get_file("association_bll.class.php");
require_once get_file("image_contact_bll.class.php");

require_once get_file("country_bll.class.php");
require_once get_file("state_bll.class.php");
require_once get_file("email_contact_bll.class.php");
require_once get_file("contact_phone_bll.class.php");

require_once get_file("text_type_bll.class.php");
require_once get_file("text_contact_bll.class.php");
require_once get_file("url_type_bll.class.php");
require_once get_file("contact_url_bll.class.php");
require_once get_file("pm_process/pm_process_bll.class.php", "module", "process_management");
require_once get_file("pm_contact_process/pm_contact_process_bll.class.php", "module", "process_management");
require_once get_file("children_contact/children_contact_bll.class.php", "module", "children");
require_once get_file("module_pl.class.php");
require_once get_file("vip_card/vip_card_bll.class.php", "module", "vip");
require_once get_file("wi_website/wi_website_bll.class.php", "module", "website_integration");
require_once get_file("wi_user_group/wi_user_group_pl.class.php", "module", "website_integration");


require_once PHONE_TYPE_BLL_CLASS_FILE;

require_once EMAIL_TYPE_BLL_CLASS_FILE;

require_once ADDRESS_TYPE_BLL_CLASS_FILE;

require_once SUBSCRIPTION_CONTACT_BLL_CLASS_FILE;

require_once PL_CLASS_FILE;

class Contact_individual_pl_class  extends Pl_class
{

	public function __construct()
	{
		$module_pl = new Module_pl_class;
		$this->name = "contact_individual";
		$this->title = "Contact";
		$this->toggle_tabs = true;
		$this->search_field_title = "";
		$this->search_field = "";
		$this->file_location = null;
		$this->module = null;
		$this->subforms = '
									$("#contact_form_data_div").html(data["contact_value"]);
									
                  $("#address_search_results_data_div").html(data["address_value"]);

                  $("#contact_phone_search_results_data_div").html(data["contact_phone_value"]);

                  $("#email_contact_search_results_data_div").html(data["email_contact_value"]);

                  $("#text_contact_search_results_data_div").html(data["text_contact_value"]);

                  $("#contact_url_search_results_data_div").html(data["contact_url_value"]);

                  $("#subscription_contact_search_results_data_div").html(data["subscription_contact_value"]);

                  $("#image_contact_search_results_data_div").html(data["image_contact_value"]);

                  $("#association_contact_search_results_data_div").html(data["association_contact_value"]);

                  $("#committee_contact_search_results_data_div").html(data["committee_contact_value"]);
									
                  $("#affiliation_contact_search_results_data_div").html(data["affiliation_contact_value"]);
									
                  $("#contact_club_relation_search_results_data_div").html(data["contact_club_relation_value"]);

                  $("#club_facility_relation_search_results_data_div").html(data["club_facility_relation_value"]);

                  $("#function_contact_search_results_data_div").html(data["function_contact_value"]);

                  $("#handicap_contact_search_results_data_div").html(data["handicap_contact_value"]);
						
                  $("#championship_contact_search_results_data_div").html(data["championship_contact_value"]);
									
                  $("#name_editing_div").html(data["name_editing_value"]);
		
		';
		$this->subforms .= $module_pl->get_contact_subforms();
		$this->parent_id = null;
		$this->id = "cid";
	}  // end construct()

	/*
	 * generate_form
	 *    
	* Mandatory:   $my_contact_individual_bll  a valid bll object that contains
	*				the data to be edited.
	* 
	*/

	public function generate_form(&$my_contact_individual_bll)
	{

		$my_ghin_bll = new ghin_bll_class;
		$my_contact_bll = new Contact_bll_class;

		// get main contact record

		$my_contact_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid")));
		$my_contact_bll->get_rows();
		$my_contact_bll->fetchObject();

		// get region array

		$region_bll = new Region_bll_class;
		$region_array = $region_bll->get_id_name_array();
		$region_array = array(0 => "None") + $region_array;

		$wi_website_bll = new wi_website_bll_class;
		$wi_website_array = $wi_website_bll->get_id_name_array();
		$wi_website_array = array(0 => "None") + $wi_website_array;

		$my_wi_user_group_pl = new wi_user_group_pl_class;

		$contact_status_array = contact_status_array();

		$pro_array = array(0 => "No", 1 => "Yes");
		$email_communication_array = array(0 => "No", 1 => "Yes");
		$third_party_soliciation_array = array(0 => "No", 1 => "Yes");
		$date_mask_prompt = get_system_variable("date_mask_prompt", "mm/dd/yyyy");


		$output = '    
    <form id="contact_individual_edit_form" name="contact_individual_edit_form" class="search">
      
     ';
		$form = "contact_individual_edit_form";
		$output .= theme_input($form, "", "cid", "hidden", $my_contact_individual_bll->get_field("cid"));
		$output .= theme_input($form, "", "ctid", "hidden", "1");
		$output .= theme_input($form, "Prefix Name", "prefix_name", "select", $my_contact_individual_bll->get_field("prefix_name"), 0, array("" => "", "Mr." => "Mr.", "Mrs." => "Mrs.", "Ms." => "Ms.", "Dr." => "Dr.", "Rev." => "Rev.", "Hon." => "Hon."));
		$output .= theme_input($form, "First Name", "first_name", "text", $my_contact_individual_bll->get_field("first_name"), $my_contact_individual_bll->get_field_length("first_name"));
		$output .= theme_input($form, "Middle Name", "middle_name", "text", $my_contact_individual_bll->get_field("middle_name"), $my_contact_individual_bll->get_field_length("middle_name"));
		$output .= theme_input($form, "Last Name", "last_name", "text", $my_contact_individual_bll->get_field("last_name"), $my_contact_individual_bll->get_field_length("last_name"));
		$output .= theme_input($form, "Suffix Name", "suffix_name", "text", $my_contact_individual_bll->get_field("suffix_name"), $my_contact_individual_bll->get_field_length("suffix_name"));
		$output .= theme_input($form, "Gender", "gender", "select", $my_contact_individual_bll->get_field("gender"), "", array("" => "", "M" => "Male", "F" => "Female"));

		if ($my_contact_individual_bll->get_field("cid") == "") {
			//      if (false) {
			// not doing an extended add screen now.
			// this is an add

			$address_type_bll = new Address_type_bll_class;
			$address_type_array = $address_type_bll->get_id_name_array();

			$country_bll = new country_bll_class;
			$country_array = $country_bll->get_id_name_array();

			$state_bll = new state_bll_class;
			$state_array = $state_bll->get_id_name_array();

			$phone_type_bll = new Phone_type_bll_class;
			$phone_type_array = $phone_type_bll->get_id_name_array();

			$email_type_bll = new Email_type_bll_class;
			$email_type_array = $email_type_bll->get_id_name_array();

			$contact_club_bll = new Contact_club_bll_class;
			//    		$contact_club_array = array(''=>'')+$contact_club_bll->get_id_name_array("cltid!=4");
			$contact_club_array = array('' => '') + $contact_club_bll->get_id_name_array("facility=0");

			$yes_no_array = array(0 => "no", 1 => "yes");

			$output .= theme_input($form, "Club", "club_cid", "select", "", 0, $contact_club_array);
			$output .= theme_input($form, "Club Member Number", "club_member_number", "text");
			$output .= theme_input($form, "Member Type", "member_type", "text");
			$output .= theme_input($form, "Date Joined (mm/dd/yyyy)", "ccr_date_joined", "text");

			$output .= theme_input($form, "Address Type", "atid", "select", "", 0, $address_type_array);
			$output .= theme_input($form, "Street 1", "street1", "text");
			$output .= theme_input($form, "Street 2", "street2", "text");
			$output .= theme_input($form, "Street 3", "street3", "text");
			$output .= theme_input($form, "City", "city", "text");
			$output .= theme_input($form, "State", "state", "select", "", 0, $state_array);
			$output .= theme_input($form, "Zip", "zip", "text");
			$output .= theme_input($form, "Country", "country", "select", "US", 0, $country_array);

			$output .= theme_input($form, "Birth Date (" . $date_mask_prompt . ")", "birth_date", "text", $my_contact_individual_bll->db_to_date($my_contact_individual_bll->get_field("birth_date")), $my_contact_individual_bll->get_field_length("birth_date") * 2);
			$output .= theme_input($form, "Spouse First Name", "spouse_name", "text", $my_contact_individual_bll->get_field("spouse_name"), $my_contact_individual_bll->get_field_length("spouse_name"));

			$output .= theme_input($form, "Phone Type", "ptyid", "select", "", 0, $phone_type_array);
			$output .= theme_input($form, "Number", "number", "text");

			$output .= theme_input($form, "Email Type", "etid", "select", "", 0, $email_type_array);
			$output .= theme_input($form, "Email", "email", "text");

			$output .= theme_input($form, "Website", "wiwid", "select", "", 0, $wi_website_array);
			$output .= theme_input($form, "User Name", "wiu_user_name", "text");
			$output .= theme_input($form, "Password", "wiu_password", "text");
			$output .= $my_wi_user_group_pl->generate_subform($form, 0);
		} else {

			// this is an edit

			$output .= theme_input_field_definition($form, "file_as", $my_contact_bll->get_field_definition("file_as"));



			//      	$output.= theme_input($form,"File As","file_as","text",$my_contact_bll->get_field("file_as"),$my_contact_bll->get_field_length("file_as"));
			$output .= theme_input($form, "Preferred First Name", "preferred_first_name", "text", $my_contact_individual_bll->get_field("preferred_first_name"), $my_contact_individual_bll->get_field_length("preferred_first_name"));
			$output .= theme_input($form, "Preferred Last Name", "preferred_last_name", "text", $my_contact_individual_bll->get_field("preferred_last_name"), $my_contact_individual_bll->get_field_length("preferred_last_name"));
			$output .= theme_input($form, "Company", "company", "text", $my_contact_individual_bll->get_field("company"), $my_contact_individual_bll->get_field_length("company"));
			$output .= theme_input($form, "Title", "title", "text", $my_contact_individual_bll->get_field("title"), $my_contact_individual_bll->get_field_length("title"));
			$output .= theme_input($form, "Birth Date (" . $date_mask_prompt . ")", "birth_date", "text", $my_contact_individual_bll->db_to_date($my_contact_individual_bll->get_field("birth_date")), $my_contact_individual_bll->get_field_length("birth_date") * 2);
			$output .= theme_input($form, "Death Date (" . $date_mask_prompt . ")", "death_date", "text", $my_contact_individual_bll->db_to_date($my_contact_individual_bll->get_field("death_date")), $my_contact_individual_bll->get_field_length("death_date") * 2);
			$output .= theme_input($form, "Spouse First Name", "spouse_name", "text", $my_contact_individual_bll->get_field("spouse_name"), $my_contact_individual_bll->get_field_length("spouse_name"));
			$output .= theme_input($form, "Spouse Last Name", "spouse_last_name", "text", $my_contact_individual_bll->get_field("spouse_last_name"), $my_contact_individual_bll->get_field_length("spouse_last_name"));
			$output .= theme_input($form, "Salutation", "salutation", "text", $my_contact_individual_bll->get_field("salutation"), $my_contact_individual_bll->get_field_length("salutation"));
			$output .= theme_input($form, "Donor Prefix", "donor_prefix", "text", $my_contact_individual_bll->get_field("donor_prefix"), $my_contact_individual_bll->get_field_length("preferred_last_name"));
			$output .= theme_input($form, "Preferred Contact Method", "preferred_contact_method", "select", $my_contact_bll->get_field("preferred_contact_method"), "", array("1" => "Email", "2" => "USPS"));
			$output .= theme_input($form, "Region", "rid", "select", $my_contact_individual_bll->get_field("rid"), 0, $region_array);
			$output .= theme_input($form, "Pro", "pro", "select", $my_contact_individual_bll->get_field("pro"), 0, $pro_array);
			$output .= theme_input($form, "Pro Year", "pro_year", "text", $my_contact_individual_bll->get_field("pro_year"), $my_contact_individual_bll->get_field_length("pro_year"));
			$output .= theme_input($form, "Year Started Volunteering", "year_started_volunteering", "text", $my_contact_individual_bll->get_field("year_started_volunteering"), $my_contact_individual_bll->get_field_length("year_started_volunteering"));
			$output .= theme_input($form, "Shirt Size", "shirt_size", "text", $my_contact_individual_bll->get_field("shirt_size"), $my_contact_individual_bll->get_field_length("shirt_size"));
			$output .= theme_input($form, "Jacket Size", "jacket_size", "text", $my_contact_individual_bll->get_field("jacket_size"), $my_contact_individual_bll->get_field_length("jacket_size"));
			$output .= theme_input($form, "Birthplace Country", "birthplace_country", "text", $my_contact_individual_bll->get_field("birthplace_country"), $my_contact_individual_bll->get_field_length("birthplace_country"));
			$output .= theme_input($form, "Passport Number", "passport_number", "text", $my_contact_individual_bll->get_field("passport_number"), $my_contact_individual_bll->get_field_length("passport_number"));
			$output .= theme_input($form, "Social Security Number", "ssn", "text", $my_contact_individual_bll->get_field("ssn"), $my_contact_individual_bll->get_field_length("ssn"));
			$output .= theme_input($form, "Handedness", "handedness", "select", $my_contact_individual_bll->get_field("handedness"), "", array("" => "", "R" => "Right", "L" => "Left"));
			$output .= theme_input($form, "Reinstated Amateur", "reinstated_amateur", "select", $my_contact_individual_bll->get_field("reinstated_amateur"), "", array("" => "", "0" => "No", "1" => "Yes"));
			$output .= theme_input($form, "Reinstated Year", "reinstated_year", "text", $my_contact_individual_bll->get_field("reinstated_year"), $my_contact_individual_bll->get_field_length("reinstated_year"));
			$output .= theme_input($form, "Contact Status", "contact_status", "select", $my_contact_bll->get_field("contact_status"), "", $contact_status_array);
			$output .= theme_input($form, "Note", "notes", "textarea", $my_contact_bll->get_field("notes"), $my_contact_bll->get_field_length("notes"));
			$output .= theme_input($form, "External Key 1", "external_key_1", "text", $my_contact_bll->get_field("external_key_1"), $my_contact_bll->get_field_length("external_key_1"));
			$output .= theme_input($form, "External Key 2", "external_key_2", "text", $my_contact_bll->get_field("external_key_2"), $my_contact_bll->get_field_length("external_key_2"));
			$output .= theme_input($form, "External Key 3", "external_key_3", "text", $my_contact_bll->get_field("external_key_3"), $my_contact_bll->get_field_length("external_key_3"));
		}


		$output .= $this->generate_save_button();

		$output .= $this->generate_change_history_button();

		$output .= '<script type="text/javascript" src="';
		$output .= get_file('jquery.timeentry.min.js');
		$output .= '"></script>';


		$output .= '
     
    </form>
    
    <script>
  
		$("#contact_individual_edit_form_number").mask("999-999-9999");
  
    $(document).ready(function(){
    
 		';

		$output .= $this->generate_save_ready_script();

		if ($my_contact_individual_bll->get_field("cid") == "")
			$output .= $this->generate_save_contact_script(null, null, null, null, null, null, "individual_search", null, false);
		else
			$output .= $this->generate_save_script(null, null, null, null, null, null, "individual_search", null, false);

		$output .= $this->generate_change_history_ready_script();

		$output .= $this->generate_change_history_script();


		$output .= ' 
     
    }); // end document ready
    </script>
    
    ';
		return $output;
	}  // end of generate_form

	/*
	 * generate_list - creates a selection list of contacts
	 *    
	* Mandatory:   $my_contact_individual_bll  a valid bll object 
	*              $popup:  if true, this is a popup that needs a different action
	*/

	public function generate_list(&$my_contact_individual_bll, $popup = false, $id_field = 'cid', $name_div = 'contact_name')
	{
		$my_contact_bll = new Contact_bll_class;
		$my_pm_contact_process_bll = new Pm_contact_process_bll_class;
		$my_address_bll = new Address_bll_class;
		$my_contact_phone_bll = new contact_phone_bll_class;
		$my_email_contact_bll = new email_contact_bll_class;
		$my_contact_club_relation_bll = new Contact_club_relation_bll_class;
		$my_contact_club_bll = new Contact_club_bll_class;

		$my_contact_club_array = $my_contact_club_bll->get_id_name_array();

		$contact_status_array = contact_status_array();

		$my_pm_process_bll = new Pm_process_bll_class;
		$my_pm_process_array = $my_pm_process_bll->get_id_name_array();

		$my_subscription_contact_bll = new Subscription_contact_bll_class;
		$my_vip_card_bll = new Vip_card_bll_class;
		$my_module_pl = new module_pl_class;

		$show_process_in_contact_list = get_system_variable("show_process_in_contact_list", 0);
		$show_magazine_in_contact_list = get_system_variable("show_magazine_in_contact_list", 0);
		$show_phone_in_contact_list = get_system_variable("show_phone_in_contact_list", 1);

		$output = "<table class=\"contact_individual_search_results search_results_table\">";
		$odd = true;
		$output .= "<tr class=\"table_header\">";
		$output .= "<td id=\"name_col_head\" class=\"table_header_col\" >";
		$output .= "Name";
		$output .= "</td>";
		$output .= "<td id=\"club_col_head\" class=\"table_header_col\" >";
		$output .= "Club";
		$output .= "</td>";

		$output .= "<td id=\"address_col_head\" class=\"table_header_col\">";
		$output .= "Address";
		$output .= "</td>";

		$output .= "<td id=\"ghin_name_col_head\" class=\"table_header_col\">";
		$output .= "GHIN #";
		$output .= "</td>";

		$output .= "<td id=\"email_col_head\" class=\"table_header_col\">";
		$output .= "Email";
		$output .= "</td>";

		if ($my_module_pl->is_module_loaded("vip")) {
			$output .= "<td id=\"ghin_name_col_head\" class=\"table_header_col\">";
			$output .= "VIP #";
			$output .= "</td>";
		}

		if ($show_magazine_in_contact_list) {
			$output .= "<td id=\"subscription_col_head\" class=\"table_header_col\">";
			$output .= "Receive Magazine";
			$output .= "</td>";
		}

		if ($show_phone_in_contact_list) {
			$output .= "<td id=\"subscription_col_head\" class=\"table_header_col\">";
			$output .= "Phone";
			$output .= "</td>";
		}

		$output .= "<td id=\"status_col_head\" class=\"table_header_col\">";
		$output .= "Status";
		$output .= "</td>";
		$output .= "</tr>";



		while ($my_contact_individual_bll->fetchObject()) {

			// get main contact record

			$my_contact_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid")));
			$my_contact_bll->get_rows();
			$my_contact_bll->fetchObject();

			$my_address_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid"), "primary_address" => "1")); //JL added in that Primary address should be listed
			$my_address_bll->get_rows();
			$my_address_bll->fetchObject();

			$my_email_contact_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid"), "primary_email" => "1")); //JL added in that Primary address should be listed
			$my_email_contact_bll->get_rows();
			$my_email_contact_bll->fetchObject();

			$my_contact_phone_bll->sql("select * from contact_phone as cp inner join phone_type as pt on pt.ptyid=cp.ptyid where cid=" . $my_contact_individual_bll->get_field("cid"));
			//error_log("select * from contact_phone as cp inner join phone_type as pt on pt.ptyid=cp.ptyid where cid=".$my_contact_individual_bll->get_field("cid"));			  	

			$my_contact_club_relation_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid"), "ccr_status" => "A"));
			$my_contact_club_relation_bll->get_rows();

			$club_name = "";

			while ($my_contact_club_relation_bll->fetchObject()) {
				if ($club_name > "") $club_name .= "<br>";
				if (isset($my_contact_club_array[$my_contact_club_relation_bll->get_field("club_cid")]))
					$club_name .= $my_contact_club_array[$my_contact_club_relation_bll->get_field("club_cid")];
			}

			if ($show_process_in_contact_list) {
				// this is igf.  show the process, which is their role in the nfm
				$my_pm_contact_process_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid")));
				$my_pm_contact_process_bll->get_rows();
				while ($my_pm_contact_process_bll->fetchObject()) {
					if ($club_name > "") $club_name .= ", ";
					$club_name .= $my_pm_process_array[$my_pm_contact_process_bll->get_field("pmpid")];
				}
			}

			$my_handicap_contact_individual_bll = new Handicap_contact_bll_class;
			$my_handicap_contact_individual_bll->id_to_add = $my_contact_individual_bll->get_field("cid");
			$my_handicap_contact_individual_bll->construct_select(array('cid' => $my_contact_individual_bll->get_field("cid")));
			$my_handicap_contact_individual_bll->get_rows();  // get all rows to get count 
			$my_handicap_contact_individual_bll->fetchObject();

			//Checking to see if you have a subscription to Met Golfer
			$hasSubscription = "No";
			$my_subscription_contact_bll->id_to_add = $my_contact_bll->get_field("cid");
			$my_subscription_contact_bll->construct_select(array('cid' => $my_contact_bll->get_field("cid"), 'sid' => "1"));
			$my_subscription_contact_bll->get_rows();  // get all rows to get count
			$all_row_count = $my_subscription_contact_bll->row_count();
			if ($all_row_count > 0) {
				$hasSubscription = "Yes";
			}

			$vip_numbers = $my_vip_card_bll->get_vip_numbers($my_contact_bll->get_field("cid"));


			/*******************************************************************************/

			$output .= $this->generate_edit_tr($my_contact_individual_bll->get_field("cid"), null, null, $my_contact_individual_bll->get_field("first_name") . " " . $my_contact_individual_bll->get_field("middle_name") . " " . $my_contact_individual_bll->get_field("last_name") . " " . $my_contact_individual_bll->get_field("suffix_name") . ", " . str_replace(" ", "&nbsp;", $my_address_bll->get_field("street1")) . ", " . str_replace(" ", "&nbsp;", $my_address_bll->get_field("city")), $odd);

			$output .= '<td class="name_col">';
			$output .= $my_contact_individual_bll->get_field("last_name");
			$output .= ", " . $my_contact_individual_bll->get_field("first_name");
			if ($my_contact_individual_bll->get_field("middle_name") > "")
				$output .= ", " . $my_contact_individual_bll->get_field("middle_name");
			if ($my_contact_individual_bll->get_field("suffix_name") > "")
				$output .= ", " . $my_contact_individual_bll->get_field("suffix_name");
			$output .= "</td>";
			$output .= '<td>';
			$output .= $club_name;
			$output .= "</td>";
			$output .= '<td>';
			$output .= str_replace(" ", "&nbsp;", $my_address_bll->get_field("street1"));
			$output .= "<br/>";
			if ($my_address_bll->get_field("city") > "")
				$output .= $my_address_bll->get_field("city") . ", ";
			$output .= $my_address_bll->get_field("state") . " ";
			$output .= $my_address_bll->get_field("zip");
			$output .= "</td>";
			$output .= '<td>';
			$output .= $my_handicap_contact_individual_bll->get_field("handicap_number");

			if ($my_handicap_contact_individual_bll->get_field("hc_status") == "I")
				$output .= "(I)";


			$output .= "</td>";

			$output .= '<td>';
			$output .= '<a href="mailto:' . $my_email_contact_bll->get_field("email") . '">';
			$output .= $my_email_contact_bll->get_field("email");
			$output .= "</a>";
			$output .= "</td>";

			if ($my_module_pl->is_module_loaded("vip")) {
				$output .= "<td>";
				$output .= $vip_numbers;
				$output .= "</td>";
			}

			if ($show_magazine_in_contact_list) {
				$output .= '<td>';
				$output .= $hasSubscription;
				$output .= "</td>";
			}

			if ($show_phone_in_contact_list) {
				$output .= '<td>';
				$phone = "";
				while ($my_contact_phone_bll->fetchObject()) {
					if ($phone > "") $phone .= "<br>";
					$phone .= $my_contact_phone_bll->get_field("number") . ": " . $my_contact_phone_bll->get_field("phone_type_name");
				}
				$output .= $phone;
				$output .= "</td>";
			}

			$output .= '<td>';
			$output .= '<img class="index-card-status" src="' . IMAGE_PATH . '/';
			$output .= strtolower($my_contact_bll->get_field("contact_status"));
			$output .= "-status-icon.png\">";
			$output .= "</td>";
			$output .= "</tr>";
			$odd = !$odd;
		}
		$output .= "</table>\n";

		$output .= '
      <script>
    $(document).ready(function(){
    
		';

		$output .= $this->generate_edit_ready_script();

		if ($popup) {
			$output .= $this->generate_select_script(null, null, null, null, null, null, "contact_individual_search", "contact_individual", null, $id_field, $name_div);
		} else {
			$output .= $this->generate_edit_script(null, null, null, null, null, null, "contact_individual_search", "contact_individual");
		}


		//error_log("pn: ".$this->generate_edit_script(null,null,null,null,null,null,"individual_search","contact"));
		$output .= ' 
	 
	 });  // end document_ready
    </script>
    
      ';
		return $output;
	}  // end of generate_list

	public function generate_short_form_fields(&$my_contact_individual_bll, $form)
	{

		$my_contact_bll = new Contact_bll_class;

		// get main contact record

		$my_contact_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid")));
		$my_contact_bll->get_rows();
		$my_contact_bll->fetchObject();


		$shirt_size_array = shirt_size_array();

		$output .= theme_input($form, "", "cid", "hidden", $my_contact_individual_bll->get_field("cid"));
		$output .= theme_input($form, "", "ctid", "hidden", "1");
		$output .= theme_input($form, "First Name", "first_name", "text", $my_contact_individual_bll->get_field("first_name"), $my_contact_individual_bll->get_field_length("first_name"));
		$output .= theme_input($form, "Middle Name", "middle_name", "text", $my_contact_individual_bll->get_field("middle_name"), $my_contact_individual_bll->get_field_length("middle_name"));
		$output .= theme_input($form, "Last Name", "last_name", "text", $my_contact_individual_bll->get_field("last_name"), $my_contact_individual_bll->get_field_length("last_name"));
		$output .= theme_input($form, "Suffix Name", "suffix_name", "text", $my_contact_individual_bll->get_field("suffix_name"), $my_contact_individual_bll->get_field_length("suffix_name"));
		$output .= theme_input($form, "Nickname", "preferred_first_name", "text", $my_contact_individual_bll->get_field("preferred_first_name"), $my_contact_individual_bll->get_field_length("preferred_first_name"));
		$output .= theme_input($form, "Gender", "gender", "select", $my_contact_individual_bll->get_field("gender"), "", array("" => "", "M" => "Male", "F" => "Female"));
		$output .= theme_input($form, "Birth Date (mm/dd/yyyy)", "birth_date", "text", $my_contact_individual_bll->db_to_date($my_contact_individual_bll->get_field("birth_date")), $my_contact_individual_bll->get_field_length("birth_date") * 2);
		$output .= theme_input($form, "Note", "notes", "textarea", $my_contact_bll->get_field("notes"), $my_contact_bll->get_field_length("notes"));

		return $output;
	}  // end of generate_form


	// function api_createindividual
	//
	// Parameters $req - an array of parameters
	//
	//  array elements used: (only 1)
	//
	//		external_key_1
	//		external_key_2
	//		external_key_3
	//    capi_external_key_field
	//		capi_external_key
	//		capi_member_number_field
	//		capi_member_number
	//		capi_club_number   (club cid
	//		handicap_number
	//
	//  	This call will only return a cid.  Will not return xml

	public function api_createindividual($req)
	{
		$first_name = "";
		$last_name = "";
		$email = "";
		$cc_email = "";
		$phone = "";
		$capi_member_number_field = "";
		$capi_member_number = "";
		$capi_club_number = "";
		$handicap_number = "";

		foreach ($req as $key => $value) ${$key} = $value;

		if ($first_name == "" || $last_name == "") return 0;

		$last_name = stripslashes(htmlspecialchars(urldecode($last_name)));
		$first_name = stripslashes(htmlspecialchars(urldecode($first_name)));

		$output = "";
		$external_key_field = "";
		$cid = 0;

		$my_contact_bll = new Contact_bll_class;
		$my_contact_individual_bll = new Contact_individual_bll_class;
		$my_email_contact_bll = new Email_contact_bll_class;
		$my_contact_phone_bll = new Contact_phone_bll_class;
		$my_contact_club_relation_bll = new Contact_club_relation_bll_class;
		$my_contact_club_bll = new Contact_club_bll_class;
		$my_handicap_contact_bll = new handicap_contact_bll_class;


		if (isset($external_key_1)) {
			if ($external_key_1 > "") {
				$external_key_field = "external_key_1";
				$external_key = $external_key_1;
			}
		} else if (isset($external_key_2)) {
			if ($external_key_2 > "") {
				$external_key_field = "external_key_2";
				$external_key = $external_key_2;
			}
		} else if (isset($external_key_3)) {
			if ($external_key_3 > "") {
				$external_key_field = "external_key_3";
				$external_key = $external_key_3;
			}
		} else if (isset($capi_external_key_field)) {
			if ($capi_external_key > "") {
				$external_key_field = $capi_external_key_field;
				$external_key = $capi_external_key;
			}
		}


		$contact_array = array(
			"ctid" => 1,
			"contact_status" => "A",
			"file_as" => $last_name . ", " . $first_name,
		);

		if (isset($external_key_field) && isset($capi_external_key))
			$contact_array[$external_key_field] = $capi_external_key;

		$my_contact_bll->insert($contact_array);
		$cid = $my_contact_bll->lastInsertId();

		// create contact_indivividual

		$contact_individual_array = array(
			"cid" => $cid,
		);

		// loop through all keys and sent whatever was passed in

		foreach ($my_contact_individual_bll->dal->field_definitions as $key => $value) {
			if (isset($req[$key])) {
				$contact_individual_array[$key] = $req[$key];
			}
		} // end foreach


		$my_contact_individual_bll->insert($contact_individual_array);

		if ($handicap_number > "") {
			// create email_contact
			$handicap_contact_array = array(
				"cid" => $cid,
				"hid" => 1,
				"handicap_number" => $handicap_number,
				"hc_status" => "A",
			);
			$my_handicap_contact_bll->insert($handicap_contact_array);
		}

		if ($email > "") {
			// create email_contact
			$email_contact_array = array(
				"cid" => $cid,
				"etid" => 1,
				"email" => $email,
				"cc_email" => $cc_email,
				"primary_email" => 1,
			);
			$my_email_contact_bll->insert($email_contact_array);
		}

		if ($phone > "") {
			// create email_contact
			$contact_phone_array = array(
				"cid" => $cid,
				"ptyid" => 1,
				"number" => $phone,
			);
			$my_contact_phone_bll->insert($contact_phone_array);
		}

		// create contact_club_relation

		// look up club

		if ($capi_club_number > "") {
			$my_contact_club_bll->construct_select(array("club_number" => $capi_club_number));
			$my_contact_club_bll->get_rows();
			if ($my_contact_club_bll->fetchObject()) {
				$contact_club_relation_array = array(
					"cid" => $cid,
					"club_cid" => $my_contact_club_bll->get_field("cid"),
					"club_member_number" => $capi_member_number,
					"ccr_status" => "A",
					"primary_club" => 1,
				);
				$my_contact_club_relation_bll->insert($contact_club_relation_array);
			}
		} // end if  capi_club_number >""



		return $cid;
	} // end api_createindividual


	// function api_getindividual
	//
	// Parameters $req - an array of parameters
	//
	//  array elements used: (only 1)
	//
	//    cid
	//		external_key_1
	//		external_key_2
	//		external_key_3
	//    capi_external_key_field
	//		capi_external_key
	//		wiwid - optional

	public function api_getindividual($req)
	{
		//error_log("api_getindividual");

		$cid = 0;
		$external_key_1 = "";
		$external_key_2 = "";
		$external_key_3 = "";
		$capi_external_key_field = "";
		$capi_external_key = "";
		$wiwid = 0;


		foreach ($req as $key => $value) ${$key} = $value;


		$output = "";

		$my_contact_bll = new Contact_bll_class;
		$my_association_contact_bll = new association_contact_bll_class;
		$my_association_bll = new association_bll_class;
		$my_contact_individual_bll = new Contact_individual_bll_class;
		$my_contact_club_relation_bll = new Contact_club_relation_bll_class;
		$my_contact_club_bll = new Contact_club_bll_class;
		$my_contact_individual_bll = new Contact_individual_bll_class;
		$my_contact_club_bll = new Contact_club_bll_class;
		$my_phone_type_bll = new Phone_type_bll_class;
		$my_contact_phone_bll = new Contact_phone_bll_class;

		$my_email_type_bll = new Email_type_bll_class;
		$my_email_contact_bll = new Email_contact_bll_class;

		$my_children_contact_bll = new children_contact_bll_class;

		$my_text_type_bll = new Text_type_bll_class;
		$my_text_contact_bll = new Text_contact_bll_class;

		$my_url_type_bll = new Url_type_bll_class;
		$my_contact_url_bll = new Contact_url_bll_class;

		$my_handicap_contact_bll = new Handicap_contact_bll_class;
		$my_image_type_bll = new Image_type_bll_class;
		$my_image_contact_bll = new Image_contact_bll_class;
		$my_address_bll = new Address_bll_class;
		$my_function_contact_bll = new Function_contact_bll_class;
		$my_subscription_contact_bll = new subscription_contact_bll_class;


		// get function array

		$function_bll = new Function_bll_class;
		$function_array = $function_bll->get_id_name_array();
		$function_array = array(0 => "None") + $function_array;

		// get address_type array

		$address_type_bll = new Address_type_bll_class;
		$address_type_array = $address_type_bll->get_id_name_array();
		$address_type_array = array(0 => "None") + $address_type_array;

		// get phone_type array

		$phone_type_bll = new Phone_type_bll_class;
		$phone_type_array = $phone_type_bll->get_id_name_array();
		$phone_type_array = array(0 => "None") + $phone_type_array;

		// get email_type array

		$email_type_bll = new Email_type_bll_class;
		$email_type_array = $email_type_bll->get_id_name_array();
		$email_type_array = array(0 => "None") + $email_type_array;

		// get text_type array

		$text_type_bll = new Text_type_bll_class;
		$text_type_array = $text_type_bll->get_id_name_array();
		$text_type_array = array(0 => "None") + $text_type_array;

		// get url_type array

		$url_type_bll = new Url_type_bll_class;
		$url_type_array = $url_type_bll->get_id_name_array();
		$url_type_array = array(0 => "None") + $url_type_array;

		// get image_type array

		$image_type_bll = new Image_type_bll_class;
		$image_type_array = $image_type_bll->get_id_name_array();
		$image_type_array = array(0 => "None") + $image_type_array;

		// get region array

		$region_bll = new Region_bll_class;
		$region_array = $region_bll->get_id_name_array();
		$region_array = array(0 => "None") + $region_array;

		$sql = "select c.*, ci.* from contact as c ";
		$sql .= "inner join contact_individual as ci on ci.cid=c.cid ";

		$sql .= "where ";

		// don't do active only
		//		$sql.= "contact_status='A' and ";

		if ($cid > 0) {
			$sql .= "c.cid=$cid";
		} else if ($external_key_1 > "") {
			$sql .= "external_key_1='$external_key_1'";
		} else if ($external_key_2 > "") {
			$sql .= "external_key_2='$external_key_2'";
		} else if ($external_key_3 > "") {
			$sql .= "external_key_3='$external_key_3'";
		} else if ($capi_external_key_field > "") {
			$sql .= $capi_external_key_field . "='" . $capi_external_key . "'";
		}
		$sql .= " order by c.cid";

		$my_contact_individual_bll->sql($sql);

		//		error_log($sql);


		if ($my_contact_individual_bll->fetchObject()) {


			$output .= xml_wrap("success", "true");
			$output .= xml_wrap("message", "");

			$output .= xml_wrap("prefix_name", $my_contact_individual_bll->get_field("prefix_name"));
			$output .= xml_wrap("first_name", $my_contact_individual_bll->get_field("first_name"));
			$output .= xml_wrap("middle_name", $my_contact_individual_bll->get_field("middle_name"));
			$output .= xml_wrap("last_name", $my_contact_individual_bll->get_field("last_name"));
			$output .= xml_wrap("suffix_name", $my_contact_individual_bll->get_field("suffix_name"));
			$output .= xml_wrap("preferred_last_name", $my_contact_individual_bll->get_field("preferred_last_name"));
			$output .= xml_wrap("preferred_first_name", $my_contact_individual_bll->get_field("preferred_first_name"));
			$output .= xml_wrap("birthplace_country", $my_contact_individual_bll->get_field("birthplace_country"));
			$output .= xml_wrap("handedness", $my_contact_individual_bll->get_field("handedness"));
			$output .= xml_wrap("title", $my_contact_individual_bll->get_field("title"));
			$output .= xml_wrap("company", $my_contact_individual_bll->get_field("company"));
			$output .= xml_wrap("passport_number", $my_contact_individual_bll->get_field("passport_number"));
			$output .= xml_wrap("cid", $my_contact_individual_bll->get_field("cid"));
			$output .= xml_wrap("contact_status", $my_contact_individual_bll->get_field("contact_status"));
			$output .= xml_wrap("gender", $my_contact_individual_bll->get_field("gender"));
			$output .= xml_wrap("reinstated_amateur", $my_contact_individual_bll->get_field("reinstated_amateur"));
			$output .= xml_wrap("reinstated_year", $my_contact_individual_bll->get_field("reinstated_year"));
			$output .= xml_wrap("birth_date", $my_contact_individual_bll->db_to_date($my_contact_individual_bll->get_field("birth_date")));
			$output .= xml_wrap("external_key_1", $my_contact_bll->get_field("external_key_1"));
			$output .= xml_wrap("external_key_2", $my_contact_bll->get_field("external_key_2"));
			$output .= xml_wrap("external_key_3", $my_contact_bll->get_field("external_key_3"));

			$club_member_number = "";
			$ccr_status = "";
			$member_type = "";
			if ($wiwid > 0) {
				// get associated club information
				$sql = "select ccr.club_member_number,ccr_status,member_type from wi_user as wu
				inner join wi_website as ww on ww.wiwid=wu.wiwid
				inner join contact_club_relation as ccr on ccr.cid=wu.cid and ww.wi_club_cid=ccr.club_cid
				where wu.cid=$cid";
				$my_contact_club_relation_bll->sql($sql);
				if ($my_contact_club_relation_bll->fetchObject()) {
					$club_member_number = $my_contact_club_relation_bll->get_field("club_member_number");
					$ccr_status = $my_contact_club_relation_bll->get_field("ccr_status");
					$member_type = $my_contact_club_relation_bll->get_field("member_type");
				}
			}

			$output .= xml_wrap("club_member_number", $my_contact_bll->get_field("club_member_number"));
			$output .= xml_wrap("ccr_status", $my_contact_bll->get_field("ccr_status"));
			$output .= xml_wrap("member_type", $my_contact_bll->get_field("member_type"));

			// get addresses

			$my_address_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid")));
			$my_address_bll->get_rows();
			$addresses = "";
			while ($my_address_bll->fetchObject()) {
				$address = "";
				$address .= xml_wrap("aid", $my_address_bll->get_field("aid"));
				$address .= xml_wrap("street1", $my_address_bll->get_field("street1"));
				$address .= xml_wrap("street2", $my_address_bll->get_field("street2"));
				$address .= xml_wrap("street3", $my_address_bll->get_field("street3"));
				$address .= xml_wrap("city", $my_address_bll->get_field("city"));
				$address .= xml_wrap("state", $my_address_bll->get_field("state"));
				$address .= xml_wrap("zip", $my_address_bll->get_field("zip"));
				$address .= xml_wrap("country", $my_address_bll->get_field("country"));
				$address .= xml_wrap("valid_from", $my_address_bll->get_field("valid_from"));
				$address .= xml_wrap("valid_to", $my_address_bll->get_field("valid_to"));
				$address .= xml_wrap("address_note", $my_address_bll->get_field("address_note"));
				$address .= xml_wrap("address_status", $my_address_bll->get_field("address_status"));
				$address .= xml_wrap("current_seasonal", $my_address_bll->get_field("current_seasonal"));
				$address .= xml_wrap("primary_address", $my_address_bll->get_field("primary_address"));
				$address .= xml_wrap("legal_address", $my_address_bll->get_field("legal_address"));
				$address .= xml_wrap("atid", $my_address_bll->get_field("atid"));
				$address .= xml_wrap("address_type", $address_type_array[$my_address_bll->get_field("atid")]);

				$addresses .= xml_wrap("address", $address);
			}
			$output .= xml_wrap("addresses", $addresses);

			// get phones

			$my_contact_phone_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid")));
			$my_contact_phone_bll->get_rows();
			$phones = "";
			while ($my_contact_phone_bll->fetchObject()) {
				$phone = "";
				$phone .= xml_wrap("cpid", $my_contact_phone_bll->get_field("cpid"));
				$phone .= xml_wrap("number", $my_contact_phone_bll->get_field("number"));
				$phone .= xml_wrap("extension", $my_contact_phone_bll->get_field("extension"));
				$phone .= xml_wrap("carrier", $my_contact_phone_bll->get_field("carrier"));
				$phone .= xml_wrap("ptyid", $my_contact_phone_bll->get_field("ptyid"));
				$phone .= xml_wrap("phone_type", $phone_type_array[$my_contact_phone_bll->get_field("ptyid")]);
				$phones .= xml_wrap("phone", $phone);
			}
			$output .= xml_wrap("phones", $phones);
			//error_log("CID: $cid phones: $phones");				
			// get emails

			$my_email_contact_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid")));
			$my_email_contact_bll->get_rows();
			$emails = "";
			while ($my_email_contact_bll->fetchObject()) {
				$email = "";
				$email .= xml_wrap("ecid", $my_email_contact_bll->get_field("ecid"));
				$email .= xml_wrap("email", $my_email_contact_bll->get_field("email"));
				$email .= xml_wrap("cc_email", $my_email_contact_bll->get_field("cc_email"));
				$email .= xml_wrap("etid", $my_email_contact_bll->get_field("etid"));
				$email .= xml_wrap("primary_email", $my_email_contact_bll->get_field("primary_email"));
				$email .= xml_wrap("email_type", $email_type_array[$my_email_contact_bll->get_field("etid")]);

				$emails .= xml_wrap("email", $email);
			}
			$output .= xml_wrap("email", $emails);
			$output .= xml_wrap("emails", $emails);

			// get children

			$my_children_contact_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid")));
			$my_children_contact_bll->get_rows();
			$children = "";
			$c = 0;
			while ($my_children_contact_bll->fetchObject()) {
				$child = "";
				$c++;
				$child .= xml_wrap("c", $c);
				$child .= xml_wrap("chcid", $my_children_contact_bll->get_field("chcid"));
				$child .= xml_wrap("child_first_name", $my_children_contact_bll->get_field("child_first_name"));
				$child .= xml_wrap("child_birth_date", $my_children_contact_bll->get_field("child_birth_date"));

				$children .= xml_wrap("child", $child);
			}
			$output .= xml_wrap("children", $children);

			// get functions

			$my_function_contact_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid")));
			$my_function_contact_bll->get_rows();
			$functions = "";
			while ($my_function_contact_bll->fetchObject()) {
				$function = "";
				$my_contact_club_bll->construct_select(array("cid" => $my_function_contact_bll->get_field("oid")));
				$my_contact_club_bll->get_rows();
				$my_contact_club_bll->fetchObject();
				$function .= xml_wrap("function_name", $function_array[$my_function_contact_bll->get_field("fid")]);
				$function .= xml_wrap("club_name", $my_contact_club_bll->get_field("club_name"));

				$functions .= xml_wrap("function", $function);
			}
			$output .= xml_wrap("functions", $functions);

			// get subscriptions

			$my_subscription_contact_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid")));
			$my_subscription_contact_bll->get_rows();
			$subscriptions = "";
			while ($my_subscription_contact_bll->fetchObject()) {
				$subscription = "";
				$subscription .= xml_wrap("sid", urlencode($my_subscription_contact_bll->get_field("sid")));
				$subscription .= xml_wrap("subscription_type", urlencode($my_subscription_contact_bll->get_field("subscription_type")));
				$subscriptions .= xml_wrap("subscription", $subscription);
			}
			$output .= xml_wrap("subscriptions", $subscriptions);
			//error_log("subs: $subscriptions cid:".$my_contact_individual_bll->get_field("cid"));				
			// get texts

			$my_text_contact_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid")));
			$my_text_contact_bll->get_rows();
			$texts = "";
			while ($my_text_contact_bll->fetchObject()) {
				$text = "";
				$text .= xml_wrap("text_value", urlencode($my_text_contact_bll->get_field("text_value")));
				$text .= xml_wrap("text_type", $text_type_array[$my_text_contact_bll->get_field("ttid")]);

				$texts .= xml_wrap("text", $text);
			}
			$output .= xml_wrap("texts", $texts);

			// get urls

			$my_contact_url_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid")));
			$my_contact_url_bll->get_rows();
			$urls = "";
			while ($my_contact_url_bll->fetchObject()) {
				$url = "";
				$url .= xml_wrap("url", $my_contact_url_bll->get_field("url"));
				$url .= xml_wrap("url_type", $url_type_array[$my_contact_url_bll->get_field("urltid")]);

				$urls .= xml_wrap("url", $url);
			}
			$output .= xml_wrap("urls", $urls);

			// get handicaps

			$my_handicap_contact_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid")));
			$my_handicap_contact_bll->get_rows();
			$handicaps = "";
			while ($my_handicap_contact_bll->fetchObject()) {
				$handicap = "";


				$handicap .= xml_wrap("handicap_number", $my_handicap_contact_bll->get_field("handicap_number"));
				//				$handicap.=xml_wrap("handicap_index",$my_handicap_contact_bll->get_field("handicap_index"));
				// get index from ghin
				$my_ghin_bll = new ghin_bll_class;
				$golfer =  $my_ghin_bll->golfers_search($my_handicap_contact_bll->get_field("handicap_number"));

				//				$ghin_info.="\nhi_display: ".$golfer['golfers'][0]['hi_display']."\n";

				if (isset($golfer['golfers'][0]['hi_display']))
					$handicap .= xml_wrap("handicap_index", $golfer['golfers'][0]['hi_display']);
				else {
					$handicap .= xml_wrap("handicap_index", $my_handicap_contact_bll->get_field("handicap_index"));
				}

				$handicap .= xml_wrap("hc_status", $my_handicap_contact_bll->get_field("hc_status"));
				$handicaps .= xml_wrap("handicap", $handicap);

				/*
				$handicap.=xml_wrap("handicap_number",$my_handicap_contact_bll->get_field("handicap_number"));
				$handicap.=xml_wrap("handicap_index",$my_handicap_contact_bll->get_field("handicap_index"));
				$handicap.=xml_wrap("hc_status",$my_handicap_contact_bll->get_field("hc_status"));
				$handicaps.=xml_wrap("handicap",$handicap);
*/
			}
			$output .= xml_wrap("handicaps", $handicaps);

			// get associations

			$my_association_contact_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid"), 'asc_status' => 'A'));
			$my_association_contact_bll->get_rows();
			$associations = "";
			while ($my_association_contact_bll->fetchObject()) {
				$association = "";
				$my_association_bll->construct_select(array('asid' => $my_association_contact_bll->get_field("asid")));
				$my_association_bll->get_rows();
				$my_association_bll->fetchObject();
				$association .= xml_wrap("association_number", $my_association_bll->get_field("association_number"));
				$association .= xml_wrap("association_name", $my_association_bll->get_field("association_name"));
				$associations .= xml_wrap("association", $association);
			}
			$output .= xml_wrap("associations", $associations);

			// get images

			$my_image_contact_bll->construct_select(array("cid" => $my_contact_individual_bll->get_field("cid")));
			$my_image_contact_bll->get_rows();
			$images = "";
			while ($my_image_contact_bll->fetchObject()) {
				$image = "";
				$image .= xml_wrap("image_url", $my_image_contact_bll->get_field("image_url"));
				$image .= xml_wrap("local_storage", $my_image_contact_bll->get_field("local_storage"));
				$image .= xml_wrap("image_type", $image_type_array[$my_image_contact_bll->get_field("itid")]);

				$images .= xml_wrap("image", $image);
			}
			$output .= xml_wrap("images", $images);

			// get clubs

			$sql = "select ccr.ccrid,ccr.ccr_status,ccr.primary_club,ccr.club_cid, cc.*, c.* from contact_club_relation as ccr ";
			$sql .= "inner join contact_club as cc on cc.cid=ccr.club_cid ";
			$sql .= "inner join contact as c on ccr.cid=c.cid ";
			$sql .= "where c.cid=" . $my_contact_individual_bll->get_field("cid") . " ";
			$sql .= "and ccr.ccr_status='A' and not dac ";
			$sql .= "order by primary_club desc, club_name";

			$my_contact_club_bll->sql($sql);

			// error_log($sql);
			$clubs = "";
			while ($my_contact_club_bll->fetchObject()) {
				$club = "";
				$club .= xml_wrap("ccrid", $my_contact_club_bll->get_field("ccrid"));
				$club .= xml_wrap("club_cid", $my_contact_club_bll->get_field("club_cid"));
				$club .= xml_wrap("club_name", $my_contact_club_bll->get_field("club_name"));
				$club .= xml_wrap("primary_club", $my_contact_club_bll->get_field("primary_club"));
				$club .= xml_wrap("cltid", $my_contact_club_bll->get_field("cltid"));
				$club .= xml_wrap("handicap_club_type", $my_contact_club_bll->get_field("handicap_club_type"));
				$club .= xml_wrap("ccr_status", $my_contact_club_bll->get_field("ccr_status"));
				//				$club.=xml_wrap("dac",$my_contact_club_bll->get_field("dac"));

				$clubs .= xml_wrap("club", $club);
			}
			$output .= xml_wrap("clubs", $clubs);
		} else {
			// else no contact number found

			$output = "<success>false</success>";
			$output .= "<message>$sql Contact cid $cid, external_key_1: $external_key_1, external_key_2: $external_key_2, external_key_3: $external_key_3, capi_external_key_field: $capi_external_key_field, capi_external_key: $capi_external_key not found</message>";
		}
		// error_log($output);		
		return $output;
	}	// end api_getindividual

	// function api_searchindividual
	//
	// Parameters $req - an array of parameters
	//
	//  array elements used: 
	//
	//    first_name
	//		middle_name
	//		last_name
	//		suffix_name
	//		street
	//		handicap_number
	//		email
	//		vip_number
	//		active_only = 0 - no, 1 - yes,  default 1 - can be overridden by variable table entry of searchindividual_active_only_default
	// 		limit - limit number of records, default=100
	//		search_ghin


	public function api_searchindividual($req)
	{


		$first_name = "";
		$middle_name = "";
		$last_name = "";
		$street = "";
		$suffix_name = "";
		$handicap_number = "";
		$birth_date = "";
		$email = "";
		$limit = 100;


		$active_only = get_system_variable("searchindividual_active_only_default", 1);
		$vip_card_number = "";


		foreach ($req as $key => $value) ${$key} = $value;
		/*
		if ($last_name=="" && $handicap_number=="") {
			// error, need at least last name or handicap number
			$output.=xml_wrap("success","false");
			$output.=xml_wrap("message","No last name nor handicap number specified.");
			return $output;
			
		}
*/
		$output = "";

		$my_contact_bll = new Contact_bll_class;
		$my_contact_individual_bll = new Contact_individual_bll_class;
		$my_contact_club_relation_bll = new Contact_club_relation_bll_class;



		$sql = "select c.*, ci.*";
		//		if ($handicap_number>"")
		$sql .= ",hc.handicap_number";
		if ($vip_card_number > "")
			$sql .= ",vc.vip_card_number";
		if ($street > "")
			$sql .= ",a.street1,a.street2,a.street3";
		$sql .= " from contact as c ";
		$sql .= "inner join contact_individual as ci on ci.cid=c.cid ";

		if ($handicap_number > "")
			$sql .= "inner join handicap_contact as hc on hc.cid=c.cid ";
		else
			$sql .= "left join handicap_contact as hc on hc.cid=c.cid ";

		if ($vip_card_number > "")
			$sql .= "inner join vip_card as vc on vc.cid=c.cid ";

		if ($email > "")
			$sql .= "inner join email_contact as ec on ec.cid=c.cid ";

		if ($street > "")
			$sql .= "inner join address as a on a.cid=c.cid ";


		$where = "";

		if ($active_only)
			$where .= "contact_status='A'";


		if ($email > "") {

			if ($where > "") $where .= " and ";
			$where .= "(email";
			if (strpos($email, "%") > 0)
				$where .= " like '" . $email . "'";
			else
				$where .= "='" . $email . "'";

			$where .= ")";
		}

		if ($handicap_number > "") {

			if ($where > "") $where .= " and ";
			$where .= "(handicap_number";
			if (strpos($handicap_number, "%") > 0)
				$where .= " like '" . $handicap_number . "'";
			else
				$where .= "='" . $handicap_number . "'";

			$where .= ")";
		}

		if ($vip_card_number > "") {

			if ($where > "") $where .= " and ";
			$where .= "(vip_card_number";
			if (strpos($vip_card_number, "%") > 0)
				$where .= " like '" . $vip_card_number . "'";
			else
				$where .= "='" . $vip_card_number . "'";

			$where .= ")";
		}

		if ($street > "") {
			if ($where > "") $where .= " and ";
			$where .= "(";
			$where .= "street1 like '%" . $street . "%' or ";
			$where .= "street2 like '%" . $street . "%' or ";
			$where .= "street3 like '%" . $street . "%'";

			$where .= ")";
		}

		if ($last_name > "") {
			if ($where > "") $where .= " and ";
			$where .= "(last_name";
			if (strpos($last_name, "%") > 0)
				$where .= " like \"" . $last_name . "\"";
			else
				$where .= "=\"" . $last_name . "\"";

			$where .= ")";
		}

		if ($first_name > "") {

			if ($where > "") $where .= " and ";
			$where .= "(first_name";
			if (strpos($first_name, "%") > 0)
				$where .= " like '" . $first_name . "'";
			else
				$where .= "='" . $first_name . "'";

			$where .= ")";
		}

		if ($middle_name > "") {

			if ($where > "") $where .= " and ";
			$where .= "(middle_name";
			if (strpos($middle_name, "%") > 0)
				$where .= " like '" . $middle_name . "'";
			else
				$where .= "='" . $middle_name . "'";

			$where .= ")";
		}

		if ($suffix_name > "") {

			if ($where > "") $where .= " and ";
			$where .= "(suffix_name";
			if (strpos($suffix_name, "%") > 0)
				$where .= " like '" . $suffix_name . "'";
			else
				$where .= "='" . $suffix_name . "'";

			$where .= ")";
		}

		if ($birth_date > "") {

			if ($where > "") $where .= " and ";
			$where .= "(birth_date='" . $birth_date . "'";

			$where .= ")";
		}

		if ($where > "")
			$sql .= " where " . $where;

		$sql .= " order by last_name,first_name ";

		$sql .= " limit $limit";
		//error_log("contact search sql:$sql");		
		$my_contact_individual_bll->sql($sql);

		//		error_log($sql);

		$count = 0;

		if ($my_contact_individual_bll->row_count() > 0) {

			while ($my_contact_individual_bll->fetchObject()) {
				$cid = $my_contact_individual_bll->get_field("cid");
				$my_contact_club_relation_bll->sql("
					select * from contact_club_relation as ccr
					inner join contact_club as cc on cc.cid=ccr.club_cid
					where ccr_status='A' and not dac and ccr.cid=$cid;
				");
				error_log("
									select * from contact_club_relation as ccr
									inner join contact_club as cc on cc.cid=ccr.club_cid
									where ccr_status='A' and not dac and cid=$cid;
								");
				$active_count = $my_contact_club_relation_bll->row_count();
				if ($active_count > 0 || !$active_only) {
					$count++;
					$individual = "";

					//				if ($handicap_number>"")
					$individual .= xml_wrap("handicap_number", $my_contact_individual_bll->get_field("handicap_number"));

					if ($vip_card_number > "")
						$individual .= xml_wrap("vip_card_number", $my_contact_individual_bll->get_field("vip_card_number"));

					$individual .= xml_wrap("prefix_name", $my_contact_individual_bll->get_field("prefix_name"));
					$individual .= xml_wrap("first_name", $my_contact_individual_bll->get_field("first_name"));
					$individual .= xml_wrap("middle_name", $my_contact_individual_bll->get_field("middle_name"));
					$individual .= xml_wrap("last_name", $my_contact_individual_bll->get_field("last_name"));
					$individual .= xml_wrap("suffix_name", $my_contact_individual_bll->get_field("suffix_name"));
					$individual .= xml_wrap("cid", $my_contact_individual_bll->get_field("cid"));
					$individual .= xml_wrap("contact_status", $my_contact_individual_bll->get_field("contact_status"));
					$individual .= xml_wrap("birth_date", $my_contact_individual_bll->get_field("birth_date"));
					$individual .= xml_wrap("street1", $my_contact_individual_bll->get_field("street1"));
					$individual .= xml_wrap("street2", $my_contact_individual_bll->get_field("street2"));
					$individual .= xml_wrap("street3", $my_contact_individual_bll->get_field("street3"));
					$individual .= xml_wrap("external_key_1", $my_contact_bll->get_field("external_key_1"));
					$individual .= xml_wrap("external_key_2", $my_contact_bll->get_field("external_key_2"));
					$individual .= xml_wrap("external_key_3", $my_contact_bll->get_field("external_key_3"));
					$output .= xml_wrap("individual", $individual);
				} // end if

			}  // end while 
		}
		if ($count > 0) {
			$output = xml_wrap("success", "true") . $output;
			$output = xml_wrap("message", "") . $output;
		} else {
			// else no contact number found

			$output = "<success>false</success>";
			$output .= "<message>Contact first_name $first_name, middle_name: $middle_name, last_name: $last_name, suffix_name: $suffix_name, active_only: $active_only, limit: $limit not found sql:$sql</message>";
		}
		// error_log($output);		
		return $output;
	}	// end api_searchindividual

	// function api_addupdateindividual
	//
	// Parameters $req - an array of parameters
	//
	//  array elements used:
	//
	//		cid = 0 (default) for new or cid for update
	//
	//		Also uses remaining elements from contact_individual
	//
	//		Adds can pass in a club number, email address and phone
	//
	// 		Also can update contact_status in contact record

	public function api_addupdateindividual($req)
	{
		$cid = 0;
		$first_name = "";
		$last_name = "";
		$email = "";
		$phone = "";
		$contact_status = "";
		$success = "true";
		$message = "";
		$ptyid = 1;
		$output = "";


		foreach ($req as $key => $value) ${$key} = $value;

		if ($first_name == "" || $last_name == "") {
			$success = "false";
			$output .= xml_wrap("success", $success);
			$output .= xml_wrap("message", "Missing data: first_name: $first_name last_name: $last_name cid: $cid");
			$output .= xml_wrap("cid", $cid);

			return $output;
		}

		$last_name = stripslashes(htmlspecialchars(urldecode($last_name)));
		$first_name = stripslashes(htmlspecialchars(urldecode($first_name)));

		$my_contact_bll = new Contact_bll_class;
		$my_contact_individual_bll = new Contact_individual_bll_class;
		$my_email_contact_bll = new Email_contact_bll_class;
		$my_contact_phone_bll = new Contact_phone_bll_class;
		$my_contact_club_relation_bll = new Contact_club_relation_bll_class;
		$my_contact_club_bll = new Contact_club_bll_class;



		if ($cid == 0) {
			// this is an add
			// can't do adds with this.  this case will never happen
			$cid = api_createindividual($req);
			if ($cid > 0) {
				$success = "true";
				$message = "";
			} else {
				$success = "false";
				$message = "Failure.";
			}

			$output .= xml_wrap("success", $success);
			$output .= xml_wrap("message", $message);
			$output .= xml_wrap("cid", $cid);
			return $output;
		}


		// this is an update

		// loop through all keys and sent whatever was passed in

		foreach ($my_contact_bll->dal->field_definitions as $key => $value) {
			if (isset($req[$key])) {
				$contact_array[$key] = $req[$key];
			}
		} // end foreach

		$where = "cid=$cid";

		if ($my_contact_bll->update($contact_array, $where))
			$success = "true";
		else {
			$success = "false";
			$message .= " update contact failed ";
		}

		// update contact_indivividual

		// loop through all keys and sent whatever was passed in

		foreach ($my_contact_individual_bll->dal->field_definitions as $key => $value) {
			if (isset($req[$key])) {
				$contact_individual_array[$key] = $req[$key];
			}
		} // end foreach


		if ($my_contact_individual_bll->update($contact_individual_array, $where))
			$success = "true";
		else {
			$success = "false";
			$message .= " update contact_individual failed ";
		}

		if ($email > "") {
			// update/create email_contact
			$email_contact_array = array(
				"cid" => $cid,
				"etid" => 1,
				"primary_email" => 1,
			);
			$my_email_contact_bll->construct_select($email_contact_array);
			$my_email_contact_bll->get_rows();
			$email_contact_array["email"] = $email;
			if ($my_email_contact_bll->row_count() == 0) {
				// this is an add	
				$email_contact_array["email"] = $email;
				$my_email_contact_bll->insert($email_contact_array);
			} else { // end rowcount
				// this is an update
				$my_email_contact_bll->fetchObject();
				$my_email_contact_bll->update($email_contact_array, "ecid=" . $my_email_contact_bll->get_field("ecid"));
			}
		}

		if ($phone > "") {
			// update/create email_contact
			$contact_phone_array = array(
				"cid" => $cid,
				"ptyid" => $ptyid,
			);
			$my_contact_phone_bll->construct_select($contact_phone_array);
			$my_contact_phone_bll->get_rows();
			$contact_phone_array["number"] = $phone;
			//error_log("phone count: ".$my_contact_phone_bll->row_count());				
			if ($my_contact_phone_bll->row_count() == 0) {
				// this is an add
				$my_contact_phone_bll->insert($contact_phone_array);
			} else { // end rowcount
				// this is an update
				$my_contact_phone_bll->fetchObject();
				//error_log("cpid: ".$my_contact_phone_bll->get_field("cpid"));				
				$my_contact_phone_bll->update($contact_phone_array, "cpid=" . $my_contact_phone_bll->get_field("cpid"));
			}
		}



		if ($contact_status > "") {
			// update/create contact status
			$contact_array = array(
				"contact_status" => $contact_status,
			);
			$my_contact_bll->update($contact_array, "cid=" . $cid);
		}


		$output .= xml_wrap("success", $success);
		$output .= xml_wrap("message", $message);
		$output .= xml_wrap("cid", $cid);
		return $output;
	} // end api_addupdateindividual



	// function api_getcommittee
	//
	// Parameters $req - an array of parameters
	//
	//  array elements used: 
	//
	//    committee_name


	public function api_getcommittee($req)
	{


		$committee_name = "";

		foreach ($req as $key => $value) ${$key} = $value;

		$output = "";

		if ($committee_name == "") {
			$output .= xml_wrap("success", "false");
			$output .= xml_wrap("message", "No committee specified");
			return $output;
		}

		$active_only = 1;
		$limit = 100;

		$my_contact_bll = new Contact_bll_class;
		$my_contact_individual_bll = new Contact_individual_bll_class;
		$my_image_contact_bll = new image_contact_bll_class;
		$my_email_contact_bll = new email_contact_bll_class;
		$my_contact_phone_bll = new contact_phone_bll_class;
		$my_text_contact_bll = new text_contact_bll_class;



		$sql = "select c.*, ci.*, cc.*,committee.*";

		$sql .= " from contact as c ";
		$sql .= "inner join contact_individual as ci on ci.cid=c.cid ";
		$sql .= "inner join committee_contact as cc on cc.cid=c.cid ";
		$sql .= "inner join committee on cc.cmid=committee.cmid ";


		$sql .= "where ";

		$sql .= "committee_name='$committee_name'";


		$sql .= " limit $limit";
		//error_log("contact search sql:$sql");		
		$my_contact_individual_bll->sql($sql);

		//		error_log($sql);


		if ($my_contact_individual_bll->row_count() > 0) {

			$output .= xml_wrap("success", "true");
			$output .= xml_wrap("message", "");

			while ($my_contact_individual_bll->fetchObject()) {
				$individual = "";


				$individual .= xml_wrap("prefix_name", $my_contact_individual_bll->get_field("prefix_name"));
				$individual .= xml_wrap("first_name", $my_contact_individual_bll->get_field("first_name"));
				$individual .= xml_wrap("middle_name", $my_contact_individual_bll->get_field("middle_name"));
				$individual .= xml_wrap("last_name", $my_contact_individual_bll->get_field("last_name"));
				$individual .= xml_wrap("suffix_name", $my_contact_individual_bll->get_field("suffix_name"));
				$individual .= xml_wrap("cid", $my_contact_individual_bll->get_field("cid"));
				$individual .= xml_wrap("contact_status", $my_contact_individual_bll->get_field("contact_status"));
				$individual .= xml_wrap("external_key_1", $my_contact_bll->get_field("external_key_1"));
				$individual .= xml_wrap("external_key_2", $my_contact_bll->get_field("external_key_2"));
				$individual .= xml_wrap("external_key_3", $my_contact_bll->get_field("external_key_3"));

				// get business phone
				$sql = "select * from contact_phone as pc  ";
				$sql .= "inner join phone_type as pt on pc.ptyid=pt.ptyid where phone_type_name='business' and cid=" . $my_contact_individual_bll->get_field("cid");
				$my_contact_phone_bll->sql($sql);
				$my_contact_phone_bll->fetchObject();

				$individual .= xml_wrap("number", $my_contact_phone_bll->get_field("number"));
				$individual .= xml_wrap("extension", $my_contact_phone_bll->get_field("extension"));
				$individual .= $sql;

				// get head shot
				$sql = "select * from image_contact as ic  ";
				$sql .= "inner join image_type on ic.itid=image_type.itid where image_type_name='headshot' and cid=" . $my_contact_individual_bll->get_field("cid");
				$my_image_contact_bll->sql($sql);
				$my_image_contact_bll->fetchObject();

				$individual .= xml_wrap("headshot", $my_image_contact_bll->get_field("image_url"));

				// get bio
				$sql = "select * from text_contact as tc  ";
				$sql .= "inner join text_type as tt on tc.ttid=tt.ttid where text_type_name='bio' and cid=" . $my_contact_individual_bll->get_field("cid");
				$my_text_contact_bll->sql($sql);
				$my_text_contact_bll->fetchObject();

				$individual .= xml_wrap("bio", $my_text_contact_bll->get_field("text_value"));

				// get email
				$sql = "select * from email_contact where primary_email ";
				$sql .= " and cid=" . $my_contact_individual_bll->get_field("cid");
				$my_email_contact_bll->sql($sql);
				$my_email_contact_bll->fetchObject();

				$individual .= xml_wrap("email", $my_email_contact_bll->get_field("email"));

				// get committee fields

				$committee_field_values = unserialize($my_contact_individual_bll->get_field("committee_field_values"));


				$cfv = "";
				foreach ($committee_field_values as $key => $value) {
					$cfv .= xml_wrap($key, $value);
				}
				$individual .= xml_wrap("committee_field_values", $cfv);

				$output .= xml_wrap("individual", $individual);
			}  // end while 
		} else {
			// else no contact number found

			$output = "<success>false</success>";
			$output .= "<message>Contact first_name $first_name, middle_name: $middle_name, last_name: $last_name, suffix_name: $suffix_name, active_only: $active_only, limit: $limit not found sql:$sql</message>";
		}
		// error_log($output);		

		return $output;
	}	// end api_getcommittee

	public function api_findgolfer_ghin($req)
	{

		$handicap_number = "";

		foreach ($req as $key => $value) ${$key} = $value;

		$my_ghin_bll = new ghin_bll_class;

		//error_log("findgolfer: $handicap_number");

		// legacy						$golfer =  $my_ghin_bll->find_golfer($handicap_number);
		$golfer2020 =  $my_ghin_bll->golfers_search($handicap_number);
		if (isset($golfer2020["golfers"])) $golfer2020 = $golfer2020["golfers"];
		else $golfer2020 = array();

		if (sizeof($golfer2020) > 0) {
			// golfer is found, extract information

			$i = 0;
			/* legacy			
							if (!isset($golfer[$i]["ghinnumber"])) $golfer[$i]["ghinnumber"]="";
							if (!isset($golfer[$i]["prefix"])) $golfer[$i]["prefix"]="";
							if (!isset($golfer[$i]["firstname"])) $golfer[$i]["firstname"]="";
							if (!isset($golfer[$i]["middlename"])) $golfer[$i]["middlename"]="";
							if (!isset($golfer[$i]["lastname"])) $golfer[$i]["lastname"]="";
							if (!isset($golfer[$i]["suffix"])) $golfer[$i]["suffix"]="";
							if (!isset($golfer[$i]["gender"])) $golfer[$i]["gender"]="";
							if (!isset($golfer[$i]["dateofbirth"])) $golfer[$i]["dateofbirth"]="";
							if (!isset($golfer[$i]["email"])) $golfer[$i]["email"]="";
							if (!isset($golfer[$i]["address1"])) $golfer[$i]["address1"]="";
							if (!isset($golfer[$i]["address2"])) $golfer[$i]["address2"]="";
							if (!isset($golfer[$i]["city"])) $golfer[$i]["city"]="";
							if (!isset($golfer[$i]["state"])) $golfer[$i]["state"]="";
							if (!isset($golfer[$i]["zip"])) $golfer[$i]["zip"]="";
*/
			$my_ghin_bll->fix_golfer_array_2020($golfers2020[$i]);
			if ($golfers2020[$i]["status"] == 'Active') $hc_status = "A";
			else $hc_status = "I";


			$output = "";
			$output .= xml_wrap("success", "true");
			$output .= xml_wrap("message", "");
			$output .= xml_wrap("ghinnumber", $golfers2020[$i]["id"]);
			$output .= xml_wrap("prefix", $golfers2020[$i]["prefix"]);
			$output .= xml_wrap("firstname", $golfers2020[$i]["first_name"]);
			$output .= xml_wrap("middlename", $golfers2020[$i]["middle_name"]);
			$output .= xml_wrap("lastname", $golfers2020[$i]["last_name"]);
			$output .= xml_wrap("suffix", $golfers2020[$i]["suffix"]);
			$output .= xml_wrap("gender", $golfers2020[$i]["gender"]);
			$output .= xml_wrap("dateofbirth", $golfers2020[$i]["date_of_birth"]);
			$output .= xml_wrap("email", $golfers2020[$i]["email"]);
			$output .= xml_wrap("address1", $golfers2020[$i]["primary_address"]["street_1"]);
			$output .= xml_wrap("address2", $golfers2020[$i]["primary_address"]["street_2"]);
			$output .= xml_wrap("state", $golfers2020[$i]["primary_address"]["state"]);
			$output .= xml_wrap("zip", $golfers2020[$i]["primary_address"]["zip"]);
		} else {
			$output .= xml_wrap("success", "false");
			$output .= xml_wrap("message", "Not found");
		}

		return $output;
	}
}  // Contact_individual_pl_class

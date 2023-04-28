<?php
/*
Copyright 2023
United States Golf Association
Paul Niebuhr

pl.class.php

This program is the presentation layer for the Command system.

This class should be inherited by the a class to manage the presentation layer for 
a specific table.

*/

require_once get_file("dashboard/dashboard_pl.class.php", "module", "dashboard");

class Pl_class
{


	public $name = null;

	public $toggle_tabs = false;

	public $title = null;

	public $search_field_title = null;

	public $search_field = null;

	public $file_location = null;

	public $module = null;

	public $subforms = null;

	public $url = null;

	public $detail_sort = null;

	public $parent_id = 0;  // select done with no limit set

	public $add_array_label = null;  // If an add requires a selection array to know what is being added.  Placed before the add button
	public $add_array_options = null;  // If an add requires a selection array to know what is being added.  Placed before the add button
	public $add_array_field = null;  // If an add requires a id to know what is being added.  Placed before the add button

	public $id = 0;  // id to be used for adding records

	/*
	generate_page_id_bar - generates the div for the top of the html pages.
	
	$name - ID name for the bar
	$title - Title text for the bar
	$search_field_title - label for the search field displayed
	$search_field - field name to be searched
	$add_flag - allow adds
	$add_array_label - screen label for add array
	$add_array_field - field name for add array
	$add_array_options - options
	$url - url to be added for shortcut)
	*/

	public function generate_page_id_bar($name = null, $title = null, $search_field_title = null, $search_field = null, $add_flag = 1, $add_array_label = null, $add_array_field = null, $add_array_options = null, $url = null)
	{
		$output = "";

		$dashboard_pl = new dashboard_pl_class;

		if ($name == null) $name = $this->name;
		if ($title == null) $title = $this->title;
		if ($search_field_title == null) $search_field_title = $this->search_field_title;
		if ($search_field == null) $search_field = $this->search_field;
		if ($add_array_label == null) $add_array_label = $this->add_array_label;
		if ($add_array_field == null) $add_array_field = $this->add_array_field;
		if ($add_array_options == null) $add_array_options = $this->add_array_options;
		if ($url == null) $url = $this->url;

		$output = '
		<div class="page-id-bar">
			<div class="page-id-bar-inner">
			' . $title;

		if (!is_null($url)) {
			$output .= " " . $dashboard_pl->generate_dashboard_add_link($this->title, $this->module, "", "0", "/" . URI, $url);
			$output .= $dashboard_pl->generate_dashboard_add_script();
		}

		$output .=
			'  |  
			</div> <!-- page-id-bar-inner -->
			<div class="page-id-bar-inner-add">
		';
		if ($add_flag) {
			$form = $name . "_edit_form";
			if (!is_null($add_array_label)) {
				$output .= theme_input($form, $add_array_label, $add_array_field, "select", 0, 0, $add_array_options);
			}
			$output .= "<a id='add_" . $name . "' name='add_" . $name . "'><span  class='icon-plus-circle'> </span></a>";
		}


		$output .= '
			</div> <!-- page-id-bar-inner-add -->
			<div class="page-id-bar-inner-record-nav">
	        <div class="return-search" id="return-search" style="display:none">
            <a href="#" class="return-search-button"><span class="icon-arrow-left"></span>Return to search</a>
    	    </div>
				<script>
		      $(document).ready(function(){
		      
		      $("#return-search").bind("click", return_search);
					});
		      
		       function return_search() {
 						  $("#record-nav").show();
 						  $("#return-search").hide();
 						  $("#all_forms_outer").hide();
						  $("#' . $name . '_search_results_outer").show();
					 }
				</script>
		
			</div> <!-- page-id-bar-inner-record-nav -->
		</div> <!-- page-id-bar -->
		';

		return $output;
	}

	/*
	
	*/

	public function generate_search_div($name = null, $title = null, $search_field_title = null, $search_field = null, $add_flag = 1, $add_array_label = null, $add_array_field = null, $add_array_options = null)
	{
		$output = "";

		if ($name == null) $name = $this->name;
		if ($title == null) $title = $this->title;
		if ($search_field_title == null) $search_field_title = $this->search_field_title;
		if ($search_field == null) $search_field = $this->search_field;

		if (isset($this->add_array_label))
			if ($add_array_label == null) $add_array_label = $this->add_array_label;
		if (isset($this->add_array_field))
			if ($add_array_field == null) $add_array_field = $this->add_array_field;
		if (isset($this->add_array_options))
			if ($add_array_options == null) $add_array_options = $this->add_array_options;

		$output = '	
		<div class="page-search-bar">
			<form name="search_form" id="search_form">
			<div class="page-search-bar-inner">
			<span class="icon-"><i class="fa fa-search" aria-hidden="true"></i></span>
		
	<input type="text" class="search-bar-term" name="' . $search_field . '"/>

	</div> <!-- page-search-bar-inner -->
	<div class="page-search-bar-type">
	</div> <!-- page-search-bar-type -->
	<div class="page-search-bar-go">
	<input type="hidden" name="show_header" value="0">
	<input type="hidden" name="ajax" value="1">
	<button class="search_button" name="search_button">Search</button>
	</div> <!-- page-search-bar-go -->
	</form>
</div> <!-- page-search-bar -->
';

		return $output;
	}

	public function generate_search_results_div($name = null, $title = null)
	{
		$output = "";

		if ($name == null) $name = $this->name;
		if ($title == null) $title = $this->title;

		$output = '
		
        <div id="' . $name . '_search_results_outer" class="wrapper_panel page-content-item large">
            <div id="' . $name . '_search_results_inner">
                <div id="' . $name . '_search_results_data_div">
                </div> <!-- ' . $name . '_search_results_data_div -->
            </div> <!-- ' . $name . '_search_results_inner -->
        </div> <!-- ' . $name . '_search_results_outer -->
		
		';
		return $output;
	}


	public function generate_ajax_button($function = null, $label = null, $data_parent_id = null, $parent_id = null, $name = null)
	{

		$output = "";
		if ($name == null) $name = $this->name;
		if ($parent_id == null) $parent_id = $this->parent_id;

		if ($parent_id != null) {

			$output = '
					<input type="button" id="' . $function . '_' . $name . '" name="' . $function . '_' . $name . '" data-' . $parent_id . '="' . $data_parent_id . '" value="' . $label . '" />
					
		   ';
		} else {
			$output = '
				<input type="button" id="' . $function . '_' . $name . '" name="' . $function . '_' . $name . '" value="' . $label . '" />
				
	   ';
		}

		return $output;
	}

	public function generate_ajax_ready_script($function = null, $name = null)
	{

		if ($name == null) $name = $this->name;

		$output = "
	      $('#" . $function . "_" . $name . "').bind('click', " . $function . "_" . $name . ");
	 ";
		return $output;
	}




	public function generate_save_cancel_button_table()
	{
		$output = "";
		$output .= "<table style='clear:both;width:150px;background:none'><tr>";
		$output .= "<td style='background:none'>" . $this->generate_save_button() . "</td>";
		$output .= "<td style='background:none'>" . $this->generate_cancel_button() . "</td>";
		$output .= "</tr></table>";
		return $output;
	}

	public function generate_save_copy_button($name = null)
	{

		if ($name == null) $name = $this->name;

		$output = '
			<div style="clear:both"></div>
	      <input type="button" id="' . $name . '_save_copy" name="' . $name . '_save_copy" class="cs save_copy_button" value="Save a Copy" />
	   ';

		return $output;
	}

	public function generate_update_div($name = null, $title = null, $sub_name = array(), $sub_title = array(), $display = 'visible')
	{

		$output = "";

		if (is_null($sub_name)) $sub_name = array();
		if (is_null($sub_title)) $sub_title = array();

		if ($name == null) $name = $this->name;
		if ($title == null) $title = $this->title;

		$output = "
	

    <div id='" . $name . "_wrapper' class='wrapper_panel' style='display:" . $display . "'>
      <div id='" . $name . "_search_results_outer'>
        <div id='" . $name . "_form_header' class='subform_header'>
        </div>
        <div id='" . $name . "_search_results_inner'>
          <div id='" . $name . "_search_results_data_div'>
          </div> <!-- " . $name . "_search_results_data_div -->
        </div> <!-- " . $name . "_search_results_inner -->
      </div> <!-- " . $name . "_search_results_outer -->

      <div id='" . $name . "_form_outer' class='wrapper_panel'>
        <div id='" . $name . "_form_inner'>
          <div id='" . $name . "_form_message_div'>
          </div> <!-- " . $name . "_form_message_div -->
          <div id='" . $name . "_form_data_div'>
          </div> <!-- " . $name . "_form_data_div -->
        </div> <!-- " . $name . "_form_inner -->
      </div> <!-- " . $name . "_form_outer -->
		";

		for ($i = 0; $i < sizeof($sub_name); $i++) {
			$output .= "
		      <div id='" . $sub_name[$i] . "_search_results_outer'>
		        <div id='" . $sub_name[$i] . "_form_header' class='subform_header'>
		        $sub_title[$i]
		        </div>
		        <div id='" . $sub_name[$i] . "_search_results_inner'>
		          <div id='" . $sub_name[$i] . "_search_results_data_div'>
		          </div> <!-- " . $sub_name[$i] . "_search_results_data_div -->
		        </div> <!-- " . $sub_name[$i] . "_search_results_inner -->
		      </div> <!-- " . $sub_name[$i] . "_search_results_outer -->
		
		      <div id='" . $sub_name[$i] . "_form_outer' class='wrapper_panel'>
		        <div id='" . $sub_name[$i] . "_form_inner'>
		          <div id='" . $sub_name[$i] . "_form_message_div'>
		          </div> <!-- " . $sub_name[$i] . "_form_message_div -->
		          <div id='" . $sub_name[$i] . "_form_data_div'>
		          </div> <!-- " . $sub_name[$i] . "_form_data_div -->
		        </div> <!-- " . $sub_name[$i] . "_form_inner -->
		      </div> <!-- " . $sub_name[$i] . "_form_outer -->
				";
		}  // end foreach		

		$output .= "
    </div> <!-- " . $name . "_wrapper -->
	
	";

		return $output;
	}




	public function generate_save_button($name = null)
	{

		if ($name == null) $name = $this->name;

		$output = '
			<div style="clear:both"></div>
	      <input type="button" id="' . $name . '_save" name="' . $name . '_save" class="cs save_button" value="Save" />
	   ';

		return $output;
	}



	public function generate_add_button($data_parent_id = null, $parent_id = null, $name = null, $add_array_options = null, $add_array_field = null)
	{

		if ($name == null) $name = $this->name;
		if ($parent_id == null) $parent_id = $this->parent_id;

		if ($add_array_options == null) $add_array_options = $this->add_array_options;
		if ($add_array_field == null) $add_array_field = $this->add_array_field;

		$output = "";

		if ($add_array_field != null) {

			$form = $name . "_edit_form";
			$output .= theme_input($form, $this->title, $add_array_field, "select", 0, 0, $add_array_options);
		}

		if ($parent_id != null) {

			$output .= '
					<input type="button" id="add_' . $name . '" name="add_' . $name . '" data-' . $parent_id . '="' . $data_parent_id . '" value="Add" />
					
		   ';
		} else {
			$output .= '
				<input type="button" id="add_' . $name . '" name="add_' . $name . '" value="Add" />
				
	   ';
		}

		return $output;
	}

	public function generate_add_ready_script($name = null)
	{

		if ($name == null) $name = $this->name;

		$output = "
	      $('#add_" . $name . "').bind('click', add_" . $name . ");
	 ";
		return $output;
	}

	public function generate_add_script($name = null, $file_location = null, $module = null, $subforms = null, $parent_id = null, $add_array_options = null, $add_array_field = null, $url = "")
	{

		$output = "";
		if ($name == null) $name = $this->name;
		if ($file_location == null) $file_location = $this->file_location;
		if ($module == null) $module = $this->module;
		if ($subforms == null) $subforms = $this->subforms;
		if ($parent_id == null) $parent_id = $this->parent_id;
		if ($add_array_options == null) $add_array_options = $this->add_array_options;
		if ($add_array_field == null) $add_array_field = $this->add_array_field;

		$output = "
	        function add_" . $name . "() {

	          $('#" . $name . "_form_message_div').html('Adding');
						";


		if (!is_null($parent_id))
			$output .= 'var $this = $(this),' . $parent_id . ' = $this.data("' . $parent_id . '");
	  ';
		/**

$output.="$('#".$name."_form_message_div').html('#".$name."_edit_form_".$add_array_field." '+$('#".$name."_edit_form_".$add_array_field."').val());
";

$output.="$('#".$name."_form_message_div').html('#".$name."_edit_form_".$add_array_field."');
		 */


		$output .= "					
	//xxx

	
	
					  $('#record-nav').hide();
					  $('#return-search').show();
					  $('#" . $name . "_search_results_outer').hide();
						$('#all_forms_outer').show();

	          $('#" . $name . "_wrapper').show();
	          $('#" . $name . "_form_outer').show();
	          $('#" . $name . "_edit_form').show();
	          $('#" . $name . "_form_data_div').show();

	          $(document).scrollTop( $('#all_forms_outer').offset().top );

	          $.ajax({
						";
		/*	
	if (!is_null($parent_id))
	  $output.='data:{'.$parent_id.":".$parent_id."},
	  ";
*/

		$output .= 'data:{';

		if (!is_null($parent_id))
			$output .= $parent_id . ":" . $parent_id . ",'parent_id':'" . $parent_id . "'";

		if (!is_null($add_array_field)) {
			if (!is_null($parent_id)) $output .= ",";

			$output .= $add_array_field . ":$('#" . $name . "_edit_form_" . $add_array_field . "').val()";
		}

		/*
	  $output.='data:{'.$parent_id.":".$parent_id.",'parent_id':'".$parent_id."'},
	  ";
		*/


		$output .= "},
	";

		$output .= '					
          url: "';
		if ($url > "") {
			$output .= $url;
		} else {
			if (is_null($module)) {
				$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_add', 'pl', null, false, false, false);
			} else {
				$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_add', "module", $module, false, false, false);
			}
		}
		$output .= '",';
		$output .= "
	            type: 'POST',
	            success:  function(data){
	                $('#" . $name . "_form_data_div').html(data);
	            },
	            error: function( jqXHR, textStatus, errorThrown){
	               console.log(jqXHR.responseText);
	                alert('failure: ' + textStatus + ' - ' + errorThrown);
	            }   
	          });        
	        
	       }
		
		";
		//error_log("output: ".$output);
		return $output;
	}	// end generate_add_script



	public function generate_add_contact_script($name = null, $file_location = null, $module = null, $subforms = null, $parent_id = null, $add_array_options = null, $add_array_field = null)
	{

		if ($name == null) $name = $this->name;
		if ($file_location == null) $file_location = $this->file_location;
		if ($module == null) $module = $this->module;
		if ($subforms == null) $subforms = $this->subforms;
		if ($parent_id == null) $parent_id = $this->parent_id;
		if ($add_array_options == null) $add_array_options = $this->add_array_options;
		if ($add_array_field == null) $add_array_field = $this->add_array_field;

		$output = "
	        function add_" . $name . "() {

	          $('#" . $name . "_form_message_div').html('Adding');
						";

		$output .= "					
	
	
					  $('.tabs').hide();
					  $('#record-nav').hide();
					  $('#return-search').show();
					  $('#" . $name . "_search_results_outer').hide();
						$('#all_forms_outer').show();

	          $('#" . $name . "_wrapper').show();
	          $('#" . $name . "_form_outer').show();
	          $('#" . $name . "_edit_form').show();
	          $('#" . $name . "_form_data_div').show();
	          $(document).scrollTop( $('#all_forms_outer').offset().top );

						
	          $.ajax({
						";

		$output .= '					
          url: "';
		$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_add', 'pl', null, false, false, false);

		$output .= '",';
		$output .= "
	            type: 'POST',
	            success:  function(data){

	                $('#editing_div').html(data);
	            },
	            error: function( jqXHR, textStatus, errorThrown){
	               console.log(jqXHR.responseText);
	                alert('failure: ' + textStatus + ' - ' + errorThrown);
	            }   
	          });        
	        
	       }
		
		";
		return $output;
	}	// end generate_add_contact_script


	public function generate_download_button($data_parent_id = null, $parent_id = null, $name = null)
	{

		if ($name == null) $name = $this->name;
		if ($parent_id == null) $parent_id = $this->parent_id;

		if ($parent_id != null) {

			$output = '
					<input type="button" id="download_' . $name . '" name="download_' . $name . '" data-' . $parent_id . '="' . $data_parent_id . '" value="Download" />
					
		   ';
		} else {
			$output = '
				<input type="button" id="download_' . $name . '" name="download_' . $name . '" value="Download" />
				
	   ';
		}

		return $output;
	}

	public function generate_download_ready_script($name = null)
	{

		if ($name == null) $name = $this->name;

		$output = "
	      $('#download_" . $name . "').bind('click', download_" . $name . ");
	 ";
		return $output;
	}



	public function generate_edit_tr($data_id, $name = null, $id = null, $contact_name = null, $odd = "")
	{

		if ($name == null) $name = $this->name;
		if ($id == null) $id = $this->id;
		$output = "";
		$output .= "<tr class=\"contact_row ";
		$output .= $odd ? "odd" : "even";
		$output .= " " . $name . '_edit_button edit_button" ';
		$output .= 'data-' . $id . '="' . $data_id . '" ';
		$output .= 'data-contact-name="' . $contact_name . '" ';
		$output .= 'id="' . $name . '_edit_' . $data_id . '" title="' .  $data_id . '" ';
		$output .= 'name="' . $name . '_edit_' . $data_id . '"';
		$output .= ">";

		return $output;
	}

	public function generate_edit_button($data_id, $name = null, $id = null, $contact_name = null)
	{

		if ($name == null) $name = $this->name;
		if ($id == null) $id = $this->id;

		$output = '
	      <img src="' . IMAGE_PATH . '/edit-button.png" class="' . $name . '_edit_button edit_button" data-' . $id . '="' . $data_id . '" data-contact-name="' . $contact_name . '" id="' . $name . '_edit_' . $data_id . '" title="' .  $data_id . '" name="' . $name . '_edit_' . $data_id . '" value="Edit" />
	   ';

		return $output;
	}

	public function generate_delete_button($data_id, $data_parent_id = null, $name = null, $id = null, $parent_id = null)
	{

		if ($name == null) $name = $this->name;
		if ($id == null) $id = $this->id;
		if ($parent_id == null) $parent_id = $this->parent_id;

		$output = '
	      <img src="' . IMAGE_PATH . '/delete-button.png" class="' . $name . '_delete_button delete_button" data-' . $id . '="' . $data_id . '" data-' . $parent_id . '="' . $data_parent_id . '" id="' . $name . '_delete_' . $data_id . '" name="' . $name . '_delete_' . $data_id . '" value="Delete" />
	   ';

		return $output;
	}

	public function generate_search_ready_script($name = null)
	{

		if ($name == null) $name = $this->name;


		$output = '
					$(".search_input").keyup(function (e) {
		    if (e.keyCode == 13) {
		        $(".search_button").click();
		    }
		});
		
		$("#search_form").on("submit",function(e) {
		    e.preventDefault();
		});
		
  $(".search_button").bind("click", search);
  $(".search_button").click();

	';

		return $output;
	}

	public function generate_search_ready_script_old($name = null)
	{

		if ($name == null) $name = $this->name;

		$output = "
	      $('#search').bind('click', search_" . $name . ");
				
				$('." . $name . "_search_input').keydown( function (e) {
				    if (e.keyCode == 13) {
	    		    e.preventDefault();
			        $('#search').click();
							return false;
				    }
				 });
				
	  ";
		return $output;
	}

	public function generate_search_script($name = null, $file_location = null, $module = null, $subforms = null, $parent_id = null)
	{

		if ($name == null) $name = $this->name;
		if ($file_location == null) $file_location = $this->file_location;
		if ($module == null) $module = $this->module;
		if ($subforms == null) $subforms = $this->subforms;
		if ($parent_id == null) $parent_id = $this->parent_id;

		$output = '
   function search() {
 
      var formData = "";

      var $this = $(this);
      formData = $("#search_form").serializeArray();
					
      $("body").css("cursor", "progress");
      $.ajax({
        url: "';

		if (is_null($module)) {
			$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_search', 'pl', $module = $module, false, false, false);
		} else {
			$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_search', "module", $module, false, false, false);
		}

		$output .= '",
        type: "POST",
        data: formData,
                dataType: "json",
                success: function(data){
        
                    if (data["type"]=="timeout") {
        							alert("Session Timed Out.  Please refresh and login");
        						}
        				';
		$output .= "
							    $('body').css('cursor', 'default');
	
					  $('#record-nav').show();
					  $('#return-search').hide();
					  $('#" . $name . "_search_results_outer').show();
						$('#all_forms_outer').hide();

//	          $('#" . $name . "_wrapper').show();
//	          $('#" . $name . "_form_outer').show();
//	          $('#" . $name . "_edit_form').show();
//	          $('#" . $name . "_form_data_div').show();
//	          $(document).scrollTop( $('#all_forms_outer').offset().top );


                 $('#" . $name . "_search_results_data_div').html(data['" . $name . "_list']);
	                 
	                
	            },
        error:function( jqXHR, textStatus, errorThrown){
        //  console.log('abc');
           console.log(jqXHR.responseText);
            alert('failure1: ' + textStatus + ' - ' + errorThrown);
	          $('body').css('cursor', 'default');
        }   
      });        
    
   }
		
	";
		return $output;
	} // end generate_search_script


	public function generate_search_script_old($name = null, $file_location = null, $module = null, $subforms = null, $parent_id = null)
	{
		$output = "";

		/*
// working on a problem with the affiliation search	
	
$output="        function search_affiliation() {
alert('1');
	          $('body').css('cursor', 'progress');
	          $('#affiliation_search_results_data_div').html('Searching');
	          var formData = $('#affiliation_search_form').serializeArray();
	          $.ajax({
	            url: 'http://dev-scga.commandsystem.org/command/app/pl/committee/do_affiliation_search.php',
	            type: 'POST',
	            data: formData,
	            dataType: 'json',
	            success:  function(data){

							    $('body').css('cursor', 'default');
	                $('#affiliation_search_results_data_div').html('');
	                $('#affiliation_form_data_div').html('');
	  alert(data['type']);
	  alert(data['affiliation_value']);
	                if (data['type']=='form') {
  	                $('#affiliation_form_outer').show();
	                  $('#affiliation_wrapper').show();
	                  $('#affiliation_form_data_div').html(data['affiliation_value']);
										
	
										
	                  $(document).scrollTop( $('#affiliation_wrapper').offset().top );
	                } else {

	                 // $('#committee_form_outer').hide();
	                 $('#affiliation_form_data_div').html('');
									 
	                 // there are multiple values so create a selection list

	                 $('#affiliation_search_results_data_div').html(data['affiliation_value']);
	                 
	                }
	                
	            },
	            error: function( jqXHR, textStatus, errorThrown){
	            //  console.log('abc');
	               console.log(jqXHR.responseText);
	                alert('failure: ' + textStatus + ' - ' + errorThrown);
									$('body').css('cursor', 'default');
	            }   
	          });   
	          $('body').css('cursor', 'default');
	        
	         // alert($('#edit-search-name').val());
	       }
";
return $output;
	
	*/


		if ($name == null) $name = $this->name;
		if ($file_location == null) $file_location = $this->file_location;
		if ($module == null) $module = $this->module;
		if ($subforms == null) $subforms = $this->subforms;
		if ($parent_id == null) $parent_id = $this->parent_id;


		$output = "

		
	        function search_" . $name . "() {
	          $('body').css('cursor', 'progress');
	          $('#" . $name . "_search_results_data_div').html('Searching');
	          var formData = $('#" . $name . "_search_form').serializeArray();

	          $.ajax({
	            url: '";
		if (is_null($module)) {
			$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_search.php', 'pl', $module = $module);
		} else {
			$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_search.php', "module", $module = $module);
		}

		$output .= "',
	            type: 'POST',
	            data: formData,
	            dataType: 'json',
	            success:  function(data){

							    $('body').css('cursor', 'default');
	                $('#" . $name . "_search_results_data_div').html('');
	                $('#" . $name . "_form_data_div').html('');
	  
	                if (data['type']=='form') {
  	                $('#" . $name . "_form_outer').show();
										// $(" . '"' . "div[id$='_wrapper']." . '"' . ").show();
	                  $('#" . $name . "_wrapper').show();
	                  $('#" . $name . "_form_data_div').html(data['" . $name . "_value']);
										
	";


		if (!is_null($subforms))
			$output .= $subforms;

		$output .= "
										
	                  $(document).scrollTop( $('#" . $name . "_wrapper').offset().top );
	                } else {

	                 // $('#" . $name . "_form_outer').hide();
	                 $('#" . $name . "_form_data_div').html('');
									 
	                 // there are multiple values so create a selection list

	                 $('#" . $name . "_search_results_data_div').html(data['" . $name . "_value']);
	                 
	                }
	                
	            },
	            error: function( jqXHR, textStatus, errorThrown){
	              console.log('abc');
	               console.log(jqXHR.responseText);
	                alert('failure: ' + textStatus + ' - ' + errorThrown + ' - ' + jqXHR.responseText);
									$('body').css('cursor', 'default');
	            }   
	          });   
	          $('body').css('cursor', 'default');
	        
	         // alert($('#edit-search-name').val());
	       }
	  
	";

		return $output;
	}


	// generate_tab_search_script. used for screens that have multiple tabs to edit, e.g. event_occurrence.

	public function generate_tab_search_script($name = null, $file_location = null, $module = null, $subforms = null, $parent_id = null)
	{

		if ($name == null) $name = $this->name;
		if ($file_location == null) $file_location = $this->file_location;
		if ($module == null) $module = $this->module;
		if ($subforms == null) $subforms = $this->subforms;
		if ($parent_id == null) $parent_id = $this->parent_id;


		$output = "
		
	        function search_" . $name . "() {

	          $('#" . $name . "_search_results_data_div').html('Searching');
	          var formData = $('#" . $name . "_search_form').serializeArray();
	          $.ajax({
	            url: '";

		if (is_null($module)) {
			$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_search.php', 'pl');
		} else {
			$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_search.php', "module", $module);
		}

		$output .= "',
	            type: 'POST',
	            data: formData,
	            dataType: 'json',
	            success:  function(data){
								observer.publish('pageTabs/toggleTable', '8');

//	                $('#'.$name.'_search_results_data_div').html('');
//	                $('#'.$name.'_form_data_div').html('');
	  
	                if (data['type']=='form') {
  	                $('#" . $name . "_form_outer').show();
										// $(" . '"' . "div[id$='_wrapper']." . '"' . ").show();
	                  $('#" . $name . "_wrapper').show();
	                  $('#" . $name . "_form_data_div').html(data['" . $name . "_value']);
										
	";


		if (!is_null($subforms))
			$output .= $subforms;

		$output .= "
                  observer.publish('pageTabs/toggleTable', '9');
//                  observer.publish('pageTabs/record-index');
										
	                  $(document).scrollTop( $('#" . $name . "_wrapper').offset().top );
	                } else {
	                 // $('#" . $name . "_form_outer').hide();
	                 $('#" . $name . "_form_data_div').html('');
									 
	                 // there are multiple values so create a selection list

	                 $('#" . $name . "_search_results_data_div').html(data['" . $name . "_value']);
	                 
	                }
	                
	            },
	            error: function( jqXHR, textStatus, errorThrown){
	            //  console.log('abc');
	               console.log(jqXHR.responseText);
	                alert('failure: ' + textStatus + ' - ' + errorThrown);
	            }   
	          });        
	        
	         // alert($('#edit-search-name').val());
	       }
	  
	";

		return $output;
	}




	public function generate_edit_ready_script($name = null)
	{

		if ($name == null) $name = $this->name;


		$output = "
				
	    $('." . $name . "_edit_button').bind('click', edit_" . $name . ");
	
	  ";
		return $output;
	}

	public function generate_edit_subform_script($name = null, $file_location = null, $module = null, $subforms = null, $parent_id = null, $id = null, $search_type = null, $search_name = null)
	{

		$output = "";
		if ($name == null) $name = $this->name;
		if ($file_location == null) $file_location = $this->file_location;
		if ($module == null) $module = $this->module;
		if ($subforms == null) $subforms = $this->subforms;
		if ($parent_id == null) $parent_id = $this->parent_id;
		if ($id == null) $id = $this->id;
		if ($search_name == null) $search_name = $name;

		// subforms that have searches like in address_pl need to override their class parent id with text NULL

		if ($parent_id == "NULL") $parent_id = null;


		$output = '
	      function edit_' . $name . '() {
	        var $this = $(this);
				  ';

		if (!is_null($id))
			$output .= '
		       $' . $id . ' = $this.data("' . $id . '");
					 
	          ';

		if (!is_null($parent_id))
			$output .= '
		       $' . $parent_id . ' = $this.data("' . $parent_id . '");
	  ';

		$output .= '
	        $.ajax({
	          url: "';
		/*
						if (is_null($module)) {
	            $output.= get_web_address($search_name.WEB_SEPARATOR.'do_'.$search_name.'_search.php','pl');
						} else {
	            $output.= get_web_address($search_name.WEB_SEPARATOR.'do_'.$search_name.'_search.php',"module",$module);
						}
*/
		if (is_null($module)) {
			$output .= get_web_address($search_name . WEB_SEPARATOR . 'do_' . $search_name . '_search', 'pl', null, false, false, false);
		} else {
			$output .= get_web_address($search_name . WEB_SEPARATOR . 'do_' . $search_name . '_search', "module", $module, false, false, false);
		}


		$output .= '",
	          type: "POST",
	          data: {a:1';

		if (!is_null($parent_id)) {
			$output .= ',' . $parent_id . ': $' . $parent_id;
			$output .= ',"parent_id": "' . $parent_id . '"';
		}

		if (!is_null($id))
			$output .= ',' . $id . ': $' . $id;

		if (!is_null($search_type))
			$output .= ',search_type: "' . $search_type . '"';



		$output .= '},
	          dataType: "json",
	          success:  function(data){
								

								// $("div[id$=' . "'" . 'form_data_div' . "'" . ']").html("");
								$("div[id$=' . "'" . 'form_message_div' . "'" . ']").html("");
	
	              if (data["type"]=="form") {';




		$output .= '
	                  $("#' . $name . '_form_data_div").show();
	                $("#' . $name . '_form_outer").show();
	                $("#' . $name . '_form_data_div").html(data["' . $name . '_value"]);
									$("div[id$=' . "'_wrapper'" . ']").show();
//                  observer.publish("pageTabs/record-index");

	';

		if (!is_null($subforms))
			$output .= $subforms;

		$output .= '
									// alert($("#name_editing_div").html());
									//alert(data["name_editing_value"]);
									
	              } else {
	                $("#' . $name . '_form_outer").hide();
	                $("#' . $name . '_search_results_data_div").html(data["' . $name . '_value"]);
								}
								$(document).scrollTop( $("#' . $name . '_form_outer").offset().top );
	          },
	          error: function( jqXHR, textStatus, errorThrown){
	        
	             console.log(jqXHR.responseText);
	              alert("failure: " + textStatus + " - " + errorThrown);
	          }   
	        });        
	       
	     } // end edit_' . $name . '
	   ';
		return $output;
	}

	public function generate_edit_script($name = null, $file_location = null, $module = null, $subforms = null, $parent_id = null, $id = null, $search_type = null, $search_name = null, $toggle_tabs = null)
	{

		$output = "";

		if ($name == null) $name = $this->name;
		if ($toggle_tabs == null) $toggle_tabs = $this->toggle_tabs;
		if ($file_location == null) $file_location = $this->file_location;
		if ($module == null) $module = $this->module;
		if ($subforms == null) $subforms = $this->subforms;
		if ($parent_id == null) $parent_id = $this->parent_id;
		if ($id == null) $id = $this->id;
		if ($search_name == null) $search_name = $name;

		// subforms that have searches like in address_pl need to override their class parent id with text NULL

		if ($parent_id == "NULL") $parent_id = null;


		$output = '
	      function edit_' . $name . '() {

	        var $this = $(this);';

		if (!is_null($id))
			$output .= '
		       $' . $id . ' = $this.data("' . $id . '");
	  ';

		if (!is_null($parent_id))
			$output .= '
		       $' . $parent_id . ' = $this.data("' . $parent_id . '");
	  ';
		//error_log("gen:".get_web_address($search_name.WEB_SEPARATOR.'do_'.$search_name.'_search','pl',null,false,false,false));	
		$output .= '
					$("body").css("cursor", "progress");
					
	        $.ajax({
	          url: "';
		if (is_null($module)) {
			$output .= get_web_address($search_name . WEB_SEPARATOR . 'do_' . $search_name . '_search', 'pl', null, false, false, false);
		} else {
			$output .= get_web_address($search_name . WEB_SEPARATOR . 'do_' . $search_name . '_search', "module", $module, false, false, false);
		}
		$output .= '",
	          type: "POST",
	          data: {a:1,detail_type:"' . $name . '"';

		if (!is_null($parent_id))
			$output .= ',' . $parent_id . ': $' . $parent_id;

		if (!is_null($id))
			$output .= ',' . $id . ': $' . $id;

		if (!is_null($search_type))
			$output .= ',search_type: "' . $search_type . '"';



		$output .= '},
	          dataType: "json",
	          success:  function(data){

						  $("body").css("cursor", "default");
						  $("#' . $name . '_search_results_outer").hide();
						  $("#record-nav").hide();
						  $("#return-search").show();
              $("#name_editing_div").html(data["name_editing"]);
						  $("#all_forms_outer").show();
							//alert("' . $name . '_form_data_div");
						  $("#' . $name . '_form_data_div").show();
						  $("#editing_div").html(data["editing_div"]);
              $("#' . $name . '_form_message_div").html("");

              $("#' . $name . '_form_data_div").html(data["' . $name . '_value"]);
							';
		if (!is_null($id)) {
			$output .= '$("#' . $id . '").val($this.data("' . $id . '"));';
			//						  $output.= 'alert($("#'.$id.'").val());';
		}

		$output .= '},
	          error: function( jqXHR, textStatus, errorThrown){
	             $("body").css("cursor", "default");
	             console.log(jqXHR.responseText);
	              alert("failure: " + textStatus + " - " + errorThrown);
	          }   
	        });        
	       
	     } // end edit_' . $name . '
	   ';
		return $output;
	}

	public function generate_select_script($name = null, $file_location = null, $module = null, $subforms = null, $parent_id = null, $id = null, $search_type = null, $search_name = null, $toggle_tabs = null, $id_field = 'cid', $name_div = 'contact_name')
	{

		$output = "";
		if ($name == null) $name = $this->name;
		if ($toggle_tabs == null) $toggle_tabs = $this->toggle_tabs;
		if ($file_location == null) $file_location = $this->file_location;
		if ($module == null) $module = $this->module;
		if ($subforms == null) $subforms = $this->subforms;
		if ($parent_id == null) $parent_id = $this->parent_id;
		if ($id == null) $id = $this->id;
		if ($search_name == null) $search_name = $name;
		// subforms that have searches like in address_pl need to override their class parent id with text NULL

		if ($parent_id == "NULL") $parent_id = null;


		$output = '
	      function edit_' . $name . '() {

	        var $this = $(this);
					';

		if (!is_null($id))
			$output .= '
		       $' . $id . ' = $this.data("' . $id . '");
	  ';


		$output .= '
								$("#' . $id_field . '").val($this.attr("data-' . $id . '"));
								$("#' . $name_div . '").html($this.attr("data-contact-name"));
								
								// hide after search
								
								$("#individual_search").hide();
								$("#contact_search_results_outer").hide();
								
								// trigger change of hidden field
								
								$("#' . $id_field . '").trigger("change");
								
	
    ';

		$output .= "}
		 ";
		return $output;
	}  // end generate_select_script

	public function generate_delete_ready_script($name = null)
	{

		if ($name == null) $name = $this->name;

		$output = "
	    $('." . $name . "_delete_button').bind('click', delete_" . $name . ");
	  ";
		return $output;
	}

	public function generate_delete_script($name = null, $file_location = null, $module = null, $subforms = null, $parent_id = null, $id = null)
	{

		if ($name == null) $name = $this->name;
		if ($file_location == null) $file_location = $this->file_location;
		if ($module == null) $module = $this->module;
		if ($subforms == null) $subforms = $this->subforms;
		if ($parent_id == null) $parent_id = $this->parent_id;
		if ($id == null) $id = $this->id;

		$output = '
			 
	      function delete_' . $name . '() {
	        
	        var $this = $(this);';

		if (!is_null($id))
			$output .= '
		       $' . $id . ' = $this.data("' . $id . '");
	  ';

		if (!is_null($parent_id))
			$output .= '
		       $' . $parent_id . ' = $this.data("' . $parent_id . '");
	  ';

		$output .= '
	      $("#event_occurrence_form_message_div").html("Deleting");
	
				var answer = confirm("Delete?");
				if (answer){
	
	        $.ajax({
	          url: "';
		if (is_null($module)) {
			$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_delete', 'bll', null, false, false, false);
		} else {
			$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_delete', "module", $module, false, false, false);
		}

		$output .= '",
	          type: "POST",
	          data: { 
						';

		if (!is_null($parent_id))
			$output .= '
		       ' . $parent_id . ': $' . $parent_id . ',
	         "parent_id": "' . $parent_id . '"
	  ';

		if (!is_null($parent_id) && !is_null($id))
			$output .= ',';

		if (!is_null($id))
			$output .= '
		       ' . $id . ': $' . $id . '
	  ';


		$output .= '
						},
					
					
		          dataType: "json",
		          success:  function(data){
		            $("#' . $name . '_form_message_div").html(data);
		          
		             if (data["success"]) {
		               $("#' . $name . '_form_message_div").html("Delete Successful.");
		              // $("#' . $name . '_form_data_div").hide();
		               $("#' . $name . '_form_data_div").html("");;
		             //  $("#' . $name . '_edit_form_evoid").val(data["evoid"]);  // pn check this
		               $("#' . $name . '_search_results_data_div").html(data["list"]);
									 
		             } else {
		               $("#' . $name . '_form_message_div").html("Delete Failed.");
		             }
		          },
		          error: function( jqXHR, textStatus, errorThrown){
		             console.log(jqXHR.responseText);
		              alert("failure: " + textStatus + " - " + errorThrown);
		          }   
		  
			      });        
						alert("Deleted.")
	
	
					}
					else{
						alert("Not deleted.")
					}
	
	
		     
	     } // end delete_event_occurrence_info
	
	';
		return $output;
	}

	public function generate_add_search_script_1($name = null, $file_location = null, $module = null, $subforms = null, $parent_id = null)
	{

		if ($name == null) $name = $this->name;
		if ($file_location == null) $file_location = $this->file_location;
		if ($module == null) $module = $this->module;
		if ($subforms == null) $subforms = $this->subforms;
		if ($parent_id == null) $parent_id = $this->parent_id;

		$output = "
		
	  <script>
	  
	  
	    $(document).ready( function(){
	  
	      $('#search').bind('click', search_" . $name . ");
				
				$('." . $name . "_search_input').keydown( function (e) {
				    if (e.keyCode == 13) {
	    		    e.preventDefault();
			        $('#search').click();
							return false;
				    }
				});
	
				   
	        function search_" . $name . "() {
	          
	          $('#" . $name . "_search_results_data_div').html('Searching');
	          var formData = $('#" . $name . "_search_form').serializeArray();
	          $.ajax({
	            url: '";

		if (is_null($module)) {
			$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_search.php', 'pl', $module = $module);
		} else {
			$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_search.php', "module", $module = $module);
		}

		$output .= "',
	            type: 'POST',
	            data: formData,
	            dataType: 'json',
	            success:  function(data){
	                $('#" . $name . "_search_results_data_div').html('');
	                $('#" . $name . "_form_data_div').html('');
	  
	                if (data['type']=='form') {
										$(" . '"' . "div[id$='_wrapper']." . '"' . ").show();
	                  $('#" . $name . "_wrapper').show();
	                  $('#" . $name . "_form_data_div').html(data['" . $name . "_value']);";


		if (!is_null($subforms))
			$output .= $subforms;

		$output .= "
										
	                  $(document).scrollTop( $('#" . $name . "_form_outer').offset().top );
	                } else {
	                  $('#" . $name . "_form_outer').hide();
	                // there are multiple values so create a selection list
	                  $('#" . $name . "_search_results_data_div').html(data['" . $name . "_value']);
	                  // empty out subform divs
	                }
	                
	            },
	            error: function( jqXHR, textStatus, errorThrown){
	            //  console.log('abc');
	               console.log(jqXHR.responseText);
	                alert('failure: ' + textStatus + ' - ' + errorThrown);
	            }   
	          });        
	        
	         // alert($('#edit-search-name').val());
	       }
	  
	      $('#add_" . $name . "').bind('click', add_" . $name . ");
	    
	        function add_" . $name . "() {
	          
	          $('" . $name . "_search_results_data_div').html('Adding');
						";

		if (!is_null($parent_id))
			$output .= "var $this = $(this)," . $parent_id . " = $this.data('" . $parent_id . "');
	  ";

		$output .= "					
	          $('#" . $name . "_wrapper').show();
	           $(document).scrollTop( $('#" . $name . "_form_outer').offset().top );
	          
	          $.ajax({
						";

		if (!is_null($parent_id))
			$output .= $parent_id . ":" . $parent_id . ";
	  ";

		$output .= "					
	            url: '";
		if (is_null($module)) {
			$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_add.php', 'pl', $module = $module);
		} else {
			$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_add.php', "module", $module = $module);
		}
		$output .= "',
	            type: 'POST',
	            success:  function(data){
	                $('#" . $name . "_search_results_data_div').html('');
	                $('#" . $name . "_form_data_div').html(data);
	            },
	            error: function( jqXHR, textStatus, errorThrown){
	            //  console.log('abc');
	               console.log(jqXHR.responseText);
	                alert('failure: ' + textStatus + ' - ' + errorThrown);
	            }   
	          });        
	        
	         // alert($('#edit-search-name').val());
	       }
	      }
	    );
	  </script>
		
		";
		return $output;
	}

	public function generate_save_ready_script($name = null)
	{

		if ($name == null) $name = $this->name;

		$output = "
	      $('#" . $name . "_save').bind('click', save_" . $name . ");

	 ";
		return $output;
	}

	public function generate_save_copy_ready_script($name = null)
	{

		if ($name == null) $name = $this->name;

		$output = "
	      $('#" . $name . "_save_copy').bind('click', save_copy_" . $name . ");

	 ";
		return $output;
	}

	/*
		$url is used to override the processing url since this function can now be used from an api call.
	*/

	public function generate_save_script($name = null, $file_location = null, $module = null, $subforms = null, $parent_id = null, $id = null, $search_type = null, $search_name = null, $hide_on_save = true, $ckeditor_elements = array(), $url = "")
	{

		if ($name == null) $name = $this->name;
		if ($file_location == null) $file_location = $this->file_location;
		if ($module == null) $module = $this->module;
		if ($subforms == null) $subforms = $this->subforms;
		if ($parent_id == null) $parent_id = $this->parent_id;
		if ($id == null) $id = $this->id;

		if ($search_name == null) $search_name = $name;

		$output = "";

		if (!is_null($id))
			$output .= '
			       $' . $id . ' = $this.data("' . $id . '");
		  ';

		if (!is_null($parent_id))
			$output .= '
			       $' . $parent_id . ' = $this.data("' . $parent_id . '");
			';


		$output = '

     function save_' . $name . '() {

		 ';
		foreach ($ckeditor_elements as $cke) {
			$output .= '
	 			$("#' . $cke . '").val(CKEDITOR.instances.' . $cke . '.getData());
	 			';
		}

		$output .= '
        $(document).scrollTop( $("#' . $search_name . '_form_outer").offset().top );

        $("#' . $search_name . '_form_message_div").html("Saving");

        var formData = $("#' . $name . '_edit_form").serializeArray();

  
        $.ajax({
          url: "';
		if ($url > "")
			$output .= $url;
		else {
			if (is_null($module)) {
				$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_save', 'bll', null, false, false, false);
			} else {
				$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_save', "module", $module, false, false, false);
			}
		}
		$output .= '",
          type: "POST",
          data: formData,
          dataType: "json",
          success: function(data){
      
            /*  $("#' . $name . 'form_message_div").html(data); */

             if (data["success"]) {
						 alert("success");
						   message="Save Successful.";
							 if (typeof data["handicap_contact_success"] !== "undefined") {
							     if (data["handicap_contact_success"] == "success") {
									   message=message+"  Data push succeeded.";
									 } else if (data["handicap_contact_success"] !== "notupdated") {
									 	 message=message+" "+ data["handicap_contact_success"];
									 }
							 }
							 if (data["error"]>"") alert(data["error"]);
               $("#' . $search_name . '_form_search_results_data_div").html(data["list"]); 
               $("#' . $search_name . '_form_message_div").html(message);
               $("#' . $search_name . '_edit_form_' . $id . '").val(data["' . $id . '"]);
              ';
		if ($hide_on_save) {
			$output .= '$("#' . $search_name . '_search_results_data_div").html(data["list"]);';
		}
		$output .= '		 
     
							 if (data["add_flag"]) window.location.replace(data["destination"]);
      
							 ';
		if ($hide_on_save) $output .= '$("#' . $search_name . '_form_data_div").hide();';
		$output .= '
             } else {
               $("#' . $search_name . '_form_message_div").html("Save Failed.<br>".concat(data["error"]));
							 alert("Failed: ".concat(data["error"]));
             } 
          },  
          error:function( jqXHR, textStatus, errorThrown){
             console.log(jqXHR.responseText);
              alert("failure: " + textStatus + " - " + errorThrown);
          }   
        });        
       
     }
    ';

		return $output;
	}

	public function generate_save_copy_script($name = null, $file_location = null, $module = null, $subforms = null, $parent_id = null, $id = null, $search_type = null, $search_name = null, $hide_on_save = true, $ckeditor_elements = array(), $url = "")
	{

		if ($name == null) $name = $this->name;
		if ($file_location == null) $file_location = $this->file_location;
		if ($module == null) $module = $this->module;
		if ($subforms == null) $subforms = $this->subforms;
		if ($parent_id == null) $parent_id = $this->parent_id;
		if ($id == null) $id = $this->id;

		if ($search_name == null) $search_name = $name;

		$output = "";

		if (!is_null($id))
			$output .= '
			       $' . $id . ' = $this.data("' . $id . '");
		  ';

		if (!is_null($parent_id))
			$output .= '
			       $' . $parent_id . ' = $this.data("' . $parent_id . '");
			';


		$output = '

     function save_copy_' . $name . '() {

		 ';
		foreach ($ckeditor_elements as $cke) {
			$output .= '
	 			$("#' . $cke . '").val(CKEDITOR.instances.' . $cke . '.getData());
	 			';
		}

		$output .= '
        $(document).scrollTop( $("#' . $search_name . '_form_outer").offset().top );

        $("#' . $search_name . '_form_message_div").html("Saving");

        var formData = $("#' . $name . '_edit_form").serializeArray();

  
        $.ajax({
          url: "';
		if ($url > "")
			$output .= $url;
		else {
			if (is_null($module)) {
				$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_save_copy', 'bll', null, false, false, false);
			} else {
				$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_save_copy', "module", $module, false, false, false);
			}
		}
		$output .= '",
          type: "POST",
          data: formData,
          dataType: "json",
          success: function(data){
      
            /*  $("#' . $name . 'form_message_div").html(data); */

             if (data["success"]) {
						 alert("success");
						   message="Save a Copy Successful.";
							 if (typeof data["handicap_contact_success"] !== "undefined") {
							     if (data["handicap_contact_success"] == "success") {
									   message=message+"  Data push succeeded.";
									 } else if (data["handicap_contact_success"] !== "notupdated") {
									 	 message=message+" "+ data["handicap_contact_success"];
									 }
							 }
							 if (data["error"]>"") alert(data["error"]);
               $("#' . $search_name . '_form_search_results_data_div").html(data["list"]); 
               $("#' . $search_name . '_form_message_div").html(message);
               $("#' . $search_name . '_edit_form_' . $id . '").val(data["' . $id . '"]);
              ';
		if ($hide_on_save) {
			$output .= '$("#' . $search_name . '_search_results_data_div").html(data["list"]);';
		}
		$output .= '		 
     
							 if (data["add_flag"]) window.location.replace(data["destination"]);
      
							 ';
		if ($hide_on_save) $output .= '$("#' . $search_name . '_form_data_div").hide();';
		$output .= '
             } else {
               $("#' . $search_name . '_form_message_div").html("Save Failed.<br>".concat(data["error"]));
							 alert("Failed: ".concat(data["error"]));
             } 
          },  
          error:function( jqXHR, textStatus, errorThrown){
             console.log(jqXHR.responseText);
              alert("failure: " + textStatus + " - " + errorThrown);
          }   
        });        
       
     }
    ';

		return $output;
	}

	public function generate_save_contact_script($name = null, $file_location = null, $module = null, $subforms = null, $parent_id = null, $id = null, $search_type = null, $search_name = null, $hide_on_save = true)
	{
		if ($name == null) $name = $this->name;
		if ($file_location == null) $file_location = $this->file_location;
		if ($module == null) $module = $this->module;
		if ($subforms == null) $subforms = $this->subforms;
		if ($parent_id == null) $parent_id = $this->parent_id;
		if ($id == null) $id = $this->id;

		if ($search_name == null) $search_name = $name;

		$output = "";

		if (!is_null($id))
			$output .= '
			       $' . $id . ' = $this.data("' . $id . '");
		  ';

		$output = '

     function save_' . $name . '() {

        $(document).scrollTop( $("#' . $search_name . '_form_outer").offset().top );

        $("#' . $search_name . '_form_message_div").html("Saving");

        var formData = $("#' . $name . '_edit_form").serializeArray();

  
        $.ajax({
          url: "';
		if (is_null($module)) {
			$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_save', 'bll', null, false, false, false);
		} else {
			$output .= get_web_address($name . WEB_SEPARATOR . 'do_' . $name . '_save', "module", $module, false, false, false);
		}
		$output .= '",
          type: "POST",
          data: formData,
          dataType: "json",
          success: function(data){
      
            /*  $("#' . $name . 'form_message_div").html(data); */

             if (data["success"]) {
						 alert("success");
						   message="Save Successful.";
               $("#' . $search_name . '_form_message_div").html(message);
               $("#' . $search_name . '_edit_form_' . $id . '").val(data["' . $id . '"]);
							 $(".tabs").show();
							 $("#cid").val(data["cid"]);
              ';
		$output .= '
             } else {
               $("#' . $search_name . '_form_message_div").html("Save Failed.<br>".concat(data["error"]));
							 alert("Failed: ".concat(data["error"]));
             } 
          },  
          error:function( jqXHR, textStatus, errorThrown){
             console.log(jqXHR.responseText);
              alert("failure: " + textStatus + " - " + errorThrown);
          }   
        });        
       
     }
    ';

		return $output;
	}  // end generate_save_contact_script

	public function generate_change_history_ready_script($name = null)
	{

		if ($name == null) $name = $this->name;

		$output = "
	      $('#" . $name . "_change_history').bind('click', " . $name . "_change_history);
	 ";
		//	 error_log("change history".$output);
		return $output;
	}

	public function generate_change_history_button($name = null)
	{

		if ($name == null) $name = $this->name;

		$output = '
	      <input type="button" id="' . $name . '_change_history" name="' . $name . '_change_history" class="cs change_history_button" value="Change History" />
	   ';

		return $output;
	}

	public function generate_change_history_script($name = null, $file_location = null, $module = null, $subforms = null, $parent_id = null, $id = null, $search_type = null, $search_name = null)
	{

		if ($name == null) $name = $this->name;
		if ($file_location == null) $file_location = $this->file_location;
		if ($module == null) $module = $this->module;
		if ($subforms == null) $subforms = $this->subforms;
		if ($parent_id == null) $parent_id = $this->parent_id;
		if ($id == null) $id = $this->id;

		if ($search_name == null) $search_name = $name;

		$output = "";

		if (!is_null($id))
			$output .= '
			       $' . $id . ' = $this.data("' . $id . '");
		  ';

		if (!is_null($parent_id))
			$output .= '
			       $' . $parent_id . ' = $this.data("' . $parent_id . '");
		  ';


		$output = '

     function ' . $name . '_change_history() {

//        $(document).scrollTop( $("#' . $search_name . '_form_outer").offset().top );

//        $("#' . $search_name . '_form_message_div").html("Saving");

        var formData = $("#' . $name . '_edit_form").serializeArray();
				 formData[(formData.length)]=[["name","table_name"],["value","' . $search_name . '"]];
         x=formData.length;
				 formData[x]=new Array;
				 formData[x]["name"]="table_name";
				 formData[x]["value"]="' . $search_name . '";

         x=formData.length;
				 formData[x]=new Array;
				 formData[x]["name"]="primary_id";
				 formData[x]["value"]="' . $id . '";
  
        $.ajax({
          url: "';
		$output .= get_web_address('add_change_log/do_add_change_history_report.php', 'pl');
		$output .= '",
          type: "POST",
          data: formData,
          dataType: "json",
          success: function(data){
      
  //            $("#' . $name . 'form_message_div").html(data);
          
             if (data["success"]) {
						     alert(data["history"]);
//               $("#' . $search_name . '_form_message_div").html("Save Successful.");
//               $("#' . $search_name . '_edit_form_' . $id . '").val(data["' . $id . '"]);
              ';
		$output .= '
             } else {
//               $("#' . $search_name . '_form_message_div").html("Save Failed.<br>".concat(data["error"]));
 								alert("Failure");
             } 
          },  
          error:function( jqXHR, textStatus, errorThrown){
             console.log(jqXHR.responseText);
              alert("failure: " + textStatus + " - " + errorThrown);
          }   
        });        
       
     }
    ';

		return $output;
	}

	public function generate_cancel_button($name = null)
	{

		if ($name == null) $name = $this->name;

		$output = '
	      <input type="button" id="' . $name . '_cancel" name="' . $name . '_cancel" class="cs cancel_button" value="Cancel" />
	   ';

		return $output;
	}

	public function generate_cancel_ready_script($name = null)
	{

		if ($name == null) $name = $this->name;

		$output = "
	      $('#" . $name . "_cancel').bind('click', cancel_" . $name . ");
	 ";
		return $output;
	}

	public function generate_cancel_script($name = null, $file_location = null, $module = null, $subforms = null, $parent_id = null, $id = null, $search_type = null, $search_name = null, $hide_on_save = true)
	{

		if ($name == null) $name = $this->name;
		if ($file_location == null) $file_location = $this->file_location;
		if ($module == null) $module = $this->module;
		if ($subforms == null) $subforms = $this->subforms;
		if ($parent_id == null) $parent_id = $this->parent_id;
		if ($id == null) $id = $this->id;
		if ($search_name == null) $search_name = $name;


		$output = '

     function cancel_' . $name . '() {
        $(document).scrollTop( $("#' . $search_name . '_wrapper").offset().top );
				$("#' . $name . '_form_outer").hide();
				$("#' . $name . '_search_results_data_div").show();
       $("#' . $search_name . '_form_message_div").html("");
			 ';
		$output .= '	 
		}';


		return $output;
	}
}

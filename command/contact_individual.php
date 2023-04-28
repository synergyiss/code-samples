/*
Copyright 2023
United States Golf Association
Paul Niebuhr

contact_individual.php

This program is the controller for the Command system
contact_individual table.

*/

<?php
require_once "app/settings/config.inc.php";

require_once COMMON_FUNCTIONS_FILE;
require_once FUNCTION_BLL_CLASS_FILE;
require_once MODULE_PL_CLASS_FILE;
require_once get_file("contact_pl.class.php");
require_once get_file("contact_individual_pl.class.php");


$module_pl = new Module_pl_class;
$contact_pl = new Contact_pl_class;
$contact_individual_pl = new Contact_individual_pl_class;

?>
<script>
	$("#contact_individual_icon").addClass("engaged");
</script>

<input type="text" id="cid" style="visibility:hidden;display:none;" />

<?php
// add the id bar to the top of the page

print $contact_individual_pl->generate_page_id_bar(); ?>



<div class="page-search-bar">
	<form name="search_form" id="search_form">
		<div class="page-search-bar-inner">
			<span class="icon-"><i class="fa fa-search" aria-hidden="true"></i></span>
			<?php
			if (isset($_REQUEST["search_term"])) {
				$search_term = $_REQUEST["search_term"];
			} else {
				$search_term = "";
			}


			print '<input type="text" class="search-bar-term" name="search_term" value="' . $search_term . '"/>'

			?>
		</div> <!-- page-search-bar-inner -->
		<div class="page-search-bar-type">
			<input type="radio" class="search_type" name="search_type" value="name" checked /> Name
			<input type="radio" class="search_type" name="search_type" value="address" /> Address
			<input type="radio" class="search_type" name="search_type" value="handicap" /> GHIN
			<input type="radio" class="search_type" name="search_type" value="email" /> Email
			<input type="radio" class="search_type" name="search_type" value="user_name" /> User
			<input type="radio" class="search_type" name="search_type" value="vip_number" /> VIP
		</div> <!-- page-search-bar-type -->
		<div class="page-search-bar-go">
			<input type="hidden" name="show_header" value="0">
			<input type="hidden" name="ajax" value="1">
			<button class="search_button" name="search_button">Go</button>
		</div> <!-- page-search-bar-go -->
	</form>
</div> <!-- page-search-bar -->

<div id="content-inner-div">

	<div id="contact_individual_search_results_outer" class="wrapper_panel page-content-item large">
		<div id="contact_individual_search_results_inner">
			<div id="contact_individual_search_results_data_div">
			</div> <!-- contact_individual_search_results_data_div -->
		</div> <!-- contact_individual_search_results_inner -->
	</div> <!-- contact_individual_search_results_outer -->



	<div id="all_forms_outer" class="page-content-item large" style="display:none">

		<div class="return-results hidden" id="return-results">
			<a href="#" class="return-results-button"><span class="icon-arrow-left"></span>Return to search results</a>
		</div>

		<div id="tabs-content">
			<div class="tabs">
				<ul class="custom-tabs-ui">
					<li class="contact_tab" id="contact_individual_wrapper_tab"><a href="#contact_individual_wrapper">Contact</a></li>
					<li class="contact_tab" id="address_wrapper_tab"><a href="#address_wrapper">Address</a></li>
					<li class="contact_tab" id="contact_phone_wrapper_tab"><a href="#contact_phone_wrapper">Phone</a></li>
					<li class="contact_tab" id="email_contact_wrapper_tab"><a href="#email_contact_wrapper">Email</a></li>
					<li class="contact_tab" id="contact_url_wrapper_tab"><a href="#contact_url_wrapper">Social</a></li>
					<li class="contact_tab" id="image_contact_wrapper_tab"><a href="#image_contact_wrapper">Image</a></li>
					<li class="contact_tab" id="text_contact_wrapper_tab"><a href="#text_contact_wrapper">Text</a></li>
					<li class="contact_tab" id="contact_club_relation_wrapper_tab"><a href="#contact_club_relation_wrapper">Club</a></li>
					<li class="contact_tab" id="subscription_contact_wrapper_tab"><a href="#subscription_contact_wrapper">Subscription</a></li>
					<li class="contact_tab" id="association_contact_wrapper_tab"><a href="#association_contact_wrapper">Association</a></li>
					<li class="contact_tab" id="handicap_contact_wrapper_tab"><a href="#handicap_contact_wrapper">Handicap</a></li>
					<li class="contact_tab" id="function_contact_wrapper_tab"><a href="#function_contact_wrapper">Function</a></li>
					<li class="contact_tab" id="affiliation_contact_wrapper_tab"><a href="#affiliation_contact_wrapper">Affiliation</a></li>
					<li class="contact_tab" id="committee_contact_wrapper_tab"><a href="#committee_contact_wrapper">Committee</a></li>
					<!--
            <li class="contact_tab" id="championship_contact_wrapper_tab"><a href="#championship_contact_wrapper">Championships</a></li>
-->
					<?php
					print $module_pl->get_contact_tabs(1);
					?>
				</ul>

			</div>


		</div> <!-- tabs-content -->

		<div id="contact_right_div" class="contact_right_div">
			<div id="name_editing_div" class="name-editing-div">
			</div> <!-- name_editing_div -->
			<div id="editing_div">
			</div> <!-- editing_div -->

		</div> <!-- contact_right_div -->

	</div> <!-- all_forms_outer -->
</div> <!-- content-inner-div -->
<script>
	$(document).ready(function() {

		<?php print $contact_individual_pl->generate_add_ready_script(); ?>

		<?php print $contact_individual_pl->generate_add_contact_script(); ?>

		$(".search_input").keyup(function(e) {
			if (e.keyCode == 13) {
				$(".search_button").click();
			}
		});

		$("#search_form").on('submit', function(e) {
			e.preventDefault();
		});

	});

	$(".search_button").bind("click", search);
	$(".contact_tab").bind("click", contact_tab);

	function contact_tab() {
		var $this = $(this);
		id = $this.attr("id");
		detail_type = id.substring(0, id.length - 12);

		module = $this.attr("data-module");
		module_location = $this.attr("data-location");


		cid = $('#cid').val();

		formData = {
			cid: cid,
			detail_type: detail_type,
			ajax: 1,
			show_header: 0,
			module: module,
			module_location,
			module_location
		};
		$('body').css('cursor', 'progress');

		$.ajax({
			url: "<?php

					//				print URL_ADDRESS.WEB_SEPARATOR; ?page=contact_individual/do_contact_individual_search"
					print get_web_address("contact_individual/do_contact_individual_search", "pl", null, false, false, false);

					?>",
			type: "POST",
			data: formData,
			dataType: "json",
			success: function(data) {

				$('body').css('cursor', 'default');

				if (data["type"] == "timeout") {
					alert("Session Timed Out.  Please refresh and login");
					return;
				}

				$("div[id$='form_data_div']").html("form data");
				$("div[id$='form_message_div']").html("form message");

				$("#editing_div").html(data["editing_div"]);
				$("#" + detail_type + "_form_data_div").html(data["contact_individual_value"]);
				$("#" + detail_type + "_search_results_data_div").html(data["contact_individual_list"]);
				$("#name_editing_div").html(data["name_editing"]);

				// there are multiple values so create a selection list
				// empty out subform divs


			},
			error: function(jqXHR, textStatus, errorThrown) {
				//  console.log("abc");
				console.log(jqXHR.responseText);
				alert("failure1: " + textStatus + " - " + errorThrown);
				$('body').css('cursor', 'default');
			}
		});

	}

	/*

	search - javascript handler for ajax button press for search

	*/

	function search(cid, search_type) {

		//alert($('input[name=search_type]:checked').val());

		var formData = "";

		if (isFinite(String(cid))) {

			formData = {
				cid: cid,
				detail_type: search_type,
				ajax: 1,
				show_header: 0
			};


		} else {

			var $this = $(this);
			formData = $("#search_form").serializeArray();

		} /* (else) not numeric */
		//alert(formData);          

		$('body').css('cursor', 'progress');
		$.ajax({
			url: "<?php

					//				print URL_ADDRESS.WEB_SEPARATOR; ?page=contact_individual/do_contact_individual_search"
					print get_web_address("contact_individual/do_contact_individual_search", "pl", null, false, false, false);

					?>",
			type: "POST",
			data: formData,
			dataType: "json",
			success: function(data) {
				//alert(data);
				if (data["type"] == "timeout") {
					alert("Session Timed Out.  Please refresh and login");
				}

				$(".page-id-bar-inner-record-nav").html(data['record_nav'] + data['return_link']);
				$("div[id$='name_editing_div']").html(data['name_editing']);
				$("div[id$='form_data_div']").html("");
				$("div[id$='form_message_div']").html("");
				if ((data["cid"]) > "") {
					// there this is an intial page load with a cid passed in
					$("#record-nav").hide();
					$("#return-search").hide();
					$("#all_forms_outer").show();
					$("#contact_individual_search_results_outer").hide();
					$("#cid").val(data["cid"]);
					$("#editing_div").html(data["editing_div"]);

				} else {
					$("#record-nav").show();
					$("#return-search").hide();
					$("#all_forms_outer").hide();
					$("#contact_individual_search_results_outer").show();
				}


				// there are multiple values so create a selection list
				$("#contact_individual_search_results_data_div").html(data["contact_individual_list"]);

				$("#contact_individual_form_data_div").html(data["contact_individual_value"]);

				$('body').css('cursor', 'default');

			},
			error: function(jqXHR, textStatus, errorThrown) {
				//  console.log("abc");
				console.log(jqXHR.responseText);
				alert("failure1: " + textStatus + " - " + errorThrown);
				$('body').css('cursor', 'default');
			}
		});

		// alert($("#edit-search-name").val());
	}


	<?php

	// if page was called with a cid parameter, do an automatic search for that cid

	if (isset($_REQUEST["cid"])) {

		print "search(" . $_REQUEST["cid"] . ",'contact_individual');\n";
	}
	?>
</script>
<?php
/*
Copyright 2023
United States Golf Association
Paul Niebuhr

contact_individual_dal.class.php

This program is the data access layer for the Command system
contact_individual table.

*/

require_once DB_CLASS_FILE;

class Contact_individual_dal_class extends Db_class
{

  public function __construct()
  {

    parent::__construct();

    // Define fields for this table

    $this->field_definitions = array(
      'cid' => array('type' => 'n', 'length' => 0, 'default' => 0),
      'prefix_name' => array('type' => 's', 'length' => 20, 'default' => '', 'label' => "Prefix Name", 'input_type' => 'text', 'mandatory' => false, 'help_text' => '', 'options' => array()),
      'first_name' => array('type' => 's', 'length' => 100, 'default' => '', 'label' => "First Name", 'input_type' => 'text', 'mandatory' => false, 'help_text' => '', 'options' => array()),
      'middle_name' => array('type' => 's', 'length' => 100, 'default' => '', 'label' => "Middle Name", 'input_type' => 'text', 'mandatory' => false, 'help_text' => '', 'options' => array()),
      'last_name' => array('type' => 's', 'length' => 100, 'default' => '', 'label' => "Last Name", 'input_type' => 'text', 'mandatory' => false, 'help_text' => '', 'options' => array()),
      'suffix_name' => array('type' => 's', 'length' => 45, 'default' => '', 'label' => "Suffix Name", 'input_type' => 'text', 'mandatory' => false, 'help_text' => '', 'options' => array()),
      'preferred_first_name' => array('type' => 's', 'length' => 100, 'default' => '', 'label' => "Preferred First Name", 'input_type' => 'text', 'mandatory' => false, 'help_text' => '', 'options' => array()),
      'preferred_last_name' => array('type' => 's', 'length' => 100, 'default' => '', 'label' => "Preferred Last Name", 'input_type' => 'text', 'mandatory' => false, 'help_text' => '', 'options' => array()),
      'gender' => array('type' => 's', 'length' => 1, 'default' => ''),
      'shirt_size' => array('type' => 's', 'length' => 20, 'default' => '', 'label' => "Shirt Size", 'input_type' => 'text', 'mandatory' => false, 'help_text' => '', 'options' => array()),
      'jacket_size' => array('type' => 's', 'length' => 20, 'default' => ''),
      'company' => array('type' => 's', 'length' => 150, 'default' => ''),
      'title' => array('type' => 's', 'length' => 100, 'default' => '', 'label' => "Title", 'input_type' => 'text', 'mandatory' => false, 'help_text' => '', 'options' => array()),
      'birth_date' => array('type' => 'd', 'length' => 10, 'default' => ''),
      'death_date' => array('type' => 'd', 'length' => 10, 'default' => ''),
      'spouse_name' => array('type' => 's', 'length' => 100, 'default' => '', 'label' => "Spouse First Name", 'input_type' => 'text', 'mandatory' => false, 'help_text' => '', 'options' => array()),
      'spouse_last_name' => array('type' => 's', 'length' => 100, 'default' => '', 'label' => "Spouse Last Name", 'input_type' => 'text', 'mandatory' => false, 'help_text' => '', 'options' => array()),
      'salutation' => array('type' => 's', 'length' => 150, 'default' => ''),
      'donor_prefix' => array('type' => 's', 'length' => 20, 'default' => ''),
      'rid' => array('type' => 'n', 'length' => 0, 'default' => 0),
      'pro' => array('type' => 'n', 'length' => 0, 'default' => 0),
      'pro_year' => array('type' => 'n', 'length' => 4, 'default' => 0),
      'year_started_volunteering' => array('type' => 'n', 'length' => 4, 'default' => 0),
      'birthplace_country' => array('type' => 's', 'length' => 50, 'default' => ''),
      'passport_number' => array('type' => 's', 'length' => 50, 'default' => ''),
      'ssn' => array('type' => 's', 'length' => 20, 'default' => ''),
      'handedness' => array('type' => 's', 'length' => 1, 'default' => 'R'),
      'reinstated_amateur' => array('type' => 'n', 'length' => 0, 'default' => 0),
      'reinstated_year' => array('type' => 'n', 'length' => 4, 'default' => 0),
    );

    $this->table = "contact_individual"; // table name

    $this->id = "cid"; // primary key
    $this->name = "";
  }
}  // End of Contact_class

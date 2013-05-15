<?php

/**
 * Model for categories in the business directory
 *
 */

class BusinessDirectoryCategory extends Category {
	
	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
		
		// set the table
		$this->setTableName('businessdirectorycategory');
		
		// unique constraint on name
		$this->unique(array("name")); 
	}
	/**
	 * Contructor method for custom functionality - add the fields to be marked as dates
	 */
	public function construct() {
		parent::construct();
		// set the custom error messages
       	$this->addCustomErrorMessages(array(
       									"name.notblank" => $this->translate->_("global_name_error"),
       									"name.unique" => $this->translate->_("global_name_unique"),
       									"name.length" => $this->translate->_("global_name_length_error")							
       	       						));
	}
	public function setUp() {
		parent::setUp();
		
		// match the parent id
		$this->hasOne('BusinessDirectoryCategory as parent',
						 array(
								'local' => 'parentid',
								'foreign' => 'id'
							)
					); 
	}
	
	function processPost($formvalues) {
		// check if the parentid is specified
		if (isArrayKeyAnEmptyString('parentid', $formvalues)) {
			$formvalues['parentid'] = NULL;
		}
		// trim spaces from the name field
		if (!isArrayKeyAnEmptyString('name', $formvalues)) {
			$formvalues['name'] = trim($formvalues['name']);
		}
		parent::processPost($formvalues);
	}
}

?>
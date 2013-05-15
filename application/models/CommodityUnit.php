<?php

/**
 * Model for the units of measure for commodities 
 *
 */

class CommodityUnit extends BaseEntity {
	
	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
		
		$this->setTableName('commodityunit');
		
		$this->hasColumn('name', 'string', 15, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('description', 'string', 255);
		
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
	/*
	 * Custom model processing
	 */
	function processPost($formvalues){
		// trim spaces from the name field
		if (!isArrayKeyAnEmptyString('name', $formvalues)) {
			$formvalues['name'] = trim($formvalues['name']);
		}
		// debugMessage($formvalues);
		parent::processPost($formvalues);
	}
}

?>
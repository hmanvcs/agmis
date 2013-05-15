<?php

/**
 * Model for a water body
 *
 */

class WaterBody extends BaseEntity {
	
	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
		
		$this->setTableName('waterbody');
		$this->hasColumn('name', 'string', 50, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('description', 'string', 500);
		
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
}

?>
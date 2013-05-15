<?php

/**
 * Model for a a category from which the commodity categories, price categories extend 
 *
 */

class Category extends BaseEntity {
	
	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
		
		$this->hasColumn('name', 'string', 150, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('description', 'string', 500);
		$this->hasColumn('parentid', 'integer'); 
	}
	/**
	 * Contructor method for custom functionality - add the fields to be marked as dates
	 */
	public function construct() {
		parent::construct();
		// set the custom error messages
       	$this->addCustomErrorMessages(array(
       									"name.notblank" => $this->translate->_("global_name_error"),	
       									"name.length" => $this->translate->_("global_name_length_error")						
       	       						));
	}
}

?>
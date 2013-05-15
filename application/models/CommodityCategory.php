<?php

/**
 * Model for commodity categories
 *
 */

class CommodityCategory extends Category {
	
	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
		
		// set the table
		$this->setTableName('commoditycategory');
		
		// unique constraint on name
		$this->unique(array("name")); 
	}
	
	public function setUp() {
		parent::setUp();
		
		// match the parent id
		$this->hasOne('CommodityCategory as parent',
						 array(
								'local' => 'parentid',
								'foreign' => 'id'
							)
					); 
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
	
	# determine commodityid from searchable name
    function findByName($name) {
    	$str_len = strlen($name);
    	trim($name);
    	$name = str_replace('category', '', strtolower($name));
    	
		$conn = Doctrine_Manager::connection();
		// query for check if location exists
		$unique_query = "SELECT id FROM commoditycategory WHERE LOWER(name) LIKE LOWER('%".$name."%') OR LOWER(name) LIKE LOWER('%".$name."%') OR LOWER(name) LIKE LOWER('%".$name."%') ";
		$result = $conn->fetchOne($unique_query);
		// debugMessage($unique_query);
		// debugMessage($result);
		return $result; 
	}
}

?>
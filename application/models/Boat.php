<?php

/**
 * Model for landing site records 
 *
 */

class Boat extends BaseEntity {

	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
				
		$this->setTableName('boat');
		$this->hasColumn('owner', 'string', 255, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('name', 'string', 50, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('description', 'string', 500);
		$this->hasColumn('type', 'string', 1, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('landingsiteid', 'integer', array('notnull' => true, 'notblank' => true));
		$this->hasColumn('regno', 'string', 15, array('notnull' => true, 'notblank' => true));
		
	} 
	/**
	 * Contructor method for custom functionality - add the fields to be marked as dates
	 */
	public function construct() {
		parent::construct();
		// set the custom error messages
       	$this->addCustomErrorMessages(array(
       									"owner.notblank" => $this->translate->_("boat_owner_error"),
       									"name.notblank" => $this->translate->_("boat_name_error"),
       									"type.notblank" => $this->translate->_("boat_type_error"),
       									"regno.notblank" => $this->translate->_("boat_regno_error"),
       									"landingsiteid.notblank" => $this->translate->_("boat_landingsiteid_error")
       	       						));
	}
	public function setUp() {
		parent::setUp(); 
		
		$this->hasOne('LandingSite as landingsite',
						 array(
								'local' => 'landingsiteid',
								'foreign' => 'id'
							)
					); 
		$this->hasOne('WaterBody as waterbody',
						 array(
								'local' => 'waterbodyid',
								'foreign' => 'id'
							)
					); 
	} 
	
	function processPost($formvalues){
			
		parent::processPost($formvalues);
	}	
	
	
}

?>
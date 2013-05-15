<?php

/**
 * Model for crop production records 
 *
 */

class CropProduction extends BaseEntity {

	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
				
		$this->setTableName('cropproduction');
		
		$this->hasColumn('commodityid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('locationid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('collectedbyid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('seedcost', 'decimal', 11, array('default' => '0'));
		$this->hasColumn('yieldperacre', 'decimal', 11, array('default' => '0'));
		$this->hasColumn('startdate','date', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('enddate','date', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('datecollected','date', null, array('notnull' => true, 'notblank' => true));
	} 
	
	public function setUp() {
		parent::setUp(); 
		
		$this->hasOne('District as location',
						 array(
								'local' => 'locationid',
								'foreign' => 'id'
							)
					); 
		
		$this->hasOne('Commodity as commodity',
						 array(
								'local' => 'commodityid',
								'foreign' => 'id'
							)
					); 
		$this->hasOne('UserAccount as collectedby',
						 array(
								'local' => 'collectedbyid',
								'foreign' => 'id'
							)
					); 
	} 
	
	function processPost($formvalues){
		
		if(isArrayKeyAnEmptyString('seedcost', $formvalues)){
			unset($formvalues['seedcost']); 
		}
		
		if(isArrayKeyAnEmptyString('yieldperacre', $formvalues)){
			unset($formvalues['yieldperacre']); 
		}
				
		parent::processPost($formvalues);
	}	
	
	
}

?>
<?php

/**
 * Model for livestock production records 
 *
 */

class LivestockProduction extends BaseEntity {

	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
				
		$this->setTableName('livestockproduction');
		
		$this->hasColumn('commodityid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('locationid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('collectedbyid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('breedcost', 'decimal', 11, array('default' => '0'));
		$this->hasColumn('yield', 'decimal', 11, array('default' => '0'));
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
		
		if(isArrayKeyAnEmptyString('breedcost', $formvalues)){
			unset($formvalues['breedcost']); 
		}
		
		if(isArrayKeyAnEmptyString('yield', $formvalues)){
			unset($formvalues['yield']); 
		}
				
		parent::processPost($formvalues);
	}	
	
	
}

?>
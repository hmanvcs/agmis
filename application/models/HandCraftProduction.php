<?php

/**
 * Model for hand craft production records 
 *
 */

class HandCraftProduction extends BaseEntity {

	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
				
		$this->setTableName('handcraftproduction');
		
		$this->hasColumn('commodityid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('locationid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('collectedbyid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('retail', 'decimal', 11, array('default' => '0'));
		$this->hasColumn('wholesale', 'decimal', 11, array('default' => '0'));
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
		
		if(isArrayKeyAnEmptyString('retail', $formvalues)){
			unset($formvalues['retail']); 
		}
		
		if(isArrayKeyAnEmptyString('wholesale', $formvalues)){
			unset($formvalues['wholesale']); 
		}
				
		parent::processPost($formvalues);
	}	
	
	
}

?>
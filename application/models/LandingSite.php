<?php

/**
 * Model for landing site records 
 *
 */

class LandingSite extends BaseEntity {

	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
				
		$this->setTableName('landingsite');
		$this->hasColumn('name', 'string', 50, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('description', 'string', 500);
		$this->hasColumn('waterbodyid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('districtid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('subcountyid','integer', array('notnull' => true, 'notblank' => true));
		$this->hasColumn('parishid','integer', array('notnull' => true, 'notblank' => true));
	} 
	
	public function setUp() {
		parent::setUp(); 
		
		$this->hasOne('District as district',
						 array(
								'local' => 'districtid',
								'foreign' => 'id'
							)
					); 
		$this->hasOne('Subcounty as subcounty',
						 array(
								'local' => 'subcountyid',
								'foreign' => 'id'
							)
					); 
		$this->hasOne('Parish as parish',
						 array(
								'local' => 'parishid',
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

	
}

?>
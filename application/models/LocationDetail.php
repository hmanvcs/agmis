<?php

/**
 * Model for the details of a location
 *
 */

class LocationDetail extends BaseEntity {
	
	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
		
		$this->setTableName('locationdetail');
		$this->hasColumn('summary', 'clob');
		$this->hasColumn('agriculturalinputprices', 'clob');
		$this->hasColumn('businesssector', 'clob');
		$this->hasColumn('tourismsector', 'clob');
		$this->hasColumn('cropproduction', 'clob');
		$this->hasColumn('livestockproduction', 'clob');
		$this->hasColumn('handcraftsector', 'clob');
		$this->hasColumn('fishproduction', 'clob');
	}
	
	public function setUp() {
		parent::setUp(); 
		
		// the location for which the details are being provided
		$this->hasOne('District as location',
						 array(
								'local' => 'id',
								'foreign' => 'id'
							)
					); 
	}
}

?>
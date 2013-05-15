<?php

/**
 * Model for Crop Sector Information records 
 *
 */

class CropSectorInformation extends BaseEntity {

	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
				
		$this->setTableName('cropsectorinformation');
		
		$this->hasColumn('commodityid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('locationid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('seedsource', 'clob');
		$this->hasColumn('pests', 'clob');
		$this->hasColumn('diseases', 'clob');
		$this->hasColumn('creditsource', 'clob');
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
	} 
	
}

?>
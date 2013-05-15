<?php

/**
 * Model for handcraft Information records 
 *
 */

class HandCraftInformation extends BaseEntity {

	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
				
		$this->setTableName('handcraftinformation');
		
		$this->hasColumn('commodityid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('locationid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('materialsource', 'clob');
		$this->hasColumn('materialtype', 'clob');
		$this->hasColumn('market', 'clob');
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
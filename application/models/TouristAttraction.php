<?php

/**
 * Model for a Tourist attraction in a 
 *
 */

class TouristAttraction extends BaseEntity {

	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
				
		$this->setTableName('touristattraction');
		
		$this->hasColumn('name', 'string', 255, array('notnull' => true, 'notblank' => true)); 
		$this->hasColumn('physicallocation', 'string', 255); 
		$this->hasColumn('districtid', 'integer', 11, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('subcountyid', 'integer');
		$this->hasColumn('touristattractions', 'clob');
		$this->hasColumn('priceoffers', 'clob');
		$this->hasColumn('accomodation', 'clob');
		$this->hasColumn('entertainment', 'clob');
		$this->hasColumn('transport', 'clob');
		$this->hasColumn('otherservices', 'clob');
		$this->hasColumn('booking', 'clob');
		$this->hasColumn('tourpackages', 'clob');
		$this->hasColumn('contact', 'string', 255); 
	} 
	
	public function setUp() {
		parent::setUp(); 
		
		$this->hasOne('District as district',
						 array(
								'local' => 'districtid',
								'foreign' => 'id'
							)
					); 
		// use a Location since it may be both subcounty and Municipality
		$this->hasOne('Subcounty as subcounty',
						 array(
								'local' => 'subcountyid',
								'foreign' => 'id'
							)
					); 
	} 
	function processPost($formvalues){
		//unset the none number attributes if they are not required fields
		if(isArrayKeyAnEmptyString('subcountyid', $formvalues)){
			unset($formvalues['subcountyid']); 
		}
		parent::processPost($formvalues);
	}
	
	
}

?>